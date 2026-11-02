<?php

namespace Tests\Feature;

use App\Models\Core\User;
use App\Models\Learning\Course;
use App\Models\Learning\Section;
use App\Models\Learning\Lesson;
use App\Models\Assessment\Assessment;
use App\Services\CourseAuthoringAccess;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CourseAuthoringIntegrityTest extends TestCase
{
    use RefreshDatabase;

    private function fixture(): array
    {
        Role::findOrCreate('instructor', 'web');
        $owner = User::factory()->create(['role' => 'instructor']);
        $course = Course::withoutEvents(fn () => Course::create(['instructor_id' => $owner->id, 'title' => 'Authoring fixture', 'slug' => 'authoring-fixture-'.uniqid(), 'description' => 'Private course draft', 'is_published' => false]));
        $section = Section::create(['course_id' => $course->id, 'title' => 'Section', 'order' => 1]);
        $lesson = Lesson::withoutEvents(fn () => Lesson::create(['section_id' => $section->id, 'title' => 'Private lesson', 'slug' => 'private-lesson-'.uniqid(), 'content' => 'Private lesson content', 'order' => 1]));
        $assessment = Assessment::create(['course_id' => $course->id, 'section_id' => $section->id, 'lesson_id' => $lesson->id, 'title' => 'Quiz fixture', 'slug' => 'quiz-'.uniqid(), 'type' => 'quiz']);
        return [$owner, $course, $lesson, $assessment];
    }

    public function test_learner_cannot_mount_any_authoring_component(): void
    {
        [, $course, $lesson, $assessment] = $this->fixture();
        $this->actingAs(User::factory()->create());
        foreach (['CourseForm' => [], 'AllCourses' => [], 'CourseReviews' => [], 'ReviewAnalytics' => [], 'CourseApprovals' => [], 'CourseCategories' => [], 'EditCourse' => ['course' => $course], 'CourseBuilder' => ['course' => $course],
            'CourseBuilder\\CourseOutline' => ['course' => $course], 'CourseBuilder\\Toolbar' => ['course' => $course],
            'CourseBuilder\\LessonEditor' => ['lessonId' => $lesson->id], 'CourseBuilder\\AssessmentManager' => ['lessonId' => $lesson->id],
            'CourseBuilder\\QuestionManager' => ['assessmentId' => $assessment->id], 'CourseBuilder\\AssignmentCriteriaManager' => ['assessmentId' => $assessment->id],
            'CourseBuilder\\ProjectCriteriaManager' => ['assessmentId' => $assessment->id], 'CourseBuilder\\QnaCriteriaManager' => ['assessmentId' => $assessment->id]] as $name => $params) {
            Livewire::test('App\\Livewire\\CourseManagement\\'.$name, $params)->assertForbidden();
        }
    }

    public function test_course_outline_follows_curriculum_order_instead_of_creation_time(): void
    {
        [$owner, $course, $lesson] = $this->fixture();
        $first = $lesson->section;
        $first->update(['title' => 'First module', 'order' => 1, 'created_at' => now()->subDays(3)]);
        $third = Section::create(['course_id' => $course->id, 'title' => 'Third module', 'order' => 3, 'created_at' => now()->subDay()]);
        $second = Section::create(['course_id' => $course->id, 'title' => 'Second module', 'order' => 2, 'created_at' => now()]);
        $lesson->update(['order' => 2, 'created_at' => now()->subDay()]);
        $earlier = Lesson::withoutEvents(fn () => Lesson::create([
            'section_id' => $first->id, 'title' => 'Earlier learning step',
            'slug' => 'earlier-learning-step', 'content' => 'Earlier practice', 'order' => 1,
        ]));

        Livewire::actingAs($owner)
            ->test(\App\Livewire\CourseManagement\CourseBuilder\CourseOutline::class, ['course' => $course])
            ->assertSeeInOrder(['First module', 'Second module', 'Third module'])
            ->assertViewHas('sections', fn ($sections) => $sections->pluck('id')->all() === [$first->id, $second->id, $third->id]
                && $sections->first()->lessons->pluck('id')->all() === [$earlier->id, $lesson->id]);
    }

    public function test_another_instructor_cannot_open_draft_editors(): void
    {
        [, $course, $lesson, $assessment] = $this->fixture();
        $this->actingAs(User::factory()->create(['role' => 'instructor']));
        Livewire::test(\App\Livewire\CourseManagement\EditCourse::class, ['course' => $course])->assertForbidden();
        Livewire::test(\App\Livewire\CourseManagement\CourseBuilder\LessonEditor::class, ['lessonId' => $lesson->id])->assertForbidden();
        Livewire::test(\App\Livewire\CourseManagement\CourseBuilder\QuestionManager::class, ['assessmentId' => $assessment->id])->assertForbidden();
        Livewire::test(\App\Livewire\CourseManagement\CoursePreview::class, ['course' => $course])->assertForbidden();
    }

    public function test_assessment_links_open_the_correct_lesson_in_the_course_builder(): void
    {
        [$owner, $course, $lesson] = $this->fixture();
        $this->actingAs($owner)
            ->get(route('lesson.assessments.manage', ['lessonId' => $lesson->id]))
            ->assertRedirect(route('course-builder', [
                'course' => $course->id, 'lesson' => $lesson->id, 'tab' => 'assessment',
            ]));

        Livewire::test(\App\Livewire\CourseManagement\CourseBuilder\LessonEditor::class, ['lessonId' => $lesson->id])
            ->assertSee('Lesson assessments')
            ->assertDontSee('Open assessment authoring workspace')
            ->assertDontSee('Lesson assessment authoring');
    }

    public function test_assessment_link_redirect_preserves_authorization(): void
    {
        [, , $lesson] = $this->fixture();
        $url = route('lesson.assessments.manage', ['lessonId' => $lesson->id]);
        $this->get($url)->assertRedirect(route('login'));
        $this->actingAs(User::factory()->create())->get($url)->assertForbidden();
        $this->actingAs(User::factory()->create(['role' => 'instructor']))->get($url)->assertForbidden();
    }

    public function test_embedded_assessment_manager_exposes_create_edit_and_question_views(): void
    {
        [$owner, , $lesson, $assessment] = $this->fixture();
        Livewire::actingAs($owner)
            ->test(\App\Livewire\CourseManagement\CourseBuilder\AssessmentManager::class, ['lessonId' => $lesson->id])
            ->call('toggleCreateForm')->assertSee('Create assessment')
            ->call('backToList')->assertSee('Quiz fixture')
            ->call('editAssessment', $assessment->id)->assertSee('Edit assessment')
            ->call('backToList')
            ->call('manageQuestions', $assessment->id)->assertSee('Quiz questions');
    }

    public function test_owner_access_and_cross_course_lesson_resolution_are_enforced(): void
    {
        [$owner, $course, $lesson] = $this->fixture();
        $this->actingAs($owner);
        $this->assertSame($course->id, app(CourseAuthoringAccess::class)->course($course->id)->id);
        $this->assertSame($lesson->id, app(CourseAuthoringAccess::class)->lesson($lesson->id)->id);
        Livewire::test(\App\Livewire\CourseManagement\CourseBuilder\Toolbar::class, ['course' => $course])->call('togglePublished')->assertStatus(422);
        $this->assertFalse($course->fresh()->is_published);
    }

    public function test_instructor_cannot_approve_or_delete_another_course_through_list_actions(): void
    {
        [, $course] = $this->fixture();
        $this->actingAs(User::factory()->create(['role' => 'instructor']));
        Livewire::test(\App\Livewire\CourseManagement\AllCourses::class)->call('toggleApproved', $course->id)->assertForbidden();
        Livewire::test(\App\Livewire\CourseManagement\AllCourses::class)->call('deleteCourse', $course->id)->assertForbidden();
        $this->assertDatabaseHas('courses', ['id' => $course->id]);
    }
    public function test_client_cannot_supply_upload_validation_or_storage_arguments(): void
    {
        [$owner, , $lesson] = $this->fixture();
        $component = Livewire::actingAs($owner)->test(\App\Livewire\CourseManagement\CourseBuilder\LessonEditor::class, ['lessonId' => $lesson->id]);
        $this->expectException(\Livewire\Exceptions\MethodNotFoundException::class);
        $component->call('handleUpload', 'documents', 'documentUpload', 'php', 999999, 'outside');
    }

    public function test_client_cannot_replace_persisted_file_metadata(): void
    {
        [$owner, , $lesson] = $this->fixture();
        $component = Livewire::actingAs($owner)->test(\App\Livewire\CourseManagement\CourseBuilder\LessonEditor::class, ['lessonId' => $lesson->id]);
        $this->expectException(\Livewire\Features\SupportLockedProperties\CannotUpdateLockedPropertyException::class);
        $component->set('documents', [['path' => 'other-users-file.pdf']]);
    }

    public function test_real_document_upload_preserves_array_metadata_and_removal_keeps_shared_asset(): void
    {
        \Illuminate\Support\Facades\Storage::fake('public');
        [$owner, , $lesson] = $this->fixture();
        $component = Livewire::actingAs($owner)->test(\App\Livewire\CourseManagement\CourseBuilder\LessonEditor::class, ['lessonId' => $lesson->id])
            ->set('documentUpload', \Illuminate\Http\UploadedFile::fake()->create('guide.pdf', 10, 'application/pdf'))->call('uploadDocument')->assertHasNoErrors();
        $files = $lesson->fresh()->documents;
        $this->assertIsArray($files);
        $this->assertCount(1, $files);
        \Illuminate\Support\Facades\Storage::disk('public')->assertExists($files[0]['path']);
        $component->call('removeFile', 'documents', 0)->assertHasNoErrors();
        $this->assertSame([], $lesson->fresh()->documents);
        \Illuminate\Support\Facades\Storage::disk('public')->assertExists($files[0]['path']);
    }

    public function test_teacher_cannot_delete_an_assessment_from_another_lesson(): void
    {
        [$owner, , $lesson] = $this->fixture();
        [, , , $otherAssessment] = $this->fixture();
        try {
            Livewire::actingAs($owner)->test(\App\Livewire\CourseManagement\CourseBuilder\AssessmentManager::class, ['lessonId' => $lesson->id])->call('deleteAssessment', $otherAssessment->id);
            $this->fail('Cross-lesson assessment deletion was accepted');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $exception) {
            $this->assertSame(Assessment::class, $exception->getModel());
        }
        $this->assertDatabaseHas('assessments', ['id' => $otherAssessment->id, 'deleted_at' => null]);
    }

    public function test_course_and_lesson_deletion_preserve_existing_legacy_learning_history(): void
    {
        [$owner, $course, $lesson] = $this->fixture();
        $learner = User::factory()->create();
        $learner->courses()->attach($course->id, ['progress' => 20]);
        try {
            $course->delete();
            $this->fail('Course deletion discarded learning history');
        } catch (\DomainException $exception) {
            $this->assertStringContainsString('history exists', $exception->getMessage());
        }
        Livewire::actingAs($owner)->test(\App\Livewire\CourseManagement\CourseBuilder\CourseOutline::class, ['course' => $course])->call('deleteLesson', $lesson->id);
        $this->assertDatabaseHas('courses', ['id' => $course->id]);
        $this->assertDatabaseHas('lessons', ['id' => $lesson->id]);
        $this->assertDatabaseHas('course_user', ['course_id' => $course->id, 'user_id' => $learner->id]);
    }

    public function test_unused_draft_course_can_still_be_removed(): void
    {
        [, $course] = $this->fixture();
        $id = $course->id;
        $course->delete();
        $this->assertDatabaseMissing('courses', ['id' => $id]);
    }

    public function test_instructor_account_removal_cannot_cascade_into_learning_history_or_strip_roles(): void
    {
        [$owner, $course] = $this->fixture();
        $learner = User::factory()->create();
        $learner->courses()->attach($course->id, ['progress' => 10]);
        try {
            $owner->delete();
            $this->fail('Instructor deletion discarded learner records');
        } catch (\DomainException $exception) {
            $this->assertStringContainsString('history exists', $exception->getMessage());
        }
        $this->assertTrue($owner->fresh()->hasRole('instructor'));
        $this->assertDatabaseHas('course_user', ['course_id' => $course->id, 'user_id' => $learner->id]);
    }

    public function test_account_deletion_preserves_issued_verification_records(): void
    {
        [, $course] = $this->fixture();
        $learner = User::factory()->create();
        $certificate = \App\Models\Credentials\Certificate::create(['user_id' => $learner->id, 'course_id' => $course->id, 'status' => 'issued']);
        $code = $certificate->verification_code;
        try {
            $learner->delete();
            $this->fail('Account removal discarded an issued verification record');
        } catch (\DomainException $exception) {
            $this->assertStringContainsString('preserving verification', $exception->getMessage());
        }
        $this->assertSame($code, $certificate->fresh()->verification_code);
        $this->assertDatabaseHas('users', ['id' => $learner->id]);
    }

}
