<?php

namespace App\Livewire\Component\CourseManagement\CourseBuilder;

use App\Models\Course;
use App\Models\Section;
use App\Models\Lesson;
use App\Models\Assessment;
use App\Models\UserProgress;
use App\Services\CourseValidationService;
use App\Services\ProgressService;
use App\Services\CourseStatsService;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CourseOutline extends Component
{
    use WithPagination;

    public Course $course;
    public $activeLessonId;
    public $searchTerm = '';
    public $filterType = 'all';
    public $selectedLessons = [];
    
    // Section management
    public $showSectionModal = false;
    public $newSectionTitle = '';
    public $newSectionDescription = '';
    public $editingSectionId = null;
    public $editingSectionTitle = '';
    public $editingSectionDescription = '';
    
    // Lesson management
    public $showLessonModal = false;
    public $isAddingLessonToSectionId = null;
    public $newLessonTitle = '';
    public $newLessonDescription = '';
    public $newLessonContentType = 'text';
    public $newLessonDuration = 0;
    public $editingLessonId = null;
    public $editingLessonTitle = '';
    
    // Assessment management
    public $showAssessmentModal = false;
    public $isAddingAssessmentToSectionId = null;
    public $newAssessmentTitle = '';
    public $newAssessmentDescription = '';
    public $newAssessmentType = 'quiz';
    public $newAssessmentDurationMinutes = 0;
    public $newAssessmentDeadline = null;
    public $editingAssessmentId = null;
    public $editingAssessmentTitle = '';

    protected CourseValidationService $validationService;
    protected ProgressService $progressService;
    protected CourseStatsService $statsService;

    public function boot(CourseValidationService $validationService, ProgressService $progressService)
    {
        $this->validationService = $validationService;
        $this->progressService = $progressService;
    }

    public function mount(Course $course)
    {
        $this->course = $course;
        $this->statsService = new CourseStatsService($this->course);
        $this->activeLessonId = $course->sections()->first()?->lessons()->first()?->id;
        // Unlock the first section by default
        $firstSection = $course->sections()->first();
        if ($firstSection) {
            $firstSection->update(['is_locked' => false]);
        }
        Log::info('CourseOutline mounted for course ID: ' . $this->course->id . ', User ID: ' . Auth::id());
    }

    #[Computed]
    public function filteredSections()
    {
        return Cache::remember("course_{$this->course->id}_sections_{$this->searchTerm}_{$this->filterType}", 300, function () {
            $query = $this->course->sections()->with(['lessons', 'assessments']);
            if ($this->searchTerm) {
                $query->where('title', 'like', "%{$this->searchTerm}%")
                      ->orWhereHas('lessons', fn($q) => $q->where('title', 'like', "%{$this->searchTerm}%"));
            }
            if ($this->filterType !== 'all') {
                $query->whereHas('lessons', fn($q) => $q->where('content_type', $this->filterType));
            }
            return $query->orderBy('order')->paginate(5);
        });
    }

    #[Computed]
    public function courseStats()
    {
        return Cache::remember("course_stats_{$this->course->id}", 300, function () {
            try {
                if (!$this->statsService) {
                    Log::error('CourseStatsService is null in courseStats for course ID: ' . $this->course->id);
                    return [
                        'total_sections' => 0,
                        'total_lessons' => 0,
                        'total_assessments' => 0,
                        'total_projects' => 0,
                        'total_quizzes' => 0,
                        'total_assignments' => 0,
                        'total_enrollments' => 0,
                        'total_duration' => 0,
                        'total_storage' => 0,
                        'completion_percentage' => 0,
                    ];
                }
                return $this->statsService->calculateStats();
            } catch (\Exception $e) {
                Log::error('Failed to calculate course stats in CourseOutline for course ID: ' . $this->course->id . ': ' . $e->getMessage());
                return [
                    'total_sections' => 0,
                    'total_lessons' => 0,
                    'total_assessments' => 0,
                    'total_projects' => 0,
                    'total_quizzes' => 0,
                    'total_assignments' => 0,
                    'total_enrollments' => 0,
                    'total_duration' => 0,
                    'total_storage' => 0,
                    'completion_percentage' => 0,
                ];
            }
        });
    }

    public function showAddSectionForm()
    {
        Log::info('showAddSectionForm called for user ID: ' . Auth::id());
        try {
            $this->showSectionModal = true;
            $this->newSectionTitle = '';
            $this->newSectionDescription = '';
            $this->editingSectionId = null;
            $this->dispatch('notify', type: 'info', message: 'Opening add step modal.');
            Log::info('Section modal opened successfully for user ID: ' . Auth::id());
            $this->dispatch('log-to-console', message: 'Add Step button clicked, modal should open.');
        } catch (\Exception $e) {
            Log::error('Failed to open section modal for user ID: ' . Auth::id() . ': ' . $e->getMessage());
            $this->dispatch('notify', type: 'error', message: 'Failed to open add step modal: ' . $e->getMessage());
        }
    }

    public function addSection()
    {
        try {
            Log::info('Attempting to add section with title: ' . $this->newSectionTitle . ', description: ' . $this->newSectionDescription . ' for course ID: ' . $this->course->id . ' by user ID: ' . Auth::id());
            $this->validationService->validateSection($this->newSectionTitle, $this->newSectionDescription);

            $this->course->sections()->create([
                'title' => $this->newSectionTitle,
                'description' => $this->newSectionDescription,
                'slug' => Str::slug($this->newSectionTitle),
                'order' => $this->course->sections()->count() + 1,
                'is_locked' => true,
            ]);

            Log::info('Section added successfully for course ID: ' . $this->course->id . ' by user ID: ' . Auth::id());
            $this->dispatch('notify', type: 'success', message: 'Step added successfully!');
            $this->dispatch('course-updated');
            $this->showSectionModal = false;
        } catch (\Exception $e) {
            Log::error('Failed to add section for course ID: ' . $this->course->id . ': ' . $e->getMessage());
            $this->dispatch('notify', type: 'error', message: 'Failed to add step: ' . $e->getMessage());
        }
    }

    public function cancelAddSection()
    {
        $this->showSectionModal = false;
        $this->newSectionTitle = '';
        $this->newSectionDescription = '';
        Log::info('Add section modal cancelled by user ID: ' . Auth::id());
    }

    public function editSection($sectionId)
    {
        try {
            $section = Section::findOrFail($sectionId);
            $this->editingSectionId = $sectionId;
            $this->editingSectionTitle = $section->title;
            $this->editingSectionDescription = $section->description;
            $this->showSectionModal = true;
            Log::info('Edit section modal opened for section ID: ' . $sectionId . ' by user ID: ' . Auth::id());
        } catch (\Exception $e) {
            Log::error('Failed to load section ID: ' . $sectionId . ': ' . $e->getMessage());
            $this->dispatch('notify', type: 'error', message: 'Failed to load step: ' . $e->getMessage());
        }
    }

    public function updateSection()
    {
        try {
            Log::info('Attempting to update section ID: ' . $this->editingSectionId . ' with title: ' . $this->editingSectionTitle . ', description: ' . $this->editingSectionDescription . ' by user ID: ' . Auth::id());
            $this->validationService->validateSection($this->editingSectionTitle, $this->editingSectionDescription);

            $section = Section::findOrFail($this->editingSectionId);
            $section->update([
                'title' => $this->editingSectionTitle,
                'description' => $this->editingSectionDescription,
                'slug' => Str::slug($this->editingSectionTitle),
            ]);

            Log::info('Section ID: ' . $this->editingSectionId . ' updated successfully by user ID: ' . Auth::id());
            $this->dispatch('notify', type: 'success', message: 'Step updated successfully!');
            $this->dispatch('course-updated');
            $this->showSectionModal = false;
        } catch (\Exception $e) {
            Log::error('Failed to update section ID: ' . $this->editingSectionId . ': ' . $e->getMessage());
            $this->dispatch('notify', type: 'error', message: 'Failed to update step: ' . $e->getMessage());
        }
    }

    public function deleteSection($sectionId)
    {
        $this->dispatch('confirm-delete', [
            'title' => 'Delete Step',
            'message' => 'Are you sure you want to delete this step and all its content?',
            'method' => 'confirmDeleteSection',
            'params' => [$sectionId],
        ]);
        Log::info('Delete section confirmation triggered for section ID: ' . $sectionId . ' by user ID: ' . Auth::id());
    }

    public function confirmDeleteSection($sectionId)
    {
        try {
            $section = Section::findOrFail($sectionId);
            $section->delete();
            Log::info('Section ID: ' . $sectionId . ' deleted successfully by user ID: ' . Auth::id());
            $this->dispatch('notify', type: 'success', message: 'Step deleted successfully!');
            $this->dispatch('course-updated');
        } catch (\Exception $e) {
            Log::error('Failed to delete section ID: ' . $sectionId . ': ' . $e->getMessage());
            $this->dispatch('notify', type: 'error', message: 'Failed to delete step: ' . $e->getMessage());
        }
    }

    public function reorderSections($orderedIds)
    {
        try {
            foreach ($orderedIds as $order => $id) {
                Section::where('id', $id)->update(['order' => $order + 1]);
            }
            Log::info('Sections reordered successfully for course ID: ' . $this->course->id . ' by user ID: ' . Auth::id());
            $this->dispatch('course-updated');
        } catch (\Exception $e) {
            Log::error('Failed to reorder sections for course ID: ' . $this->course->id . ': ' . $e->getMessage());
            $this->dispatch('notify', type: 'error', message: 'Failed to reorder steps: ' . $e->getMessage());
        }
    }

    public function showAddLessonForm($sectionId)
    {
        if (!Auth::user()->isInstructor()) {
            Log::warning('Unauthorized attempt to add lesson by user ID: ' . Auth::id());
            $this->dispatch('notify', type: 'error', message: 'Only instructors can add lessons.');
            return;
        }
        try {
            $section = Section::findOrFail($sectionId);
            if (!$this->progressService->canAccessSection($this->course, $section) && !Auth::user()->isInstructor()) {
                Log::warning('Attempt to add lesson to locked section ID: ' . $sectionId . ' by user ID: ' . Auth::id());
                $this->dispatch('notify', type: 'error', message: 'Complete the previous step to add content.');
                return;
            }
            $this->isAddingLessonToSectionId = $sectionId;
            $this->showLessonModal = true;
            $this->newLessonTitle = '';
            $this->newLessonDescription = '';
            $this->newLessonContentType = 'text';
            $this->newLessonDuration = 0;
            Log::info('Add lesson modal opened for section ID: ' . $sectionId . ' by user ID: ' . Auth::id());
        } catch (\Exception $e) {
            Log::error('Failed to load lesson form for section ID: ' . $sectionId . ': ' . $e->getMessage());
            $this->dispatch('notify', type: 'error', message: 'Failed to load lesson form: ' . $e->getMessage());
        }
    }

    public function addLesson()
    {
        if (!Auth::user()->isInstructor()) {
            Log::warning('Unauthorized attempt to add lesson by user ID: ' . Auth::id());
            $this->dispatch('notify', type: 'error', message: 'Only instructors can add lessons.');
            return;
        }
        try {
            $this->validationService->validateLesson(
                $this->newLessonTitle,
                $this->newLessonDescription,
                $this->newLessonDuration,
                $this->newLessonContentType
            );

            $section = Section::findOrFail($this->isAddingLessonToSectionId);
            $section->lessons()->create([
                'title' => $this->newLessonTitle,
                'description' => $this->newLessonDescription,
                'content_type' => $this->newLessonContentType,
                'duration_minutes' => $this->newLessonDuration,
                'slug' => Str::slug($this->newLessonTitle),
                'order' => $section->lessons()->count() + 1,
            ]);

            Log::info('Lesson added successfully to section ID: ' . $this->isAddingLessonToSectionId . ' by user ID: ' . Auth::id());
            $this->dispatch('notify', type: 'success', message: 'Lesson added successfully!');
            $this->dispatch('course-updated');
            $this->showLessonModal = false;
        } catch (\Exception $e) {
            Log::error('Failed to add lesson to section ID: ' . $this->isAddingLessonToSectionId . ': ' . $e->getMessage());
            $this->dispatch('notify', type: 'error', message: 'Failed to add lesson: ' . $e->getMessage());
        }
    }

    public function cancelAddLesson()
    {
        $this->isAddingLessonToSectionId = null;
        $this->showLessonModal = false;
        $this->newLessonTitle = '';
        $this->newLessonDescription = '';
        $this->newLessonContentType = 'text';
        $this->newLessonDuration = 0;
        Log::info('Add lesson modal cancelled by user ID: ' . Auth::id());
    }

    public function editLesson($lessonId)
    {
        if (!Auth::user()->isInstructor()) {
            Log::warning('Unauthorized attempt to edit lesson by user ID: ' . Auth::id());
            $this->dispatch('notify', type: 'error', message: 'Only instructors can edit lessons.');
            return;
        }
        try {
            $lesson = Lesson::findOrFail($lessonId);
            $this->editingLessonId = $lessonId;
            $this->editingLessonTitle = $lesson->title;
            $this->showLessonModal = true;
            Log::info('Edit lesson modal opened for lesson ID: ' . $lessonId . ' by user ID: ' . Auth::id());
        } catch (\Exception $e) {
            Log::error('Failed to load lesson ID: ' . $lessonId . ': ' . $e->getMessage());
            $this->dispatch('notify', type: 'error', message: 'Failed to load lesson: ' . $e->getMessage());
        }
    }

    public function updateLesson()
    {
        if (!Auth::user()->isInstructor()) {
            Log::warning('Unauthorized attempt to update lesson by user ID: ' . Auth::id());
            $this->dispatch('notify', type: 'error', message: 'Only instructors can edit lessons.');
            return;
        }
        try {
            $this->validationService->validateLessonTitle($this->editingLessonTitle);

            $lesson = Lesson::findOrFail($this->editingLessonId);
            $lesson->update([
                'title' => $this->editingLessonTitle,
                'slug' => Str::slug($this->editingLessonTitle),
            ]);

            Log::info('Lesson ID: ' . $this->editingLessonId . ' updated successfully by user ID: ' . Auth::id());
            $this->dispatch('notify', type: 'success', message: 'Lesson updated successfully!');
            $this->dispatch('course-updated');
            $this->showLessonModal = false;
        } catch (\Exception $e) {
            Log::error('Failed to update lesson ID: ' . $this->editingLessonId . ': ' . $e->getMessage());
            $this->dispatch('notify', type: 'error', message: 'Failed to update lesson: ' . $e->getMessage());
        }
    }

    public function deleteLesson($lessonId)
    {
        if (!Auth::user()->isInstructor()) {
            Log::warning('Unauthorized attempt to delete lesson by user ID: ' . Auth::id());
            $this->dispatch('notify', type: 'error', message: 'Only instructors can delete lessons.');
            return;
        }
        $this->dispatch('confirm-delete', [
            'title' => 'Delete Lesson',
            'message' => 'Are you sure you want to delete this lesson?',
            'method' => 'confirmDeleteLesson',
            'params' => [$lessonId],
        ]);
        Log::info('Delete lesson confirmation triggered for lesson ID: ' . $lessonId . ' by user ID: ' . Auth::id());
    }

    public function confirmDeleteLesson($lessonId)
    {
        if (!Auth::user()->isInstructor()) {
            Log::warning('Unauthorized attempt to confirm delete lesson by user ID: ' . Auth::id());
            $this->dispatch('notify', type: 'error', message: 'Only instructors can delete lessons.');
            return;
        }
        try {
            $lesson = Lesson::findOrFail($lessonId);
            $lesson->delete();
            Log::info('Lesson ID: ' . $lessonId . ' deleted successfully by user ID: ' . Auth::id());
            $this->dispatch('notify', type: 'success', message: 'Lesson deleted successfully!');
            $this->dispatch('course-updated');
        } catch (\Exception $e) {
            Log::error('Failed to delete lesson ID: ' . $lessonId . ': ' . $e->getMessage());
            $this->dispatch('notify', type: 'error', message: 'Failed to delete lesson: ' . $e->getMessage());
        }
    }

    public function bulkDeleteLessons()
    {
        if (!Auth::user()->isInstructor()) {
            Log::warning('Unauthorized attempt to bulk delete lessons by user ID: ' . Auth::id());
            $this->dispatch('notify', type: 'error', message: 'Only instructors can delete lessons.');
            return;
        }
        $this->dispatch('confirm-delete', [
            'title' => 'Delete Selected Lessons',
            'message' => 'Are you sure you want to delete ' . count($this->selectedLessons) . ' selected lessons?',
            'method' => 'confirmBulkDeleteLessons',
            'params' => [],
        ]);
        Log::info('Bulk delete lessons confirmation triggered by user ID: ' . Auth::id());
    }

    public function confirmBulkDeleteLessons()
    {
        if (!Auth::user()->isInstructor()) {
            Log::warning('Unauthorized attempt to confirm bulk delete lessons by user ID: ' . Auth::id());
            $this->dispatch('notify', type: 'error', message: 'Only instructors can delete lessons.');
            return;
        }
        try {
            Lesson::whereIn('id', $this->selectedLessons)->delete();
            $this->selectedLessons = [];
            Log::info('Bulk deleted lessons successfully by user ID: ' . Auth::id());
            $this->dispatch('notify', type: 'success', message: 'Selected lessons deleted successfully!');
            $this->dispatch('course-updated');
        } catch (\Exception $e) {
            Log::error('Failed to bulk delete lessons: ' . $e->getMessage());
            $this->dispatch('notify', type: 'error', message: 'Failed to delete lessons: ' . $e->getMessage());
        }
    }

    public function showAddAssessmentForm($sectionId)
    {
        if (!Auth::user()->isInstructor()) {
            Log::warning('Unauthorized attempt to add assessment by user ID: ' . Auth::id());
            $this->dispatch('notify', type: 'error', message: 'Only instructors can add assessments.');
            return;
        }
        try {
            $section = Section::findOrFail($sectionId);
            if (!$this->progressService->canAccessSection($this->course, $section) && !Auth::user()->isInstructor()) {
                Log::warning('Attempt to add assessment to locked section ID: ' . $sectionId . ' by user ID: ' . Auth::id());
                $this->dispatch('notify', type: 'error', message: 'Complete the previous step to add content.');
                return;
            }
            $this->isAddingAssessmentToSectionId = $sectionId;
            $this->showAssessmentModal = true;
            $this->newAssessmentTitle = '';
            $this->newAssessmentDescription = '';
            $this->newAssessmentType = 'quiz';
            $this->newAssessmentDurationMinutes = 0;
            $this->newAssessmentDeadline = null;
            Log::info('Add assessment modal opened for section ID: ' . $sectionId . ' by user ID: ' . Auth::id());
        } catch (\Exception $e) {
            Log::error('Failed to load assessment form for section ID: ' . $sectionId . ': ' . $e->getMessage());
            $this->dispatch('notify', type: 'error', message: 'Failed to load assessment form: ' . $e->getMessage());
        }
    }

    public function addAssessment()
    {
        if (!Auth::user()->isInstructor()) {
            Log::warning('Unauthorized attempt to add assessment by user ID: ' . Auth::id());
            $this->dispatch('notify', type: 'error', message: 'Only instructors can add assessments.');
            return;
        }
        try {
            $this->validationService->validateAssessment(
                $this->newAssessmentTitle,
                $this->newAssessmentDescription,
                $this->newAssessmentType,
                $this->newAssessmentDurationMinutes,
                $this->newAssessmentDeadline
            );

            $section = Section::findOrFail($this->isAddingAssessmentToSectionId);
            $section->assessments()->create([
                'title' => $this->newAssessmentTitle,
                'description' => $this->newAssessmentDescription,
                'type' => $this->newAssessmentType,
                'estimated_duration_minutes' => $this->newAssessmentDurationMinutes,
                'deadline' => $this->newAssessmentDeadline,
                'slug' => Str::slug($this->newAssessmentTitle),
                'order' => $section->assessments()->count() + 1,
            ]);

            Log::info('Assessment added successfully to section ID: ' . $this->isAddingAssessmentToSectionId . ' by user ID: ' . Auth::id());
            $this->dispatch('notify', type: 'success', message: 'Assessment added successfully!');
            $this->dispatch('course-updated');
            $this->showAssessmentModal = false;
        } catch (\Exception $e) {
            Log::error('Failed to add assessment to section ID: ' . $this->isAddingAssessmentToSectionId . ': ' . $e->getMessage());
            $this->dispatch('notify', type: 'error', message: 'Failed to add assessment: ' . $e->getMessage());
        }
    }

    public function cancelAddAssessment()
    {
        $this->isAddingAssessmentToSectionId = null;
        $this->showAssessmentModal = false;
        $this->newAssessmentTitle = '';
        $this->newAssessmentDescription = '';
        $this->newAssessmentType = 'quiz';
        $this->newAssessmentDurationMinutes = 0;
        $this->newAssessmentDeadline = null;
        Log::info('Add assessment modal cancelled by user ID: ' . Auth::id());
    }

    public function editAssessment($assessmentId)
    {
        if (!Auth::user()->isInstructor()) {
            Log::warning('Unauthorized attempt to edit assessment by user ID: ' . Auth::id());
            $this->dispatch('notify', type: 'error', message: 'Only instructors can edit assessments.');
            return;
        }
        try {
            $assessment = Assessment::findOrFail($assessmentId);
            $this->editingAssessmentId = $assessmentId;
            $this->editingAssessmentTitle = $assessment->title;
            $this->showAssessmentModal = true;
            Log::info('Edit assessment modal opened for assessment ID: ' . $assessmentId . ' by user ID: ' . Auth::id());
        } catch (\Exception $e) {
            Log::error('Failed to load assessment ID: ' . $assessmentId . ': ' . $e->getMessage());
            $this->dispatch('notify', type: 'error', message: 'Failed to load assessment: ' . $e->getMessage());
        }
    }

    public function updateAssessment()
    {
        if (!Auth::user()->isInstructor()) {
            Log::warning('Unauthorized attempt to update assessment by user ID: ' . Auth::id());
            $this->dispatch('notify', type: 'error', message: 'Only instructors can edit assessments.');
            return;
        }
        try {
            $this->validationService->validateAssessmentTitle($this->editingAssessmentTitle);

            $assessment = Assessment::findOrFail($this->editingAssessmentId);
            $assessment->update([
                'title' => $this->editingAssessmentTitle,
                'slug' => Str::slug($this->editingAssessmentTitle),
            ]);

            Log::info('Assessment ID: ' . $this->editingAssessmentId . ' updated successfully by user ID: ' . Auth::id());
            $this->dispatch('notify', type: 'success', message: 'Assessment updated successfully!');
            $this->dispatch('course-updated');
            $this->showAssessmentModal = false;
        } catch (\Exception $e) {
            Log::error('Failed to update assessment ID: ' . $this->editingAssessmentId . ': ' . $e->getMessage());
            $this->dispatch('notify', type: 'error', message: 'Failed to update assessment: ' . $e->getMessage());
        }
    }

    public function deleteAssessment($assessmentId)
    {
        if (!Auth::user()->isInstructor()) {
            Log::warning('Unauthorized attempt to delete assessment by user ID: ' . Auth::id());
            $this->dispatch('notify', type: 'error', message: 'Only instructors can delete assessments.');
            return;
        }
        $this->dispatch('confirm-delete', [
            'title' => 'Delete Assessment',
            'message' => 'Are you sure you want to delete this assessment?',
            'method' => 'confirmDeleteAssessment',
            'params' => [$assessmentId],
        ]);
        Log::info('Delete assessment confirmation triggered for assessment ID: ' . $assessmentId . ' by user ID: ' . Auth::id());
    }

    public function confirmDeleteAssessment($assessmentId)
    {
        if (!Auth::user()->isInstructor()) {
            Log::warning('Unauthorized attempt to confirm delete assessment by user ID: ' . Auth::id());
            $this->dispatch('notify', type: 'error', message: 'Only instructors can delete assessments.');
            return;
        }
        try {
            $assessment = Assessment::findOrFail($assessmentId);
            $assessment->delete();
            Log::info('Assessment ID: ' . $assessmentId . ' deleted successfully by user ID: ' . Auth::id());
            $this->dispatch('notify', type: 'success', message: 'Assessment deleted successfully!');
            $this->dispatch('course-updated');
        } catch (\Exception $e) {
            Log::error('Failed to delete assessment ID: ' . $assessmentId . ': ' . $e->getMessage());
            $this->dispatch('notify', type: 'error', message: 'Failed to delete assessment: ' . $e->getMessage());
        }
    }

    public function reorderAssessments($sectionId, $orderedIds)
    {
        if (!Auth::user()->isInstructor()) {
            Log::warning('Unauthorized attempt to reorder assessments by user ID: ' . Auth::id());
            $this->dispatch('notify', type: 'error', message: 'Only instructors can reorder assessments.');
            return;
        }
        try {
            foreach ($orderedIds as $order => $id) {
                Assessment::where('id', $id)->update(['order' => $order + 1]);
            }
            Log::info('Assessments reordered successfully for section ID: ' . $sectionId . ' by user ID: ' . Auth::id());
            $this->dispatch('course-updated');
        } catch (\Exception $e) {
            Log::error('Failed to reorder assessments for section ID: ' . $sectionId . ': ' . $e->getMessage());
            $this->dispatch('notify', type: 'error', message: 'Failed to reorder assessments: ' . $e->getMessage());
        }
    }

    #[On('lesson-completed')]
    public function handleLessonCompleted($lessonId)
    {
        try {
            $lesson = Lesson::findOrFail($lessonId);
            $this->progressService->completeLesson($this->course, $lesson);
            Log::info('Lesson ID: ' . $lessonId . ' marked as completed by user ID: ' . Auth::id());
            $this->dispatch('notify', type: 'success', message: 'Lesson completed!');
            $this->dispatch('course-updated');
        } catch (\Exception $e) {
            Log::error('Failed to mark lesson ID: ' . $lessonId . ' as completed: ' . $e->getMessage());
            $this->dispatch('notify', type: 'error', message: 'Failed to mark lesson as completed: ' . $e->getMessage());
        }
    }

    #[On('assessment-completed')]
    public function handleAssessmentCompleted($assessmentId, $passed)
    {
        try {
            $assessment = Assessment::findOrFail($assessmentId);
            $this->progressService->completeAssessment($this->course, $assessment, $passed);
            $message = $passed ? 'Assessment passed! Next step unlocked.' : 'Assessment not passed. Please try again.';
            Log::info('Assessment ID: ' . $assessmentId . ' completed with passed=' . ($passed ? 'true' : 'false') . ' by user ID: ' . Auth::id());
            $this->dispatch('notify', type: $passed ? 'success' : 'error', message: $message);
            $this->dispatch('course-updated');
        } catch (\Exception $e) {
            Log::error('Failed to process assessment ID: ' . $assessmentId . ': ' . $e->getMessage());
            $this->dispatch('notify', type: 'error', message: 'Failed to process assessment: ' . $e->getMessage());
        }
    }

    public function render()
    {
        Log::info('Rendering CourseOutline for course ID: ' . $this->course->id . ' by user ID: ' . Auth::id());
        return view('livewire.component.course-management.course-builder.course-outline', [
            'filteredSections' => $this->filteredSections,
            'courseStats' => $this->courseStats,
        ]);
    }
}