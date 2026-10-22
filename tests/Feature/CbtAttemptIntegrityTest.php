<?php

namespace Tests\Feature;

use App\Livewire\Cbt\CbtExamInterface;
use App\Models\Assessment\Assessment;
use App\Models\Assessment\Question;
use App\Models\Core\User;
use App\Models\Learning\Course;
use App\Services\CbtAttemptService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CbtAttemptIntegrityTest extends TestCase
{
    use RefreshDatabase;

    private function fixture(): array
    {
        $user = User::factory()->create();
        $course = Course::withoutEvents(fn () => Course::create(['title' => 'Exam fixture', 'slug' => 'exam-fixture', 'instructor_id' => User::factory()->create()->id]));
        $user->courses()->attach($course->id);
        $assessment = Assessment::create(['course_id' => $course->id, 'title' => 'Secure exam fixture', 'slug' => 'secure-exam-fixture', 'type' => 'quiz', 'pass_percentage' => 80, 'estimated_duration_minutes' => 5, 'max_attempts' => 1]);
        $question = Question::create(['assessment_id' => $assessment->id, 'question_text' => 'Choose the correct fixture option', 'question_type' => 'multiple_choice', 'options' => ['First option', 'Second option'], 'correct_answers' => [1], 'points' => 2, 'order' => 1]);
        $user->getOrCreateGamificationData()->update(['daily_quests' => [], 'last_quest_reset' => now()]);
        return [$user, $assessment, $question];
    }

    public function test_course_exam_rechecks_enrollment_on_mount_and_selection(): void
    {
        [, $assessment] = $this->fixture();
        $this->actingAs(User::factory()->create());
        Livewire::test(CbtExamInterface::class, ['assessment' => $assessment->id])->assertForbidden();
        Livewire::test(\App\Livewire\Cbt\CbtExamSelection::class)->call('startExam', $assessment->id)->assertForbidden();
    }

    public function test_question_payload_excludes_answer_keys_and_client_cannot_replace_metadata(): void
    {
        [$user, $assessment] = $this->fixture();
        $component = Livewire::actingAs($user)->test(CbtExamInterface::class, ['assessment' => $assessment->id]);
        $this->assertArrayNotHasKey('correct_answers', $component->get('questions')[0]);
        $this->assertArrayNotHasKey('original_correct_answers', $component->get('questions')[0]);
        $this->expectException(\Livewire\Features\SupportLockedProperties\CannotUpdateLockedPropertyException::class);
        $component->set('questions', []);
    }

    public function test_start_reuses_persisted_attempt_and_submission_replay_cannot_duplicate_grades_or_rewards(): void
    {
        [$user, $assessment, $question] = $this->fixture();
        $service = app(CbtAttemptService::class);
        $session = $service->start($user, $assessment);
        $this->assertSame($session->id, $service->start($user->fresh(), $assessment)->id);
        $result = $service->submit($user, $assessment, $session->id, [$question->id => 1]);
        $this->assertTrue($result['passed']); $this->assertEquals(100, $result['percentage']);
        $this->assertEquals($result, $service->submit($user->fresh(), $assessment, $session->id, [$question->id => 0]));
        $this->assertDatabaseCount('student_answers', 1);
        $this->assertSame(50, $user->getOrCreateGamificationData()->total_points);
        $this->assertDatabaseHas('learning_sessions', ['id' => $session->id, 'activity_type' => 'assessment']);
    }

    public function test_unstarted_submission_is_rejected(): void
    {
        [$user, $assessment] = $this->fixture();
        Livewire::actingAs($user)->test(CbtExamInterface::class, ['assessment' => $assessment->id])->call('submitExam')->assertStatus(422);
        $this->assertDatabaseCount('student_answers', 0);
    }

    public function test_cross_user_attempt_and_forged_answer_ids_are_rejected_without_writes(): void
    {
        [$user, $assessment] = $this->fixture();
        $service = app(CbtAttemptService::class);$session = $service->start($user, $assessment);
        try { $service->submit($user, $assessment, $session->id, [999999 => 1]); $this->fail('Unrelated answer accepted'); }
        catch (\Symfony\Component\HttpKernel\Exception\HttpException $exception) { $this->assertSame(422, $exception->getStatusCode()); }
        $this->assertDatabaseCount('student_answers', 0);
        $other = User::factory()->create();
        try { $service->submit($other, $assessment, $session->id, []); $this->fail('Other learner submitted attempt'); }
        catch (\Symfony\Component\HttpKernel\Exception\HttpException $exception) { $this->assertSame(403, $exception->getStatusCode()); }
        $this->assertDatabaseCount('student_answers', 0);
    }

    public function test_expired_attempt_cannot_accept_new_answers_or_reset_time(): void
    {
        [$user, $assessment, $question] = $this->fixture();
        $service = app(CbtAttemptService::class);$session = $service->start($user, $assessment);
        $session->update(['started_at' => now()->subMinutes(6)]);
        $this->assertTrue($service->start($user, $assessment)->started_at->lt(now()->subMinutes(5)));
        $result = $service->submit($user, $assessment, $session->id, [$question->id => 1]);
        $this->assertTrue($result['expired']); $this->assertFalse($result['passed']); $this->assertEquals(0, $result['percentage']);
    }

    public function test_grading_failure_rolls_back_all_answers_and_session_completion(): void
    {
        [$user, $assessment, $question] = $this->fixture();
        $service = app(CbtAttemptService::class);$session = $service->start($user, $assessment);
        \App\Models\Assessment\StudentAnswer::created(fn () => throw new \RuntimeException('Forced persistence failure'));
        try { $service->submit($user, $assessment, $session->id, [$question->id => 1]); $this->fail('Expected rollback'); }
        catch (\RuntimeException $exception) { $this->assertSame('Forced persistence failure', $exception->getMessage()); }
        finally { \App\Models\Assessment\StudentAnswer::flushEventListeners(); }
        $this->assertDatabaseCount('student_answers', 0); $this->assertNull($session->fresh()->ended_at);
    }

    public function test_saved_answers_survive_reload_and_expiry_cannot_replace_them(): void
    {
        [$user, $assessment, $question] = $this->fixture();
        $service = app(CbtAttemptService::class); $session = $service->start($user, $assessment);
        $service->saveAnswer($user, $assessment, $session->id, $question->id, 1);
        $this->assertEquals(1, $service->start($user, $assessment)->metadata['answers'][$question->id]);
        $session->update(['started_at' => now()->subMinutes(6)]);
        $result = $service->submit($user, $assessment, $session->id, [$question->id => 0]);
        $this->assertTrue($result['expired']); $this->assertTrue($result['passed']);
        $this->assertEquals(100, $result['percentage']);
    }

    public function test_true_false_questions_and_maximum_attempts_use_the_recorded_contract(): void
    {
        [$user, $assessment, $question] = $this->fixture();
        $question->update(['question_type' => 'true_false', 'options' => null, 'correct_answers' => [0]]);
        $service = app(CbtAttemptService::class); $session = $service->start($user, $assessment);
        $this->assertTrue($service->submit($user, $assessment, $session->id, [$question->id => 0])['passed']);
        try { $service->start($user, $assessment); $this->fail('Maximum attempts bypassed'); }
        catch (\Symfony\Component\HttpKernel\Exception\HttpException $exception) { $this->assertSame(403, $exception->getStatusCode()); }
    }

    public function test_grading_snapshot_survives_question_edits_and_completed_attempt_reloads(): void
    {
        [$user, $assessment, $question] = $this->fixture();
        $service = app(CbtAttemptService::class); $session = $service->start($user, $assessment);
        $question->update(['correct_answers' => [0], 'points' => 100]);
        $results = $service->submit($user, $assessment, $session->id, [$question->id => 1]);
        $this->assertTrue($results['passed']); $this->assertEquals(2, $results['total_points']);
        Livewire::actingAs($user)->test(CbtExamInterface::class, ['assessment' => $assessment->id])->assertSet('examCompleted', true)->assertSee('Congratulations!');
    }
}
