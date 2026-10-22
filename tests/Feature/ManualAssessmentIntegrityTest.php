<?php

namespace Tests\Feature;

use App\Livewire\Cbt\CbtManagement;
use App\Livewire\Cbt\CbtViewer;
use App\Livewire\CourseManagement\AssessmentReviewQueue;
use App\Livewire\StudentManagement\CourseView\StudentAssessmentTaker;
use App\Models\Assessment\Assessment;
use App\Models\Assessment\Question;
use App\Models\Assessment\StudentAnswer;
use App\Models\Core\User;
use App\Models\Learning\Course;
use App\Models\Learning\Lesson;
use App\Models\Learning\Section;
use App\Services\AssessmentReviewService;
use App\Services\CbtAttemptService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ManualAssessmentIntegrityTest extends TestCase
{
    use RefreshDatabase;

    private function fixture(): array
    {
        Role::findOrCreate('instructor', 'web');
        $teacher = User::factory()->create(['role' => 'instructor']);
        $learner = User::factory()->create();
        $course = Course::withoutEvents(fn () => Course::create(['title' => 'Written fixture', 'slug' => 'written-fixture', 'instructor_id' => $teacher->id, 'is_published' => true, 'is_approved' => true]));
        $learner->courses()->attach($course->id);
        $section = Section::create(['course_id' => $course->id, 'title' => 'First section', 'order' => 1]);
        $lesson = Lesson::withoutEvents(fn () => Lesson::create(['section_id' => $section->id, 'title' => 'Written lesson', 'slug' => 'written-lesson', 'published_at' => now(), 'order' => 1]));
        $assessment = Assessment::create(['course_id' => $course->id, 'lesson_id' => $lesson->id, 'title' => 'Written assessment', 'type' => 'quiz', 'pass_percentage' => 70, 'estimated_duration_minutes' => 10, 'max_attempts' => 1]);
        $one = Question::create(['assessment_id' => $assessment->id, 'question_type' => 'essay', 'question_text' => 'Explain your approach', 'explanation' => 'PRIVATE RUBRIC', 'correct_answers' => ['PRIVATE KEY'], 'points' => 4, 'order' => 1]);
        $two = Question::create(['assessment_id' => $assessment->id, 'question_type' => 'short_answer', 'question_text' => 'Explain the tradeoff', 'correct_answers' => ['PRIVATE KEY'], 'points' => 6, 'order' => 2]);
        $learner->getOrCreateGamificationData()->update(['daily_quests' => [], 'last_quest_reset' => now()]);
        $this->actingAs($learner);
        return [$teacher, $learner, $lesson, $assessment, $one, $two];
    }

    private function submitted(array $fixture)
    {
        [, $learner, , $assessment, $one, $two] = $fixture;
        $session = app(CbtAttemptService::class)->start($learner, $assessment, true);
        app(CbtAttemptService::class)->submit($learner, $assessment, $session->id, [$one->id => 'My reasoned explanation', $two->id => 'A tradeoff']);
        return $session->fresh();
    }

    public function test_written_submission_stays_pending_without_pass_or_rewards_and_history_is_retained(): void
    {
        $fixture = $this->fixture(); [, $learner, $lesson, $assessment] = $fixture;
        $session = $this->submitted($fixture);
        $this->assertTrue($session->metadata['results']['pending_review']);
        $this->assertNull($session->metadata['results']['percentage']);
        $this->assertFalse($assessment->isPassedByStudent($learner->id));
        $this->assertSame(0, $learner->getOrCreateGamificationData()->total_points);
        Livewire::test(StudentAssessmentTaker::class, ['lesson' => $lesson])->call('clearPreviousAttempts', $assessment->id)->assertSee('Attempt history is retained');
        $this->assertDatabaseCount('student_answers', 2);
        Livewire::test(StudentAssessmentTaker::class, ['lesson' => $lesson])->call('startAssessment', $assessment->id)->assertForbidden();
    }

    public function test_pending_review_blocks_lesson_progress_and_new_certificate_eligibility(): void
    {
        $fixture = $this->fixture(); [, $learner, $lesson, $assessment] = $fixture;
        $this->submitted($fixture);
        Livewire::actingAs($learner)->test(\App\Livewire\StudentManagement\CourseView::class, ['course' => $assessment->course])
            ->call('handleLessonCompleted', $lesson->id);
        $this->assertFalse($learner->completedLessons()->where('lessons.id', $lesson->id)->exists());
        try { app(\App\Services\ProgressService::class)->completeLesson($assessment->course, $lesson); $this->fail('Pending assessment accepted for progress'); }
        catch (\Symfony\Component\HttpKernel\Exception\HttpException $exception) { $this->assertSame(422, $exception->getStatusCode()); }
        $learner->completedLessons()->attach($lesson->id, ['completed_at' => now()]); // Existing completion is preserved, but cannot issue a new credential.
        $this->assertFalse(app(\App\Services\GamificationService::class)->handleLessonCompletion($learner, $lesson)['success']);
        $this->assertFalse(app(\App\Services\CertificateService::class)->canRequestCertificate($learner->id, $assessment->course_id)['can_request']);
        $this->assertDatabaseCount('certificates', 0);
    }

    public function test_completion_rejects_cross_course_lessons_and_request_after_enrollment_is_removed(): void
    {
        [, $learner, $lesson, $assessment] = $this->fixture();
        $component = Livewire::actingAs($learner)->test(\App\Livewire\StudentManagement\CourseView::class, ['course' => $assessment->course]);
        $otherCourse = Course::withoutEvents(fn () => Course::create(['title' => 'Other course', 'slug' => 'other-course', 'instructor_id' => $learner->id]));
        $section = Section::create(['course_id' => $otherCourse->id, 'title' => 'Other section', 'order' => 1]);
        $otherLesson = Lesson::withoutEvents(fn () => Lesson::create(['section_id' => $section->id, 'title' => 'Other lesson', 'slug' => 'other-lesson', 'order' => 1]));
        $component->call('handleLessonCompleted', $otherLesson->id);
        $this->assertFalse($learner->completedLessons()->where('lessons.id', $otherLesson->id)->exists());
        $learner->courses()->detach();
        \Illuminate\Support\Facades\DB::table('course_enrollments')->where('user_id', $learner->id)->delete();
        $component->call('handleLessonCompleted', $lesson->id)->assertForbidden();
    }

    public function test_lesson_enrollment_and_assessment_parent_are_checked_on_every_action(): void
    {
        [, $learner, $lesson, $assessment] = $this->fixture();
        Livewire::actingAs(User::factory()->create())->test(StudentAssessmentTaker::class, ['lesson' => $lesson])->assertForbidden();
        $component = Livewire::actingAs($learner)->test(StudentAssessmentTaker::class, ['lesson' => $lesson]);
        $other = Assessment::create(['title' => 'Unrelated exam', 'type' => 'quiz']);
        $component->call('startAssessment', $other->id)->assertNotFound();
        $component = Livewire::test(StudentAssessmentTaker::class, ['lesson' => $lesson]);
        $learner->courses()->detach();
        $component->call('showAssessmentResults', $assessment->id)->assertForbidden();
    }

    public function test_unpublished_and_locked_lessons_cannot_be_opened_through_the_assessment_route(): void
    {
        [, $learner, $lesson] = $this->fixture();
        $lesson->update(['published_at' => null]);
        Livewire::actingAs($learner)->test(StudentAssessmentTaker::class, ['lesson' => $lesson])->assertForbidden();
        $lesson->update(['published_at' => now()]);
        $next = Section::create(['course_id' => $lesson->section->course_id, 'title' => 'Locked next section', 'order' => 2]);
        $nextLesson = Lesson::withoutEvents(fn () => Lesson::create(['section_id' => $next->id, 'title' => 'Next lesson', 'slug' => 'next-lesson', 'published_at' => now(), 'order' => 1]));
        Livewire::test(StudentAssessmentTaker::class, ['lesson' => $nextLesson])->assertForbidden();
        $learner->completedLessons()->attach($lesson->id, ['completed_at' => now()]);
        Livewire::test(StudentAssessmentTaker::class, ['lesson' => $nextLesson])->assertOk();
    }

    public function test_invalid_written_answer_shapes_and_oversized_responses_cannot_write_drafts(): void
    {
        [, $learner, , $assessment, $one] = $this->fixture();
        $session = app(CbtAttemptService::class)->start($learner, $assessment, true);
        foreach ([[$one->id => str_repeat('x', 10001)], [$one->id => ['nested']]] as $answers) {
            try { app(CbtAttemptService::class)->submit($learner, $assessment, $session->id, $answers); $this->fail('Invalid answer accepted'); }
            catch (\Symfony\Component\HttpKernel\Exception\HttpException $exception) { $this->assertSame(422, $exception->getStatusCode()); }
        }
        $this->assertDatabaseCount('student_answers', 0);
        $this->assertNull($session->fresh()->ended_at);
    }

    public function test_missing_pass_configuration_cannot_start_or_treat_a_legacy_zero_score_as_passed(): void
    {
        [, $learner, $lesson, $assessment, $one] = $this->fixture();
        $assessment->update(['pass_percentage' => null]);
        Livewire::actingAs($learner)->test(StudentAssessmentTaker::class, ['lesson' => $lesson])->assertSee('must configure a pass mark')->call('startAssessment', $assessment->id)->assertStatus(422);
        $this->assertDatabaseCount('learning_sessions', 0);
        $one->update(['question_type' => 'multiple_choice', 'options' => ['A', 'B'], 'correct_answers' => [1]]);
        StudentAnswer::create(['user_id' => $learner->id, 'assessment_id' => $assessment->id, 'question_id' => $one->id, 'attempt_number' => 1, 'answer' => '0', 'points_earned' => 0, 'submitted_at' => now()]);
        $this->assertFalse($assessment->isPassedByStudent($learner->id));
        $this->assertFalse(app(\App\Services\GamificationService::class)->handleQuizCompletion($learner, $assessment)['success']);
    }

    public function test_client_receives_no_rubric_or_keys_and_cannot_change_locked_attempt_metadata(): void
    {
        [, $learner, $lesson, $assessment] = $this->fixture();
        $component = Livewire::actingAs($learner)->test(StudentAssessmentTaker::class, ['lesson' => $lesson])->call('startAssessment', $assessment->id);
        $this->assertArrayNotHasKey('correct_answers', $component->get('questions')[0]);
        $this->assertArrayNotHasKey('explanation', $component->get('questions')[0]);
        $component->assertDontSee('PRIVATE RUBRIC')->assertDontSee('PRIVATE KEY');
        $this->expectException(\Livewire\Features\SupportLockedProperties\CannotUpdateLockedPropertyException::class);
        $component->set('examSessionId', 999999);
    }

    public function test_partial_review_does_not_publish_and_final_review_uses_original_marks_and_pass_threshold(): void
    {
        $fixture = $this->fixture(); [$teacher, $learner, , $assessment, $one, $two] = $fixture;
        $session = $this->submitted($fixture);
        $assessment->update(['pass_percentage' => 100]); $two->update(['points' => 100]);
        $review = app(AssessmentReviewService::class);
        $first = StudentAnswer::where('question_id', $one->id)->firstOrFail();
        $last = StudentAnswer::where('question_id', $two->id)->firstOrFail();
        $this->assertTrue($review->grade($teacher, $session->id, $first->id, 3, 'Good reasoning')['pending_review']);
        $this->assertFalse($assessment->isPassedByStudent($learner->id));
        $result = $review->grade($teacher, $session->id, $last->id, 4, 'Clear tradeoff');
        $this->assertFalse($result['pending_review']); $this->assertTrue($result['passed']);
        $this->assertEquals(70, $result['percentage']); $this->assertEquals(10, $result['max_points']);
        $this->assertTrue($assessment->isPassedByStudent($learner->id));
        $this->assertSame(50, $learner->getOrCreateGamificationData()->total_points);
        $this->assertSame($teacher->id, $last->fresh()->graded_by);
        $this->assertNotNull($last->fresh()->graded_at);
        $this->assertEquals($result, $review->grade($teacher->fresh(), $session->id, $last->id, 4, 'Clear tradeoff'));
        $this->assertSame(50, $learner->getOrCreateGamificationData()->total_points);
        Livewire::actingAs($learner)->test(StudentAssessmentTaker::class, ['lesson' => $fixture[2]])->call('showAssessmentResults', $assessment->id)->assertSee('70%')->assertSee('Clear tradeoff');
    }

    public function test_learner_other_instructor_self_grading_and_unrelated_answer_are_denied(): void
    {
        $fixture = $this->fixture(); [$teacher, $learner, , $assessment, $one] = $fixture;
        $session = $this->submitted($fixture); $answer = StudentAnswer::where('question_id', $one->id)->firstOrFail();
        Livewire::actingAs($learner)->test(AssessmentReviewQueue::class)->assertForbidden();
        $other = User::factory()->create(['role' => 'instructor']);
        Livewire::actingAs($other)->test(AssessmentReviewQueue::class)->call('review', $session->id)->assertForbidden();
        foreach ([$learner, $other] as $reviewer) {
            try { app(AssessmentReviewService::class)->grade($reviewer, $session->id, $answer->id, 4); $this->fail('Unauthorized reviewer accepted'); }
            catch (\Symfony\Component\HttpKernel\Exception\HttpException $exception) { $this->assertSame(403, $exception->getStatusCode()); }
        }
        $learner->update(['role' => 'instructor']); $assessment->course->update(['instructor_id' => $learner->id]);
        try { app(AssessmentReviewService::class)->grade($learner->fresh(), $session->id, $answer->id, 4); $this->fail('Self grading accepted'); }
        catch (\Symfony\Component\HttpKernel\Exception\HttpException $exception) { $this->assertSame(403, $exception->getStatusCode()); }
        $this->assertNull($answer->fresh()->graded_at);
    }

    public function test_reviewer_cannot_mark_an_answer_from_another_attempt(): void
    {
        $fixture = $this->fixture(); [$teacher, $learner, , $assessment, $one] = $fixture;
        $session = $this->submitted($fixture);
        $foreign = StudentAnswer::create(['user_id' => $learner->id, 'assessment_id' => $assessment->id, 'question_id' => $one->id, 'attempt_number' => 9, 'answer' => 'Other attempt', 'submitted_at' => now()]);
        try { app(AssessmentReviewService::class)->grade($teacher, $session->id, $foreign->id, 4); $this->fail('Other attempt answer was marked'); }
        catch (\Illuminate\Database\Eloquent\ModelNotFoundException $exception) { $this->assertNull($foreign->fresh()->graded_at); }
    }

    public function test_review_marks_are_bounded_by_snapshot_and_cannot_be_silently_overwritten(): void
    {
        $fixture = $this->fixture(); [$teacher, , , , $one] = $fixture; $session = $this->submitted($fixture);
        $answer = StudentAnswer::where('question_id', $one->id)->firstOrFail();
        $component = Livewire::actingAs($teacher)->test(AssessmentReviewQueue::class)->call('review', $session->id)
            ->set('marks.'.$answer->id, 5)->call('grade', $answer->id)->assertHasErrors('marks.'.$answer->id);
        $this->assertNull($answer->fresh()->graded_at);
        $component->set('marks.'.$answer->id, 3)->set('feedback.'.$answer->id, 'Recorded feedback')->call('grade', $answer->id)->assertHasNoErrors();
        try { app(AssessmentReviewService::class)->grade($teacher, $session->id, $answer->id, 4); $this->fail('Existing mark overwritten'); }
        catch (\Symfony\Component\HttpKernel\Exception\HttpException $exception) { $this->assertSame(409, $exception->getStatusCode()); }
        $this->assertEquals(3, $answer->fresh()->points_earned);
    }

    public function test_final_mark_and_result_roll_back_together_when_reward_persistence_fails(): void
    {
        $fixture = $this->fixture(); [$teacher, , , , $one, $two] = $fixture; $session = $this->submitted($fixture);
        $first = StudentAnswer::where('question_id', $one->id)->firstOrFail(); $last = StudentAnswer::where('question_id', $two->id)->firstOrFail();
        app(AssessmentReviewService::class)->grade($teacher, $session->id, $first->id, 4);
        $this->mock(\App\Services\GamificationService::class, fn ($mock) => $mock->shouldReceive('handleQuizCompletion')->once()->andThrow(new \RuntimeException('Forced reward failure')));
        try { app(AssessmentReviewService::class)->grade($teacher, $session->id, $last->id, 6); $this->fail('Expected rollback'); }
        catch (\RuntimeException $exception) { $this->assertSame('Forced reward failure', $exception->getMessage()); }
        $this->assertNull($last->fresh()->graded_at); $this->assertTrue($session->fresh()->metadata['results']['pending_review']);
    }

    public function test_saving_and_grading_do_not_reset_the_original_server_clock(): void
    {
        [$teacher, $learner, , $assessment, $one, $two] = $this->fixture();
        $session = app(CbtAttemptService::class)->start($learner, $assessment, true);
        $started = $session->getRawOriginal('started_at');
        $this->travel(2)->minutes();
        app(CbtAttemptService::class)->saveAnswer($learner, $assessment, $session->id, $one->id, 'Saved before the deadline');
        $this->assertSame($started, $session->fresh()->getRawOriginal('started_at'));
        $this->travel(9)->minutes();
        $result = app(CbtAttemptService::class)->submit($learner, $assessment, $session->id, [$one->id => 'Late replacement']);
        $this->assertTrue($result['expired']); $this->assertSame(600, $result['time_spent']);
        $this->assertSame('Saved before the deadline', StudentAnswer::where('question_id', $one->id)->firstOrFail()->answer);
        $this->travel(1)->hours();
        app(AssessmentReviewService::class)->grade($teacher, $session->id, StudentAnswer::where('question_id', $one->id)->firstOrFail()->id, 3);
        app(AssessmentReviewService::class)->grade($teacher, $session->id, StudentAnswer::where('question_id', $two->id)->firstOrFail()->id, 0);
        $this->assertSame($started, $session->fresh()->getRawOriginal('started_at'));
        $this->travelBack();
    }

    public function test_multiple_choice_requires_the_exact_selected_set_and_answers_survive_resume(): void
    {
        [, $learner, $lesson, $assessment, $one, $two] = $this->fixture();
        $one->update(['question_type' => 'multiple_choice', 'options' => ['A', 'B', 'C'], 'correct_answers' => [0, 2]]);
        $two->update(['question_type' => 'true_false', 'correct_answers' => [1]]);
        $component = Livewire::actingAs($learner)->test(StudentAssessmentTaker::class, ['lesson' => $lesson])->call('startAssessment', $assessment->id)
            ->set('answers.'.$one->id, ['0', '2'])->set('answers.'.$two->id, '1');
        $sessionId = $component->get('examSessionId');
        Livewire::test(StudentAssessmentTaker::class, ['lesson' => $lesson])->call('startAssessment', $assessment->id)
            ->assertSet('examSessionId', $sessionId)->assertSet('answers.'.$one->id, ['0', '2'])->call('submitAssessment')->assertSet('results.passed', true);
        $answer = StudentAnswer::where('question_id', $one->id)->firstOrFail();
        $answer->update(['answer' => '[0]']); $answer->autoGrade();
        $this->assertEquals(0, $answer->fresh()->points_earned);
    }

    public function test_unrelated_attempt_results_and_summary_routes_are_owner_scoped(): void
    {
        $fixture = $this->fixture(); [, $learner, , $assessment] = $fixture; $this->submitted($fixture);
        Livewire::actingAs(User::factory()->create())->test(CbtViewer::class)->call('viewAssessmentDetails', $assessment->id)->assertNotFound();
        Livewire::actingAs($learner)->test(CbtViewer::class)->call('viewAssessmentDetails', $assessment->id)->call('viewAttemptDetails', 1)->assertSee('Pending instructor review');
        $this->actingAs($learner)->get(route('cbt.exam.summary', ['assessmentId' => $assessment->id]))->assertOk();
    }

    public function test_response_history_preserves_original_selected_options_and_never_returns_keys(): void
    {
        [, $learner, $lesson, $assessment, $one, $two] = $this->fixture();
        $one->update(['question_type' => 'multiple_choice', 'options' => ['Original A', 'Original B'], 'correct_answers' => [1]]);
        $two->update(['question_type' => 'true_false', 'correct_answers' => [0]]);
        $session = app(CbtAttemptService::class)->start($learner, $assessment, true);
        app(CbtAttemptService::class)->submit($learner, $assessment, $session->id, [$one->id => 1, $two->id => 0]);
        $one->update(['question_text' => 'Changed question', 'options' => ['Changed A', 'Changed B'], 'correct_answers' => [0]]);
        $rows = app(\App\Services\AssessmentResultService::class)->responses($assessment, $learner, 1);
        $this->assertSame('Original B', $rows[0]['response']); $this->assertSame('True', $rows[1]['response']);
        $this->assertSame('Explain your approach', $rows[0]['question']);
        $this->assertArrayNotHasKey('correct_answers', $rows[0]); $this->assertArrayNotHasKey('explanation', $rows[0]);
        Livewire::actingAs($learner)->test(CbtViewer::class)->call('viewAssessmentDetails', $assessment->id)->call('viewAttemptDetails', 1)->assertSee('Original B')->assertDontSee('Changed B')->assertDontSee('PRIVATE RUBRIC');
        Livewire::test(StudentAssessmentTaker::class, ['lesson' => $lesson])->call('showAssessmentResults', $assessment->id)->assertSee('Original B');
        $this->actingAs(User::factory()->create());
        try { app(\App\Services\AssessmentResultService::class)->responses($assessment, $learner, 1); $this->fail('Other account read responses'); }
        catch (\Symfony\Component\HttpKernel\Exception\HttpException $exception) { $this->assertSame(403, $exception->getStatusCode()); }
    }

    public function test_standalone_attempt_history_blocks_assessment_and_question_deletion(): void
    {
        [, $learner, , $assessment, $one] = $this->fixture();
        $assessment->update(['course_id' => null, 'lesson_id' => null]);
        $one->update(['question_type' => 'multiple_choice', 'options' => ['A', 'B'], 'correct_answers' => [0]]);
        $assessment->questions()->where('id', '!=', $one->id)->delete();
        app(CbtAttemptService::class)->start($learner, $assessment);
        Role::findOrCreate('super_admin', 'web'); $admin = User::factory()->create(['role' => 'super_admin']);
        Livewire::actingAs($admin)->test(CbtManagement::class)->call('deleteQuestion', $one->id)->assertStatus(422);
        Livewire::test(CbtManagement::class)->call('deleteAssessment', $assessment->id)->assertStatus(422);
        $this->assertNotNull($assessment->fresh()); $this->assertNotNull($one->fresh());
    }

    public function test_cbt_management_scopes_instructor_crud_and_preserves_attempts(): void
    {
        $fixture = $this->fixture(); [$teacher, , , $assessment] = $fixture; $this->submitted($fixture);
        $assessment->update(['lesson_id' => null]);
        $other = User::factory()->create(['role' => 'instructor']);
        Livewire::actingAs($other)->test(CbtManagement::class)->assertDontSee($assessment->title)->call('editAssessment', $assessment->id)->assertForbidden();
        Livewire::actingAs($other)->test(CbtManagement::class)->call('viewParticipants', $assessment->id)->assertForbidden();
        Livewire::actingAs($teacher)->test(CbtManagement::class)->call('viewParticipants', $assessment->id)->call('clearAllUserAttempts', $fixture[1]->id)->assertSee('Assessment history is retained');
        $this->assertDatabaseCount('student_answers', 2);
        Livewire::actingAs($other)->test(CbtManagement::class)->set('title', 'Forged')->set('course_id', $assessment->course_id)->call('createAssessment')->assertForbidden();
    }
}
