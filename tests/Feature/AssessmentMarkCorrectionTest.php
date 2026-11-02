<?php

namespace Tests\Feature;

use App\Livewire\Cbt\CbtViewer;
use App\Livewire\CourseManagement\AssessmentReviewQueue;
use App\Livewire\StudentManagement\CourseView\StudentAssessmentTaker;
use App\Models\Assessment\Assessment;
use App\Models\Assessment\LearningSession;
use App\Models\Assessment\Question;
use App\Models\Assessment\StudentAnswer;
use App\Models\Core\User;
use App\Models\Credentials\Certificate;
use App\Models\Learning\Course;
use App\Models\Learning\Lesson;
use App\Models\Learning\Section;
use App\Services\AssessmentResultService;
use App\Services\AssessmentReviewService;
use App\Services\CbtAttemptService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class AssessmentMarkCorrectionTest extends TestCase
{
    use RefreshDatabase;

    private function fixture(bool $final = true, bool $automatic = false): array
    {
        Role::findOrCreate('instructor', 'web');
        $teacher = User::factory()->create(['role' => 'instructor']);
        $learner = User::factory()->create();
        $course = Course::withoutEvents(fn () => Course::create(['title' => 'Correction course', 'slug' => 'correction-'.Str::uuid(), 'instructor_id' => $teacher->id, 'is_published' => true, 'is_approved' => true]));
        $learner->courses()->attach($course->id);
        $section = Section::create(['course_id' => $course->id, 'title' => 'Review section', 'order' => 1]);
        $lesson = Lesson::withoutEvents(fn () => Lesson::create(['section_id' => $section->id, 'title' => 'Review lesson', 'slug' => 'review-'.Str::uuid(), 'published_at' => now(), 'order' => 1]));
        $assessment = Assessment::create(['course_id' => $course->id, 'lesson_id' => $lesson->id, 'title' => 'Correction assessment', 'type' => 'quiz', 'pass_percentage' => 70, 'estimated_duration_minutes' => 10, 'max_attempts' => 2]);
        $one = Question::create(['assessment_id' => $assessment->id, 'question_type' => $automatic ? 'multiple_choice' : 'essay', 'question_text' => 'Explain your approach', 'options' => $automatic ? ['First', 'Second'] : [], 'explanation' => 'PRIVATE RUBRIC', 'correct_answers' => $automatic ? [0] : ['PRIVATE KEY'], 'points' => 4, 'order' => 1]);
        $two = Question::create(['assessment_id' => $assessment->id, 'question_type' => 'short_answer', 'question_text' => 'Explain the tradeoff', 'points' => 6, 'order' => 2]);
        $learner->getOrCreateGamificationData()->update(['daily_quests' => [], 'last_quest_reset' => now()]);
        $this->actingAs($learner);
        $session = app(CbtAttemptService::class)->start($learner, $assessment, true);
        app(CbtAttemptService::class)->submit($learner, $assessment, $session->id, [$one->id => $automatic ? '0' : 'My approach', $two->id => 'A reasoned tradeoff']);
        $first = StudentAnswer::where('question_id', $one->id)->firstOrFail();
        $last = StudentAnswer::where('question_id', $two->id)->firstOrFail();
        if (!$automatic) app(AssessmentReviewService::class)->grade($teacher, $session->id, $first->id, 3, 'Original feedback');
        if ($final) app(AssessmentReviewService::class)->grade($teacher, $session->id, $last->id, 4, 'Tradeoff feedback');
        return compact('teacher', 'learner', 'course', 'lesson', 'assessment', 'one', 'two', 'first', 'last', 'session');
    }

    private function correct(array $f, $points = 2, array $options = []): array
    {
        return app(AssessmentReviewService::class)->correct($options['reviewer'] ?? $f['teacher'], $f['session']->id, $options['answer'] ?? $f['first']->id,
            $points, $options['feedback'] ?? 'Corrected feedback', $options['reason'] ?? 'The rubric credit was counted incorrectly.',
            $options['revision'] ?? app(AssessmentReviewService::class)->revision($f['session']->fresh()), $options['operation'] ?? (string) Str::uuid());
    }

    private function denied(callable $action, int $status): void
    {
        try { $action(); $this->fail('Expected refusal'); }
        catch (HttpException $exception) { $this->assertSame($status, $exception->getStatusCode()); }
    }

    public function test_correction_retains_original_mark_results_clock_and_private_review_log(): void
    {
        $f = $this->fixture();
        $before = $f['session']->fresh(); $originalAnswer = $f['first']->fresh();
        $f['assessment']->update(['pass_percentage' => 100]); $f['one']->update(['points' => 100]);
        $result = $this->correct($f);
        $after = $f['session']->fresh(); $event = $after->metadata['mark_corrections'][0];
        $this->assertEquals(60, $result['percentage']); $this->assertFalse($result['passed']);
        $this->assertEquals(10, $result['max_points']); $this->assertEquals(2, $f['first']->fresh()->points_earned);
        $this->assertEquals(3, $event['before']['points']); $this->assertEquals(2, $event['after']['points']);
        $this->assertSame('Original feedback', $event['before']['feedback']);
        $this->assertEquals($before->metadata['results'], $event['before_result']); $this->assertEquals($result, $event['after_result']);
        $this->assertEquals($before->metadata['review_log'], $after->metadata['review_log']);
        $this->assertSame($before->getRawOriginal('started_at'), $after->getRawOriginal('started_at'));
        $this->assertSame($before->getRawOriginal('ended_at'), $after->getRawOriginal('ended_at'));
        $this->assertSame($originalAnswer->answer, $f['first']->fresh()->answer);
        $this->assertSame($before->metadata['results']['graded_at'], $result['graded_at']);
        $this->assertArrayNotHasKey('metadata', $after->toArray());
    }

    public function test_replay_is_idempotent_and_a_different_payload_cannot_reuse_the_operation(): void
    {
        $f = $this->fixture(); $operation = (string) Str::uuid(); $revision = app(AssessmentReviewService::class)->revision($f['session']->fresh());
        $result = $this->correct($f, 2, ['operation' => $operation, 'revision' => $revision]);
        $this->assertEquals($result, $this->correct($f, 2, ['operation' => $operation, 'revision' => $revision]));
        $this->assertCount(1, $f['session']->fresh()->metadata['mark_corrections']);
        $this->denied(fn () => $this->correct($f, 1, ['operation' => $operation, 'revision' => $revision]), 409);
        $this->assertEquals(2, $f['first']->fresh()->points_earned);
    }

    public function test_stale_corrections_are_refused_instead_of_overwriting_a_later_decision(): void
    {
        $f = $this->fixture(); $revision = app(AssessmentReviewService::class)->revision($f['session']->fresh());
        $this->correct($f, 2);
        $this->denied(fn () => $this->correct($f, 1, ['revision' => $revision]), 409);
        $this->assertCount(1, $f['session']->fresh()->metadata['mark_corrections']);
        $this->assertEquals(2, $f['first']->fresh()->points_earned);
    }

    public function test_role_ownership_self_grading_and_cross_attempt_answer_guards_apply(): void
    {
        $f = $this->fixture(); $other = User::factory()->create(['role' => 'instructor']);
        foreach ([$f['learner'], $other] as $reviewer) $this->denied(fn () => $this->correct($f, 2, ['reviewer' => $reviewer]), 403);
        $foreign = StudentAnswer::create(['user_id' => $f['learner']->id, 'assessment_id' => $f['assessment']->id, 'question_id' => $f['one']->id, 'attempt_number' => 9, 'submitted_at' => now(), 'graded_at' => now()]);
        $this->expectModelNotFound(fn () => $this->correct($f, 2, ['answer' => $foreign->id]));
        $f['learner']->update(['role' => 'instructor']); $f['course']->update(['instructor_id' => $f['learner']->id]);
        $this->denied(fn () => $this->correct($f, 2, ['reviewer' => $f['learner']->fresh()]), 403);
        $this->assertArrayNotHasKey('mark_corrections', $f['session']->fresh()->metadata);
    }

    private function expectModelNotFound(callable $action): void
    {
        try { $action(); $this->fail('Expected missing owned record'); }
        catch (\Illuminate\Database\Eloquent\ModelNotFoundException $exception) { $this->assertNotEmpty($exception->getModel()); }
        catch (HttpException $exception) { $this->assertSame(404, $exception->getStatusCode()); }
    }

    public function test_mark_bounds_precision_reason_and_no_change_are_validated_without_writes(): void
    {
        $f = $this->fixture();
        foreach ([[-1, []], [5, []], [1.001, []], [2, ['reason' => 'short']], [2, ['reason' => str_repeat('x', 2001)]], [3, ['feedback' => 'Original feedback']]] as [$points, $options]) {
            try { $this->correct($f, $points, $options); $this->fail('Invalid correction accepted'); }
            catch (ValidationException $exception) { $this->assertNotEmpty($exception->errors()); }
        }
        $this->assertEquals(3, $f['first']->fresh()->points_earned);
        $this->assertArrayNotHasKey('mark_corrections', $f['session']->fresh()->metadata);
        try { app(AssessmentReviewService::class)->grade($f['teacher'], $f['session']->id, $f['first']->id, 3.001); $this->fail('Excess mark precision accepted'); }
        catch (ValidationException $exception) { $this->assertArrayHasKey('points', $exception->errors()); }
    }

    public function test_pending_automatic_and_unsupported_historical_records_cannot_be_corrected(): void
    {
        $pending = $this->fixture(false);
        $this->denied(fn () => $this->correct($pending), 422);
        $automatic = $this->fixture(true, true);
        $this->denied(fn () => $this->correct($automatic), 422);
        $historical = $this->fixture(); $metadata = $historical['session']->fresh()->metadata; unset($metadata['grading']);
        $historical['session']->update(['metadata' => $metadata]);
        $this->denied(fn () => $this->correct($historical), 422);
        $this->assertEquals(3, $historical['first']->fresh()->points_earned);
    }

    public function test_correction_changes_future_eligibility_without_rewriting_rewards_completions_or_credentials(): void
    {
        $f = $this->fixture();
        $f['learner']->completedLessons()->attach($f['lesson']->id, ['completed_at' => now()]);
        $certificate = Certificate::create(['user_id' => $f['learner']->id, 'course_id' => $f['course']->id, 'status' => 'issued', 'verification_code' => 'PRESERVED-CODE', 'metadata' => ['original' => true]]);
        $certificate->forceFill(['verification_token' => 'preserved-token'])->save();
        $original = $certificate->fresh()->getAttributes(); $transactions = $f['learner']->gamificationTransactions()->count();
        $this->assertTrue($f['assessment']->isPassedByStudent($f['learner']->id));
        $this->correct($f);
        $this->assertFalse($f['assessment']->isPassedByStudent($f['learner']->id));
        $this->assertFalse(app(\App\Services\CertificateService::class)->canRequestCertificate($f['learner']->id, $f['course']->id)['can_request']);
        $this->assertEquals($original, $certificate->fresh()->getAttributes());
        $this->assertTrue($f['learner']->completedLessons()->where('lessons.id', $f['lesson']->id)->exists());
        $this->assertSame($transactions, $f['learner']->gamificationTransactions()->count());
        $this->assertSame(50, $f['learner']->getOrCreateGamificationData()->total_points);
        $this->correct($f, 4);
        $this->assertTrue($f['assessment']->isPassedByStudent($f['learner']->id));
        $this->assertSame($transactions, $f['learner']->gamificationTransactions()->count());
    }

    public function test_mark_and_audit_roll_back_together_if_result_persistence_fails(): void
    {
        $f = $this->fixture(); $before = $f['session']->fresh()->metadata;
        Event::listen('eloquent.updating: '.LearningSession::class, fn () => throw new \RuntimeException('Forced audit failure'));
        try { $this->correct($f); $this->fail('Expected audit failure'); }
        catch (\RuntimeException $exception) { $this->assertSame('Forced audit failure', $exception->getMessage()); }
        finally { Event::forget('eloquent.updating: '.LearningSession::class); }
        $this->assertEquals(3, $f['first']->fresh()->points_earned);
        $this->assertEquals($before, $f['session']->fresh()->metadata);
    }

    public function test_owner_request_is_retained_once_per_mark_and_correcting_resolves_it_atomically(): void
    {
        $f = $this->fixture(); $service = app(AssessmentReviewService::class); $reason = 'Please reconsider the credit for my explanation.';
        $request = $service->requestMarkReview($f['learner'], $f['assessment'], 1, $f['first']->id, $reason);
        $this->assertEquals($request, $service->requestMarkReview($f['learner'], $f['assessment'], 1, $f['first']->id, $reason));
        $this->assertSame(1, $f['session']->fresh()->metadata['open_mark_reviews']);
        $this->assertEquals(70, $f['assessment']->getStudentResults($f['learner']->id)['percentage']);
        try { $service->requestMarkReview($f['learner'], $f['assessment'], 1, $f['first']->id, 'A different duplicate request.'); $this->fail('Duplicate accepted'); }
        catch (ValidationException $exception) { $this->assertArrayHasKey('reason', $exception->errors()); }
        $this->correct($f, 4);
        $metadata = $f['session']->fresh()->metadata;
        $this->assertSame(0, $metadata['open_mark_reviews']); $this->assertSame('corrected', $metadata['mark_reviews'][0]['status']);
        $this->assertSame($metadata['mark_corrections'][0]['id'], $metadata['mark_reviews'][0]['correction_id']);
        $this->assertSame($request['reason'], $metadata['mark_reviews'][0]['reason']);
        $service->requestMarkReview($f['learner'], $f['assessment'], 1, $f['first']->id, 'Please reconsider the updated feedback as well.');
        $this->assertCount(2, $f['session']->fresh()->metadata['mark_reviews']);
    }

    public function test_upheld_decision_is_explained_replay_safe_and_cannot_reopen_the_same_mark(): void
    {
        $f = $this->fixture(); $service = app(AssessmentReviewService::class);
        $request = $service->requestMarkReview($f['learner'], $f['assessment'], 1, $f['first']->id, 'Please review this recorded mark.');
        $revision = $service->revision($f['session']->fresh()); $operation = (string) Str::uuid(); $reason = 'The rubric requires an explanation of the tradeoff.';
        $before = $f['session']->fresh()->metadata['results'];
        $decision = $service->upholdMark($f['teacher'], $f['session']->id, $request['id'], $reason, $revision, $operation);
        $this->assertSame('upheld', $decision['status']);
        $this->assertEquals($decision, $service->upholdMark($f['teacher'], $f['session']->id, $request['id'], $reason, $revision, $operation));
        $this->denied(fn () => $service->upholdMark($f['teacher'], $f['session']->id, $request['id'], 'A different decision reason.', $revision, (string) Str::uuid()), 409);
        $this->assertEquals($before, $f['session']->fresh()->metadata['results']);
        $this->assertSame(0, $f['session']->fresh()->metadata['open_mark_reviews']);
        $this->assertSame('upheld', $service->requestMarkReview($f['learner'], $f['assessment'], 1, $f['first']->id, $request['reason'])['status']);
    }

    public function test_new_learner_request_invalidates_a_reviewers_stale_decision(): void
    {
        $f = $this->fixture(); $service = app(AssessmentReviewService::class); $revision = $service->revision($f['session']->fresh());
        $request = $service->requestMarkReview($f['learner'], $f['assessment'], 1, $f['first']->id, 'Please review this recorded mark.');
        $this->denied(fn () => $this->correct($f, 2, ['revision' => $revision]), 409);
        $this->denied(fn () => $service->upholdMark($f['teacher'], $f['session']->id, $request['id'], 'The original credit is justified.', $revision, (string) Str::uuid()), 409);
        $this->assertSame('open', $f['session']->fresh()->metadata['mark_reviews'][0]['status']);
    }

    public function test_requests_reject_another_owner_attempt_pending_or_automatic_answer(): void
    {
        $f = $this->fixture(); $service = app(AssessmentReviewService::class); $outsider = User::factory()->create();
        $this->expectModelNotFound(fn () => $service->requestMarkReview($outsider, $f['assessment'], 1, $f['first']->id, 'Please review this recorded mark.'));
        $this->expectModelNotFound(fn () => $service->requestMarkReview($f['learner'], $f['assessment'], 2, $f['first']->id, 'Please review this recorded mark.'));
        $pending = $this->fixture(false);
        $this->denied(fn () => $service->requestMarkReview($pending['learner'], $pending['assessment'], 1, $pending['first']->id, 'Please review this recorded mark.'), 422);
        $automatic = $this->fixture(true, true);
        $this->denied(fn () => $service->requestMarkReview($automatic['learner'], $automatic['assessment'], 1, $automatic['first']->id, 'Please review this recorded mark.'), 422);
    }

    public function test_projection_and_personal_results_show_only_owned_safe_history(): void
    {
        $f = $this->fixture(); $this->correct($f);
        $service = app(AssessmentResultService::class);
        $safe = $service->responses($f['assessment'], $f['learner'], 1);
        $this->assertEquals(3, $safe[0]['corrections'][0]['before_points']);
        $this->assertEquals(2, $safe[0]['corrections'][0]['after_points']);
        foreach (['PRIVATE RUBRIC', 'PRIVATE KEY', 'reviewer_id', 'correct_answers', 'before_result', 'operation_id'] as $private) $this->assertStringNotContainsString($private, json_encode($safe));
        Livewire::actingAs($f['learner'])->test(CbtViewer::class)->call('viewAssessmentDetails', $f['assessment']->id)->call('viewAttemptDetails', 1)
            ->assertSee('60%')->assertSee('Mark correction history')->assertDontSee('PRIVATE KEY');
        $other = User::factory()->create(); $this->actingAs($other);
        $this->denied(fn () => $service->responses($f['assessment'], $f['learner'], 1), 403);
        Livewire::test(CbtViewer::class)->call('viewAssessmentDetails', $f['assessment']->id)->assertNotFound();
    }

    public function test_shared_learner_request_action_and_retained_results_work_after_enrollment_ends(): void
    {
        $f = $this->fixture();
        Livewire::actingAs($f['learner'])->test(StudentAssessmentTaker::class, ['lesson' => $f['lesson']])->call('showAssessmentResults', $f['assessment']->id)
            ->set('reviewReasons.'.$f['first']->id, 'short')->call('requestMarkReview', $f['first']->id)->assertHasErrors('reviewReasons.'.$f['first']->id)
            ->set('reviewReasons.'.$f['first']->id, 'Please reconsider my explanation.')->call('requestMarkReview', $f['first']->id)->assertSee('Mark review requested.')->assertSee('Awaiting a decision');
        $f['learner']->courses()->detach();
        Livewire::test(CbtViewer::class)->call('viewAssessmentDetails', $f['assessment']->id)->call('viewAttemptDetails', 1)
            ->set('reviewReasons.'.$f['last']->id, 'Please reconsider the tradeoff mark.')->call('requestMarkReview', $f['last']->id)->assertHasNoErrors()->assertSee('Mark review requested.');
        $this->assertSame(2, $f['session']->fresh()->metadata['open_mark_reviews']);
    }

    public function test_queue_filters_corrections_and_decisions_keep_server_issued_context_locked(): void
    {
        $f = $this->fixture(); $request = app(AssessmentReviewService::class)->requestMarkReview($f['learner'], $f['assessment'], 1, $f['last']->id, 'Please reconsider the tradeoff mark.');
        $component = Livewire::actingAs($f['teacher'])->test(AssessmentReviewQueue::class)->set('status', 'requested')->assertSee('Correction assessment')
            ->call('review', $f['session']->id)->call('beginCorrection', $f['first']->id)
            ->set('correctionPoints', 9)->set('correctionReason', 'The rubric credit was counted incorrectly.')->call('publishCorrection')->assertHasErrors('correctionPoints')
            ->set('correctionPoints', 2)->call('publishCorrection')->assertHasNoErrors()->assertSee('Correction published.')
            ->set('decisionReasons.'.$request['id'], 'The original credit matches the rubric.')->call('upholdMark', $request['id'])->assertSee('Review decision recorded.');
        $this->assertSame(0, $f['session']->fresh()->metadata['open_mark_reviews']);
        $this->expectException(\Livewire\Features\SupportLockedProperties\CannotUpdateLockedPropertyException::class);
        $component->set('reviewRevision', str_repeat('a', 64));
    }

    public function test_staff_action_rechecks_ownership_after_mount_and_other_instructors_cannot_list_results(): void
    {
        $f = $this->fixture(); $other = User::factory()->create(['role' => 'instructor']);
        Livewire::actingAs($other)->test(AssessmentReviewQueue::class)->set('status', 'finalized')->assertDontSee('Correction assessment')->call('review', $f['session']->id)->assertForbidden();
        $component = Livewire::actingAs($f['teacher'])->test(AssessmentReviewQueue::class)->call('review', $f['session']->id)->call('beginCorrection', $f['first']->id);
        $f['course']->update(['instructor_id' => $other->id]);
        $component->call('publishCorrection')->assertForbidden();
        $this->assertEquals(3, $f['first']->fresh()->points_earned);
    }
}
