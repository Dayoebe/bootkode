<?php

namespace App\Livewire\Component\CourseManagement\CourseBuilder;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\On;

class MediaModals extends Component
{
    use WithFileUploads;

    // Modal states
    public $showImageModal = false;
    public $showVideoModal = false;
    public $showFileModal = false;
    public $showAudioModal = false;
    
    // Upload fields
    public $mediaFile;
    public $mediaCaption = '';
    public $videoUrl = '';
    public $videoTitle = '';
    public $fileDescription = '';
    public $audioTitle = '';

    protected $rules = [
        'mediaFile' => 'nullable|file|max:102400', // 100MB max
        'videoUrl' => 'nullable|url',
        'mediaCaption' => 'nullable|string|max:500',
        'videoTitle' => 'nullable|string|max:255',
        'fileDescription' => 'nullable|string|max:500',
        'audioTitle' => 'nullable|string|max:255',
    ];

    #[On('show-image-modal')]
    public function showImageModal()
    {
        $this->showImageModal = true;
        $this->resetFields();
    }

    #[On('show-video-modal')]
    public function showVideoModal()
    {
        $this->showVideoModal = true;
        $this->resetFields();
    }

    #[On('show-file-modal')]
    public function showFileModal()
    {
        $this->showFileModal = true;
        $this->resetFields();
    }

    #[On('show-audio-modal')]
    public function showAudioModal()
    {
        $this->showAudioModal = true;
        $this->resetFields();
    }

    #[On('close-modals')]
    public function closeModals()
    {
        $this->reset([
            'showImageModal', 'showVideoModal', 'showFileModal', 'showAudioModal'
        ]);
        $this->resetFields();
    }

    private function resetFields()
    {
        $this->reset([
            'mediaFile', 'mediaCaption', 'videoUrl', 'videoTitle', 
            'fileDescription', 'audioTitle'
        ]);
    }

    public function addImage()
    {
        $this->validate([
            'mediaFile' => 'required|image|max:10240', // 10MB max for images
        ]);

        $filePath = $this->mediaFile->store('lessons/images', 'public');
        
        $block = [
            'id' => uniqid(),
            'type' => 'image',
            'file_path' => $filePath,
            'file_name' => $this->mediaFile->getClientOriginalName(),
            'caption' => $this->mediaCaption,
            'file_size' => $this->mediaFile->getSize(),
            'created_at' => now()->toISOString()
        ];

        $this->dispatch('media-added', $block);
        $this->closeModals();
        $this->dispatch('notify', 'Image added successfully!', 'success');
    }

    public function addVideo()
    {
        if ($this->videoUrl) {
            $this->validate(['videoUrl' => 'required|url']);
            
            // Convert YouTube URLs to embed format
            $embedUrl = $this->convertToEmbedUrl($this->videoUrl);
            
            $block = [
                'id' => uniqid(),
                'type' => 'video',
                'video_url' => $embedUrl,
                'title' => $this->videoTitle ?: 'Video',
                'created_at' => now()->toISOString()
            ];
        } else {
            $this->validate([
                'mediaFile' => 'required|mimes:mp4,avi,mov,wmv,webm|max:102400', // 100MB max for videos
            ]);

            $filePath = $this->mediaFile->store('lessons/videos', 'public');
            
            $block = [
                'id' => uniqid(),
                'type' => 'video',
                'file_path' => $filePath,
                'file_name' => $this->mediaFile->getClientOriginalName(),
                'file_size' => $this->mediaFile->getSize(),
                'title' => $this->videoTitle ?: $this->mediaFile->getClientOriginalName(),
                'created_at' => now()->toISOString()
            ];
        }

        $this->dispatch('media-added', $block);
        $this->closeModals();
        $this->dispatch('notify', 'Video added successfully!', 'success');
    }

    public function addFile()
    {
        $this->validate([
            'mediaFile' => 'required|file|max:51200', // 50MB max for files
        ]);

        $filePath = $this->mediaFile->store('lessons/files', 'public');
        
        $block = [
            'id' => uniqid(),
            'type' => 'file',
            'file_path' => $filePath,
            'file_name' => $this->mediaFile->getClientOriginalName(),
            'description' => $this->fileDescription,
            'file_size' => $this->mediaFile->getSize(),
            'mime_type' => $this->mediaFile->getMimeType(),
            'created_at' => now()->toISOString()
        ];

        $this->dispatch('media-added', $block);
        $this->closeModals();
        $this->dispatch('notify', 'File added successfully!', 'success');
    }

    public function addAudio()
    {
        $this->validate([
            'mediaFile' => 'required|file|mimes:mp3,wav,aac,ogg|max:51200', // 50MB max for audio
        ]);

        $filePath = $this->mediaFile->store('lessons/audio', 'public');
        
        $block = [
            'id' => uniqid(),
            'type' => 'audio',
            'file_path' => $filePath,
            'file_name' => $this->mediaFile->getClientOriginalName(),
            'title' => $this->audioTitle ?: $this->mediaFile->getClientOriginalName(),
            'file_size' => $this->mediaFile->getSize(),
            'created_at' => now()->toISOString()
        ];

        $this->dispatch('media-added', $block);
        $this->closeModals();
        $this->dispatch('notify', 'Audio added successfully!', 'success');
    }

    private function convertToEmbedUrl($url)
    {
        // Convert YouTube watch URLs to embed URLs
        if (preg_match('/youtube\.com\/watch\?v=([^&]+)/', $url, $matches)) {
            return 'https://www.youtube.com/embed/' . $matches[1];
        }
        
        // Convert YouTube shortened URLs
        if (preg_match('/youtu\.be\/([^?]+)/', $url, $matches)) {
            return 'https://www.youtube.com/embed/' . $matches[1];
        }
        
        // Convert Vimeo URLs
        if (preg_match('/vimeo\.com\/(\d+)/', $url, $matches)) {
            return 'https://player.vimeo.com/video/' . $matches[1];
        }
        
        // Return original URL if no conversion needed
        return $url;
    }

    public function render()
    {
        return view('livewire.component.course-management.course-builder.media-modals');
    }
}