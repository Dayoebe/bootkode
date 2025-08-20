<?php

namespace App\Livewire\Component\CourseManagement\CourseBuilder;

use App\Models\Lesson;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class LessonEditor extends Component
{
    use WithFileUploads;

    public $lessonId;
    public $lesson;
    public $title = '';
    public $slug = '';
    public $description = '';
    public $content = '';
    public $video_url = '';
    public $duration_minutes;
    public $is_free = false;
    public $imageUpload;
    public $audioUpload;
    public $fileUpload;

    protected $rules = [
        'title' => 'required|string|max:255',
        'slug' => 'required|string|max:255|unique:lessons,slug',
        'description' => 'nullable|string|max:1000',
        'content' => 'nullable|string',
        'video_url' => 'nullable|url|regex:/^(https?:\/\/)?(www\.)?(youtube\.com|youtu\.be)\/.+$/',
        'duration_minutes' => 'nullable|integer|min:1|max:600',
        'is_free' => 'boolean',
        'imageUpload' => 'nullable|image|max:5120',
        'audioUpload' => 'nullable|mimes:mp3,wav|max:10240',
        'fileUpload' => 'nullable|mimes:pdf,doc,docx,txt|max:10240',
    ];

    public function mount($lessonId)
    {
        $this->lessonId = $lessonId;
        $this->loadLesson();
    }

    protected function loadLesson()
    {
        $this->lesson = Lesson::with('section')->findOrFail($this->lessonId);
        $this->title = $this->lesson->title ?? '';
        $this->slug = $this->lesson->slug ?? '';
        $this->description = (string) ($this->lesson->description ?? '');
        $this->content = (string) ($this->lesson->content ?? '');
        $this->video_url = (string) ($this->lesson->video_url ?? '');
        $this->duration_minutes = $this->lesson->duration_minutes;
        $this->is_free = $this->lesson->is_free ?? false;
    }

    public function generateSlug()
    {
        $this->slug = Str::slug($this->title);
        $this->validateOnly('slug');
    }

    public function saveLesson()
    {
        $this->validate();

        $this->lesson->update([
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'content' => $this->content,
            'video_url' => $this->video_url,
            'duration_minutes' => $this->duration_minutes,
            'is_free' => $this->is_free,
        ]);

        $this->dispatch('lesson-updated')->to('component.course-management.course-builder');
    }

    public function render()
    {
        return view('livewire.component.course-management.course-builder.lesson-editor');
    }
}