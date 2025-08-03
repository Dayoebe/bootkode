<?php

namespace App\Livewire\Component\CourseManagement\CourseBuilder;

use App\Models\Course;
use App\Models\CourseSection;
use App\Models\CourseLesson;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Component;

class CourseOutline extends Component
{
    public Course $course;
    public $activeLessonId;
    public $searchTerm = '';
    public $filterType = 'all';
    public $selectedLessons = [];
    
    // Section management
    public $isAddingSection = false;
    public $newSectionTitle = '';
    public $newSectionDescription = '';
    public $editingSectionId = null;
    public $editingSectionTitle = '';
    public $editingSectionDescription = '';
    
    // Lesson management
    public $isAddingLessonToSectionId = null;
    public $newLessonTitle = '';
    public $newLessonDescription = '';
    public $newLessonContentType = 'text';
    public $newLessonDuration = 0;
    public $editingLessonId = null;
    public $editingLessonTitle = '';

    protected $rules = [
        'newSectionTitle' => 'required|string|max:255',
        'newSectionDescription' => 'nullable|string|max:1000',
        'newLessonTitle' => 'required|string|max:255',
        'newLessonDescription' => 'nullable|string|max:1000',
        'newLessonDuration' => 'nullable|integer|min:0|max:1440',
        'editingSectionTitle' => 'required|string|max:255',
        'editingSectionDescription' => 'nullable|string|max:1000',
        'editingLessonTitle' => 'required|string|max:255',
    ];

    public function mount(Course $course, $activeLessonId = null)
    {
        $this->course = $course;
        $this->activeLessonId = $activeLessonId;
    }

    #[On('lesson-selected')]
    public function updateActiveLessonId($lessonId)
    {
        $this->activeLessonId = $lessonId;
    }

    public function selectLesson($lessonId)
    {
        $this->dispatch('lesson-selected', $lessonId);
    }

    // Section Management
    public function showAddSectionForm()
    {
        $this->isAddingSection = true;
        $this->reset(['newSectionTitle', 'newSectionDescription']);
    }

    public function cancelAddSection()
    {
        $this->isAddingSection = false;
        $this->reset(['newSectionTitle', 'newSectionDescription']);
    }

    public function addSection()
    {
        $this->validate([
            'newSectionTitle' => 'required|string|max:255',
            'newSectionDescription' => 'nullable|string|max:1000'
        ]);
        
        $this->course->sections()->create([
            'title' => $this->newSectionTitle,
            'description' => $this->newSectionDescription,
            'order' => $this->course->sections()->count(),
        ]);
    
        $this->reset(['newSectionTitle', 'newSectionDescription', 'isAddingSection']);
        $this->course->load('sections.lessons');
        $this->dispatch('course-updated');
        $this->dispatch('notify', 'New section added successfully!', 'success');
    }

    public function editSection($sectionId)
    {
        $section = CourseSection::find($sectionId);
        $this->editingSectionId = $sectionId;
        $this->editingSectionTitle = $section->title;
        $this->editingSectionDescription = $section->description;
    }

    public function updateSection()
    {
        $this->validate([
            'editingSectionTitle' => 'required|string|max:255',
            'editingSectionDescription' => 'nullable|string|max:1000'
        ]);
        
        $section = CourseSection::find($this->editingSectionId);
        $section->update([
            'title' => $this->editingSectionTitle,
            'description' => $this->editingSectionDescription
        ]);
        
        $this->reset(['editingSectionId', 'editingSectionTitle', 'editingSectionDescription']);
        $this->course->load('sections.lessons');
        $this->dispatch('course-updated');
        $this->dispatch('notify', 'Section updated successfully!', 'success');
    }

    public function cancelEditSection()
    {
        $this->reset(['editingSectionId', 'editingSectionTitle', 'editingSectionDescription']);
    }

    public function deleteSection($sectionId)
    {
        $section = CourseSection::findOrFail($sectionId);

        // Delete associated files and lessons
        foreach ($section->lessons as $lesson) {
            $this->deleteAssociatedFiles($lesson);
        }

        $section->lessons()->delete();
        $section->delete();
        
        $this->course->load('sections.lessons');
        $this->dispatch('course-updated');
        $this->dispatch('notify', 'Section and all lessons deleted successfully!', 'success');
    }

    public function duplicateSection($sectionId)
    {
        $section = CourseSection::with('lessons')->findOrFail($sectionId);
        
        $duplicatedSection = $section->replicate();
        $duplicatedSection->title = $section->title . ' (Copy)';
        $duplicatedSection->order = $this->course->sections()->count();
        $duplicatedSection->save();

        // Duplicate all lessons in the section
        foreach ($section->lessons as $lesson) {
            $duplicatedLesson = $lesson->replicate();
            $duplicatedLesson->course_section_id = $duplicatedSection->id;
            $duplicatedLesson->save();
        }

        $this->course->load('sections.lessons');
        $this->dispatch('course-updated');
        $this->dispatch('notify', 'Section duplicated successfully!', 'success');
    }

    // Lesson Management
    public function showAddLessonForm($sectionId)
    {
        $this->reset(['newLessonTitle', 'newLessonDescription', 'newLessonContentType', 'newLessonDuration']);
        $this->isAddingLessonToSectionId = $sectionId;
    }

    public function cancelAddLesson()
    {
        $this->reset(['isAddingLessonToSectionId', 'newLessonTitle', 'newLessonDescription', 'newLessonContentType', 'newLessonDuration']);
    }

    public function addLesson()
    {
        $this->validate([
            'newLessonTitle' => 'required|string|max:255',
            'newLessonDescription' => 'nullable|string|max:1000',
            'newLessonDuration' => 'nullable|integer|min:0|max:1440'
        ]);
        
        $section = CourseSection::find($this->isAddingLessonToSectionId);
        
        $newLesson = $section->lessons()->create([
            'title' => $this->newLessonTitle,
            'slug' => Str::slug($this->newLessonTitle),
            'content_type' => $this->newLessonContentType,
            'order' => $section->lessons()->count(),
            'duration_minutes' => $this->newLessonDuration,
            'content' => json_encode(['body' => '', 'blocks' => []])
        ]);
    
        $this->course->load('sections.lessons');
        $this->dispatch('course-updated');
        $this->dispatch('notify', 'New lesson added successfully!', 'success');
        $this->selectLesson($newLesson->id);
        $this->reset(['isAddingLessonToSectionId', 'newLessonTitle', 'newLessonDescription', 'newLessonContentType', 'newLessonDuration']);
    }

    public function editLesson($lessonId)
    {
        $lesson = CourseLesson::find($lessonId);
        $this->editingLessonId = $lessonId;
        $this->editingLessonTitle = $lesson->title;
    }

    public function updateLesson()
    {
        $this->validate([
            'editingLessonTitle' => 'required|string|max:255'
        ]);
        
        $lesson = CourseLesson::find($this->editingLessonId);
        $lesson->update([
            'title' => $this->editingLessonTitle,
            'slug' => Str::slug($this->editingLessonTitle),
        ]);
        
        $this->reset(['editingLessonId', 'editingLessonTitle']);
        $this->course->load('sections.lessons');
        $this->dispatch('course-updated');
        $this->dispatch('notify', 'Lesson updated successfully!', 'success');
    }

    public function cancelEditLesson()
    {
        $this->reset(['editingLessonId', 'editingLessonTitle']);
    }

    public function deleteLesson($lessonId)
    {
        $lesson = CourseLesson::findOrFail($lessonId);
        $this->deleteAssociatedFiles($lesson);
        $lesson->delete();
        
        $this->course->load('sections.lessons');
        $this->dispatch('course-updated');
        $this->dispatch('notify', 'Lesson deleted successfully!', 'success');
    }

    public function duplicateLesson($lessonId)
    {
        $lesson = CourseLesson::findOrFail($lessonId);
        
        $duplicatedLesson = $lesson->replicate();
        $duplicatedLesson->title = $lesson->title . ' (Copy)';
        $duplicatedLesson->slug = Str::slug($duplicatedLesson->title);
        $duplicatedLesson->order = $lesson->section->lessons()->count();
        $duplicatedLesson->save();

        $this->course->load('sections.lessons');
        $this->dispatch('course-updated');
        $this->dispatch('notify', 'Lesson duplicated successfully!', 'success');
    }

    // Bulk operations
    public function toggleLessonSelection($lessonId)
    {
        if (in_array($lessonId, $this->selectedLessons)) {
            $this->selectedLessons = array_diff($this->selectedLessons, [$lessonId]);
        } else {
            $this->selectedLessons[] = $lessonId;
        }
    }

    public function deselectAllLessons()
    {
        $this->selectedLessons = [];
    }

    public function bulkDeleteLessons()
    {
        foreach ($this->selectedLessons as $lessonId) {
            $lesson = CourseLesson::find($lessonId);
            if ($lesson) {
                $this->deleteAssociatedFiles($lesson);
                $lesson->delete();
            }
        }

        $count = count($this->selectedLessons);
        $this->selectedLessons = [];
        $this->course->load('sections.lessons');
        $this->dispatch('course-updated');
        $this->dispatch('notify', "{$count} lessons deleted successfully!", 'success');
    }

    private function deleteAssociatedFiles($lesson)
    {
        $content = is_string($lesson->content) ? json_decode($lesson->content, true) : $lesson->content;
        
        if (isset($content['blocks'])) {
            foreach ($content['blocks'] as $block) {
                if (isset($block['file_path'])) {
                    \Storage::disk('public')->delete($block['file_path']);
                }
            }
        }
    }

    public function getFilteredSectionsProperty()
    {
        $sections = $this->course->sections;

        if (!empty($this->searchTerm)) {
            $sections = $sections->filter(function($section) {
                $titleMatch = stripos($section->title, $this->searchTerm) !== false;
                $descriptionMatch = stripos($section->description, $this->searchTerm) !== false;
                $lessonMatch = $section->lessons->some(function($lesson) {
                    return stripos($lesson->title, $this->searchTerm) !== false;
                });
                
                return $titleMatch || $descriptionMatch || $lessonMatch;
            });
        }

        if ($this->filterType !== 'all') {
            $sections = $sections->filter(function($section) {
                return $section->lessons->some(function($lesson) {
                    return $lesson->content_type === $this->filterType;
                });
            });
        }

        return $sections;
    }

    public function getCourseStatsProperty()
    {
        $totalLessons = $this->course->sections->sum(function($section) {
            return $section->lessons->count();
        });

        $totalDuration = $this->course->sections->sum(function($section) {
            return $section->lessons->sum('duration_minutes');
        });

        $publishedLessons = $this->course->sections->sum(function($section) {
            return $section->lessons->where('content', '!=', null)->count();
        });

        return [
            'total_sections' => $this->course->sections->count(),
            'total_lessons' => $totalLessons,
            'total_duration' => $totalDuration,
            'published_lessons' => $publishedLessons,
            'completion_percentage' => $totalLessons > 0 ? round(($publishedLessons / $totalLessons) * 100) : 0,
        ];
    }

    public function render()
    {
        return view('livewire.component.course-management.course-builder.course-outline', [
            'filteredSections' => $this->filteredSections,
            'courseStats' => $this->courseStats
        ]);
    }
}