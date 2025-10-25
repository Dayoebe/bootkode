<?php

namespace App\Livewire\Pages\Partials;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use App\Models\Content\PageMedia;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class MediaManager extends Component
{
    use WithFileUploads, WithPagination;

    // Media management properties
    // public $mediaFiles = [];
    public $mediaSearch = '';
    public $mediaFilter = '';
    public $selectedMedia = [];
    public $uploadingFiles = [];
    public $showMediaUpload = false;
    public $viewMode = 'grid'; // grid or list
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';

    // Upload properties
    public $bulkFiles = [];
    public $uploadProgress = [];
    public $maxFileSize = 10; // MB
    public $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'video/mp4', 'application/pdf'];

    // Media editing
    public $editingMedia = null;
    public $editingAltText = '';
    public $editingDescription = '';
    public $editingTags = '';

    // Bulk actions
    public $bulkAction = '';
    public $showBulkActions = false;

    // Statistics
    public $mediaStats = [
        'total_files' => 0,
        'total_size' => 0,
        'images_count' => 0,
        'videos_count' => 0,
        'documents_count' => 0,
        'unused_files' => 0,
    ];

    public function mount()
    {
        // $this->loadMediaFiles();
        $this->loadMediaStats();
    }

    public function loadMediaFiles()
    {
        $query = PageMedia::with(['uploader', 'pages']);

        if ($this->mediaSearch) {
            $query->search($this->mediaSearch);
        }

        if ($this->mediaFilter) {
            switch ($this->mediaFilter) {
                case 'unused':
                    $query->doesntHave('pages');
                    break;
                case 'recent':
                    $query->where('created_at', '>=', now()->subDays(7));
                    break;
                case 'large':
                    $query->where('file_size', '>', 1024 * 1024); // > 1MB
                    break;
                case 'unoptimized':
                    $query->where('is_optimized', false)->where('media_type', 'image');
                    break;
                default:
                    $query->where('media_type', $this->mediaFilter);
            }
        }

        $this->mediaFiles = $query
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(24);
    }

    public function loadMediaStats()
    {
        $this->mediaStats = [
            'total_files' => PageMedia::count(),
            'total_size' => PageMedia::sum('file_size'),
            'images_count' => PageMedia::where('media_type', 'image')->count(),
            'videos_count' => PageMedia::where('media_type', 'video')->count(),
            'documents_count' => PageMedia::where('media_type', 'document')->count(),
            'unused_files' => PageMedia::doesntHave('pages')->count(),
        ];
    }

    public function updatedMediaSearch()
    {
        $this->resetPage();
        $this->loadMediaFiles();
    }

    public function updatedMediaFilter()
    {
        $this->resetPage();
        $this->loadMediaFiles();
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
        $this->loadMediaFiles();
    }

    public function toggleViewMode()
    {
        $this->viewMode = $this->viewMode === 'grid' ? 'list' : 'grid';
    }

    public function uploadFiles()
    {
        $this->validate([
            'bulkFiles.*' => [
                'required',
                'file',
                'max:' . ($this->maxFileSize * 1024), // Convert MB to KB
                function ($attribute, $value, $fail) {
                    if (!in_array($value->getMimeType(), $this->allowedTypes)) {
                        $fail('The file type is not allowed.');
                    }
                },
            ],
        ]);

        try {
            $uploadedCount = 0;
            
            foreach ($this->bulkFiles as $file) {
                $this->uploadProgress[$file->getClientOriginalName()] = 0;
                
                // Upload the file
                $media = PageMedia::uploadFile($file, 'pages/media', true);
                
                // Update progress
                $this->uploadProgress[$file->getClientOriginalName()] = 100;
                $uploadedCount++;
            }

            // Reset upload state
            $this->bulkFiles = [];
            $this->uploadProgress = [];
            $this->showMediaUpload = false;

            // Refresh data
            $this->loadMediaFiles();
            $this->loadMediaStats();

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => "{$uploadedCount} files uploaded successfully!"
            ]);

        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Upload failed: ' . $e->getMessage()
            ]);
        }
    }

    public function getMediaFilesQuery()
    {
        $query = PageMedia::with(['uploader', 'pages']);

        if ($this->mediaSearch) {
            $query->search($this->mediaSearch);
        }

        if ($this->mediaFilter) {
            switch ($this->mediaFilter) {
                case 'unused':
                    $query->doesntHave('pages');
                    break;
                case 'recent':
                    $query->where('created_at', '>=', now()->subDays(7));
                    break;
                case 'large':
                    $query->where('file_size', '>', 1024 * 1024); // > 1MB
                    break;
                case 'unoptimized':
                    $query->where('is_optimized', false)->where('media_type', 'image');
                    break;
                default:
                    $query->where('media_type', $this->mediaFilter);
            }
        }

        return $query->orderBy($this->sortBy, $this->sortDirection);
    }
    public function deleteMedia($mediaId)
    {
        try {
            $media = PageMedia::findOrFail($mediaId);
            
            // Check if media is being used
            if ($media->pages()->count() > 0) {
                $this->dispatch('notify', [
                    'type' => 'warning',
                    'message' => 'Cannot delete media that is being used in pages'
                ]);
                return;
            }

            $media->delete();
            $this->loadMediaStats();

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Media deleted successfully'
            ]);

        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Failed to delete media: ' . $e->getMessage()
            ]);
        }
    }

    public function editMedia($mediaId)
    {
        $this->editingMedia = PageMedia::findOrFail($mediaId);
        $this->editingAltText = $this->editingMedia->alt_text ?? '';
        $this->editingDescription = $this->editingMedia->description ?? '';
        $this->editingTags = implode(', ', $this->editingMedia->tags ?? []);
    }

    public function updateMedia()
    {
        $this->validate([
            'editingAltText' => 'nullable|string|max:255',
            'editingDescription' => 'nullable|string|max:500',
            'editingTags' => 'nullable|string',
        ]);

        try {
            $tags = array_filter(array_map('trim', explode(',', $this->editingTags)));

            $this->editingMedia->update([
                'alt_text' => $this->editingAltText,
                'description' => $this->editingDescription,
                'tags' => $tags,
            ]);

            $this->editingMedia = null;
            $this->loadMediaFiles();

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Media updated successfully'
            ]);

        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Failed to update media: ' . $e->getMessage()
            ]);
        }
    }

    public function cancelEdit()
    {
        $this->editingMedia = null;
        $this->editingAltText = '';
        $this->editingDescription = '';
        $this->editingTags = '';
    }

    public function toggleMediaSelection($mediaId)
    {
        if (in_array($mediaId, $this->selectedMedia)) {
            $this->selectedMedia = array_filter($this->selectedMedia, fn($id) => $id != $mediaId);
        } else {
            $this->selectedMedia[] = $mediaId;
        }

        $this->showBulkActions = count($this->selectedMedia) > 0;
    }

    public function selectAllMedia()
    {
        $mediaFiles = $this->getMediaFilesQuery()->paginate(24);
        $this->selectedMedia = $mediaFiles->pluck('id')->toArray();
        $this->showBulkActions = true;
    }

    public function deselectAllMedia()
    {
        $this->selectedMedia = [];
        $this->showBulkActions = false;
        $this->bulkAction = '';
    }

    public function executeBulkAction()
    {
        if (empty($this->selectedMedia) || empty($this->bulkAction)) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Please select media files and an action'
            ]);
            return;
        }

        try {
            $count = count($this->selectedMedia);

            switch ($this->bulkAction) {
                case 'delete':
                    $this->bulkDelete();
                    break;
                case 'optimize':
                    $this->bulkOptimize();
                    break;
                case 'add_tags':
                    $this->bulkAddTags();
                    break;
                default:
                    throw new \Exception('Invalid bulk action');
            }

            $this->deselectAllMedia();
            $this->loadMediaFiles();
            $this->loadMediaStats();

        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    private function bulkDelete()
    {
        $mediaItems = PageMedia::whereIn('id', $this->selectedMedia)->get();
        $deletedCount = 0;

        foreach ($mediaItems as $media) {
            if ($media->pages()->count() === 0) {
                $media->delete();
                $deletedCount++;
            }
        }

        $skipped = count($this->selectedMedia) - $deletedCount;
        $message = "{$deletedCount} files deleted successfully";
        if ($skipped > 0) {
            $message .= " ({$skipped} files skipped - in use)";
        }

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => $message
        ]);
    }

    private function bulkOptimize()
    {
        $mediaItems = PageMedia::whereIn('id', $this->selectedMedia)
            ->where('media_type', 'image')
            ->where('is_optimized', false)
            ->get();

        $optimizedCount = 0;

        foreach ($mediaItems as $media) {
            if ($media->optimize()) {
                $optimizedCount++;
            }
        }

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => "{$optimizedCount} images optimized successfully"
        ]);
    }

    private function bulkAddTags()
    {
        // This would open a modal to add tags to selected media
        $this->dispatch('open-bulk-tag-modal');
    }

    public function optimizeAllImages()
    {
        try {
            $unoptimizedImages = PageMedia::where('media_type', 'image')
                ->where('is_optimized', false)
                ->get();

            $optimizedCount = 0;
            foreach ($unoptimizedImages as $media) {
                if ($media->optimize()) {
                    $optimizedCount++;
                }
            }

            $this->loadMediaFiles();
            $this->loadMediaStats();

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => "{$optimizedCount} images optimized successfully"
            ]);

        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Optimization failed: ' . $e->getMessage()
            ]);
        }
    }

    public function cleanupUnusedMedia()
    {
        try {
            $unusedMedia = PageMedia::doesntHave('pages')->get();
            $deletedCount = 0;

            foreach ($unusedMedia as $media) {
                $media->delete();
                $deletedCount++;
            }

            $this->loadMediaFiles();
            $this->loadMediaStats();

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => "{$deletedCount} unused files deleted successfully"
            ]);

        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Cleanup failed: ' . $e->getMessage()
            ]);
        }
    }

    public function getFormattedSize($bytes)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, 2) . ' ' . $units[$pow];
    }

    public function render()
    {
        return view('livewire.pages.partials.media-manager', [
            'mediaFiles' => $this->getMediaFilesQuery()->paginate(24)
        ]);
    }
}