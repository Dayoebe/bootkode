<?php

namespace App\Livewire\CourseManagement\CourseBuilder;

use App\Models\Learning\Course;
use App\Models\Learning\Section;
use App\Models\Learning\Lesson;
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

    // Listen for lesson selection from parent
    protected $listeners = [
        'lesson-selected' => 'handleLessonSelected',
        'course-data-updated' => 'checkForUpdates',
    ];

    public function mount(Course $course, $activeLessonId = null, $activeSectionId = null)
    {
        $this->course = $course->load('sections.lessons');
        $this->activeLessonId = $activeLessonId;
        $this->activeSectionId = $activeSectionId;
        $this->expandedSections = $this->course->sections->pluck('id')->toArray();
        $this->lastUpdateHash = $this->generateOutlineHash();
    }

    // Handle lesson selection updates from parent
    public function handleLessonSelected($lessonId)
    {
        $lesson = Lesson::find($lessonId);
        if ($lesson) {
            $this->activeLessonId = $lessonId;
            $this->activeSectionId = $lesson->section_id;
            
            // Ensure section is expanded
            if (!in_array($lesson->section_id, $this->expandedSections)) {
                $this->expandedSections[] = $lesson->section_id;
            }
        }
    }

    // Check for updates without disrupting user work
    public function checkForUpdates()
    {
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
            
            // If deleting active section, clear active lesson
            if ($this->activeSectionId == $sectionId) {
                $this->activeLessonId = null;
                $this->activeSectionId = null;
                $this->dispatch('lesson-deselected')->to('course-management.course-builder');
            }
            
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
            $section = Section::with('course')->findOrFail($sectionId);
            $lessonTitle = $this->newLessonTitles[$sectionId];
            
            // Generate proper slug with course prefix
            $slug = $this->generateLessonSlug($section->course, $lessonTitle);
            
            $lesson = Lesson::create([
                'section_id' => $sectionId,
                'title' => $lessonTitle,
                'slug' => $slug,
                'content' => '', // Blank content for new lesson
            ]);

            $this->newLessonTitles[$sectionId] = '';
            $this->refreshCourse();
            $this->dispatchOutlineUpdated();

            // Automatically select the new lesson
            $this->selectLesson($lesson->id);

            $this->notify('Lesson created successfully! Start editing below.', 'success');
        } catch (\Exception $e) {
            \Log::error('Failed to create lesson: ' . $e->getMessage());
            $this->notify('Failed to create lesson: ' . $e->getMessage(), 'error');
        } finally {
            $this->markAsEditing(false);
        }
    }

    /**
     * Generate a unique slug for a lesson with course prefix
     */
    private function generateLessonSlug($course, $lessonTitle)
    {
        // Create course prefix
        $coursePrefix = $this->createCoursePrefix($course->title);
        
        // Combine with lesson title
        $baseSlug = Str::slug($coursePrefix . '-' . $lessonTitle);
        
        // Limit length
        $baseSlug = Str::limit($baseSlug, 100, '');
        
        // Check uniqueness
        $slug = $baseSlug;
        $counter = 1;
        
        while (Lesson::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
            
            if ($counter > 100) {
                $slug = $baseSlug . '-' . Str::random(5);
                break;
            }
        }
        
        return $slug;
    }

    /**
     * Create a short, meaningful course prefix from course title
     */
    private function createCoursePrefix($courseTitle)
    {
        $stopWords = ['introduction', 'to', 'the', 'a', 'an', 'for', 'in', 'on', 'with', 'and', 'or'];
        $words = explode(' ', Str::lower($courseTitle));
        
        $filtered = [];
        foreach ($words as $index => $word) {
            $word = trim($word);
            
            if ($index === 0) {
                if (in_array($word, ['introduction', 'introductory'])) {
                    $filtered[] = 'intro';
                } else {
                    $filtered[] = $word;
                }
            } elseif (!in_array($word, $stopWords)) {
                $word = $this->abbreviateTerm($word);
                $filtered[] = $word;
            }
        }
        
        $filtered = array_slice($filtered, 0, 3);
        $prefix = implode('-', $filtered);
        return Str::limit(Str::slug($prefix), 30, '');
    }

    /**
     * Abbreviate common technical terms
     */
    private function abbreviateTerm($word)
    {
        $abbreviations = [
            'javascript' => 'js',
            'typescript' => 'ts',
            'development' => 'dev',
            'programming' => 'prog',
            'application' => 'app',
            'database' => 'db',
            'machine' => 'ml',
            'learning' => 'learn',
            'advanced' => 'adv',
            'beginner' => 'begin',
            'intermediate' => 'inter',
            'professional' => 'pro',
        ];
        
        return $abbreviations[$word] ?? $word;
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
        $lesson = Lesson::find($lessonId);
        
        if (!$lesson) {
            $this->notify('Lesson not found', 'error');
            return;
        }

        // Update local state
        $this->activeLessonId = $lessonId;
        $this->activeSectionId = $lesson->section_id;
        
        // Ensure section is expanded
        if (!in_array($lesson->section_id, $this->expandedSections)) {
            $this->expandedSections[] = $lesson->section_id;
        }

        // Dispatch to parent CourseBuilder component
        $this->dispatch('lesson-selected', lessonId: $lessonId)
            ->to('course-management.course-builder');

        // Signal user activity
        $this->dispatch('user-activity')->to('course-management.course-builder');
    }

    public function deleteLesson($lessonId)
    {
        $this->markAsEditing(true);

        try {
            // If deleting active lesson, clear selection
            if ($this->activeLessonId == $lessonId) {
                $this->activeLessonId = null;
                $this->activeSectionId = null;
                $this->dispatch('lesson-deselected')->to('course-management.course-builder');
            }
            
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