<?php

namespace App\Livewire\Component\CourseManagement\CourseBuilder;

use App\Models\Course;
use Livewire\Attributes\On;
use Livewire\Component;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Storage;

class Toolbar extends Component
{
    public Course $course;
    public $courseStats = [];

    public function mount(Course $course)
    {
        $this->course = $course;
        $this->calculateStats();
    }

    #[On('course-updated')]
    public function refreshCourse()
    {
        $this->course->refresh();
        $this->calculateStats();
    }

    public function togglePublished()
    {
        try {
            $this->course->update([
                'is_published' => !$this->course->is_published
            ]);

            $status = $this->course->is_published ? 'published' : 'unpublished';
            $this->dispatch('notify', type: 'success', message: "Course {$status} successfully!");
            $this->dispatch('course-updated');
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: "Failed to update course status: " . $e->getMessage());
        }
    }

    public function saveContent()
    {
        try {
            // Dispatch an event that will be handled by parent components
            $this->dispatch('save-content-requested');
            $this->dispatch('notify', type: 'success', message: "Content saved successfully!");
        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: "Failed to save content: " . $e->getMessage());
        }
    }


    // public function exportCourseOutline()
    // {
    //     $outline = [
    //         'course' => [
    //             'title' => $this->course->title,
    //             'description' => $this->course->description,
    //             'created_at' => $this->course->created_at->toISOString(),
    //             'sections' => []
    //         ]
    //     ];

    //     foreach ($this->course->sections as $section) {
    //         $sectionData = [
    //             'title' => $section->title,
    //             'description' => $section->description,
    //             'order' => $section->order,
    //             'lessons' => []
    //         ];

    //         foreach ($section->lessons as $lesson) {
    //             $sectionData['lessons'][] = [
    //                 'title' => $lesson->title,
    //                 'content_type' => $lesson->content_type,
    //                 'duration_minutes' => $lesson->duration_minutes,
    //                 'order' => $lesson->order
    //             ];
    //         }

    //         $outline['course']['sections'][] = $sectionData;
    //     }

    //     $filename = str_slug($this->course->title) . '-outline-' . now()->format('Y-m-d') . '.json';

    //     return response()->json($outline)
    //         ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    // }

    private function calculateStats()
    {
        $totalLessons = $this->course->sections->sum(function ($section) {
            return $section->lessons->count();
        });

        $totalDuration = $this->course->sections->sum(function ($section) {
            return $section->lessons->sum('duration_minutes');
        });

        $publishedLessons = $this->course->sections->sum(function ($section) {
            return $section->lessons->where('content', '!=', null)->count();
        });

        $this->courseStats = [
            'total_sections' => $this->course->sections->count(),
            'total_lessons' => $totalLessons,
            'total_duration' => $totalDuration,
            'published_lessons' => $publishedLessons,
            'completion_percentage' => $totalLessons > 0 ? round(($publishedLessons / $totalLessons) * 100) : 0,
        ];
    }

  
    public function previewCourse()
    {
        try {
            if (!$this->course->is_published) {
                $this->dispatch('notify',
                    message: "Please publish the course before previewing.",
                    type: 'warning'
                );
                return;
            }
    
            return redirect()->route('course.preview', $this->course);
    
        } catch (\Exception $e) {
            $this->dispatch('notify',
                message: "Failed to preview course: " . $e->getMessage(),
                type: 'error'
            );
        }
    }

    public function exportCourseOutline()
    {
        $outline = [
            'course' => [
                'title' => $this->course->title,
                'description' => $this->course->description,
                'created_at' => $this->course->created_at->toISOString(),
                'sections' => []
            ]
        ];

            foreach ($this->course->sections as $section) {
                $sectionData = [
                    'title' => $section->title,
                    'description' => $section->description,
                    'order' => $section->order,
                    'lessons' => []
                ];

                foreach ($section->lessons as $lesson) {
                    $sectionData['lessons'][] = [
                        'title' => $lesson->title,
                        'content_type' => $lesson->content_type,
                        'duration_minutes' => $lesson->duration_minutes,
                        'order' => $lesson->order
                    ];
                }

                $outline['course']['sections'][] = $sectionData;
            }

            $filename = "{$this->course->slug}-outline-" . now()->format('Y-m-d') . '.json';
            $content = json_encode($outline, JSON_PRETTY_PRINT);

            return response()->streamDownload(
                function () use ($content) {
                    echo $content;
                },
                $filename,
                [
                    'Content-Type' => 'application/json',
                ]
            );



        }
    

    public function openCourseSettings()
    {
        // Dispatch an event that will be handled by the parent component
        $this->dispatch('open-course-settings', courseId: $this->course->id);
    }
    public function render()
    {
        return view('livewire.component.course-management.course-builder.toolbar');
    }
}