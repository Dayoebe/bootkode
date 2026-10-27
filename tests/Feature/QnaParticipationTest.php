<?php

namespace Tests\Feature;

use App\Livewire\CourseManagement\CourseBuilder\QnaCriteriaManager;
use App\Livewire\StudentManagement\CourseView\AssessmentDiscussion;
use App\Livewire\StudentManagement\CourseView\StudentAssessmentTaker;
use App\Models\Assessment\Assessment;
use App\Models\Assessment\LearningSession;
use App\Models\Assessment\Question;
use App\Models\Assessment\StudentAnswer;
use App\Models\Core\User;
use App\Models\Learning\Course;
use App\Models\Learning\Lesson;
use App\Models\Learning\Section;
use App\Services\AssessmentResultService;
use App\Services\AssessmentReviewService;
use App\Services\CbtAttemptService;
use App\Services\QnaDiscussionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class QnaParticipationTest extends TestCase
{
    use RefreshDatabase;

    private function fixture(array $options = []): array
    {
        Role::findOrCreate('instructor', 'web');
        $teacher = User::factory()->create(['role' => 'instructor']);
        $learner = User::factory()->create(); $peer = User::factory()->create();
        $course = Course::withoutEvents(fn () => Course::create(['title' => 'Private Q&A course', 'slug' => 'qna-'.Str::uuid(), 'instructor_id' => $teacher->id, 'is_published' => true, 'is_approved' => true]));
        $section = Section::create(['course_id' => $course->id, 'title' => 'Discussion', 'order' => 1]);
        $lesson = Lesson::withoutEvents(fn () => Lesson::create(['section_id' => $section->id, 'title' => 'Reasoned responses', 'slug' => 'qna-'.Str::uuid(), 'published_at' => now(), 'order' => 1]));
        $assessment = Assessment::create(['course_id' => $course->id, 'lesson_id' => $lesson->id, 'title' => 'Discuss practical tradeoffs', 'type' => 'qna', 'pass_percentage' => 70, 'estimated_duration_minutes' => 20, 'max_attempts' => 2]);
        $question = Question::create(['assessment_id' => $assessment->id, 'question_type' => 'qna_topic', 'question_text' => 'Explain your choice', 'explanation' => 'Use evidence and examples.', 'is_required' => true, 'points' => 10,
            'options' => array_merge(['version' => 1, 'topic_type' => 'discussion', 'min_responses' => 2, 'min_response_length' => 20,
                'allow_peer_review' => true, 'moderator_approval' => true, 'discussion_prompts' => ['Consider the tradeoff.'],
                'evaluation_criteria' => [['name' => 'PRIVATE EVALUATION', 'weight' => 100, 'description' => 'PRIVATE GUIDANCE']]], $options)]);
        foreach ([$learner, $peer] as $user) { $user->courses()->attach($course->id); $user->getOrCreateGamificationData()->update(['daily_quests' => [], 'last_quest_reset' => now()]); }
        $this->actingAs($learner);
        return compact('teacher', 'learner', 'peer', 'course', 'section', 'lesson', 'assessment', 'question');
    }
    private function responses(): array { return ['My first original response explains the practical evidence.', 'My second original response considers a different tradeoff.']; }
    private function submit(array $f, ?User $user = null): array
    {
        $user ??= $f['learner']; $this->actingAs($user);
        $session = app(CbtAttemptService::class)->start($user, $f['assessment'], true);
        $result = app(CbtAttemptService::class)->submit($user, $f['assessment'], $session->id, [$f['question']->id => $this->responses()]);
        return [$session->fresh(), StudentAnswer::where('user_id', $user->id)->where('assessment_id', $f['assessment']->id)->where('attempt_number', $result['attempt_number'])->firstOrFail()];
    }
    private function denied(callable $action, int $status): void
    {
        try { $action(); $this->fail('Expected refusal'); } catch (HttpException $e) { $this->assertSame($status, $e->getStatusCode()); }
    }
    private function service(): QnaDiscussionService { return app(QnaDiscussionService::class); }
    private function publish(array $f, StudentAnswer $answer): void
    {
        $owner = User::findOrFail($answer->user_id);
        $this->service()->share($owner, $f['assessment'], $answer->id);
        $this->service()->moderate($f['teacher'], $f['assessment'], $answer->id, $answer->fresh()->exam_data['qna']['version'], 'publish', 'Approved for course discussion.');
    }

    public function test_original_rules_and_private_evaluation_are_snapshotted_and_resume_survives_edits(): void
    {
        $f = $this->fixture(); $s = app(CbtAttemptService::class)->start($f['learner'], $f['assessment'], true);
        app(CbtAttemptService::class)->saveAnswer($f['learner'], $f['assessment'], $s->id, $f['question']->id, $this->responses());
        $f['question']->update(['options' => []]);
        Livewire::actingAs($f['learner'])->test(StudentAssessmentTaker::class, ['lesson' => $f['lesson']])->call('startAssessment', $f['assessment']->id)
            ->assertSet('questions.0.discussion.min_responses', 2)->assertSet('answers.'.$f['question']->id, $this->responses())->assertSee('Consider the tradeoff.')->assertDontSee('PRIVATE EVALUATION');
        $this->assertSame('PRIVATE GUIDANCE', $s->fresh()->metadata['grading'][$f['question']->id]['criteria']['evaluation_criteria'][0]['description']);
        $this->assertStringNotContainsString('PRIVATE', json_encode(app(CbtAttemptService::class)->publicQuestions($s)));
    }
    public function test_incomplete_short_duplicate_and_oversized_responses_cannot_submit(): void
    {
        $f = $this->fixture(); $s = app(CbtAttemptService::class)->start($f['learner'], $f['assessment'], true);
        foreach ([[], ['Too short'], [$this->responses()[0]], [$this->responses()[0], $this->responses()[0]], [str_repeat('x', 4001)], array_fill(0, 3, str_repeat('x', 3900)), ['bad' => 'An associative payload is invalid']] as $value) {
            $this->denied(fn () => app(CbtAttemptService::class)->submit($f['learner'], $f['assessment'], $s->id, [$f['question']->id => $value]), 422);
        }
        $this->assertDatabaseCount('student_answers', 0); $this->assertNull($s->fresh()->ended_at);
    }
    public function test_optional_blank_responses_are_not_counted_as_answered(): void
    {
        $f = $this->fixture(); $f['question']->update(['is_required' => false]);
        $s = app(CbtAttemptService::class)->start($f['learner'], $f['assessment'], true);
        $result = app(CbtAttemptService::class)->submit($f['learner'], $f['assessment'], $s->id, [$f['question']->id => ['', ' ']]);
        $this->assertSame(0, $result['answered_questions']); $this->assertTrue($result['pending_review']);
        $answer = StudentAnswer::first(); $this->assertNull($answer->answer);
        $this->denied(fn () => $this->service()->share($f['learner'], $f['assessment'], $answer->id), 422);
    }
    public function test_expiry_retains_actual_saved_work_without_inventing_required_responses(): void
    {
        $f = $this->fixture(); $s = app(CbtAttemptService::class)->start($f['learner'], $f['assessment'], true);
        app(CbtAttemptService::class)->saveAnswer($f['learner'], $f['assessment'], $s->id, $f['question']->id, ['Saved partial work']);
        $s->update(['started_at' => now()->subMinutes(30)]);
        $result = app(CbtAttemptService::class)->submit($f['learner'], $f['assessment'], $s->id, [$f['question']->id => $this->responses()]);
        $this->assertTrue($result['expired']); $this->assertTrue($result['pending_review']); $this->assertNull($result['percentage']);
        $answer = StudentAnswer::first(); $this->assertSame(['Saved partial work'], json_decode($answer->answer, true));
        $this->denied(fn () => $this->service()->share($f['learner'], $f['assessment'], $answer->id), 422);
    }
    public function test_submission_is_pending_private_and_only_official_marking_awards_results(): void
    {
        $f = $this->fixture(); [$s, $a] = $this->submit($f);
        $this->assertTrue($s->metadata['results']['pending_review']); $this->assertNull($s->metadata['results']['percentage']);
        $this->assertSame($this->responses(), json_decode($a->answer, true)); $this->assertSame(0, $f['learner']->getOrCreateGamificationData()->total_points);
        $this->assertCount(0, $this->service()->entries($f['peer'], $f['assessment'])->items());
        app(AssessmentReviewService::class)->grade($f['teacher'], $s->id, $a->id, 8, 'PRIVATE INSTRUCTOR FEEDBACK');
        $this->assertEquals(80, $f['assessment']->getStudentResults($f['learner']->id)['percentage']);
        $this->actingAs($f['learner']); $safe = app(AssessmentResultService::class)->responses($f['assessment'], $f['learner'], 1);
        $this->assertStringContainsString('Response 2:', $safe[0]['response']);
        $this->assertStringNotContainsString('PRIVATE EVALUATION', json_encode($safe));
    }
    public function test_sharing_requires_explicit_owner_action_and_original_approval(): void
    {
        $f = $this->fixture(); [, $a] = $this->submit($f); $this->submit($f, $f['peer']);
        $this->denied(fn () => $this->service()->share($f['peer'], $f['assessment'], $a->id), 403);
        $this->service()->share($f['learner'], $f['assessment'], $a->id); $this->service()->share($f['learner'], $f['assessment'], $a->id);
        $this->assertSame('pending', $a->fresh()->exam_data['qna']['status']); $this->assertCount(1, $a->fresh()->exam_data['qna']['history']);
        $this->assertCount(1, $this->service()->entries($f['peer'], $f['assessment'])->items());
        $this->publish($f, $a); $this->assertCount(2, $this->service()->entries($f['peer'], $f['assessment'])->items());
        $safe = json_encode($this->service()->entries($f['peer'], $f['assessment'])->items());
        foreach (['PRIVATE', 'user_id', 'graded_by', 'qna_rules', 'history', $f['learner']->email] as $private) $this->assertStringNotContainsString($private, $safe);
    }
    public function test_peers_must_submit_the_same_topic_before_viewing_or_reviewing(): void
    {
        $f = $this->fixture(); [, $a] = $this->submit($f); $this->publish($f, $a);
        $this->assertCount(0, $this->service()->entries($f['peer'], $f['assessment'])->items());
        $this->denied(fn () => $this->service()->review($f['peer'], $f['assessment'], $a->id, 'Some considered constructive peer feedback.'), 403);
        $this->submit($f, $f['peer']);
        $this->assertCount(2, $this->service()->entries($f['peer'], $f['assessment'])->items());
        $this->denied(fn () => $this->service()->review($f['learner'], $f['assessment'], $a->id, 'Cannot review my own submitted work.'), 403);
        $this->denied(fn () => $this->service()->review($f['teacher'], $f['assessment'], $a->id, 'Official grading belongs in the review queue.'), 403);
    }
    public function test_peer_feedback_is_pending_until_approved_and_replay_safe_without_mark_changes(): void
    {
        $f = $this->fixture(); [$s, $a] = $this->submit($f); $this->submit($f, $f['peer']); $this->publish($f, $a);
        $text = 'Your evidence is clear; consider explaining the alternative in more detail.';
        $this->service()->review($f['peer'], $f['assessment'], $a->id, $text); $this->service()->review($f['peer'], $f['assessment'], $a->id, $text);
        $this->assertCount(1, $a->fresh()->exam_data['qna']['reviews']);
        $own = collect($this->service()->entries($f['learner'], $f['assessment'])->items())->firstWhere('id', $a->id); $this->assertCount(0, $own['reviews']);
        $state = $a->fresh()->exam_data['qna'];
        $this->service()->moderateReview($f['teacher'], $f['assessment'], $a->id, $state['version'], $state['reviews'][0]['id'], 'publish', 'Constructive and relevant feedback.');
        $own = collect($this->service()->entries($f['learner'], $f['assessment'])->items())->firstWhere('id', $a->id); $this->assertSame($text, $own['reviews'][0]['text']);
        $this->denied(fn () => $this->service()->review($f['peer'], $f['assessment'], $a->id, 'A changed replacement is not an exact replay.'), 422);
        $this->assertNull($a->fresh()->graded_at); $this->assertEquals(0, $a->fresh()->points_earned); $this->assertTrue($s->fresh()->metadata['results']['pending_review']);
        $this->assertSame(0, $f['learner']->getOrCreateGamificationData()->total_points);
    }
    public function test_withdrawal_hides_shared_work_and_refuses_stale_moderation(): void
    {
        $f = $this->fixture(); [, $a] = $this->submit($f); $this->submit($f, $f['peer']);
        $this->service()->share($f['learner'], $f['assessment'], $a->id); $version = $a->fresh()->exam_data['qna']['version'];
        $this->service()->withdraw($f['learner'], $f['assessment'], $a->id); $this->service()->withdraw($f['learner'], $f['assessment'], $a->id);
        $this->denied(fn () => $this->service()->moderate($f['teacher'], $f['assessment'], $a->id, $version, 'publish', 'This was stale and must be refused.'), 409);
        $this->assertCount(1, $this->service()->entries($f['peer'], $f['assessment'])->items());
        $this->assertCount(2, $a->fresh()->exam_data['qna']['history']); $this->assertSame($this->responses(), json_decode($a->fresh()->answer, true));
        $this->denied(fn () => $this->service()->share($f['learner'], $f['assessment'], $a->id), 422);
    }
    public function test_owner_can_withdraw_after_enrollment_ends_without_reopening_course_access(): void
    {
        $f = $this->fixture(); [, $a] = $this->submit($f); $this->publish($f, $a);
        $f['learner']->courses()->detach($f['course']->id);
        $this->denied(fn () => $this->service()->entries($f['learner'], $f['assessment']), 403);
        $this->service()->withdraw($f['learner'], $f['assessment'], $a->id);
        $this->assertSame('withdrawn', $a->fresh()->exam_data['qna']['status']);
    }
    public function test_no_approval_setting_publishes_explicit_shares_and_feedback_immediately(): void
    {
        $f = $this->fixture(['moderator_approval' => false]); [, $a] = $this->submit($f); $this->submit($f, $f['peer']);
        $this->service()->share($f['learner'], $f['assessment'], $a->id);
        $this->service()->review($f['peer'], $f['assessment'], $a->id, 'A useful perspective with a clearly explained tradeoff.');
        $this->assertSame('published', $a->fresh()->exam_data['qna']['status']);
        $this->assertSame('published', $a->fresh()->exam_data['qna']['reviews'][0]['status']);
    }
    public function test_other_instructors_unenrolled_inactive_and_revoked_reviewers_are_denied(): void
    {
        $f = $this->fixture(); [, $a] = $this->submit($f);
        $outsider = User::factory()->create(['role' => 'instructor']);
        $this->denied(fn () => $this->service()->entries($outsider, $f['assessment']), 403);
        $f['peer']->update(['is_active' => false]); $this->denied(fn () => $this->service()->entries($f['peer'], $f['assessment']), 403);
        $f['teacher']->syncRoles([]); $f['teacher']->update(['role' => 'student']);
        $this->service()->share($f['learner'], $f['assessment'], $a->id);
        $this->denied(fn () => $this->service()->moderate($f['teacher'], $f['assessment'], $a->id, 1, 'publish', 'A revoked reviewer must not moderate.'), 403);
    }
    public function test_legacy_text_attempts_never_gain_peer_permissions_from_later_edits(): void
    {
        $f = $this->fixture(); $newOptions = $f['question']->options; $f['question']->update(['options' => []]);
        $s = app(CbtAttemptService::class)->start($f['learner'], $f['assessment'], true);
        $f['question']->update(['options' => $newOptions]);
        app(CbtAttemptService::class)->submit($f['learner'], $f['assessment'], $s->id, [$f['question']->id => 'Original private text-only response.']);
        $this->assertCount(0, $this->service()->entries($f['learner'], $f['assessment'])->items());
        $this->assertArrayNotHasKey('qna_rules', StudentAnswer::first()->exam_data);
    }
    public function test_moderation_and_sharing_rollback_with_failed_answer_write(): void
    {
        $f = $this->fixture(); [, $a] = $this->submit($f);
        StudentAnswer::updating(function () { throw new \RuntimeException('Simulated failed write'); });
        try { $this->service()->share($f['learner'], $f['assessment'], $a->id); $this->fail('Expected rollback'); }
        catch (\RuntimeException $e) { $this->assertSame('Simulated failed write', $e->getMessage()); }
        finally { StudentAnswer::flushEventListeners(); }
        $this->assertArrayNotHasKey('qna', $a->fresh()->exam_data); $this->assertNull($a->fresh()->graded_at);
    }
    public function test_authoring_stores_object_metadata_validates_weights_and_scopes_topic_actions(): void
    {
        $f = $this->fixture(); $component = Livewire::actingAs($f['teacher'])->test(QnaCriteriaManager::class, ['assessmentId' => $f['assessment']->id]);
        $component->call('toggleCreateForm')->set('topicTitle', 'A new discussion')->set('topicDescription', 'Discuss the practical design choices.')
            ->set('evaluationCriteria.0.weight', 90)->call('createTopic')->assertHasErrors('evaluationCriteria')
            ->set('evaluationCriteria.0.weight', 100)->call('createTopic')->assertHasNoErrors()->assertSet('showCreateForm', false);
        $new = $f['assessment']->questions()->where('question_text', 'A new discussion')->firstOrFail();
        $this->assertSame('OBJECT', DB::table('questions')->where('id', $new->id)->selectRaw('JSON_TYPE(options) as kind')->first()->kind);
        $component->call('duplicateTopic', $new->id)->call('moveTopic', $new->id, -1)->assertHasNoErrors();
        $this->assertSame($new->id, $f['assessment']->questions()->orderBy('order')->first()->id);
        $other = Assessment::create(['course_id' => $f['course']->id, 'lesson_id' => $f['lesson']->id, 'type' => 'quiz', 'title' => 'Different format']);
        $foreign = Question::create(['assessment_id' => $other->id, 'question_type' => 'multiple_choice', 'question_text' => 'Wrong type']);
        try { $component->call('editTopic', $foreign->id); $this->fail('Unrelated topic accepted'); }
        catch (\Illuminate\Database\Eloquent\ModelNotFoundException $exception) { $this->assertSame(Question::class, $exception->getModel()); }
        Livewire::actingAs($f['teacher'])->test(QnaCriteriaManager::class, ['assessmentId' => $other->id])->assertForbidden();
    }
    public function test_livewire_renders_safe_board_and_rechecks_ownership_on_actions(): void
    {
        $f = $this->fixture(); [, $a] = $this->submit($f);
        Livewire::actingAs($f['learner'])->test(AssessmentDiscussion::class, ['assessment' => $f['assessment']])
            ->assertSee('Your submitted response')->assertDontSee('PRIVATE EVALUATION')->call('share', $a->id)->assertSee('Awaiting sharing approval');
        Livewire::actingAs($f['peer'])->test(AssessmentDiscussion::class, ['assessment' => $f['assessment']])->call('withdraw', $a->id)->assertForbidden();
        Livewire::actingAs($f['teacher'])->test(AssessmentDiscussion::class, ['assessment' => $f['assessment']])
            ->set('reasons.'.$a->id, 'Approved constructive discussion text.')->call('moderate', $a->id, 1, 'publish')->assertSee('Shared with eligible peers');
    }
    public function test_sharing_and_feedback_preserve_finalized_marks_rewards_and_original_text(): void
    {
        $f = $this->fixture(); [$session, $answer] = $this->submit($f); $this->submit($f, $f['peer']);
        app(AssessmentReviewService::class)->grade($f['teacher'], $session->id, $answer->id, 8, 'Private official marking feedback.');
        $before = $answer->fresh()->only(['answer', 'points_earned', 'graded_by', 'graded_at', 'feedback']);
        $result = $session->fresh()->metadata['results']; $rewards = DB::table('gamification_transactions')->where('user_id', $f['learner']->id)->get()->toJson();
        $this->publish($f, $answer);
        $this->service()->review($f['peer'], $f['assessment'], $answer->id, 'Peer feedback should never replace the official recorded grade.');
        $this->service()->withdraw($f['learner'], $f['assessment'], $answer->id);
        $this->assertEquals($before, $answer->fresh()->only(array_keys($before)));
        $this->assertSame($result, $session->fresh()->metadata['results']);
        $this->assertSame($rewards, DB::table('gamification_transactions')->where('user_id', $f['learner']->id)->get()->toJson());
    }
    public function test_pending_peer_feedback_and_reasons_do_not_leak_to_response_author(): void
    {
        $f = $this->fixture(); [, $a] = $this->submit($f); $this->submit($f, $f['peer']); $this->publish($f, $a);
        $this->service()->review($f['peer'], $f['assessment'], $a->id, 'PRIVATE PENDING FEEDBACK with details awaiting moderation.');
        $state = $a->fresh()->exam_data['qna'];
        $this->service()->moderateReview($f['teacher'], $f['assessment'], $a->id, $state['version'], $state['reviews'][0]['id'], 'reject', 'PRIVATE FEEDBACK MODERATION EXPLANATION');
        $safe = json_encode($this->service()->entries($f['learner'], $f['assessment'])->items());
        $this->assertStringNotContainsString('PRIVATE PENDING', $safe); $this->assertStringNotContainsString('PRIVATE FEEDBACK MODERATION', $safe);
        $this->assertStringContainsString('PRIVATE PENDING', json_encode($this->service()->entries($f['peer'], $f['assessment'])->items()));
    }
    public function test_personal_results_can_withdraw_after_enrollment_is_removed(): void
    {
        $f = $this->fixture(); [, $a] = $this->submit($f); $this->publish($f, $a); $f['learner']->courses()->detach($f['course']->id);
        Livewire::actingAs($f['learner'])->test(\App\Livewire\Cbt\CbtViewer::class)
            ->call('viewAssessmentDetails', $f['assessment']->id)->call('viewAttemptDetails', 1)->assertSee('Withdraw Q&A sharing')
            ->call('withdrawQnaSharing', $a->id)->assertSee('Q&A sharing withdrawn')->assertDontSee('Withdraw Q&A sharing');
        $this->assertSame('withdrawn', $a->fresh()->exam_data['qna']['status']);
    }
    public function test_cross_assessment_records_and_forged_recorded_rules_cannot_be_shared(): void
    {
        $f = $this->fixture(); [, $a] = $this->submit($f);
        $other = Assessment::create(['course_id' => $f['course']->id, 'lesson_id' => $f['lesson']->id, 'type' => 'qna', 'title' => 'Other Q&A']);
        try { $this->service()->share($f['learner'], $other, $a->id); $this->fail('Cross-assessment answer accepted'); }
        catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) { $this->assertSame(StudentAnswer::class, $e->getModel()); }
        $data = $a->exam_data; $data['qna_rules']['moderator_approval'] = false; $a->update(['exam_data' => $data]);
        $this->denied(fn () => $this->service()->share($f['learner'], $f['assessment'], $a->id), 422);
        $this->assertArrayNotHasKey('qna', $a->fresh()->exam_data);
    }
    public function test_qna_inputs_and_peer_text_are_escaped_and_dialog_roles_are_not_fabricated(): void
    {
        $f = $this->fixture(['moderator_approval' => false]); [, $a] = $this->submit($f); $this->submit($f, $f['peer']);
        $this->service()->share($f['learner'], $f['assessment'], $a->id);
        $text = '<script>window.qnaInjected = true</script> Constructive plain text.';
        $this->service()->review($f['peer'], $f['assessment'], $a->id, $text);
        Livewire::actingAs($f['learner'])->test(AssessmentDiscussion::class, ['assessment' => $f['assessment']])
            ->assertSee($text)->assertDontSeeHtml('<script>window.qnaInjected = true</script>')->assertDontSee('PRIVATE GUIDANCE');
    }

    public function test_blank_optional_submission_cannot_unlock_peer_responses(): void
    {
        $f = $this->fixture(); [, $answer] = $this->submit($f); $this->publish($f, $answer);
        $f['question']->update(['is_required' => false]);
        $s = app(CbtAttemptService::class)->start($f['peer'], $f['assessment'], true);
        app(CbtAttemptService::class)->submit($f['peer'], $f['assessment'], $s->id, []);
        $entries = $this->service()->entries($f['peer'], $f['assessment'])->items();
        $this->assertNotContains($answer->id, array_column($entries, 'id'));
        $this->denied(fn () => $this->service()->review($f['peer'], $f['assessment'], $answer->id, 'Blank optional work must not unlock peer participation.'), 403);
    }

    public function test_unicode_responses_at_the_recorded_total_limit_fit_the_existing_text_column(): void
    {
        $f = $this->fixture(); $s = app(CbtAttemptService::class)->start($f['learner'], $f['assessment'], true);
        $value = [str_repeat('🌱', 3000), str_repeat('🌍', 3000), str_repeat('📚', 3000), str_repeat('🔧', 1000)];
        $result = app(CbtAttemptService::class)->submit($f['learner'], $f['assessment'], $s->id, [$f['question']->id => $value]);
        $this->assertTrue($result['pending_review']); $this->assertSame($value, json_decode(StudentAnswer::first()->answer, true));
        $this->assertLessThan(65535, strlen(StudentAnswer::first()->answer));
    }

    public function test_author_context_is_locked_and_learner_cannot_mount_authoring(): void
    {
        $f = $this->fixture();
        $component = Livewire::actingAs($f['teacher'])->test(QnaCriteriaManager::class, ['assessmentId' => $f['assessment']->id]);
        try { $component->set('assessmentId', $f['assessment']->id + 1); $this->fail('Locked context was writable'); }
        catch (\Livewire\Features\SupportLockedProperties\CannotUpdateLockedPropertyException $exception) { $this->assertStringContainsString('assessmentId', $exception->getMessage()); }
        Livewire::actingAs($f['learner'])->test(QnaCriteriaManager::class, ['assessmentId' => $f['assessment']->id])->assertForbidden();
    }
    public function test_malformed_public_answers_and_invalid_setup_have_visible_safe_errors(): void
    {
        $f = $this->fixture();
        Livewire::actingAs($f['learner'])->test(StudentAssessmentTaker::class, ['lesson' => $f['lesson']])
            ->call('startAssessment', $f['assessment']->id)->set('answers.'.$f['question']->id, 'Forged scalar in response fields')
            ->assertHasErrors('answers.'.$f['question']->id)->assertSet('results', null);
        $meta = $f['question']->options; $meta['evaluation_criteria'][0]['weight'] = 30; $f['question']->update(['options' => $meta]);
        // A different learner starts a new attempt; the existing attempt keeps its original valid rules.
        Livewire::actingAs($f['peer'])->test(StudentAssessmentTaker::class, ['lesson' => $f['lesson']])
            ->call('startAssessment', $f['assessment']->id)->assertHasErrors('submission')->assertSee('Your instructor must review the Q&A requirements')
            ->assertDontSee('PRIVATE GUIDANCE')->assertSet('examSessionId', null);
    }

    public function test_navbar_titles_escape_untrusted_text_exactly_once(): void
    {
        $f = $this->fixture(); $this->actingAs($f['learner']);
        $html = \Illuminate\Support\Facades\Blade::render('<x-navbar :title="$title" />', ['title' => 'Q&A <script>window.navInjected = true</script>']);
        $this->assertStringContainsString('Q&amp;A &lt;script&gt;window.navInjected = true&lt;/script&gt;', $html);
        $this->assertStringNotContainsString('Q&amp;amp;A', $html);
        $this->assertStringNotContainsString('<script>window.navInjected = true</script>', $html);
    }

}
