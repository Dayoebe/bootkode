<?php

namespace App\Livewire\Content\Partial;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Document;
use App\Models\LearningMaterial;
use App\Models\VideoLibrary;

class ContentModeration extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedContentType = 'all';
    public $selectedStatus = 'flagged';
    public $sortBy = 'updated_at';
    public $sortDirection = 'desc';
    
    public $showModerationModal = false;
    public $selectedContent = null;
    public $contentType = '';
    public $moderationAction = '';
    public $moderationReason = '';

    protected $paginationTheme = 'tailwind';

    protected $rules = [
        'moderationReason' => 'required|string|max:500',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openModerationModal($contentId, $type, $action)
    {
        $this->contentType = $type;
        $this->moderationAction = $action;
        
        switch ($type) {
            case 'document':
                $this->selectedContent = Document::findOrFail($contentId);
                break;
            case 'material':
                $this->selectedContent = LearningMaterial::findOrFail($contentId);
                break;
            case 'video':
                $this->selectedContent = VideoLibrary::findOrFail($contentId);
                break;
        }
        
        $this->moderationReason = '';
        $this->showModerationModal = true;
    }

    public function closeModerationModal()
    {
        $this->showModerationModal = false;
        $this->selectedContent = null;
        $this->contentType = '';
        $this->moderationAction = '';
        $this->moderationReason = '';
    }

    public function submitModeration()
    {
        $this->validate();

        try {
            switch ($this->moderationAction) {
                case 'approve':
                    $this->approveContent();
                    $message = 'Content approved successfully!';
                    break;
                case 'reject':
                    $this->rejectContent();
                    $message = 'Content rejected successfully!';
                    break;
                case 'flag':
                    $this->flagContent();
                    $message = 'Content flagged successfully!';
                    break;
                case 'archive':
                    $this->archiveContent();
                    $message = 'Content archived successfully!';
                    break;
            }

            $this->closeModerationModal();
            session()->flash('message', $message);
            
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to moderate content: ' . $e->getMessage());
        }
    }

    private function approveContent()
    {
        switch ($this->contentType) {
            case 'document':
                $this->selectedContent->update(['status' => 'published']);
                break;
            case 'material':
            case 'video':
                $this->selectedContent->update(['status' => 'published']);
                break;
        }
    }

    private function rejectContent()
    {
        switch ($this->contentType) {
            case 'document':
                $this->selectedContent->update(['status' => 'draft']);
                break;
            case 'material':
            case 'video':
                $this->selectedContent->update(['status' => 'draft']);
                break;
        }
    }

    private function flagContent()
    {
        // You might want to create a separate flagged_content table
        // For now, we'll use status
        $this->selectedContent->update(['status' => 'flagged']);
    }

    private function archiveContent()
    {
        $this->selectedContent->update(['status' => 'archived']);
    }

    public function quickApprove($contentId, $type)
    {
        try {
            switch ($type) {
                case 'document':
                    Document::findOrFail($contentId)->update(['status' => 'published']);
                    break;
                case 'material':
                    LearningMaterial::findOrFail($contentId)->update(['status' => 'published']);
                    break;
                case 'video':
                    VideoLibrary::findOrFail($contentId)->update(['status' => 'published']);
                    break;
            }
            
            session()->flash('message', 'Content approved successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to approve content: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $content = collect();

        // Fetch documents
        if ($this->selectedContentType === 'all' || $this->selectedContentType === 'documents') {
            $documents = Document::query()
                ->when($this->search, function($query) {
                    $query->where('title', 'like', '%' . $this->search . '%');
                })
                ->when($this->selectedStatus !== 'all', function($query) {
                    $query->where('status', $this->selectedStatus);
                })
                ->with('creator')
                ->get()
                ->map(function($item) {
                    $item->content_type = 'document';
                    return $item;
                });
            $content = $content->merge($documents);
        }

        // Fetch materials
        if ($this->selectedContentType === 'all' || $this->selectedContentType === 'materials') {
            $materials = LearningMaterial::query()
                ->when($this->search, function($query) {
                    $query->where('title', 'like', '%' . $this->search . '%');
                })
                ->when($this->selectedStatus !== 'all', function($query) {
                    $query->where('status', $this->selectedStatus);
                })
                ->with('creator')
                ->get()
                ->map(function($item) {
                    $item->content_type = 'material';
                    return $item;
                });
            $content = $content->merge($materials);
        }

        // Fetch videos
        if ($this->selectedContentType === 'all' || $this->selectedContentType === 'videos') {
            $videos = VideoLibrary::query()
                ->when($this->search, function($query) {
                    $query->where('title', 'like', '%' . $this->search . '%');
                })
                ->when($this->selectedStatus !== 'all', function($query) {
                    $query->where('status', $this->selectedStatus);
                })
                ->with('uploader')
                ->get()
                ->map(function($item) {
                    $item->content_type = 'video';
                    return $item;
                });
            $content = $content->merge($videos);
        }

        // Sort the collection
        if ($this->sortDirection === 'desc') {
            $content = $content->sortByDesc($this->sortBy);
        } else {
            $content = $content->sortBy($this->sortBy);
        }

        $contentTypes = [
            'all' => 'All Content',
            'documents' => 'Documents',
            'materials' => 'Learning Materials',
            'videos' => 'Videos',
        ];

        $statuses = [
            'all' => 'All Statuses',
            'pending_review' => 'Pending Review',
            'flagged' => 'Flagged',
            'published' => 'Published',
            'draft' => 'Draft',
            'archived' => 'Archived',
        ];

        return view('livewire.content.partial.content-moderation', compact('content', 'contentTypes', 'statuses'));
    }
}