<?php

namespace App\Livewire\Pages\Partials;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Content\Page;
use App\Models\Content\PageMedia;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CreatePage extends Component
{
    use WithFileUploads;

    // Form Management
    public $editingPageId = null;
    public $currentStep = 1;
    public $isEditing = false;
    
    // Basic Page Info
    public $title = '';
    public $slug = '';
    public $content = '';
    public $excerpt = '';
    public $status = 'draft';
    public $template = 'default';
    
    // SEO Properties
    public $meta_title = '';
    public $meta_description = '';
    public $meta_keywords = '';
    public $no_index = false;
    
    // Publishing Properties
    public $published_at = '';
    public $scheduled_at = '';
    public $expires_at = '';
    
    // Media Properties
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
    public $autoSave = true;
    public $lastSaved = null;

    protected $rules = [
        'title' => 'required|string|max:255',
        'slug' => 'nullable|string|max:255|unique:pages,slug',
        'content' => 'required|string',
        'excerpt' => 'nullable|string|max:500',
        'status' => 'required|in:draft,published,archived',
        'template' => 'required|string',
        'meta_title' => 'nullable|string|max:60',
        'meta_description' => 'nullable|string|max:160',
        'meta_keywords' => 'nullable|string',
        'featured_image' => 'nullable|image|max:2048',
        'gallery_images.*' => 'image|max:2048',
    ];

    public function mount()
    {
        // Check if editing existing page
        if ($editPageId = session('editPageId')) {
            $this->loadPageForEditing($editPageId);
            session()->forget('editPageId');
        } else {
            $this->initializeDefaults();
        }
    }

    protected function rules()
    {
        $rules = [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'excerpt' => 'nullable|string|max:500',
            'status' => 'required|in:draft,published,archived',
            'template' => 'required|string',
            'meta_title' => 'nullable|string|max:60',
            'meta_description' => 'nullable|string|max:160',
            'meta_keywords' => 'nullable|string',
            'featured_image' => 'nullable|image|max:2048',
            'gallery_images.*' => 'image|max:2048',
        ];

        // Modify slug validation if editing
        if ($this->editingPageId) {
            $rules['slug'] = 'nullable|string|max:255|unique:pages,slug,' . $this->editingPageId;
        } else {
            $rules['slug'] = 'nullable|string|max:255|unique:pages,slug';
        }

        return $rules;
    }

    public function loadPageForEditing($pageId)
    {
        try {
            $page = Page::findOrFail($pageId);
            
            $this->editingPageId = $pageId;
            $this->isEditing = true;
            
            // Load page data
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
            
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Failed to load page for editing'
            ]);
            
            return redirect()->route('pages.index');
        }
    }

    // Step Navigation
    public function nextStep()
    {
        $this->validateCurrentStep();
        
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
        if ($step > $this->currentStep) {
            $this->validateCurrentStep();
        }
        
        $this->currentStep = $step;
    }

    protected function validateCurrentStep()
    {
        switch ($this->currentStep) {
            case 1:
                $this->validate([
                    'title' => 'required|string|max:255',
                    'template' => 'required|string',
                ]);
                break;
            case 2:
                $this->validate([
                    'content' => 'required|string',
                ]);
                break;
        }
    }

    // Form Field Updates
    public function updatedTitle()
    {
        if (empty($this->slug) || !$this->isEditing) {
            $this->generateSlug();
        }
        $this->generateMetaTitle();
        $this->triggerAutoSave();
    }

    public function updatedContent()
    {
        $this->generateExcerpt();
        $this->generateMetaDescription();
        $this->triggerAutoSave();
    }

    public function updatedSlug()
    {
        $this->slug = Str::slug($this->slug);
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

        return [
            'score' => max($score, 0),
            'issues' => $issues,
            'suggestions' => $suggestions,
            'word_count' => $wordCount,
            'reading_time' => ceil($wordCount / 200),
        ];
    }

    // Page Blocks Management
    public function togglePageBlock($blockType)
    {
        if (!isset($this->page_blocks[$blockType])) {
            $this->page_blocks[$blockType] = $this->getDefaultBlockData($blockType);
        }
        
        $this->page_blocks[$blockType]['enabled'] = !($this->page_blocks[$blockType]['enabled'] ?? false);
    }

    private function getDefaultBlockData($blockType)
    {
        return match($blockType) {
            'hero' => [
                'enabled' => true,
                'title' => '',
                'subtitle' => '',
                'cta_text' => 'Get Started',
                'cta_link' => '#',
                'background_image' => '',
            ],
            'features' => [
                'enabled' => true,
                'title' => 'Features',
                'items' => [
                    ['icon' => 'fas fa-star', 'title' => '', 'description' => ''],
                    ['icon' => 'fas fa-rocket', 'title' => '', 'description' => ''],
                    ['icon' => 'fas fa-heart', 'title' => '', 'description' => ''],
                ]
            ],
            'testimonials' => [
                'enabled' => true,
                'title' => 'What Our Customers Say',
                'items' => [
                    ['name' => '', 'role' => '', 'content' => '', 'avatar' => ''],
                ]
            ],
            'cta' => [
                'enabled' => true,
                'title' => 'Ready to get started?',
                'description' => 'Join thousands of satisfied customers.',
                'button_text' => 'Get Started Now',
                'button_link' => '#',
            ],
            default => ['enabled' => true]
        };
    }

    public function addFeatureItem()
    {
        if (!isset($this->page_blocks['features']['items'])) {
            $this->page_blocks['features']['items'] = [];
        }
        
        $this->page_blocks['features']['items'][] = [
            'icon' => 'fas fa-star',
            'title' => '',
            'description' => ''
        ];
    }

    public function removeFeatureItem($index)
    {
        unset($this->page_blocks['features']['items'][$index]);
        $this->page_blocks['features']['items'] = array_values($this->page_blocks['features']['items']);
    }

    // Shortcodes Management
    public function addShortcode()
    {
        $key = 'new_shortcode_' . time();
        $this->shortcodes[$key] = '';
    }

    public function removeShortcode($key)
    {
        unset($this->shortcodes[$key]);
    }

    // Auto-save functionality
    public function triggerAutoSave()
    {
        if ($this->autoSave && $this->editingPageId) {
            $this->autoSaveContent();
        }
    }

    public function autoSaveContent()
    {
        try {
            $page = Page::find($this->editingPageId);
            if ($page) {
                $page->update([
                    'title' => $this->title,
                    'content' => $this->content,
                    'excerpt' => $this->excerpt,
                ]);
                
                $this->lastSaved = now();
                $this->dispatch('auto-saved');
            }
        } catch (\Exception $e) {
            // Silent fail for auto-save
        }
    }

    // Save Methods
    public function save($action = 'draft')
    {
        $this->validate();

        try {
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

            $this->handleMediaUploads($page);

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => $message
            ]);

            return redirect()->route('pages.index');

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
                if (empty($this->published_at)) {
                    $this->published_at = now()->format('Y-m-d\TH:i');
                }
                break;
            case 'schedule':
                if (empty($this->scheduled_at)) {
                    throw new \Exception('Scheduled date is required for scheduling.');
                }
                $this->status = 'draft';
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
            'published_at' => $this->published_at ? Carbon::parse($this->published_at) : null,
            'scheduled_at' => $this->scheduled_at ? Carbon::parse($this->scheduled_at) : null,
            'expires_at' => $this->expires_at ? Carbon::parse($this->expires_at) : null,
            'page_blocks' => $this->page_blocks,
            'shortcodes' => $this->shortcodes,
            'settings' => $this->settings,
            'custom_css' => $this->custom_css,
            'custom_js' => $this->custom_js,
        ];
    }

    private function handleMediaUploads($page)
    {
        if ($this->featured_image) {
            $media = PageMedia::uploadFile($this->featured_image, 'pages/featured');
            
            $page->media()->wherePivot('context', 'featured')->detach();
            $page->media()->attach($media->id, ['context' => 'featured']);
            $page->update(['og_image' => $media->getUrl()]);
        }

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

    private function initializeDefaults()
    {
        $this->published_at = now()->format('Y-m-d\TH:i');
        
        $this->page_blocks = [];
        
        $this->shortcodes = [
            'company_name' => config('app.name'),
            'current_year' => date('Y'),
            'contact_email' => 'contact@example.com',
        ];

        $this->settings = [
            'enable_comments' => false,
            'enable_sharing' => true,
            'enable_reading_time' => true,
            'enable_toc' => false,
        ];
    }

    public function render()
    {
        return view('livewire.pages.partials.create-page', [
            'templateOptions' => Page::getAvailableTemplates(),
        ]);
    }
}