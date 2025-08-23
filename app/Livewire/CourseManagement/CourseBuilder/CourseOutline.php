<?php

namespace App\Livewire\CourseManagement\CourseBuilder;

use App\Models\Course;
use App\Models\Section;
use App\Models\Lesson;
use Livewire\Component;
use Illuminate\Support\Str;

class CourseOutline extends Component
{
    public Course $course;
    public $activeLessonId = null;
    public $activeSectionId = null;
    public $newSectionTitle = '';
    public $editingSectionId = null;
    public $newSectionTitleEdit = '';
    public $editingLessonId = null;
    public $newLessonTitleEdit = '';
    public $expandedSections = [];
    public $newLessonTitles = [];

    protected $rules = [
        'newSectionTitle' => 'required|string|max:255',
        'newSectionTitleEdit' => 'required|string|max:255',
        'newLessonTitles.*' => 'required|string|max:255',
        'newLessonTitleEdit' => 'required|string|max:255',
    ];

    public function mount(Course $course)
    {
        $this->course = $course->load('sections.lessons');
        $this->expandedSections = $this->course->sections->pluck('id')->toArray();
    }

    public function createSection()
    {
        $this->validateOnly('newSectionTitle');

        try {
            $section = Section::create([
                'course_id' => $this->course->id,
                'title' => $this->newSectionTitle,
            ]);

            $this->newSectionTitle = '';
            $this->expandedSections[] = $section->id;
            $this->refreshCourse();

            // Dispatch event to update toolbar counts
            $this->dispatchOutlineUpdated();

            $this->notify('Section created successfully!', 'success');
        } catch (\Exception $e) {
            \Log::error('Failed to create section: ' . $e->getMessage());
            $this->notify('Failed to create section: ' . $e->getMessage(), 'error');
        }
    }

    public function startEditSection($sectionId)
    {
        $section = Section::findOrFail($sectionId);
        $this->editingSectionId = $sectionId;
        $this->newSectionTitleEdit = $section->title;
    }

    public function updateSection()
    {
        $this->validateOnly('newSectionTitleEdit');

        try {
            Section::findOrFail($this->editingSectionId)->update([
                'title' => $this->newSectionTitleEdit
            ]);

            $this->cancelEditSection();
            $this->refreshCourse();

            // Dispatch event to update toolbar
            $this->dispatchOutlineUpdated();

            $this->notify('Section updated successfully!', 'success');
        } catch (\Exception $e) {
            \Log::error('Failed to update section: ' . $e->getMessage());
            $this->notify('Failed to update section: ' . $e->getMessage(), 'error');
        }
    }

    public function cancelEditSection()
    {
        $this->editingSectionId = null;
        $this->newSectionTitleEdit = '';
    }

    public function deleteSection($sectionId)
    {
        try {
            $section = Section::findOrFail($sectionId);

            // Remove from expanded sections if it exists
            $this->expandedSections = array_diff($this->expandedSections, [$sectionId]);

            $section->delete();
            $this->refreshCourse();

            // Dispatch event to update toolbar counts
            $this->dispatchOutlineUpdated();

            $this->notify('Section deleted successfully!', 'success');
        } catch (\Exception $e) {
            \Log::error('Failed to delete section: ' . $e->getMessage());
            $this->notify('Failed to delete section: ' . $e->getMessage(), 'error');
        }
    }

    public function toggleSection($sectionId)
    {
        if (in_array($sectionId, $this->expandedSections)) {
            $this->expandedSections = array_diff($this->expandedSections, [$sectionId]);
        } else {
            $this->expandedSections[] = $sectionId;
        }
    }

    public function createLesson($sectionId)
    {
        $this->validate([
            'newLessonTitles.' . $sectionId => 'required|string|max:255'
        ]);

        try {
            $lesson = Lesson::create([
                'section_id' => $sectionId,
                'title' => $this->newLessonTitles[$sectionId],
                'slug' => Str::slug($this->newLessonTitles[$sectionId])
            ]);

            $this->newLessonTitles[$sectionId] = '';
            $this->refreshCourse();

            // Dispatch event to update toolbar counts
            $this->dispatchOutlineUpdated();

            $this->dispatch('lesson-selected', lessonId: $lesson->id)
                ->to('course-management.course-builder');

            $this->notify('Lesson created successfully!', 'success');
        } catch (\Exception $e) {
            \Log::error('Failed to create lesson: ' . $e->getMessage());
            $this->notify('Failed to create lesson: ' . $e->getMessage(), 'error');
        }
    }

    public function startEditLesson($lessonId)
    {
        $lesson = Lesson::findOrFail($lessonId);
        $this->editingLessonId = $lessonId;
        $this->newLessonTitleEdit = $lesson->title;
    }

    public function updateLesson()
    {
        $this->validateOnly('newLessonTitleEdit');

        try {
            Lesson::findOrFail($this->editingLessonId)->update([
                'title' => $this->newLessonTitleEdit,
                'slug' => Str::slug($this->newLessonTitleEdit)
            ]);

            $this->cancelEditLesson();
            $this->refreshCourse();

            // Dispatch event to update toolbar
            $this->dispatchOutlineUpdated();

            $this->notify('Lesson updated successfully!', 'success');
        } catch (\Exception $e) {
            \Log::error('Failed to update lesson: ' . $e->getMessage());
            $this->notify('Failed to update lesson: ' . $e->getMessage(), 'error');
        }
    }

    public function cancelEditLesson()
    {
        $this->editingLessonId = null;
        $this->newLessonTitleEdit = '';
    }

    public function selectLesson($lessonId)
    {
        $this->dispatch('lesson-selected', lessonId: $lessonId)
            ->to('course-management.course-builder');
    }

    public function deleteLesson($lessonId)
    {
        try {
            Lesson::findOrFail($lessonId)->delete();
            $this->refreshCourse();

            // Dispatch event to update toolbar counts
            $this->dispatchOutlineUpdated();

            $this->notify('Lesson deleted successfully!', 'success');
        } catch (\Exception $e) {
            \Log::error('Failed to delete lesson: ' . $e->getMessage());
            $this->notify('Failed to delete lesson: ' . $e->getMessage(), 'error');
        }
    }

    private function refreshCourse()
    {
        $this->course->refresh()->load('sections.lessons');
    }

    private function dispatchOutlineUpdated()
    {
        // Dispatch to toolbar component specifically
        $this->dispatch('outline-updated')
            ->to('course-management.course-builder.toolbar');
    }

    public function notify($message, $type = 'success')
    {
        $this->dispatch('notify', message: $message, type: $type);
    }

    public function render()
    {
        return view('livewire.course-management.course-builder.course-outline', [
            'sections' => $this->course->sections()
                ->with(['lessons' => fn($q) => $q->orderBy('created_at', 'desc')])
                ->orderBy('created_at', 'desc')
                ->get(),
        ]);
    }
}