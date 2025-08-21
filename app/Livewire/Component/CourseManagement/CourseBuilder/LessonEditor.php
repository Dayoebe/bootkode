<?php

namespace App\Livewire\Component\CourseManagement\CourseBuilder;

use App\Models\Lesson;
use App\Models\Course;
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
    public $is_premium = false;
    public $price = 0;
    public $scheduled_publish_at;
    public $completion_time_type = 'reading';
    public $difficulty_level = 'beginner';

    // File uploads
    public $imageUpload;
    public $audioUpload;
    public $documentUpload;
    public $videoUpload;

    // Multiple file arrays
    public $images = [];
    public $documents = [];
    public $audios = [];
    public $videos = [];
    public $external_links = [];

    // External links form data
    public $newLinkTitle = '';
    public $newLinkUrl = '';

    protected $rules = [
        'title' => 'required|string|max:255',
        'slug' => 'required|string|max:255',
        'description' => 'nullable|string|max:1000',
        'content' => 'nullable|string',
        'video_url' => 'nullable|url|regex:/^(https?:\/\/)?(www\.)?(youtube\.com|youtu\.be)\/.+$/',
        'duration_minutes' => 'nullable|integer|min:1|max:600',
        'is_free' => 'boolean',
        'is_premium' => 'boolean',
        'price' => 'nullable|numeric|min:0',
        'scheduled_publish_at' => 'nullable|date|after:now',
        'completion_time_type' => 'in:reading,watching,practice,total',
        'difficulty_level' => 'in:beginner,intermediate,advanced,expert',
        'imageUpload.*' => 'nullable|image|max:5120',
        'audioUpload' => 'nullable|mimes:mp3,wav,m4a,aac|max:51200',
        'documentUpload' => 'nullable|mimes:pdf,doc,docx,txt,epub,ppt,pptx|max:51200',
        'videoUpload' => 'nullable|mimes:mp4,avi,mov,wmv|max:512000',
        'newLinkTitle' => 'required_with:newLinkUrl|string|max:255',
        'newLinkUrl' => 'required_with:newLinkTitle|url',
    ];

    public function mount($lessonId)
    {
        $this->lessonId = $lessonId;
        $this->loadLesson();
    }

    protected function loadLesson()
    {
        $this->lesson = Lesson::with(['section.course'])->findOrFail($this->lessonId);
        $this->title = $this->lesson->title ?? '';
        $this->slug = $this->lesson->slug ?? '';
        $this->description = (string) ($this->lesson->description ?? '');
        $this->content = (string) ($this->lesson->content ?? '');
        $this->video_url = (string) ($this->lesson->video_url ?? '');
        $this->duration_minutes = $this->lesson->duration_minutes;
        $this->is_free = $this->lesson->is_free ?? false;
        $this->is_premium = $this->lesson->is_premium ?? false;
        $this->price = $this->lesson->price ?? 0;
        $this->scheduled_publish_at = $this->lesson->scheduled_publish_at;
        $this->completion_time_type = $this->lesson->completion_time_type ?? 'reading';
        $this->difficulty_level = $this->lesson->difficulty_level ?? 'beginner';

        // Load existing files
        $this->loadExistingFiles();
    }

    protected function loadExistingFiles()
    {
        $this->images = json_decode($this->lesson->images ?? '[]', true);
        $this->documents = json_decode($this->lesson->documents ?? '[]', true);
        $this->audios = json_decode($this->lesson->audios ?? '[]', true);
        $this->videos = json_decode($this->lesson->videos ?? '[]', true);
        $this->external_links = json_decode($this->lesson->external_links ?? '[]', true);
    }

    public function generateSlug()
    {
        $courseTitle = $this->lesson->section->course->title ?? '';
        $baseSlug = $courseTitle ? Str::slug($courseTitle . '-' . $this->title) : Str::slug($this->title);

        // Ensure uniqueness
        $slug = $baseSlug;
        $counter = 1;
        while (Lesson::where('slug', $slug)->where('id', '!=', $this->lessonId)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        $this->slug = $slug;
        $this->validateOnly('slug');
    }

    public function uploadImage()
    {
        $this->validate(['imageUpload' => 'required|image|max:5120']);

        $path = $this->imageUpload->store('lessons/images', 'public');
        $this->images[] = [
            'path' => $path,
            'name' => $this->imageUpload->getClientOriginalName(),
            'size' => $this->imageUpload->getSize(),
            'uploaded_at' => now()
        ];

        $this->imageUpload = null;
        $this->saveFiles();
    }

    public function uploadDocument()
    {
        $this->validate(['documentUpload' => 'required|mimes:pdf,doc,docx,txt,epub,ppt,pptx|max:51200']);

        $path = $this->documentUpload->store('lessons/documents', 'public');
        $this->documents[] = [
            'path' => $path,
            'name' => $this->documentUpload->getClientOriginalName(),
            'size' => $this->documentUpload->getSize(),
            'type' => $this->documentUpload->getClientOriginalExtension(),
            'uploaded_at' => now()
        ];

        $this->documentUpload = null;
        $this->saveFiles();
    }

    public function uploadAudio()
    {
        $this->validate(['audioUpload' => 'required|mimes:mp3,wav,m4a,aac|max:51200']);

        $path = $this->audioUpload->store('lessons/audios', 'public');
        $this->audios[] = [
            'path' => $path,
            'name' => $this->audioUpload->getClientOriginalName(),
            'size' => $this->audioUpload->getSize(),
            'duration' => 0, // You can implement duration detection if needed
            'uploaded_at' => now()
        ];

        $this->audioUpload = null;
        $this->saveFiles();
    }

    public function uploadVideo()
    {
        $this->validate(['videoUpload' => 'required|mimes:mp4,avi,mov,wmv|max:512000']);

        $path = $this->videoUpload->store('lessons/videos', 'public');
        $this->videos[] = [
            'path' => $path,
            'name' => $this->videoUpload->getClientOriginalName(),
            'size' => $this->videoUpload->getSize(),
            'uploaded_at' => now()
        ];

        $this->videoUpload = null;
        $this->saveFiles();
    }

    public function addExternalLink()
    {
        $this->validate([
            'newLinkTitle' => 'required|string|max:255',
            'newLinkUrl' => 'required|url'
        ]);

        $this->external_links[] = [
            'title' => $this->newLinkTitle,
            'url' => $this->newLinkUrl,
            'added_at' => now()
        ];

        $this->newLinkTitle = '';
        $this->newLinkUrl = '';
        $this->saveFiles();
    }

    public function removeFile($type, $index)
    {
        if (isset($this->{$type}[$index])) {
            $file = $this->{$type}[$index];

            // Delete physical file if it has a path
            if (isset($file['path']) && Storage::disk('public')->exists($file['path'])) {
                Storage::disk('public')->delete($file['path']);
            }

            // Remove from array
            array_splice($this->{$type}, $index, 1);
            $this->saveFiles();
        }
    }

    protected function saveFiles()
    {
        $this->lesson->update([
            'images' => json_encode($this->images),
            'documents' => json_encode($this->documents),
            'audios' => json_encode($this->audios),
            'videos' => json_encode($this->videos ?? []),
            'external_links' => json_encode($this->external_links)
        ]);
    }

    public function saveLesson()
    {
        // Custom validation for slug uniqueness
        $this->rules['slug'] = 'required|string|max:255|unique:lessons,slug,' . $this->lessonId;
        $this->validate();

        $this->lesson->update([
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'content' => $this->content,
            'video_url' => $this->video_url,
            'duration_minutes' => $this->duration_minutes,
            'is_free' => $this->is_free,
            'is_premium' => $this->is_premium,
            'price' => $this->price,
            'scheduled_publish_at' => $this->scheduled_publish_at,
            'completion_time_type' => $this->completion_time_type,
            'difficulty_level' => $this->difficulty_level,
            'images' => json_encode($this->images),
            'documents' => json_encode($this->documents),
            'audios' => json_encode($this->audios),
            'videos' => json_encode($this->videos ?? []),
            'external_links' => json_encode($this->external_links)
        ]);

        session()->flash('success', 'Lesson updated successfully!');
        $this->dispatch('lesson-updated')->to('component.course-management.course-builder');
    }

    public function render()
    {
        return view('livewire.component.course-management.course-builder.lesson-editor');
    }
}