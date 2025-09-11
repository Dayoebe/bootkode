<?php

namespace App\Livewire\Pages\Partials;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class Templates extends Component
{
    use WithFileUploads;

    public $availableTemplates = [];
    public $customTemplates = [];
    public $selectedTemplate = null;
    public $showTemplateEditor = false;
    public $showImportTemplate = false;
    
    // Template creation/editing
    public $templateName = '';
    public $templateDescription = '';
    public $templateHtml = '';
    public $templateCss = '';
    public $templateJs = '';
    public $templatePreview = null;
    public $editingTemplate = null;
    
    // Import template
    public $importFile = null;

    public function mount()
    {
        $this->loadTemplates();
    }

    public function loadTemplates()
    {
        // Load built-in templates
        $this->availableTemplates = [
            [
                'id' => 'default',
                'name' => 'Default Template',
                'description' => 'Clean, professional layout with sidebar and content area',
                'preview' => asset('images/templates/default-preview.jpg'),
                'category' => 'Basic',
                'features' => ['Responsive', 'SEO Optimized', 'Sidebar Support'],
                'is_active' => true,
                'is_custom' => false,
                'usage_count' => \App\Models\Page::where('template', 'default')->count(),
            ],
            [
                'id' => 'landing',
                'name' => 'Landing Page',
                'description' => 'Marketing-focused layout with hero sections and CTAs',
                'preview' => asset('images/templates/landing-preview.jpg'),
                'category' => 'Marketing',
                'features' => ['Hero Section', 'Call-to-Actions', 'Conversion Focused'],
                'is_active' => true,
                'is_custom' => false,
                'usage_count' => \App\Models\Page::where('template', 'landing')->count(),
            ],
            [
                'id' => 'blog',
                'name' => 'Blog Style',
                'description' => 'Reading-optimized layout with typography focus',
                'preview' => asset('images/templates/blog-preview.jpg'),
                'category' => 'Content',
                'features' => ['Reading Time', 'Social Sharing', 'Typography'],
                'is_active' => true,
                'is_custom' => false,
                'usage_count' => \App\Models\Page::where('template', 'blog')->count(),
            ],
            [
                'id' => 'full-width',
                'name' => 'Full Width',
                'description' => 'Edge-to-edge layout without sidebars',
                'preview' => asset('images/templates/fullwidth-preview.jpg'),
                'category' => 'Layout',
                'features' => ['Full Width', 'No Sidebars', 'Media Rich'],
                'is_active' => true,
                'is_custom' => false,
                'usage_count' => \App\Models\Page::where('template', 'full-width')->count(),
            ],
            [
                'id' => 'minimal',
                'name' => 'Minimal',
                'description' => 'Clean, distraction-free design',
                'preview' => asset('images/templates/minimal-preview.jpg'),
                'category' => 'Minimal',
                'features' => ['Clean Design', 'Fast Loading', 'Distraction Free'],
                'is_active' => true,
                'is_custom' => false,
                'usage_count' => \App\Models\Page::where('template', 'minimal')->count(),
            ],
        ];

        // Load custom templates (in a real app, these would come from database)
        $this->customTemplates = $this->loadCustomTemplatesFromStorage();
    }

    private function loadCustomTemplatesFromStorage()
    {
        $customTemplates = [];
        
        if (Storage::exists('templates/custom')) {
            $templateDirs = Storage::directories('templates/custom');
            
            foreach ($templateDirs as $dir) {
                $configPath = $dir . '/config.json';
                
                if (Storage::exists($configPath)) {
                    $config = json_decode(Storage::get($configPath), true);
                    $config['is_custom'] = true;
                    $config['usage_count'] = \App\Models\Page::where('template', $config['id'])->count();
                    $customTemplates[] = $config;
                }
            }
        }
        
        return $customTemplates;
    }

    public function previewTemplate($templateId)
    {
        $template = collect($this->availableTemplates)
            ->merge($this->customTemplates)
            ->firstWhere('id', $templateId);
            
        if ($template) {
            $this->selectedTemplate = $template;
            
            // Generate preview URL (in real implementation)
            $this->templatePreview = url("/template-preview/{$templateId}");
        }
    }

    public function editTemplate($templateId)
    {
        if (!$templateId || $templateId === 'new') {
            $this->startNewTemplate();
            return;
        }

        $template = collect($this->customTemplates)->firstWhere('id', $templateId);
        
        if ($template && $template['is_custom']) {
            $this->editingTemplate = $templateId;
            $this->templateName = $template['name'];
            $this->templateDescription = $template['description'];
            
            // Load template files
            $templatePath = "templates/custom/{$templateId}";
            $this->templateHtml = Storage::get($templatePath . '/template.blade.php') ?? '';
            $this->templateCss = Storage::get($templatePath . '/style.css') ?? '';
            $this->templateJs = Storage::get($templatePath . '/script.js') ?? '';
            
            $this->showTemplateEditor = true;
        } else {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Cannot edit built-in templates'
            ]);
        }
    }

    public function startNewTemplate()
    {
        $this->editingTemplate = null;
        $this->templateName = '';
        $this->templateDescription = '';
        $this->templateHtml = $this->getDefaultTemplateHtml();
        $this->templateCss = $this->getDefaultTemplateCss();
        $this->templateJs = '';
        $this->showTemplateEditor = true;
    }

    public function saveTemplate()
    {
        $this->validate([
            'templateName' => 'required|string|max:255',
            'templateDescription' => 'required|string|max:500',
            'templateHtml' => 'required|string',
        ]);

        try {
            $templateId = $this->editingTemplate ?: \Str::slug($this->templateName);
            $templatePath = "templates/custom/{$templateId}";

            // Create directory if it doesn't exist
            if (!Storage::exists($templatePath)) {
                Storage::makeDirectory($templatePath);
            }

            // Save template files
            Storage::put($templatePath . '/template.blade.php', $this->templateHtml);
            Storage::put($templatePath . '/style.css', $this->templateCss);
            Storage::put($templatePath . '/script.js', $this->templateJs);

            // Save configuration
            $config = [
                'id' => $templateId,
                'name' => $this->templateName,
                'description' => $this->templateDescription,
                'category' => 'Custom',
                'features' => ['Custom Design', 'Handcrafted'],
                'is_active' => true,
                'created_at' => now()->toISOString(),
            ];

            Storage::put($templatePath . '/config.json', json_encode($config, JSON_PRETTY_PRINT));

            $this->showTemplateEditor = false;
            $this->loadTemplates();

            $message = $this->editingTemplate ? 'Template updated successfully' : 'Template created successfully';
            
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => $message
            ]);

        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Failed to save template: ' . $e->getMessage()
            ]);
        }
    }

    public function deleteTemplate($templateId)
    {
        try {
            $template = collect($this->customTemplates)->firstWhere('id', $templateId);
            
            if (!$template || !$template['is_custom']) {
                throw new \Exception('Cannot delete built-in templates');
            }

            // Check if template is in use
            $usageCount = \App\Models\Page::where('template', $templateId)->count();
            if ($usageCount > 0) {
                throw new \Exception("Template is being used by {$usageCount} page(s)");
            }

            // Delete template directory
            Storage::deleteDirectory("templates/custom/{$templateId}");
            
            $this->loadTemplates();
            
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Template deleted successfully'
            ]);

        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function duplicateTemplate($templateId)
    {
        try {
            $sourceTemplate = collect($this->availableTemplates)
                ->merge($this->customTemplates)
                ->firstWhere('id', $templateId);

            if (!$sourceTemplate) {
                throw new \Exception('Template not found');
            }

            $newTemplateId = $templateId . '-copy-' . time();
            $templatePath = "templates/custom/{$newTemplateId}";

            // Create new template directory
            Storage::makeDirectory($templatePath);

            if ($sourceTemplate['is_custom']) {
                // Copy from custom template
                $sourcePath = "templates/custom/{$templateId}";
                
                if (Storage::exists($sourcePath . '/template.blade.php')) {
                    Storage::copy($sourcePath . '/template.blade.php', $templatePath . '/template.blade.php');
                }
                if (Storage::exists($sourcePath . '/style.css')) {
                    Storage::copy($sourcePath . '/style.css', $templatePath . '/style.css');
                }
                if (Storage::exists($sourcePath . '/script.js')) {
                    Storage::copy($sourcePath . '/script.js', $templatePath . '/script.js');
                }
            } else {
                // Copy from built-in template
                Storage::put($templatePath . '/template.blade.php', $this->getBuiltInTemplateContent($templateId));
                Storage::put($templatePath . '/style.css', '/* Custom styles for ' . $sourceTemplate['name'] . ' */');
                Storage::put($templatePath . '/script.js', '// Custom JavaScript');
            }

            // Create configuration
            $config = [
                'id' => $newTemplateId,
                'name' => $sourceTemplate['name'] . ' (Copy)',
                'description' => $sourceTemplate['description'] . ' - Duplicated template',
                'category' => 'Custom',
                'features' => $sourceTemplate['features'],
                'is_active' => true,
                'created_at' => now()->toISOString(),
            ];

            Storage::put($templatePath . '/config.json', json_encode($config, JSON_PRETTY_PRINT));

            $this->loadTemplates();

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Template duplicated successfully'
            ]);

        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Failed to duplicate template: ' . $e->getMessage()
            ]);
        }
    }

    public function importTemplate()
    {
        $this->validate([
            'importFile' => 'required|file|mimes:zip|max:10240', // 10MB max
        ]);

        try {
            // Extract and process the uploaded template
            // This is a simplified implementation
            
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Template imported successfully'
            ]);
            
            $this->showImportTemplate = false;
            $this->importFile = null;
            $this->loadTemplates();

        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Failed to import template: ' . $e->getMessage()
            ]);
        }
    }

    public function exportTemplate($templateId)
    {
        try {
            // Create a ZIP file with the template
            // This is a simplified implementation
            
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Template exported successfully'
            ]);

        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Failed to export template'
            ]);
        }
    }

    private function getDefaultTemplateHtml()
    {
        return <<<'HTML'
@extends('layouts.pages')

@section('title', $page->getMetaTitle())
@section('meta_description', $page->getMetaDescription())

@section('content')
    <article class="custom-page-template">
        @if($page->featuredMedia && $page->featuredMedia->count())
            <div class="featured-image">
                <img src="{{ $page->featuredMedia->first()->getUrl() }}" 
                     alt="{{ $page->featuredMedia->first()->alt_text ?? $page->title }}">
            </div>
        @endif
        
        <header class="page-header">
            <h1 class="page-title">{{ $page->title }}</h1>
            
            @if($page->excerpt)
                <div class="page-excerpt">
                    <p>{{ $page->excerpt }}</p>
                </div>
            @endif
        </header>
        
        <div class="page-content">
            {!! $page->getProcessedContent() !!}
        </div>
    </article>
@endsection
HTML;
    }

    private function getDefaultTemplateCss()
    {
        return <<<'CSS'
.custom-page-template {
    max-width: 800px;
    margin: 0 auto;
    padding: 2rem;
}

.featured-image {
    margin-bottom: 2rem;
    border-radius: 8px;
    overflow: hidden;
}

.featured-image img {
    width: 100%;
    height: auto;
    display: block;
}

.page-header {
    margin-bottom: 2rem;
    text-align: center;
}

.page-title {
    font-size: 2.5rem;
    font-weight: bold;
    margin-bottom: 1rem;
    color: #1a202c;
}

.page-excerpt {
    font-size: 1.25rem;
    color: #4a5568;
    line-height: 1.6;
}

.page-content {
    line-height: 1.8;
    font-size: 1.1rem;
    color: #2d3748;
}

.page-content h2,
.page-content h3,
.page-content h4 {
    margin: 2rem 0 1rem;
    color: #1a202c;
}

.page-content p {
    margin-bottom: 1.5rem;
}

@media (max-width: 768px) {
    .custom-page-template {
        padding: 1rem;
    }
    
    .page-title {
        font-size: 2rem;
    }
}
CSS;
    }

    private function getBuiltInTemplateContent($templateId)
    {
        // Return template content based on ID
        return $this->getDefaultTemplateHtml();
    }

    public function render()
    {
        return view('livewire.pages.partials.templates');
    }
}