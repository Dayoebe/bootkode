<?php

namespace App\Livewire\Component\Common;

use App\Models\SavedResource;
use Livewire\Component;

class BookmarkButton extends Component
{
    public $resourceableType;
    public $resourceableId;
    public $courseId = null;
    public $size = 'md'; // sm, md, lg
    public $showText = false;
    public $isBookmarked = false;

    protected $listeners = ['bookmarkUpdated' => 'checkBookmarkStatus'];

    public function mount($resourceableType, $resourceableId, $courseId = null, $size = 'md', $showText = false)
    {
        $this->resourceableType = $resourceableType;
        $this->resourceableId = $resourceableId;
        $this->courseId = $courseId;
        $this->size = $size;
        $this->showText = $showText;
        
        $this->checkBookmarkStatus();
        $this->dispatch('bookmarkStatusChecked');
    }

    public function toggleBookmark()
    {
        if (!auth()->check()) {
            $this->dispatch('show-login-modal');
            return;
        }

        if ($this->isBookmarked) {
            auth()->user()->savedResources()
                ->where('resourceable_type', $this->resourceableType)
                ->where('resourceable_id', $this->resourceableId)
                ->delete();
                
            $this->isBookmarked = false;
            $this->dispatch('notify', type: 'success', message: 'Removed from saved resources');
        } else {
            auth()->user()->savedResources()->create([
                'resourceable_type' => $this->resourceableType,
                'resourceable_id' => $this->resourceableId,
                'course_id' => $this->courseId,
                'type' => $this->determineResourceType()
            ]);
            
            $this->isBookmarked = true;
            $this->dispatch('notify', type: 'success', message: 'Added to saved resources');
        }

        $this->dispatch('bookmarkUpdated');
    }

    protected function determineResourceType()
    {
        // Map resourceable types to simpler types
        return match($this->resourceableType) {
            'App\Models\Lesson' => 'lesson',
            'App\Models\Note' => 'note',
            default => strtolower(class_basename($this->resourceableType))
        };
    }

    public function checkBookmarkStatus()
    {
        $this->isBookmarked = auth()->check() && auth()->user()->savedResources()
            ->where('resourceable_type', $this->resourceableType)
            ->where('resourceable_id', $this->resourceableId)
            ->exists();
    }

    public function render()
    {
        return view('livewire.component.common.bookmark-button');
    }
}