<?php

namespace App\Livewire\Pages\Partials;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Page;
use App\Models\User;
use Carbon\Carbon;

class PagesList extends Component
{
    use WithPagination;

    // Filters and Search
    public $search = '';
    public $statusFilter = '';
    public $templateFilter = '';
    public $authorFilter = '';
    public $dateFilter = '';
    public $sortBy = 'updated_at';
    public $sortDirection = 'desc';
    
    // Bulk actions
    public $selectedPages = [];
    public $bulkAction = '';
    public $showBulkActions = false;

    // Quick actions
    public $showQuickEdit = false;
    public $quickEditPage = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'templateFilter' => ['except' => ''],
        'sortBy' => ['except' => 'updated_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    public function mount()
    {
        //
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingTemplateFilter()
    {
        $this->resetPage();
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
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->statusFilter = '';
        $this->templateFilter = '';
        $this->authorFilter = '';
        $this->dateFilter = '';
        $this->sortBy = 'updated_at';
        $this->sortDirection = 'desc';
        $this->resetPage();

        $this->dispatch('notify', [
            'type' => 'info',
            'message' => 'Filters cleared'
        ]);
    }

    public function getPages()
    {
        return Page::with(['creator', 'updater', 'featuredMedia'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', "%{$this->search}%")
                      ->orWhere('content', 'like', "%{$this->search}%")
                      ->orWhere('slug', 'like', "%{$this->search}%")
                      ->orWhere('meta_description', 'like', "%{$this->search}%");
                });
            })
            ->when($this->statusFilter, function ($query) {
                if ($this->statusFilter === 'scheduled') {
                    $query->scheduled();
                } elseif ($this->statusFilter === 'expired') {
                    $query->expired();
                } else {
                    $query->where('status', $this->statusFilter);
                }
            })
            ->when($this->templateFilter, function ($query) {
                $query->where('template', $this->templateFilter);
            })
            ->when($this->authorFilter, function ($query) {
                $query->where('created_by', $this->authorFilter);
            })
            ->when($this->dateFilter, function ($query) {
                $startDate = match($this->dateFilter) {
                    'today' => now()->startOfDay(),
                    'week' => now()->startOfWeek(),
                    'month' => now()->startOfMonth(),
                    'year' => now()->startOfYear(),
                    default => null
                };
                if ($startDate) {
                    $query->where('created_at', '>=', $startDate);
                }
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(15);
    }

    // Page Actions
    public function viewPage($pageId)
    {
        $page = Page::find($pageId);
        if ($page) {
            return redirect()->route('page.show', $page->slug);
        }
    }

    public function editPage($pageId)
    {
        return redirect()->route('pages.create')->with('editPageId', $pageId);
    }

    public function duplicatePage($pageId)
    {
        try {
            $originalPage = Page::findOrFail($pageId);
            
            $duplicatedPage = $originalPage->replicate();
            $duplicatedPage->title = $originalPage->title . ' (Copy)';
            $duplicatedPage->slug = $this->generateUniqueSlug($duplicatedPage->title);
            $duplicatedPage->status = Page::STATUS_DRAFT;
            $duplicatedPage->published_at = null;
            $duplicatedPage->view_count = 0;
            $duplicatedPage->created_by = auth()->id();
            $duplicatedPage->save();

            // Copy media relationships
            foreach ($originalPage->media as $media) {
                $duplicatedPage->media()->attach($media->id, [
                    'context' => $media->pivot->context,
                    'sort_order' => $media->pivot->sort_order
                ]);
            }

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Page duplicated successfully'
            ]);

            $this->resetPage();
            
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Failed to duplicate page: ' . $e->getMessage()
            ]);
        }
    }

    public function togglePageStatus($pageId)
    {
        try {
            $page = Page::findOrFail($pageId);
            
            $newStatus = $page->status === Page::STATUS_PUBLISHED 
                ? Page::STATUS_DRAFT 
                : Page::STATUS_PUBLISHED;
            
            $page->update([
                'status' => $newStatus,
                'published_at' => $newStatus === Page::STATUS_PUBLISHED ? now() : null
            ]);
            
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => "Page status updated to " . ucfirst($newStatus)
            ]);

        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Failed to update page status'
            ]);
        }
    }

    public function deletePage($pageId)
    {
        try {
            if (!auth()->user()->can('delete', Page::class)) {
                throw new \Exception('You do not have permission to delete pages.');
            }

            $page = Page::findOrFail($pageId);
            $page->delete();
            
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Page deleted successfully'
            ]);

        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    // Quick Edit
    public function openQuickEdit($pageId)
    {
        $this->quickEditPage = Page::find($pageId);
        $this->showQuickEdit = true;
    }

    public function closeQuickEdit()
    {
        $this->showQuickEdit = false;
        $this->quickEditPage = null;
    }

    public function updateQuickEdit()
    {
        if (!$this->quickEditPage) return;

        try {
            $this->quickEditPage->save();
            $this->closeQuickEdit();
            
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Page updated successfully'
            ]);

        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Failed to update page'
            ]);
        }
    }

    // Bulk Actions
    public function togglePageSelection($pageId)
    {
        if (in_array($pageId, $this->selectedPages)) {
            $this->selectedPages = array_filter($this->selectedPages, fn($id) => $id != $pageId);
        } else {
            $this->selectedPages[] = $pageId;
        }

        $this->showBulkActions = count($this->selectedPages) > 0;
    }

    public function selectAllPages()
    {
        $this->selectedPages = $this->getPages()->pluck('id')->toArray();
        $this->showBulkActions = true;
    }

    public function deselectAllPages()
    {
        $this->selectedPages = [];
        $this->showBulkActions = false;
        $this->bulkAction = '';
    }

    public function executeBulkAction()
    {
        if (empty($this->selectedPages) || empty($this->bulkAction)) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Please select pages and an action'
            ]);
            return;
        }

        $pages = Page::whereIn('id', $this->selectedPages);
        $count = count($this->selectedPages);

        try {
            switch ($this->bulkAction) {
                case 'publish':
                    $pages->update([
                        'status' => Page::STATUS_PUBLISHED,
                        'published_at' => now()
                    ]);
                    $message = "{$count} pages published successfully";
                    break;

                case 'draft':
                    $pages->update(['status' => Page::STATUS_DRAFT]);
                    $message = "{$count} pages moved to draft";
                    break;

                case 'archive':
                    $pages->update(['status' => Page::STATUS_ARCHIVED]);
                    $message = "{$count} pages archived";
                    break;

                case 'delete':
                    if (!auth()->user()->can('delete', Page::class)) {
                        throw new \Exception('You do not have permission to delete pages.');
                    }
                    $pages->delete();
                    $message = "{$count} pages deleted successfully";
                    break;

                default:
                    throw new \Exception('Invalid bulk action');
            }

            $this->deselectAllPages();
            $this->resetPage();
            
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => $message
            ]);

        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    // Helper methods
    private function generateUniqueSlug($title, $excludeId = null)
    {
        $baseSlug = \Illuminate\Support\Str::slug($title);
        $slug = $baseSlug;
        $counter = 1;

        while ($this->slugExists($slug, $excludeId)) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    private function slugExists($slug, $excludeId = null)
    {
        $query = Page::where('slug', $slug);
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        return $query->exists();
    }

    public function getAuthors()
    {
        return User::whereHas('createdPages')
            ->select('id', 'name')
            ->orderBy('name')
            ->get();
    }

    public function render()
    {
        $pages = $this->getPages();
        
        return view('livewire.pages.partials.pages-list', [
            'pages' => $pages,
            'authors' => $this->getAuthors(),
            'statusOptions' => [
                '' => 'All Status',
                'published' => 'Published',
                'draft' => 'Draft',
                'archived' => 'Archived',
                'scheduled' => 'Scheduled',
                'expired' => 'Expired',
            ],
            'templateOptions' => [
                '' => 'All Templates',
                'default' => 'Default Template',
                'landing' => 'Landing Page',
                'blog' => 'Blog Style',
                'full-width' => 'Full Width',
                'minimal' => 'Minimal',
            ],
            'bulkActions' => [
                '' => 'Bulk Actions...',
                'publish' => 'Publish Selected',
                'draft' => 'Move to Draft',
                'archive' => 'Archive Selected',
                'delete' => 'Delete Selected',
            ],
            'dateFilters' => [
                '' => 'All Dates',
                'today' => 'Today',
                'week' => 'This Week',
                'month' => 'This Month',
                'year' => 'This Year',
            ],
        ]);
    }
}