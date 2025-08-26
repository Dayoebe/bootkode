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

    // Real-time update tracking
    public $lastUpdateHash = null;
    public $isEditing = false;

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
        $this->lastUpdateHash = $this->generateOutlineHash();
    }

    // Check for updates without disrupting user work
    #[Livewire\Attributes\On('course-data-updated')]
    public function checkForUpdates()
    {
        // Don't update if user is currently editing
        if ($this->isEditing) {
            return;
        }

        $currentHash = $this->generateOutlineHash();
        if ($currentHash !== $this->lastUpdateHash) {
            $this->refreshCourse();
            $this->lastUpdateHash = $currentHash;
        }
    }

    public function createSection()
    {
        $this->validateOnly('newSectionTitle');
        $this->markAsEditing(true);

        try {
            $section = Section::create([
                'course_id' => $this->course->id,
                'title' => $this->newSectionTitle,
            ]);

            $this->newSectionTitle = '';
            $this->expandedSections[] = $section->id;
            $this->refreshCourse();
            $this->dispatchOutlineUpdated();
            $this->notify('Section created successfully!', 'success');
        } catch (\Exception $e) {
            \Log::error('Failed to create section: ' . $e->getMessage());
            $this->notify('Failed to create section: ' . $e->getMessage(), 'error');
        } finally {
            $this->markAsEditing(false);
        }
    }

    public function startEditSection($sectionId)
    {
        $this->markAsEditing(true);
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
            $this->dispatchOutlineUpdated();
            $this->notify('Section updated successfully!', 'success');
        } catch (\Exception $e) {
            \Log::error('Failed to update section: ' . $e->getMessage());
            $this->notify('Failed to update section: ' . $e->getMessage(), 'error');
        }
    }

    public function cancelEditSection()
    {
        $this->markAsEditing(false);
        $this->editingSectionId = null;
        $this->newSectionTitleEdit = '';
    }

    public function deleteSection($sectionId)
    {
        $this->markAsEditing(true);

        try {
            $section = Section::findOrFail($sectionId);
            $this->expandedSections = array_diff($this->expandedSections, [$sectionId]);
            $section->delete();
            $this->refreshCourse();
            $this->dispatchOutlineUpdated();
            $this->notify('Section deleted successfully!', 'success');
        } catch (\Exception $e) {
            \Log::error('Failed to delete section: ' . $e->getMessage());
            $this->notify('Failed to delete section: ' . $e->getMessage(), 'error');
        } finally {
            $this->markAsEditing(false);
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

        $this->markAsEditing(true);

        try {
            $lesson = Lesson::create([
                'section_id' => $sectionId,
                'title' => $this->newLessonTitles[$sectionId],
                'slug' => Str::slug($this->newLessonTitles[$sectionId])
            ]);

            $this->newLessonTitles[$sectionId] = '';
            $this->refreshCourse();
            $this->dispatchOutlineUpdated();

            $this->dispatch('lesson-selected', lessonId: $lesson->id)
                ->to('course-management.course-builder');

            $this->notify('Lesson created successfully!', 'success');
        } catch (\Exception $e) {
            \Log::error('Failed to create lesson: ' . $e->getMessage());
            $this->notify('Failed to create lesson: ' . $e->getMessage(), 'error');
        } finally {
            $this->markAsEditing(false);
        }
    }

    public function startEditLesson($lessonId)
    {
        $this->markAsEditing(true);
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
            $this->dispatchOutlineUpdated();
            $this->notify('Lesson updated successfully!', 'success');
        } catch (\Exception $e) {
            \Log::error('Failed to update lesson: ' . $e->getMessage());
            $this->notify('Failed to update lesson: ' . $e->getMessage(), 'error');
        }
    }

    public function cancelEditLesson()
    {
        $this->markAsEditing(false);
        $this->editingLessonId = null;
        $this->newLessonTitleEdit = '';
    }

    public function selectLesson($lessonId)
    {
        $this->dispatch('lesson-selected', lessonId: $lessonId)
            ->to('course-management.course-builder');

        // Signal user activity
        $this->dispatch('user-activity')->to('course-management.course-builder');
    }

    public function deleteLesson($lessonId)
    {
        $this->markAsEditing(true);

        try {
            Lesson::findOrFail($lessonId)->delete();
            $this->refreshCourse();
            $this->dispatchOutlineUpdated();
            $this->notify('Lesson deleted successfully!', 'success');
        } catch (\Exception $e) {
            \Log::error('Failed to delete lesson: ' . $e->getMessage());
            $this->notify('Failed to delete lesson: ' . $e->getMessage(), 'error');
        } finally {
            $this->markAsEditing(false);
        }
    }

    // Track editing state to prevent updates during user input
    private function markAsEditing($editing)
    {
        $this->isEditing = $editing;
        if (!$editing) {
            $this->dispatch('user-activity')->to('course-management.course-builder');
        }
    }

    private function refreshCourse()
    {
        $this->course->refresh()->load('sections.lessons');
        $this->lastUpdateHash = $this->generateOutlineHash();
    }

    private function dispatchOutlineUpdated()
    {
        $this->dispatch('outline-updated')->to('course-management.course-builder.toolbar');
        $this->dispatch('user-activity')->to('course-management.course-builder');
    }

    // Generate hash to detect structural changes
    private function generateOutlineHash()
    {
        $this->course->refresh();
        $data = [];

        foreach ($this->course->sections()->with('lessons')->get() as $section) {
            $data[] = [
                'id' => $section->id,
                'title' => $section->title,
                'updated_at' => $section->updated_at?->timestamp,
                'lessons' => $section->lessons->map(fn($lesson) => [
                    'id' => $lesson->id,
                    'title' => $lesson->title,
                    'updated_at' => $lesson->updated_at?->timestamp,
                ])->toArray()
            ];
        }

        return md5(json_encode($data));
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