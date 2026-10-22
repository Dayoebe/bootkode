<?php

namespace Tests\Feature;

use App\Livewire\CourseManagement\CourseBuilder\AssessmentManager;
use App\Livewire\CourseManagement\CourseBuilder\QuestionManager;
use App\Livewire\StudentManagement\CourseView\StudentAssessmentTaker;
use App\Models\Assessment\Assessment;
use App\Models\Assessment\LearningSession;
use App\Models\Assessment\Question;
use App\Models\Assessment\StudentAnswer;
use App\Models\Core\User;
use App\Models\Learning\Course;
use App\Models\Learning\Lesson;
use App\Models\Learning\Section;
use App\Services\AssessmentAuthoringService;
use App\Services\AssessmentResultService;
use App\Services\CbtAttemptService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class StructuredAssessmentIntegrityTest extends TestCase
{
    use RefreshDatabase;

    private function fixture(): array
    {
        Role::findOrCreate('instructor', 'web');
        $teacher = User::factory()->create(['role' => 'instructor']); $learner = User::factory()->create();
        $course = Course::withoutEvents(fn () => Course::create(['title' => 'Structured fixture', 'slug' => 'structured-fixture-'.uniqid(), 'instructor_id' => $teacher->id, 'is_published' => true, 'is_approved' => true]));
        $section = Section::create(['course_id' => $course->id, 'title' => 'First section', 'order' => 1]);
        $lesson = Lesson::withoutEvents(fn () => Lesson::create(['section_id' => $section->id, 'title' => 'Structured lesson', 'slug' => 'structured-lesson-'.uniqid(), 'published_at' => now(), 'order' => 1]));
        $assessment = Assessment::create(['course_id' => $course->id, 'lesson_id' => $lesson->id, 'title' => 'Structured assessment', 'type' => 'quiz', 'pass_percentage' => 70, 'estimated_duration_minutes' => 10, 'max_attempts' => 2]);
        $learner->courses()->attach($course->id); $learner->getOrCreateGamificationData()->update(['daily_quests' => [], 'last_quest_reset' => now()]);
        $this->actingAs($teacher);
        return [$teacher, $learner, $lesson, $assessment];
    }

    private function structured(Assessment $assessment, string $type): Question
    {
        return Question::create(['assessment_id' => $assessment->id, 'question_text' => 'Arrange or match the fixture items', 'question_type' => $type,
            'options' => $type === 'ordering' ? ['version' => 1, 'items' => ['Install', 'Build', 'Verify']] : ['version' => 1, 'prompts' => ['Install target', 'Build target', 'Verify target'], 'choices' => ['Install', 'Build', 'Verify']],
            'correct_answers' => [0, 1, 2], 'explanation' => 'PRIVATE STRUCTURED GUIDANCE', 'points' => 3]);
    }

    private function correctResponse(LearningSession $session, int $id): array
    {
        $map = $session->metadata['grading'][$id]['option_map'];
        return array_map(fn ($original) => array_search($original, $map, true), [0, 1, 2]);
    }

    public function test_each_structured_format_grades_the_private_mapping_and_submission_replay_once(): void
    {
        [, $learner, , $assessment] = $this->fixture(); $questions = [];
        foreach (['matching', 'ordering', 'drag_drop'] as $type) $questions[] = $this->structured($assessment, $type);
        $this->actingAs($learner); $service = app(CbtAttemptService::class); $session = $service->start($learner, $assessment, true);
        $responses = [];
        foreach ($questions as $question) {
            $responses[$question->id] = $this->correctResponse($session, $question->id);
            $public = collect($session->metadata['questions'])->firstWhere('id', $question->id);
            $this->assertArrayNotHasKey('mapping', $public); $this->assertArrayNotHasKey('display_to_original_map', $public); $this->assertArrayNotHasKey('correct_answers', $public);
        }
        $result = $service->submit($learner, $assessment, $session->id, $responses);
        $this->assertTrue($result['passed']); $this->assertFalse($result['pending_review']); $this->assertEquals(100, $result['percentage']);
        $this->assertEquals($result, $service->submit($learner, $assessment, $session->id, []));
        $this->assertDatabaseCount('student_answers', 3); $this->assertSame(50, $learner->getOrCreateGamificationData()->total_points);
    }

    public function test_untouched_structured_controls_do_not_invent_answered_questions(): void
    {
        [, $learner, $lesson, $assessment] = $this->fixture(); $this->structured($assessment, 'ordering'); $this->structured($assessment, 'matching');
        Livewire::actingAs($learner)->test(StudentAssessmentTaker::class, ['lesson' => $lesson])->call('startAssessment', $assessment->id)->call('submitAssessment')->assertSet('results.answered_questions', 0)->assertSet('results.passed', false);
    }

    public function test_true_false_labels_and_private_reference_answers_remain_available_to_the_correct_roles(): void
    {
        [$teacher, $learner, $lesson, $assessment] = $this->fixture();
        Livewire::actingAs($teacher)->test(QuestionManager::class, ['assessmentId' => $assessment->id])->set('questionText', 'Choose a boolean')->set('questionType', 'true_false')->set('correctAnswer', 'true')->set('trueAnswerText', 'Yes')->set('falseAnswerText', 'No')->call('createQuestion')->assertHasNoErrors();
        $question = $assessment->questions()->firstOrFail();
        $this->assertSame(['Yes', 'No'], app(CbtAttemptService::class)->questions($assessment)[0]['options']);
        Question::create(['assessment_id' => $assessment->id, 'question_text' => 'Write a reason', 'question_type' => 'short_answer', 'correct_answers' => ['PRIVATE EXPECTED RESPONSE'], 'points' => 2]);
        $this->actingAs($learner); $session = app(CbtAttemptService::class)->start($learner, $assessment, true);
        app(CbtAttemptService::class)->submit($learner, $assessment, $session->id, [$question->id => 0]);
        Livewire::test(StudentAssessmentTaker::class, ['lesson' => $lesson])->call('showAssessmentResults', $assessment->id)->assertDontSee('PRIVATE EXPECTED RESPONSE')->assertSee('Yes');
        Livewire::actingAs($teacher)->test(\App\Livewire\CourseManagement\AssessmentReviewQueue::class)->call('review', $session->id)->assertSee('PRIVATE EXPECTED RESPONSE');
    }

    public function test_partial_or_repeated_matches_and_wrong_order_receive_zero_without_fabricated_partial_credit(): void
    {
        [, $learner, , $assessment] = $this->fixture();
        $match = $this->structured($assessment, 'matching'); $order = $this->structured($assessment, 'ordering'); $drag = $this->structured($assessment, 'drag_drop');
        $this->actingAs($learner); $service = app(CbtAttemptService::class); $session = $service->start($learner, $assessment, true);
        $wrong = array_reverse($this->correctResponse($session, $order->id));
        $result = $service->submit($learner, $assessment, $session->id, [$match->id => [0, 0, 0], $order->id => $wrong, $drag->id => [0 => 1]]);
        $this->assertFalse($result['passed']); $this->assertEquals(0, $result['percentage']); $this->assertFalse($result['pending_review']);
        $this->assertSame(0, $learner->getOrCreateGamificationData()->total_points);
    }

    public function test_invalid_item_indices_nested_values_and_non_permutations_cannot_write_an_attempt(): void
    {
        [, $learner, , $assessment] = $this->fixture(); $question = $this->structured($assessment, 'ordering');
        $this->actingAs($learner); $service = app(CbtAttemptService::class); $session = $service->start($learner, $assessment, true);
        foreach ([[0, 0, 2], [0, 1, 999], [0, ['nested'], 2], [1 => 0, 2 => 1, 3 => 2], [0, null, 2]] as $answer) {
            try { $service->submit($learner, $assessment, $session->id, [$question->id => $answer]); $this->fail('Invalid sequence accepted'); }
            catch (\Symfony\Component\HttpKernel\Exception\HttpException $exception) { $this->assertSame(422, $exception->getStatusCode()); }
        }
        $this->assertDatabaseCount('student_answers', 0); $this->assertNull($session->fresh()->ended_at);
    }

    public function test_saved_pairs_survive_reload_and_expiry_uses_only_saved_responses(): void
    {
        [, $learner, $lesson, $assessment] = $this->fixture(); $question = $this->structured($assessment, 'matching');
        $component = Livewire::actingAs($learner)->test(StudentAssessmentTaker::class, ['lesson' => $lesson])->call('startAssessment', $assessment->id);
        $session = LearningSession::findOrFail($component->get('examSessionId')); $answer = $this->correctResponse($session, $question->id);
        foreach ($answer as $target => $choice) $component->set('answers.'.$question->id.'.'.$target, (string) $choice);
        Livewire::test(StudentAssessmentTaker::class, ['lesson' => $lesson])->call('startAssessment', $assessment->id)->assertSet('examSessionId', $session->id)->assertSet('answers.'.$question->id.'.0', (string) $answer[0]);
        $this->travel(11)->minutes();
        $result = app(CbtAttemptService::class)->submit($learner, $assessment, $session->id, [$question->id => [0, 0, 0]]);
        $this->assertTrue($result['expired']); $this->assertTrue($result['passed']);
    }

    public function test_structured_grading_and_response_history_keep_original_snapshots_after_edits(): void
    {
        [, $learner, , $assessment] = $this->fixture(); $question = $this->structured($assessment, 'drag_drop');
        $this->actingAs($learner); $session = app(CbtAttemptService::class)->start($learner, $assessment, true); $answer = $this->correctResponse($session, $question->id);
        $question->update(['options' => ['version' => 1, 'prompts' => ['New target A', 'New target B'], 'choices' => ['New item A', 'New item B']], 'correct_answers' => [1, 0], 'points' => 100]);
        $result = app(CbtAttemptService::class)->submit($learner, $assessment, $session->id, [$question->id => $answer]);
        $this->assertEquals(3, $result['total_points']); $this->assertTrue($result['passed']);
        $rows = app(AssessmentResultService::class)->responses($assessment, $learner, 1);
        $this->assertStringContainsString('Install target: Install', $rows[0]['response']); $this->assertStringNotContainsString('New item', $rows[0]['response']);
    }

    public function test_malformed_legacy_structured_questions_require_setup_without_new_history(): void
    {
        [, $learner, , $assessment] = $this->fixture(); $question = $this->structured($assessment, 'matching');
        $question->update(['options' => ['old undocumented shape']]);
        $this->actingAs($learner);
        try { app(CbtAttemptService::class)->start($learner, $assessment, true); $this->fail('Malformed legacy question started'); }
        catch (\Symfony\Component\HttpKernel\Exception\HttpException $exception) { $this->assertSame(422, $exception->getStatusCode()); }
        $this->assertDatabaseCount('learning_sessions', 0); $this->assertSame(['old undocumented shape'], $question->fresh()->options);
    }

    public function test_keyboard_sequence_moves_persist_and_cannot_target_other_question_types(): void
    {
        [, $learner, $lesson, $assessment] = $this->fixture(); $question = $this->structured($assessment, 'ordering'); $match = $this->structured($assessment, 'matching');
        $component = Livewire::actingAs($learner)->test(StudentAssessmentTaker::class, ['lesson' => $lesson])->call('startAssessment', $assessment->id)->call('moveOrderingItem', $question->id, 0, 1)->assertSet('answers.'.$question->id, [1, 0, 2])->assertDispatched('bootkode-list-moved');
        $session = LearningSession::findOrFail($component->get('examSessionId')); $this->assertSame([1, 0, 2], $session->metadata['answers'][$question->id]);
        $component->call('saveOrdering', $match->id)->assertStatus(422);
    }

    public function test_structured_submission_rolls_back_all_answers_if_grading_persistence_fails(): void
    {
        [, $learner, , $assessment] = $this->fixture(); $question = $this->structured($assessment, 'matching');
        $this->actingAs($learner); $session = app(CbtAttemptService::class)->start($learner, $assessment, true);
        $this->mock(\App\Services\GamificationService::class, fn ($mock) => $mock->shouldReceive('handleQuizCompletion')->andThrow(new \RuntimeException('Forced failure')));
        try { app(CbtAttemptService::class)->submit($learner, $assessment, $session->id, [$question->id => $this->correctResponse($session, $question->id)]); $this->fail('Expected rollback'); }
        catch (\RuntimeException $exception) { $this->assertSame('Forced failure', $exception->getMessage()); }
        $this->assertDatabaseCount('student_answers', 0); $this->assertNull($session->fresh()->ended_at);
    }

    public function test_author_can_create_edit_and_duplicate_every_structured_format_without_double_encoding(): void
    {
        [$teacher, , , $assessment] = $this->fixture();
        foreach (['matching', 'ordering', 'drag_drop'] as $type) {
            $component = Livewire::actingAs($teacher)->test(QuestionManager::class, ['assessmentId' => $assessment->id])->set('questionText', $type.' fixture')->set('questionType', $type);
            if ($type === 'ordering') $component->set('items', ['Install', 'Verify']);
            else $component->set('pairs', [['prompt' => 'First target', 'choice' => 'First item'], ['prompt' => 'Second target', 'choice' => 'Second item']]);
            $component->call('createQuestion')->assertHasNoErrors();
            $question = $assessment->questions()->latest('id')->firstOrFail(); $this->assertIsArray($question->options); $this->assertSame([0, 1], $question->correct_answers);
            $component->call('editQuestion', $question->id)->set('questionText', 'Updated '.$type)->call('updateQuestion')->assertHasNoErrors()->call('duplicateQuestion', $question->id);
            $this->assertDatabaseHas('questions', ['assessment_id' => $assessment->id, 'question_text' => 'Updated '.$type.' (Copy)']);
        }
        $this->assertDatabaseCount('questions', 6);
    }

    public function test_blank_choice_positions_invalid_keys_and_duplicate_structured_items_are_rejected(): void
    {
        [$teacher, , , $assessment] = $this->fixture();
        $component = Livewire::actingAs($teacher)->test(QuestionManager::class, ['assessmentId' => $assessment->id])->set('questionText', 'Choice fixture')->set('options', ['First', '', 'Third'])->set('correctAnswers', [2])->call('createQuestion')->assertHasErrors('options.1');
        $this->assertDatabaseCount('questions', 0);
        $component->set('options.1', 'Second')->set('correctAnswers', [99])->call('createQuestion')->assertHasErrors('correctAnswers.0');
        $component->set('questionType', 'matching')->set('pairs', [['prompt' => 'A', 'choice' => 'Same'], ['prompt' => 'B', 'choice' => 'Same']])->call('createQuestion')->assertHasErrors('pairs.0.choice');
        $this->assertDatabaseCount('questions', 0);
    }

    public function test_removing_a_choice_preserves_the_correct_remaining_indices_and_zero_text(): void
    {
        [$teacher, $learner, , $assessment] = $this->fixture();
        Livewire::actingAs($teacher)->test(QuestionManager::class, ['assessmentId' => $assessment->id])->set('questionText', 'Zero is a valid option')->set('options', ['0', 'Remove me', 'Keep me'])->set('correctAnswers', [0, 2])->call('removeOption', 1)->assertSet('correctAnswers', [0, 1])->call('createQuestion')->assertHasNoErrors();
        $question = $assessment->questions()->firstOrFail(); $this->assertSame(['0', 'Keep me'], $question->options); $this->assertSame([0, 1], $question->correct_answers);
        $this->actingAs($learner); $session = app(CbtAttemptService::class)->start($learner, $assessment, true);
        $this->assertTrue(app(CbtAttemptService::class)->submit($learner, $assessment, $session->id, [$question->id => [0, 1]])['passed']);
    }

    public function test_assessment_attempt_settings_persist_and_reload(): void
    {
        [$teacher, , $lesson, $assessment] = $this->fixture();
        $component = Livewire::actingAs($teacher)->test(AssessmentManager::class, ['lessonId' => $lesson->id])->set('assessmentTitle', 'One attempt fixture')->set('allowMultipleAttempts', false)->call('createAssessment')->assertHasNoErrors();
        $created = $lesson->assessments()->latest('id')->firstOrFail(); $this->assertSame(1, $created->max_attempts);
        $component->call('editAssessment', $created->id)->assertSet('allowMultipleAttempts', false)->set('allowMultipleAttempts', true)->set('maxAttempts', 3)->call('updateAssessment')->assertHasNoErrors();
        $this->assertSame(3, $created->fresh()->max_attempts);
        Livewire::test(AssessmentManager::class, ['lessonId' => $lesson->id])->call('editAssessment', $created->id)->assertSet('maxAttempts', 3);
    }

    public function test_cross_lesson_duplicate_reorder_and_mutable_question_payloads_are_rejected(): void
    {
        [$teacher, , $lesson, $assessment] = $this->fixture(); [, , , $other] = $this->fixture();
        $original = $this->structured($assessment, 'ordering'); $foreign = $this->structured($other, 'matching');
        $this->actingAs($teacher);
        try { Livewire::test(AssessmentManager::class, ['lessonId' => $lesson->id])->call('duplicateAssessment', $other->id); $this->fail('Foreign assessment copied'); }
        catch (\Illuminate\Database\Eloquent\ModelNotFoundException $exception) { $this->assertDatabaseCount('assessments', 2); }
        Livewire::test(AssessmentManager::class, ['lessonId' => $lesson->id])->call('reorderAssessments', [$other->id])->assertStatus(422);
        Livewire::test(QuestionManager::class, ['assessmentId' => $assessment->id])->call('reorderQuestions', [$foreign->id])->assertStatus(422);
        $this->assertSame(1, $foreign->fresh()->order);
        $this->expectException(\Livewire\Features\SupportLockedProperties\CannotUpdateLockedPropertyException::class);
        Livewire::test(QuestionManager::class, ['assessmentId' => $assessment->id])->set('editingQuestion', ['id' => $foreign->id]);
    }

    public function test_assessment_copy_is_atomic_and_does_not_copy_any_learning_history(): void
    {
        [$teacher, , $lesson, $assessment] = $this->fixture(); $this->structured($assessment, 'ordering'); $this->structured($assessment, 'matching');
        $this->actingAs($teacher); $copy = app(AssessmentAuthoringService::class)->duplicateAssessment($lesson->id, $assessment->id);
        $this->assertSame($lesson->id, $copy->lesson_id); $this->assertCount(2, $copy->questions); $this->assertSame(0, $copy->studentAnswers()->count());
        Question::created(fn () => throw new \RuntimeException('Forced copy failure'));
        try { app(AssessmentAuthoringService::class)->duplicateAssessment($lesson->id, $assessment->id); $this->fail('Expected atomic copy failure'); }
        catch (\RuntimeException $exception) { $this->assertSame('Forced copy failure', $exception->getMessage()); }
        finally { Question::flushEventListeners(); }
        $this->assertDatabaseCount('assessments', 2); $this->assertDatabaseCount('questions', 4);
    }

    public function test_legacy_grading_helpers_preserve_sequence_order_and_do_not_auto_mark_written_work(): void
    {
        [, , , $assessment] = $this->fixture(); $question = $this->structured($assessment, 'ordering');
        $this->assertFalse($question->isCorrectAnswer([2, 1, 0])); $this->assertTrue($question->isCorrectAnswer([0, 1, 2]));
        $question->update(['question_type' => 'multiple_choice', 'options' => ['A', 'B', 'C'], 'correct_answers' => [0, 2]]);
        $this->assertFalse($question->isCorrectAnswer(0)); $this->assertEquals(0, $question->calculatePartialCredit([0]));
        $this->assertTrue($question->isCorrectAnswer([2, 0]));
        $question->update(['question_type' => 'short_answer', 'correct_answers' => ['Reference']]);
        $this->assertFalse($question->isCorrectAnswer('Reference'));
        $legacy = new \App\Models\Assessment\CbtAnswer(['selected_answer' => [0]]); $legacy->setRelation('question', $question);
        $this->assertFalse($legacy->checkCorrectness()); $this->assertFalse($legacy->exists);
    }

    public function test_exact_reordering_and_question_statistics_do_not_recurse_or_lose_records(): void
    {
        [$teacher, , , $assessment] = $this->fixture(); $first = $this->structured($assessment, 'ordering'); $second = $this->structured($assessment, 'matching');
        $this->actingAs($teacher); app(AssessmentAuthoringService::class)->reorderQuestions($assessment->id, [$second->id, $first->id]);
        $this->assertEquals([$second->id, $first->id], $assessment->questions()->pluck('id')->all());
        $this->assertSame('insufficient_data', $first->stats['difficulty_actual']);
        Livewire::test(QuestionManager::class, ['assessmentId' => $assessment->id])->call('reorderQuestions', [$first->id, $first->id])->assertHasErrors('ids.0');
        $this->assertEquals([$second->id, $first->id], $assessment->questions()->pluck('id')->all());
    }
}
