<?php

namespace Tests\Feature;

use App\Livewire\CourseManagement\AssessmentReviewQueue;
use App\Livewire\CourseManagement\CourseBuilder\AssignmentCriteriaManager;
use App\Livewire\CourseManagement\CourseBuilder\ProjectCriteriaManager;
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
use App\Services\AssessmentSubmissionService;
use App\Services\CbtAttemptService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class AssessmentSubmissionIntegrityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void { parent::setUp(); Storage::fake('local'); }

    private function fixture(string $type = 'project', array $options = []): array
    {
        Role::findOrCreate('instructor', 'web');
        $teacher = User::factory()->create(['role' => 'instructor']); $learner = User::factory()->create();
        $course = Course::withoutEvents(fn () => Course::create(['title' => 'Submission course', 'slug' => 'submission-'.Str::uuid(), 'instructor_id' => $teacher->id, 'is_published' => true, 'is_approved' => true]));
        $section = Section::create(['course_id' => $course->id, 'title' => 'Submission section', 'order' => 1]);
        $lesson = Lesson::withoutEvents(fn () => Lesson::create(['section_id' => $section->id, 'title' => 'Submission lesson', 'slug' => 'submission-'.Str::uuid(), 'published_at' => now(), 'order' => 1]));
        $assessment = Assessment::create(['course_id' => $course->id, 'lesson_id' => $lesson->id, 'title' => 'Submission assessment', 'type' => $type, 'pass_percentage' => 70, 'estimated_duration_minutes' => 20, 'max_attempts' => 2]);
        $criteria = $type === 'project' ? ['criteria_type' => 'deliverable', 'min_files' => 1, 'max_files' => 2] : ['assignment_type' => 'analysis', 'allow_file_upload' => true, 'word_limit' => 10, 'sample_answer' => 'PRIVATE SAMPLE ANSWER', 'grading_notes' => 'PRIVATE GRADING NOTES', 'rubric_criteria' => [['name' => 'PRIVATE RUBRIC', 'weight' => 100]]];
        $question = Question::create(['assessment_id' => $assessment->id, 'question_type' => $type === 'project' ? 'project_criteria' : 'assignment_question', 'question_text' => 'Submit your work', 'explanation' => 'Public learner instructions', 'points' => 10, 'options' => array_merge($criteria, ['file_types' => ['txt'], 'max_file_size' => 1], $options)]);
        $learner->courses()->attach($course->id); $learner->getOrCreateGamificationData()->update(['daily_quests' => [], 'last_quest_reset' => now()]);
        $this->actingAs($learner);
        return compact('teacher', 'learner', 'course', 'lesson', 'assessment', 'question');
    }

    private function start(array $f): LearningSession { return app(CbtAttemptService::class)->start($f['learner'], $f['assessment'], true); }
    private function attach(array $f, LearningSession $session, string $name = 'solution.txt', string $text = 'My original project work'): array
    {
        return app(AssessmentSubmissionService::class)->attach($f['learner'], $f['assessment'], $session->id, $f['question']->id, UploadedFile::fake()->createWithContent($name, $text));
    }
    private function denied(callable $action, int $status): void
    {
        try { $action(); $this->fail('Expected refusal'); } catch (HttpException $exception) { $this->assertSame($status, $exception->getStatusCode()); }
    }
    private function missing(callable $action): void
    {
        try { $action(); $this->fail('Expected missing record'); }
        catch (\Illuminate\Database\Eloquent\ModelNotFoundException $exception) { $this->assertNotEmpty($exception->getModel()); }
        catch (HttpException $exception) { $this->assertSame(404, $exception->getStatusCode()); }
    }

    public function test_assignment_private_options_never_enter_new_or_resumed_learner_payloads(): void
    {
        $f = $this->fixture('assignment'); $s = $this->start($f);
        $safe = app(CbtAttemptService::class)->publicQuestions($s);
        $this->assertStringNotContainsString('PRIVATE', json_encode($safe));
        $this->assertSame([], $safe[0]['options']); $this->assertSame('Public learner instructions', $safe[0]['instructions']);
        $this->assertSame('PRIVATE SAMPLE ANSWER', $s->metadata['grading'][$f['question']->id]['criteria']['sample_answer']);
        $meta = $s->metadata; $meta['questions'][0]['options'] = ['PRIVATE LEGACY SAMPLE', 'PRIVATE LEGACY NOTES']; unset($meta['grading'][$f['question']->id]['submission_rules']);
        $s->update(['metadata' => $meta]);
        Livewire::actingAs($f['learner'])->test(StudentAssessmentTaker::class, ['lesson' => $f['lesson']])->call('startAssessment', $f['assessment']->id)
            ->assertSet('questions.0.options', [])->assertDontSee('PRIVATE LEGACY')->assertDontSee('PRIVATE SAMPLE');
        $this->denied(fn () => $this->attach($f, $s), 422);
    }

    public function test_file_draft_survives_resume_and_only_owner_can_download_before_submission(): void
    {
        $f = $this->fixture(); $s = $this->start($f); $file = $this->attach($f, $s);
        Storage::disk('local')->assertExists($file['path']); $this->assertStringStartsWith('private/assessment-submissions/', $file['path']);
        $this->assertSame('private', Storage::disk('local')->getVisibility($file['path']));
        $this->get(route('assessment.submission-file', [$s->id, $file['id']]))->assertOk()->assertHeader('X-Content-Type-Options', 'nosniff');
        $this->actingAs($f['teacher'])->get(route('assessment.submission-file', [$s->id, $file['id']]))->assertForbidden();
        Livewire::actingAs($f['learner'])->test(StudentAssessmentTaker::class, ['lesson' => $f['lesson']])->call('startAssessment', $f['assessment']->id)->assertSee('solution.txt')->assertDontSee($file['path']);
        $this->assertSame($s->id, $this->start($f)->id); $this->assertCount(1, $s->fresh()->metadata['submission_files'][$f['question']->id]);
    }

    public function test_file_only_submission_counts_as_answered_and_stays_pending_until_manual_review(): void
    {
        $f = $this->fixture('assignment'); $s = $this->start($f); $file = $this->attach($f, $s);
        $result = app(CbtAttemptService::class)->submit($f['learner'], $f['assessment'], $s->id, []);
        $this->assertSame(1, $result['answered_questions']); $this->assertTrue($result['pending_review']); $this->assertNull($result['percentage']);
        $answer = StudentAnswer::where('user_id', $f['learner']->id)->firstOrFail();
        $this->assertNull($answer->answer); $this->assertSame([$file['id']], $answer->exam_data['submission_file_ids']);
        $this->assertSame(0, $f['learner']->getOrCreateGamificationData()->total_points);
        Livewire::actingAs($f['teacher'])->test(AssessmentReviewQueue::class)->call('review', $s->id)->assertSee('solution.txt')->assertSee('PRIVATE SAMPLE ANSWER')->assertSee('PRIVATE GRADING NOTES');
        $this->actingAs($f['teacher'])->get(route('assessment.submission-file', [$s->id, $file['id']]))->assertOk()->assertHeader('Content-Security-Policy', 'sandbox');
        app(AssessmentReviewService::class)->grade($f['teacher'], $s->id, $answer->id, 7, 'Reviewed actual submitted file');
        $this->actingAs($f['learner']); $safe = app(AssessmentResultService::class)->responses($f['assessment'], $f['learner'], 1);
        $this->assertEquals(70, $f['assessment']->getStudentResults($f['learner']->id)['percentage']);
        $this->assertSame('No written response.', $safe[0]['response']); $this->assertSame('solution.txt', $safe[0]['files'][0]['name']);
        $this->assertStringNotContainsString($file['path'], json_encode($safe)); $this->assertStringNotContainsString('PRIVATE', json_encode($safe));
    }

    public function test_required_minimum_and_maximum_files_are_enforced_before_any_answer_is_written(): void
    {
        $f = $this->fixture('project', ['min_files' => 2, 'max_files' => 2]); $s = $this->start($f); $this->attach($f, $s);
        $this->denied(fn () => app(CbtAttemptService::class)->submit($f['learner'], $f['assessment'], $s->id, []), 422);
        $this->assertDatabaseCount('student_answers', 0); $this->assertNull($s->fresh()->ended_at);
        $this->attach($f, $s, 'second.txt', 'Additional original work');
        $this->denied(fn () => $this->attach($f, $s, 'third.txt', 'Too many files'), 422);
        $this->assertCount(2, $s->fresh()->metadata['submission_files'][$f['question']->id]);
        $this->assertTrue(app(CbtAttemptService::class)->submit($f['learner'], $f['assessment'], $s->id, [])['pending_review']);
    }

    public function test_extension_contents_size_and_empty_files_cannot_bypass_frozen_requirements(): void
    {
        $f = $this->fixture('project', ['file_types' => ['pdf']]); $s = $this->start($f);
        foreach ([UploadedFile::fake()->createWithContent('fake.pdf', 'This is plain text'), UploadedFile::fake()->createWithContent('wrong.txt', 'Wrong extension'), UploadedFile::fake()->create('large.pdf', 1025, 'application/pdf'), UploadedFile::fake()->create('empty.pdf', 0, 'application/pdf')] as $upload) {
            try { app(AssessmentSubmissionService::class)->attach($f['learner'], $f['assessment'], $s->id, $f['question']->id, $upload); $this->fail('Invalid file accepted'); }
            catch (ValidationException $exception) { $this->assertArrayHasKey('file', $exception->errors()); }
        }
        $this->assertSame([], Storage::disk('local')->allFiles()); $this->assertArrayNotHasKey('submission_files', $s->fresh()->metadata);
    }

    public function test_identical_attach_retries_do_not_store_twice_and_draft_removal_is_scoped(): void
    {
        $f = $this->fixture(); $s = $this->start($f); $first = $this->attach($f, $s); $same = $this->attach($f, $s);
        $this->assertSame($first['id'], $same['id']); $this->assertCount(1, Storage::disk('local')->allFiles());
        $this->missing(fn () => app(AssessmentSubmissionService::class)->remove($f['learner'], $f['assessment'], $s->id, $f['question']->id + 100, $first['id']));
        app(AssessmentSubmissionService::class)->remove($f['learner'], $f['assessment'], $s->id, $f['question']->id, $first['id']);
        Storage::disk('local')->assertMissing($first['path']); $this->assertSame([], $s->fresh()->metadata['submission_files'][$f['question']->id]);
    }

    public function test_deadline_ownership_and_enrollment_are_rechecked_for_every_file_write(): void
    {
        $f = $this->fixture(); $s = $this->start($f); $outsider = User::factory()->create();
        $this->denied(fn () => app(AssessmentSubmissionService::class)->attach($outsider, $f['assessment'], $s->id, $f['question']->id, UploadedFile::fake()->createWithContent('work.txt', 'No access')), 403);
        $this->missing(fn () => app(AssessmentSubmissionService::class)->attach($f['learner'], $f['assessment'], $s->id, $f['question']->id + 100, UploadedFile::fake()->createWithContent('work.txt', 'Wrong question')));
        $this->travel(21)->minutes(); $this->denied(fn () => $this->attach($f, $s), 422); $this->travelBack();
        $f['learner']->courses()->detach(); $this->denied(fn () => $this->attach($f, $s), 403);
        $this->assertSame([], Storage::disk('local')->allFiles());
    }

    public function test_submitted_files_are_immutable_and_another_instructor_cannot_read_them(): void
    {
        $f = $this->fixture(); $s = $this->start($f); $file = $this->attach($f, $s);
        app(CbtAttemptService::class)->submit($f['learner'], $f['assessment'], $s->id, []);
        $this->missing(fn () => $this->attach($f, $s, 'replacement.txt', 'Late change'));
        $this->missing(fn () => app(AssessmentSubmissionService::class)->remove($f['learner'], $f['assessment'], $s->id, $f['question']->id, $file['id']));
        $this->actingAs(User::factory()->create(['role' => 'instructor']))->get(route('assessment.submission-file', [$s->id, $file['id']]))->assertForbidden();
        Storage::disk('local')->assertExists($file['path']);
        $this->actingAs($f['learner']); $f['learner']->courses()->detach();
        $response = $this->get(route('assessment.submission-file', [$s->id, $file['id']]))->assertOk();
        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));
        $this->assertStringContainsString('private', $response->headers->get('Cache-Control'));
    }

    public function test_failed_metadata_save_removes_only_the_new_file_and_retains_original_draft(): void
    {
        $f = $this->fixture(); $s = $this->start($f); $first = $this->attach($f, $s);
        Event::listen('eloquent.updating: '.LearningSession::class, fn () => throw new \RuntimeException('Forced attachment failure'));
        try { $this->attach($f, $s, 'new.txt', 'New file must roll back'); $this->fail('Expected failure'); }
        catch (\RuntimeException $exception) { $this->assertSame('Forced attachment failure', $exception->getMessage()); }
        finally { Event::forget('eloquent.updating: '.LearningSession::class); }
        $this->assertSame([$first['path']], Storage::disk('local')->allFiles()); $this->assertCount(1, $s->fresh()->metadata['submission_files'][$f['question']->id]);
    }

    public function test_original_requirements_and_server_clock_survive_author_edits_and_file_saves(): void
    {
        $f = $this->fixture(); $s = $this->start($f); $clock = $s->getRawOriginal('started_at');
        $f['question']->update(['options' => ['criteria_type' => 'deliverable', 'file_types' => ['pdf'], 'max_file_size' => 100, 'min_files' => 5, 'max_files' => 5]]);
        $this->travel(2)->minutes(); $file = $this->attach($f, $s);
        $this->assertSame($clock, $s->fresh()->getRawOriginal('started_at')); $this->assertSame(['txt'], $s->fresh()->metadata['grading'][$f['question']->id]['submission_rules']['extensions']);
        $this->assertTrue(app(CbtAttemptService::class)->submit($f['learner'], $f['assessment'], $s->id, [])['pending_review']);
        Storage::disk('local')->assertExists($file['path']); $this->travelBack();
    }

    public function test_expiry_records_saved_files_without_inventing_missing_required_work(): void
    {
        $f = $this->fixture('project', ['min_files' => 2]); $s = $this->start($f); $this->attach($f, $s);
        app(CbtAttemptService::class)->saveAnswer($f['learner'], $f['assessment'], $s->id, $f['question']->id, 'Saved original response');
        $this->travel(21)->minutes();
        $result = app(CbtAttemptService::class)->submit($f['learner'], $f['assessment'], $s->id, [$f['question']->id => 'Late replacement']);
        $this->assertTrue($result['expired']); $this->assertTrue($result['pending_review']); $this->assertFalse($result['passed']);
        $this->assertSame('Saved original response', StudentAnswer::firstOrFail()->answer); $this->assertCount(1, StudentAnswer::firstOrFail()->exam_data['submission_file_ids']);
        $this->travelBack();
    }

    public function test_word_limit_errors_do_not_write_a_draft_and_are_associated_in_the_workspace(): void
    {
        $f = $this->fixture('assignment');
        $component = Livewire::actingAs($f['learner'])->test(StudentAssessmentTaker::class, ['lesson' => $f['lesson']])->call('startAssessment', $f['assessment']->id)
            ->set('answers.'.$f['question']->id, 'A valid short response')->set('answers.'.$f['question']->id, str_repeat('word ', 11))->assertHasErrors('answers.'.$f['question']->id)
            ->call('submitAssessment')->assertHasErrors('submission');
        $s = LearningSession::findOrFail($component->get('examSessionId')); $this->assertSame('A valid short response', $s->metadata['answers'][$f['question']->id]);
        $this->assertDatabaseCount('student_answers', 0);
        $component->set('answers.'.$f['question']->id, 'A revised valid response')->call('submitAssessment')->assertSet('results.pending_review', true);
    }

    public function test_missing_or_changed_private_files_never_report_a_successful_download(): void
    {
        $f = $this->fixture(); $s = $this->start($f); $file = $this->attach($f, $s);
        Storage::disk('local')->put($file['path'], 'Changed contents');
        $this->get(route('assessment.submission-file', [$s->id, $file['id']]))->assertNotFound();
        Storage::disk('local')->delete($file['path']);
        $this->get(route('assessment.submission-file', [$s->id, $file['id']]))->assertNotFound();
        $this->denied(fn () => app(CbtAttemptService::class)->submit($f['learner'], $f['assessment'], $s->id, []), 422);
        $this->assertFalse(app(AssessmentSubmissionService::class)->projectedFiles($s->fresh(), $f['question']->id)[0]['available']);
    }

    public function test_livewire_uploads_attach_to_the_actual_owned_question_and_restore_safe_result_links(): void
    {
        $f = $this->fixture();
        Livewire::actingAs($f['learner'])->test(StudentAssessmentTaker::class, ['lesson' => $f['lesson']])->call('startAssessment', $f['assessment']->id)
            ->call('submitAssessment')->assertHasErrors('submission')
            ->set('uploads.'.$f['question']->id, UploadedFile::fake()->createWithContent('native.txt', 'Real uploaded content'))
            ->call('attachFile', $f['question']->id)->assertHasNoErrors()->assertSee('native.txt')->call('submitAssessment')->assertSet('results.pending_review', true)->assertSee('Submitted files');
        $this->assertSame(1, StudentAnswer::count());
    }

    public function test_project_editor_preserves_array_options_and_rejects_invalid_file_and_rubric_limits(): void
    {
        $f = $this->fixture();
        $component = Livewire::actingAs($f['teacher'])->test(ProjectCriteriaManager::class, ['assessmentId' => $f['assessment']->id])->set('criteriaTitle', 'A second deliverable')->set('criteriaDescription', 'Clear learner instructions')
            ->set('minFiles', 3)->set('maxFiles', 2)->call('createCriteria')->assertHasErrors('maxFiles')
            ->set('maxFiles', 3)->set('fileTypes', ['.TXT'])->call('createCriteria')->assertHasNoErrors();
        $question = $f['assessment']->questions()->reorder()->latest('id')->firstOrFail();
        $this->assertIsArray($question->options); $this->assertSame(['txt'], $question->options['file_types']);
        $component->call('editCriteria', $question->id)->set('criteriaTitle', 'Edited deliverable')->call('updateCriteria')->assertHasNoErrors();
        $this->assertSame('Edited deliverable', $question->fresh()->question_text);
        $component->set('criteriaType', 'rubric')->set('points', 2)->call('createCriteria')->assertHasErrors('rubricLevels.0.points');
    }

    public function test_assignment_editor_requires_real_rubric_totals_and_retains_private_guidance(): void
    {
        $f = $this->fixture('assignment');
        $component = Livewire::actingAs($f['teacher'])->test(AssignmentCriteriaManager::class, ['assessmentId' => $f['assessment']->id])->set('questionText', 'A new assignment')
            ->set('rubricCriteria.0.weight', 50)->call('createQuestion')->assertHasErrors('rubricCriteria')
            ->set('rubricCriteria.0.weight', 40)->set('sampleAnswer', 'PRIVATE NEW SAMPLE')->set('gradingNotes', 'PRIVATE NEW NOTES')
            ->set('allowFileUpload', true)->set('fileTypes', ['TXT'])->call('createQuestion')->assertHasNoErrors();
        $question = $f['assessment']->questions()->reorder()->latest('id')->firstOrFail();
        $this->assertIsArray($question->options); $this->assertSame(['txt'], $question->options['file_types']); $this->assertSame('PRIVATE NEW SAMPLE', $question->options['sample_answer']);
        $component->call('editQuestion', $question->id)->set('questionText', 'Edited assignment')->call('updateQuestion')->assertHasNoErrors();
        $this->assertSame('Edited assignment', $question->fresh()->question_text);
    }

    public function test_editor_ids_and_loaded_criteria_are_locked_and_unrelated_deletions_are_denied(): void
    {
        $f = $this->fixture(); $foreign = Question::create(['assessment_id' => $f['assessment']->id, 'question_type' => 'essay', 'question_text' => 'Different format', 'points' => 1]);
        $this->missing(fn () => Livewire::actingAs($f['teacher'])->test(ProjectCriteriaManager::class, ['assessmentId' => $f['assessment']->id])->call('deleteCriteria', $foreign->id));
        $component = Livewire::test(ProjectCriteriaManager::class, ['assessmentId' => $f['assessment']->id]);
        $this->expectException(\Livewire\Features\SupportLockedProperties\CannotUpdateLockedPropertyException::class);
        $component->set('criteria', [['id' => $foreign->id]]);
    }
    public function test_real_pdf_and_zip_bytes_use_the_declared_private_upload_boundary(): void
    {
        $f = $this->fixture('project', ['file_types' => ['pdf', 'zip']]); $s = $this->start($f);
        $inputs = ['report.pdf' => "%PDF-1.4\n1 0 obj\n<< /Type /Catalog >>\nendobj\n%%EOF\n", 'project.zip' => base64_decode('UEsDBBQAAAAAAKiIVl30EE0dKwAAACsAAAAJAAAAUkVBRE1FLm1kT3JpZ2luYWwgcHJpdmF0ZSBwYWNrYWdlZCBwcm9qZWN0IGZpeHR1cmUuClBLAQIUAxQAAAAAAKiIVl30EE0dKwAAACsAAAAJAAAAAAAAAAAAAACAAQAAAABSRUFETUUubWRQSwUGAAAAAAEAAQA3AAAAUgAAAAAA')];
        foreach ($inputs as $name => $bytes) {
            $file = app(AssessmentSubmissionService::class)->attach($f['learner'], $f['assessment'], $s->id, $f['question']->id, UploadedFile::fake()->createWithContent($name, $bytes));
            $this->assertSame(hash('sha256', $bytes), $file['sha256']);
            $this->assertSame($bytes, Storage::disk('local')->get($file['path']));
            $this->get(route('assessment.submission-file', [$s->id, $file['id']]))->assertOk()->assertDownload($name);
        }
    }

    public function test_unconfigured_legacy_project_definitions_show_setup_state_without_inferred_file_permissions(): void
    {
        $f = $this->fixture(); $f['question']->update(['options' => null]);
        Livewire::actingAs($f['learner'])->test(StudentAssessmentTaker::class, ['lesson' => $f['lesson']])->call('startAssessment', $f['assessment']->id)
            ->assertHasErrors('submission')->assertSee('must configure this project criterion');
        $this->assertDatabaseCount('learning_sessions', 0);
    }

}
