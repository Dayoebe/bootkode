<?php

namespace App\Livewire\Pages;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Page;
use App\Models\PageMedia;
use App\Models\User;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;

#[Layout('layouts.dashboard', ['title' => 'Page Management', 'description' => 'Create and manage SEO-friendly pages', 'icon' => 'fas fa-file-alt', 'active' => 'pages'])]

class PageManager extends Component
{
    use WithPagination, WithFileUploads;

    // Tab Management
    public $activeTab = 'all-pages';
    
    // Page List Properties
    public $search = '';
    public $statusFilter = '';
    public $templateFilter = '';
    public $sortBy = 'updated_at';
    public $sortDirection = 'desc';
    public $selectedPages = [];
    public $bulkAction = '';
    
    // Statistics
    public $stats = [
        'total_pages' => 0,
        'published_pages' => 0,
        'draft_pages' => 0,
        'scheduled_pages' => 0,
        'total_views' => 0,
    ];

    // Create/Edit Form Properties
    public $showCreateForm = false;
    public $showEditForm = false;
    public $editingPageId = null;
    public $currentStep = 1;
    
    // Basic Page Info
    #[Rule('required|string|max:255')]
    public $title = '';
    
    #[Rule('nullable|string|max:255')]
    public $slug = '';
    
    #[Rule('required|string')]
    public $content = '';
    
    #[Rule('nullable|string|max:500')]
    public $excerpt = '';
    
    #[Rule('required|in:draft,published,archived')]
    public $status = 'draft';
    
    #[Rule('required|string')]
    public $template = 'default';
    
    // SEO Properties
    #[Rule('nullable|string|max:60')]
    public $meta_title = '';
    
    #[Rule('nullable|string|max:160')]
    public $meta_description = '';
    
    #[Rule('nullable|string')]
    public $meta_keywords = '';
    
    public $no_index = false;
    
    // Publishing Properties
    public $published_at = '';
    public $scheduled_at = '';
    public $expires_at = '';
    
    // Media Properties
    #[Rule('nullable|image|max:2048')]
    public $featured_image;
    
    public $gallery_images = [];
    
    // Advanced Properties
    public $page_blocks = [];
    public $shortcodes = [];
    public $settings = [];
    public $custom_css = [];
    public $custom_js = [];
    
    // UI State
    public $showSeoAnalysis = false;
    public $seoAnalysisData = [];
    public $showPreview = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'templateFilter' => ['except' => ''],
        'activeTab' => ['except' => 'all-pages'],
    ];

    public function mount()
    {
        $this->loadStatistics();
        $this->initializeDefaults();
    }
    public function rules()
{
    return [
        'title' => ['required', 'string', 'max:255'],
        'slug' => ['nullable', 'string', 'max:255'],
        'content' => ['required', 'string'],
        'excerpt' => ['nullable', 'string', 'max:500'],
        'status' => ['required', 'in:draft,published,archived'],
        'template' => ['required', 'string'],
        'meta_title' => ['nullable', 'string', 'max:60'],
        'meta_description' => ['nullable', 'string', 'max:160'],
        'meta_keywords' => ['nullable', 'string'],
        'no_index' => ['boolean'],
        'published_at' => ['nullable', 'date'],
        'scheduled_at' => ['nullable', 'date'],
        'expires_at' => ['nullable', 'date'],
        'featured_image' => ['nullable', 'image', 'max:2048'],
        'gallery_images' => ['nullable', 'array'],
        'gallery_images.*' => ['image', 'max:2048'],
    ];
}

    // Tab Management Methods
    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
        
        if ($tab === 'create-page') {
            $this->showCreateForm();
        } else {
            $this->hideCreateForm();
        }
    }

    // Statistics Methods
    public function loadStatistics()
    {
        try {
            $this->stats = [
                'total_pages' => Page::count(),
                'published_pages' => Page::published()->count(),
                'draft_pages' => Page::draft()->count(),
                'scheduled_pages' => Page::scheduled()->count(),
                'total_views' => Page::sum('view_count'),
            ];
        } catch (\Exception $e) {
            $this->stats = [
                'total_pages' => 0,
                'published_pages' => 0,
                'draft_pages' => 0,
                'scheduled_pages' => 0,
                'total_views' => 0,
            ];
        }
    }

    public function refreshStats()
    {
        $this->loadStatistics();
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Statistics refreshed successfully!'
        ]);
    }

    // Page List Methods
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
        $this->sortBy = 'updated_at';
        $this->sortDirection = 'desc';
        $this->resetPage();
    }

    public function getPages()
    {
        return Page::with(['creator', 'updater', 'featuredMedia'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', "%{$this->search}%")
                      ->orWhere('content', 'like', "%{$this->search}%")
                      ->orWhere('slug', 'like', "%{$this->search}%");
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
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(15);
    }

    // Bulk Actions Methods
    public function togglePageSelection($pageId)
    {
        if (in_array($pageId, $this->selectedPages)) {
            $this->selectedPages = array_filter($this->selectedPages, fn($id) => $id != $pageId);
        } else {
            $this->selectedPages[] = $pageId;
        }
    }

    public function selectAllPages()
    {
        $this->selectedPages = $this->getPages()->pluck('id')->toArray();
    }

    public function deselectAllPages()
    {
        $this->selectedPages = [];
    }

    public function executeBulkAction()
    {
        if (empty($this->selectedPages) || empty($this->bulkAction)) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Please select pages and an action.'
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
                    $message = "{$count} pages published successfully.";
                    break;

                case 'draft':
                    $pages->update(['status' => Page::STATUS_DRAFT]);
                    $message = "{$count} pages moved to draft.";
                    break;

                case 'archive':
                    $pages->update(['status' => Page::STATUS_ARCHIVED]);
                    $message = "{$count} pages archived.";
                    break;

                case 'delete':
                    if (!auth()->user()->canManagePages()) {
                        throw new \Exception('You do not have permission to delete pages.');
                    }
                    $pages->delete();
                    $message = "{$count} pages deleted successfully.";
                    break;

                default:
                    throw new \Exception('Invalid bulk action.');
            }

            $this->selectedPages = [];
            $this->bulkAction = '';
            $this->loadStatistics();
            
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

    // Individual Page Actions
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

            $this->loadStatistics();
            
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => "Page status updated to " . ucfirst($newStatus)
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Failed to update page status.'
            ]);
        }
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

            $this->loadStatistics();
            
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Page duplicated successfully.'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Failed to duplicate page.'
            ]);
        }
    }

    public function deletePage($pageId)
    {
        try {
            if (!auth()->user()->canManagePages()) {
                throw new \Exception('You do not have permission to delete pages.');
            }

            $page = Page::findOrFail($pageId);
            $page->delete();
            
            $this->loadStatistics();
            
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Page deleted successfully.'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    // Form Management Methods
    public function showCreateForm()
    {
        $this->showCreateForm = true;
        $this->showEditForm = false;
        $this->resetForm();
        $this->currentStep = 1;
    }

    public function hideCreateForm()
    {
        $this->showCreateForm = false;
        $this->resetForm();
    }

    public function editPage($pageId)
    {
        try {
            $page = Page::findOrFail($pageId);
            
            $this->editingPageId = $pageId;
            $this->showEditForm = true;
            $this->showCreateForm = false;
            $this->currentStep = 1;
            
            // Populate form with page data
            $this->title = $page->title;
            $this->slug = $page->slug;
            $this->content = $page->content;
            $this->excerpt = $page->excerpt;
            $this->status = $page->status;
            $this->template = $page->template;
            $this->meta_title = $page->meta_title;
            $this->meta_description = $page->meta_description;
            $this->meta_keywords = $page->meta_keywords;
            $this->no_index = $page->no_index;
            $this->published_at = $page->published_at ? $page->published_at->format('Y-m-d\TH:i') : '';
            $this->scheduled_at = $page->scheduled_at ? $page->scheduled_at->format('Y-m-d\TH:i') : '';
            $this->expires_at = $page->expires_at ? $page->expires_at->format('Y-m-d\TH:i') : '';
            $this->page_blocks = $page->page_blocks ?: [];
            $this->shortcodes = $page->shortcodes ?: [];
            $this->settings = $page->settings ?: [];
            $this->custom_css = $page->custom_css ?: [];
            $this->custom_js = $page->custom_js ?: [];
            
            $this->activeTab = 'edit-page';
            
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Failed to load page for editing.'
            ]);
        }
    }

    public function hideEditForm()
    {
        $this->showEditForm = false;
        $this->editingPageId = null;
        $this->resetForm();
        $this->activeTab = 'all-pages';
    }

    public function resetForm()
    {
        $this->title = '';
        $this->slug = '';
        $this->content = '';
        $this->excerpt = '';
        $this->status = 'draft';
        $this->template = 'default';
        $this->meta_title = '';
        $this->meta_description = '';
        $this->meta_keywords = '';
        $this->no_index = false;
        $this->published_at = now()->format('Y-m-d\TH:i');
        $this->scheduled_at = '';
        $this->expires_at = '';
        $this->featured_image = null;
        $this->gallery_images = [];
        $this->initializeDefaults();
        $this->resetValidation();
    }

    // Form Step Navigation
    public function nextStep()
    {
        if ($this->currentStep < 4) {
            $this->currentStep++;
        }
    }

    public function previousStep()
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    public function setStep($step)
    {
        $this->currentStep = $step;
    }

    // Form Field Updates
    public function updatedTitle()
    {
        if (empty($this->slug) || (!$this->editingPageId)) {
            $this->generateSlug();
        }
        $this->generateMetaTitle();
    }

    public function updatedContent()
    {
        $this->generateExcerpt();
        $this->generateMetaDescription();
    }

    // Auto-generation Methods
    public function generateSlug()
    {
        $this->slug = $this->generateUniqueSlug($this->title, $this->editingPageId);
    }

    public function generateUniqueSlug($title, $excludeId = null)
    {
        $baseSlug = Str::slug($title);
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

    public function generateMetaTitle()
    {
        if (empty($this->meta_title)) {
            $this->meta_title = Str::limit($this->title, 55);
        }
    }

    public function generateExcerpt()
    {
        if (empty($this->excerpt) && !empty($this->content)) {
            $this->excerpt = Str::limit(strip_tags($this->content), 150);
        }
    }

    public function generateMetaDescription()
    {
        if (empty($this->meta_description)) {
            if (!empty($this->excerpt)) {
                $this->meta_description = Str::limit($this->excerpt, 155);
            } elseif (!empty($this->content)) {
                $this->meta_description = Str::limit(strip_tags($this->content), 155);
            }
        }
    }

    // SEO Analysis
    public function analyzeSeo()
    {
        $this->seoAnalysisData = $this->performSeoAnalysis();
        $this->showSeoAnalysis = true;
    }

    public function closeSeoAnalysis()
    {
        $this->showSeoAnalysis = false;
    }

    private function performSeoAnalysis()
    {
        $issues = [];
        $suggestions = [];
        $score = 100;

        // Title analysis
        if (empty($this->title)) {
            $issues[] = 'Page title is required';
            $score -= 20;
        } elseif (strlen($this->title) < 30) {
            $suggestions[] = 'Title could be longer (30+ characters recommended)';
            $score -= 5;
        } elseif (strlen($this->title) > 60) {
            $issues[] = 'Title too long (60 characters max recommended)';
            $score -= 10;
        }

        // Meta title analysis
        if (empty($this->meta_title)) {
            $suggestions[] = 'Consider adding a custom meta title';
            $score -= 5;
        } elseif (strlen($this->meta_title) > 60) {
            $issues[] = 'Meta title too long (60 characters max)';
            $score -= 10;
        }

        // Meta description analysis
        if (empty($this->meta_description)) {
            $issues[] = 'Meta description is missing';
            $score -= 15;
        } elseif (strlen($this->meta_description) < 120) {
            $suggestions[] = 'Meta description could be longer (120+ characters)';
            $score -= 5;
        } elseif (strlen($this->meta_description) > 160) {
            $issues[] = 'Meta description too long (160 characters max)';
            $score -= 10;
        }

        // Content analysis
        $wordCount = str_word_count(strip_tags($this->content));
        if ($wordCount < 300) {
            $suggestions[] = 'Content is quite short (300+ words recommended)';
            $score -= 10;
        }

        // Featured image
        if (!$this->featured_image && !$this->editingPageId) {
            $suggestions[] = 'Consider adding a featured image';
            $score -= 5;
        }

        return [
            'score' => max($score, 0),
            'issues' => $issues,
            'suggestions' => $suggestions,
            'word_count' => $wordCount,
            'reading_time' => ceil($wordCount / 200),
        ];
    }

    // Page Blocks Management
    public function addPageBlock($blockType)
    {
        if (!isset($this->page_blocks[$blockType])) {
            $this->page_blocks[$blockType] = [
                'enabled' => true,
                'title' => '',
                'content' => '',
                'settings' => []
            ];
        } else {
            $this->page_blocks[$blockType]['enabled'] = true;
        }
    }

    public function removePageBlock($blockType)
    {
        if (isset($this->page_blocks[$blockType])) {
            $this->page_blocks[$blockType]['enabled'] = false;
        }
    }

    // Shortcodes Management
    public function addShortcode()
    {
        $this->shortcodes['new_shortcode'] = '';
    }

    public function removeShortcode($key)
    {
        unset($this->shortcodes[$key]);
    }

    // Save Methods
    public function save($action = 'draft')
    {
        $this->validate();

        try {
            // Handle status based on action
            $this->handleSaveAction($action);

            $pageData = $this->preparePageData();

            if ($this->editingPageId) {
                $page = Page::findOrFail($this->editingPageId);
                $page->update($pageData);
                $message = 'Page updated successfully!';
            } else {
                $page = Page::create($pageData);
                $message = 'Page created successfully!';
            }

            // Handle media uploads
            $this->handleMediaUploads($page);

            $this->loadStatistics();
            
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => $message
            ]);

            // Reset form and go back to list
            $this->hideCreateForm();
            $this->hideEditForm();
            $this->activeTab = 'all-pages';

        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Failed to save page: ' . $e->getMessage()
            ]);
        }
    }

    private function handleSaveAction($action)
    {
        switch ($action) {
            case 'publish':
                $this->status = 'published';
                $this->published_at = now()->format('Y-m-d\TH:i');
                break;
            case 'schedule':
                $this->status = 'draft';
                if (empty($this->scheduled_at)) {
                    throw new \Exception('Scheduled date is required for scheduling.');
                }
                break;
            case 'draft':
            default:
                $this->status = 'draft';
                break;
        }
    }

    private function preparePageData()
    {
        return [
            'title' => $this->title,
            'slug' => $this->slug ?: $this->generateUniqueSlug($this->title, $this->editingPageId),
            'content' => $this->content,
            'excerpt' => $this->excerpt,
            'status' => $this->status,
            'template' => $this->template,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'meta_keywords' => $this->meta_keywords,
            'no_index' => $this->no_index,
            'published_at' => $this->status === 'published' && $this->published_at ? 
                Carbon::parse($this->published_at) : null,
            'scheduled_at' => $this->scheduled_at ? 
                Carbon::parse($this->scheduled_at) : null,
            'expires_at' => $this->expires_at ? 
                Carbon::parse($this->expires_at) : null,
            'page_blocks' => $this->page_blocks,
            'shortcodes' => $this->shortcodes,
            'settings' => $this->settings,
            'custom_css' => $this->custom_css,
            'custom_js' => $this->custom_js,
        ];
    }

    private function handleMediaUploads($page)
    {
        // Handle featured image
        if ($this->featured_image) {
            $media = PageMedia::uploadFile($this->featured_image, 'pages/featured');
            
            // Remove existing featured media
            $page->media()->wherePivot('context', 'featured')->detach();
            
            // Attach new featured media
            $page->media()->attach($media->id, ['context' => 'featured']);
            $page->update(['og_image' => $media->getUrl()]);
        }

        // Handle gallery images
        if (!empty($this->gallery_images)) {
            foreach ($this->gallery_images as $index => $image) {
                $media = PageMedia::uploadFile($image, 'pages/gallery');
                $page->media()->attach($media->id, [
                    'context' => 'gallery',
                    'sort_order' => $index
                ]);
            }
        }
    }

    // Initialize Default Values
    private function initializeDefaults()
    {
        $this->published_at = now()->format('Y-m-d\TH:i');
        
        $this->page_blocks = [
            'hero' => [
                'enabled' => false,
                'title' => '',
                'subtitle' => '',
                'background_image' => '',
                'cta_text' => 'Get Started',
                'cta_link' => '#',
            ],
            'features' => [
                'enabled' => false,
                'items' => [
                    ['icon' => 'fas fa-star', 'title' => '', 'description' => ''],
                    ['icon' => 'fas fa-rocket', 'title' => '', 'description' => ''],
                    ['icon' => 'fas fa-heart', 'title' => '', 'description' => ''],
                ]
            ],
            'cta' => [
                'enabled' => false,
                'title' => 'Ready to get started?',
                'description' => 'Join thousands of satisfied customers.',
                'button_text' => 'Get Started Now',
                'button_link' => '#',
            ]
        ];

        $this->shortcodes = [
            'company_name' => config('app.name'),
            'current_year' => date('Y'),
            'contact_email' => 'contact@example.com',
            'phone' => '+1-234-567-8900',
        ];

        $this->settings = [
            'enable_comments' => false,
            'enable_sharing' => true,
            'enable_reading_time' => true,
            'enable_toc' => false,
            'custom_css_enabled' => false,
            'custom_js_enabled' => false,
        ];
    }

    public function render()
    {
        $pages = $this->activeTab === 'all-pages' ? $this->getPages() : collect();
        
        return view('livewire.pages.page-manager', [
            'pages' => $pages,
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
        ]);
    }
}