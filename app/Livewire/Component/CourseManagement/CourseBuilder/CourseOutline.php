<?php

namespace App\Livewire\Component\CourseManagement\CourseBuilder;

use App\Models\Course;
use App\Models\Section;
use App\Models\Lesson;
use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\Attributes\On;

class CourseOutline extends Component
{
    public Course $course;
    public $activeLessonId = null;
    public $activeSectionId = null;
    public $newSectionTitle = '';
    public $editingSectionId = null;
    public $newSectionTitleEdit = '';
    public $expandedSections = [];
    public $newLessonTitles = [];

    protected $rules = [
        'newSectionTitle' => 'required|string|max:255',
        'newSectionTitleEdit' => 'required|string|max:255',
        'newLessonTitles.*' => 'required|string|max:255',
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
            Section::create([
                'course_id' => $this->course->id,
                'title' => $this->newSectionTitle,
                'order' => $this->course->sections()->max('order') + 1 ?? 1,
            ]);

            $this->newSectionTitle = '';
            $this->refreshCourse();
            $this->notify('Section created successfully!', 'success');
        } catch (\Exception $e) {
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
            $this->notify('Section updated successfully!', 'success');
        } catch (\Exception $e) {
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
            Section::findOrFail($sectionId)->delete();
            $this->refreshCourse();
            $this->notify('Section deleted successfully!', 'success');
        } catch (\Exception $e) {
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
            'newLessonTitles.'.$sectionId => 'required|string|max:255'
        ]);

        try {
            $lesson = Lesson::create([
                'section_id' => $sectionId,
                'title' => $this->newLessonTitles[$sectionId],
                'order' => Lesson::where('section_id', $sectionId)->max('order') + 1 ?? 1,
                'slug' => Str::slug($this->newLessonTitles[$sectionId])
            ]);

            $this->newLessonTitles[$sectionId] = '';
            $this->refreshCourse();
            $this->dispatch('lesson-selected', lessonId: $lesson->id)
                ->to('component.course-management.course-builder');
            $this->notify('Lesson created successfully!', 'success');
        } catch (\Exception $e) {
            $this->notify('Failed to create lesson: ' . $e->getMessage(), 'error');
        }
    }

    public function selectLesson($lessonId)
    {
        $this->dispatch('lesson-selected', lessonId: $lessonId)
            ->to('component.course-management.course-builder');
    }

    public function deleteLesson($lessonId)
    {
        try {
            Lesson::findOrFail($lessonId)->delete();
            $this->refreshCourse();
            $this->notify('Lesson deleted successfully!', 'success');
        } catch (\Exception $e) {
            $this->notify('Failed to delete lesson: ' . $e->getMessage(), 'error');
        }
    }

    #[On('reorder-sections')]
    public function reorderSections($orderedIds)
    {
        try {
            Section::setNewOrder($orderedIds);
            $this->refreshCourse();
        } catch (\Exception $e) {
            $this->notify('Failed to reorder sections: ' . $e->getMessage(), 'error');
        }
    }

    #[On('reorder-lessons')]
    public function reorderLessons($sectionId, $orderedIds)
    {
        try {
            Lesson::where('section_id', $sectionId)
                ->setNewOrder($orderedIds);
            $this->refreshCourse();
        } catch (\Exception $e) {
            $this->notify('Failed to reorder lessons: ' . $e->getMessage(), 'error');
        }
    }

    private function refreshCourse()
    {
        $this->course->refresh()->load('sections.lessons');
    }

    public function notify($message, $type = 'success')
    {
        $this->dispatch('notify', message: $message, type: $type);
    }

    public function render()
    {
        return view('livewire.component.course-management.course-builder.course-outline', [
            'sections' => $this->course->sections()
                ->with(['lessons' => fn($q) => $q->orderBy('order')])
                ->orderBy('order')
                ->get(),
        ]);
    }
}