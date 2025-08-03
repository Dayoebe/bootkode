<?php

namespace App\Livewire\Component\CourseManagement\CourseBuilder;

use App\Models\CourseLesson;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class LessonEditor extends Component
{
    use WithFileUploads;

    public $lessonId;
    public $lesson;
    public $content = '';
    public $contentBlocks = [];
    public $isDirty = false;
    public $autoSaveMessage = '';

    public function mount($lessonId)
    {
        $this->lessonId = $lessonId;
        $this->lesson = CourseLesson::findOrFail($lessonId);

        $content = is_string($this->lesson->content)
            ? json_decode($this->lesson->content, true)
            : $this->lesson->content;

        $this->content = $content['body'] ?? '';
        $this->contentBlocks = $content['blocks'] ?? [];
    }

    #[On('save-content')]
    public function save()
    {
        $content = [
            'body' => $this->content,
            'blocks' => $this->contentBlocks,
            'last_updated' => now()->toISOString(),
            'word_count' => str_word_count(strip_tags($this->content))
        ];

        $this->lesson->update(['content' => $content]);

        $this->isDirty = false;
        $this->autoSaveMessage = 'Content saved successfully!';
        $this->dispatch('notify', 'Lesson content saved successfully!', 'success');
        $this->dispatch('content-saved');

        // Clear auto-save message after 3 seconds
        $this->clearAutoSaveMessage();
    }

    public function autoSave()
    {
        if ($this->isDirty) {
            $content = [
                'body' => $this->content,
                'blocks' => $this->contentBlocks,
                'last_updated' => now()->toISOString(),
                'word_count' => str_word_count(strip_tags($this->content))
            ];

            $this->lesson->update(['content' => $content]);
            $this->autoSaveMessage = 'Auto-saved at ' . now()->format('g:i A');
            $this->isDirty = false;

            // Clear auto-save message after 3 seconds
            $this->clearAutoSaveMessage();
        }
    }

    private function clearAutoSaveMessage()
    {
        $this->dispatch('clear-message', ['property' => 'autoSaveMessage', 'delay' => 3000]);
    }

    public function updatedContent()
    {
        $this->isDirty = true;
        // Auto-save after 3 seconds of inactivity
        $this->dispatch('schedule-auto-save');
    }

    public function updatedContentBlocks()
    {
        $this->isDirty = true;
    }

    // Media Management - these now dispatch to the global media modals
    public function showImageModal()
    {
        $this->dispatch('show-image-modal');
    }

    public function showVideoModal()
    {
        $this->dispatch('show-video-modal');
    }

    public function showFileModal()
    {
        $this->dispatch('show-file-modal');
    }

    public function showAudioModal()
    {
        $this->dispatch('show-audio-modal');
    }

    #[On('media-added')]
    public function addContentBlock($block)
    {
        $this->contentBlocks[] = $block;
        $this->isDirty = true;
        $this->dispatch('notify', ucfirst($block['type']) . ' added successfully!', 'success');
    }

    public function addCodeBlock()
    {
        $block = [
            'id' => uniqid(),
            'type' => 'code',
            'language' => 'javascript',
            'code' => '// Enter your code here...',
            'title' => 'Code Example',
            'created_at' => now()->toISOString()
        ];

        $this->contentBlocks[] = $block;
        $this->isDirty = true;
        $this->dispatch('notify', 'Code block added!', 'success');
    }

    public function addNoteBlock()
    {
        $block = [
            'id' => uniqid(),
            'type' => 'note',
            'note_type' => 'tip',
            'title' => 'Tip',
            'content' => 'Enter your tip or note here...',
            'created_at' => now()->toISOString()
        ];

        $this->contentBlocks[] = $block;
        $this->isDirty = true;
        $this->dispatch('notify', 'Note block added!', 'success');
    }

    public function removeContentBlock($blockId)
    {
        $blockIndex = collect($this->contentBlocks)->search(function ($block) use ($blockId) {
            return $block['id'] === $blockId;
        });

        if ($blockIndex !== false) {
            // Delete associated file if exists
            $block = $this->contentBlocks[$blockIndex];
            if (isset($block['file_path'])) {
                Storage::disk('public')->delete($block['file_path']);
            }

            array_splice($this->contentBlocks, $blockIndex, 1);
            $this->isDirty = true;
            $this->dispatch('notify', 'Content block removed!', 'success');
        }
    }

    public function updateContentBlock($blockId, $field, $value)
    {
        $blockIndex = collect($this->contentBlocks)->search(function ($block) use ($blockId) {
            return $block['id'] === $blockId;
        });

        if ($blockIndex !== false) {
            $this->contentBlocks[$blockIndex][$field] = $value;
            $this->isDirty = true;
        }
    }

    public function reorderContentBlocks($orderedIds)
    {
        $reorderedBlocks = [];
        foreach ($orderedIds as $id) {
            $block = collect($this->contentBlocks)->firstWhere('id', $id);
            if ($block) {
                $reorderedBlocks[] = $block;
            }
        }
        $this->contentBlocks = $reorderedBlocks;
        $this->isDirty = true;
    }

    public function previewLesson()
{
    try {
        // First, ensure we have a valid lesson ID
        if (!$this->lessonId) {
            $this->dispatch('notify',
                message: "No lesson selected for preview.",
                type: 'error'
            );
            return;
        }

        // Load the lesson with necessary relationships
        $lesson = CourseLesson::with([
            'section' => function($query) {
                $query->with(['course']);
            }
        ])->findOrFail($this->lessonId);

        // Verify the course structure
        if (!$lesson->section || !$lesson->section->course) {
            $this->dispatch('notify',
                message: "This lesson isn't properly assigned to a course section.",
                type: 'error'
            );
            return;
        }

        $course = $lesson->section->course;

        if (!$course->is_published) {
            $this->dispatch('notify',
                message: "Please publish the course before previewing lessons.",
                type: 'warning'
            );
            return;
        }

        // Redirect to course preview with lesson highlight
        return redirect()->route('course.preview', [
            'course' => $course,
            'highlight' => $lesson->id
        ]);

    } catch (\Exception $e) {
        $this->dispatch('notify',
            message: "Failed to preview lesson: " . $e->getMessage(),
            type: 'error'
        );
    }
}
    public function formatFileSize($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, 2) . ' ' . $units[$pow];
    }

    public function getNoteIcon($noteType)
    {
        return match ($noteType) {
            'tip' => 'lightbulb',
            'warning' => 'exclamation-triangle',
            'info' => 'info-circle',
            'success' => 'check-circle',
            default => 'lightbulb'
        };
    }

    public function render()
    {
        return view('livewire.component.course-management.course-builder.lesson-editor');
    }
}