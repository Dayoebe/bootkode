<?php

namespace App\Livewire\CourseManagement\CourseBuilder;

use App\Models\Lesson;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Mews\Purifier\Facades\Purifier;

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

    protected function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'slug' => "required|string|alpha_dash|max:255|unique:lessons,slug,{$this->lessonId}",
            'description' => 'nullable|string|max:1000',
            'content' => 'nullable|string',
            'video_url' => ['nullable', 'url', 'regex:/^(https?:\/\/)?(www\.)?(youtube\.com|youtu\.be)\/.+$/'],
            'duration_minutes' => 'nullable|integer|min:1|max:600',
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
    }

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
        $this->description = $this->lesson->description ?? '';
        $this->content = $this->lesson->getRawOriginal('content') ?? '';
        $this->video_url = $this->lesson->video_url ?? '';
        $this->duration_minutes = $this->lesson->duration_minutes;
        $this->scheduled_publish_at = $this->lesson->scheduled_publish_at;
        $this->completion_time_type = $this->lesson->completion_time_type ?? 'reading';
        $this->difficulty_level = $this->lesson->difficulty_level ?? 'beginner';

        $this->loadExistingFiles();
    }

    protected function loadExistingFiles()
    {
        $this->images = $this->lesson->getImagesArray();
        $this->documents = $this->lesson->getDocumentsArray();
        $this->audios = $this->lesson->getAudiosArray();
        $this->videos = $this->lesson->getVideosArray();
        $this->external_links = $this->lesson->getExternalLinksArray();
    }

    public function generateSlug()
    {
        if (empty($this->title)) {
            $this->addError('slug', 'Please enter a lesson title first');
            return;
        }

        $course = $this->lesson->section->course;
        
        // Create a short course prefix (max 30 chars, remove common words)
        $coursePrefix = $this->createCoursePrefix($course->title);
        
        // Combine course prefix with lesson title
        $baseSlug = Str::slug($coursePrefix . '-' . $this->title);
        
        // Limit total slug length to 100 characters for database/SEO
        $baseSlug = Str::limit($baseSlug, 100, '');
        
        // Check for uniqueness and add counter if needed
        $slug = $baseSlug;
        $counter = 1;
        
        while (Lesson::where('slug', $slug)->where('id', '!=', $this->lessonId)->exists()) {
            // Add counter before any truncation happened
            $slug = $baseSlug . '-' . $counter;
            $counter++;
            
            // Prevent infinite loops
            if ($counter > 100) {
                $slug = $baseSlug . '-' . time();
                break;
            }
        }

        $this->slug = $slug;
        $this->validateOnly('slug');
    }

    /**
     * Create a short, meaningful course prefix from course title
     * Examples:
     * "Introduction to Web Development" -> "intro-web-dev"
     * "Advanced JavaScript Programming" -> "advanced-js"
     * "Machine Learning Basics" -> "ml-basics"
     */
    private function createCoursePrefix($courseTitle)
    {
        // Common words to remove for brevity
        $stopWords = ['introduction', 'to', 'the', 'a', 'an', 'for', 'in', 'on', 'with', 'and', 'or'];
        
        // Convert to lowercase and split into words
        $words = explode(' ', Str::lower($courseTitle));
        
        // Remove stop words but keep first word if it's important
        $filtered = [];
        foreach ($words as $index => $word) {
            $word = trim($word);
            
            // Keep first word even if it's a stop word (but shorten it)
            if ($index === 0) {
                if (in_array($word, ['introduction', 'introductory'])) {
                    $filtered[] = 'intro';
                } else {
                    $filtered[] = $word;
                }
            } 
            // For other words, skip stop words
            elseif (!in_array($word, $stopWords)) {
                // Abbreviate common tech terms
                $word = $this->abbreviateTerm($word);
                $filtered[] = $word;
            }
        }
        
        // Limit to first 3 meaningful words
        $filtered = array_slice($filtered, 0, 3);
        
        // Join and create slug, limit to 30 chars
        $prefix = implode('-', $filtered);
        return Str::limit(Str::slug($prefix), 30, '');
    }

    /**
     * Abbreviate common technical terms for shorter slugs
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

    // Fixed autoSave - doesn't trigger re-render
    public function autoSave()
    {
        try {
            $this->validate([
                'title' => 'required|string|max:255',
                'content' => 'nullable|string',
            ]);

            $cleanContent = $this->content;
            
            if (!empty($cleanContent) && is_string($cleanContent)) {
                $cleanContent = Purifier::clean($cleanContent, [
                    'HTML.Allowed' => 'h1,h2,h3,h4,h5,h6,p,br,strong,em,u,s,ul,ol,li,a[href],img[src|alt],blockquote,pre,code,div[class],span[class]',
                    'CSS.AllowTricky' => true,
                    'AutoFormat.RemoveEmpty' => false,
                ]);
            }

            $this->lesson->update([
                'title' => $this->title,
                'content' => $cleanContent,
                'description' => $this->description,
            ]);

            // Don't dispatch events that cause re-render
            $this->dispatch('lesson-autosaved', ['lessonId' => $this->lessonId]);
            
            return true;
        } catch (\Exception $e) {
            \Log::error('Auto-save failed: ' . $e->getMessage());
            return false;
        }
    }

    public function quickSave()
    {
        $result = $this->autoSave();
        if ($result) {
            $this->dispatch('lesson-saved');
        }
        return $result;
    }

    // Rest of your methods remain the same...
    public function handleUpload($type, $fileProperty, $mimes, $maxSize, $storageFolder)
    {
        $this->validate([
            $fileProperty => "required|mimes:{$mimes}|max:{$maxSize}",
        ]);

        $file = $this->{$fileProperty};
        $path = $file->store("lessons/{$storageFolder}", 'public');
        $data = [
            'path' => $path,
            'name' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
            'uploaded_at' => now()->toDateTimeString(),
        ];

        if ($type === 'documents') {
            $data['type'] = $file->getClientOriginalExtension();
        }

        $this->{$type}[] = $data;
        $this->{$fileProperty} = null;
        $this->saveFiles();
    }

    public function uploadImage()
    {
        $this->handleUpload('images', 'imageUpload', 'jpg,png,jpeg,gif', 5120, 'images');
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
            'uploaded_at' => now()->toDateTimeString()
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
            'duration' => 0,
            'uploaded_at' => now()->toDateTimeString()
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
            'uploaded_at' => now()->toDateTimeString()
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
            'added_at' => now()->toDateTimeString()
        ];

        $this->newLinkTitle = '';
        $this->newLinkUrl = '';
        $this->saveFiles();
    }

    public function removeFile($type, $index)
    {
        if (isset($this->{$type}[$index])) {
            $file = $this->{$type}[$index];

            if (isset($file['path']) && Storage::disk('public')->exists($file['path'])) {
                Storage::disk('public')->delete($file['path']);
            }

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
        $this->validate();

        $cleanContent = $this->content;
        
        if (!empty($cleanContent) && is_string($cleanContent)) {
            $cleanContent = Purifier::clean($cleanContent, [
                'HTML.Allowed' => 'h1,h2,h3,h4,h5,h6,p,br,strong,em,u,s,ul,ol,li,a[href],img[src|alt],blockquote,pre,code,div[class],span[class]',
                'CSS.AllowTricky' => true,
                'AutoFormat.RemoveEmpty' => false,
            ]);
        }

        $this->lesson->update([
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'content' => $cleanContent,
            'video_url' => $this->video_url,
            'duration_minutes' => $this->duration_minutes,
            'scheduled_publish_at' => $this->scheduled_publish_at,
            'completion_time_type' => $this->completion_time_type,
            'difficulty_level' => $this->difficulty_level,
            'images' => json_encode($this->images),
            'documents' => json_encode($this->documents),
            'audios' => json_encode($this->audios),
            'videos' => json_encode($this->videos ?? []),
            'external_links' => json_encode($this->external_links),
        ]);

        session()->flash('success', 'Lesson updated successfully!');
        $this->dispatch('lesson-updated');
    }

    public function render()
    {
        return view('livewire.course-management.course-builder.lesson-editor');
    }
}