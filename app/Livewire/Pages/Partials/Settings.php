<?php

namespace App\Livewire\Pages\Partials;

use Livewire\Component;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;

class Settings extends Component
{
    // General Settings
    public $autoGenerateSlugs = true;
    public $enableSeoAnalysis = true;
    public $enableVersioning = false;
    public $defaultTemplate = 'default';
    public $defaultStatus = 'draft';
    
    // Performance Settings
    public $cachePages = true;
    public $optimizeImages = true;
    public $minifyCss = false;
    public $minifyJs = false;
    public $enableCdn = false;
    public $cdnUrl = '';
    
    // SEO Settings
    public $autoGenerateMetaDescriptions = true;
    public $autoGenerateOgImages = false;
    public $enableBreadcrumbs = true;
    public $enableSitemap = true;
    public $sitemapFrequency = 'daily';
    
    // Content Settings
    public $enableComments = false;
    public $enableSocialSharing = true;
    public $enableReadingTime = true;
    public $enableTableOfContents = false;
    public $maxRevisions = 10;
    
    // Security Settings
    public $enableCsrfProtection = true;
    public $allowHtmlInContent = true;
    public $sanitizeContent = true;
    public $enableRateLimiting = true;
    
    // Notification Settings
    public $emailOnPagePublished = false;
    public $emailOnPageUpdated = false;
    public $notificationEmail = '';
    
    // Advanced Settings
    public $customCssGlobal = '';
    public $customJsGlobal = '';
    public $googleAnalyticsId = '';
    public $googleTagManagerId = '';
    public $facebookPixelId = '';

    protected $rules = [
        'defaultTemplate' => 'required|string',
        'defaultStatus' => 'required|in:draft,published',
        'cdnUrl' => 'nullable|url',
        'sitemapFrequency' => 'required|in:always,hourly,daily,weekly,monthly,yearly,never',
        'maxRevisions' => 'required|integer|min:1|max:100',
        'notificationEmail' => 'nullable|email',
        'customCssGlobal' => 'nullable|string',
        'customJsGlobal' => 'nullable|string',
        'googleAnalyticsId' => 'nullable|string|regex:/^G-[A-Z0-9]+$/i',
        'googleTagManagerId' => 'nullable|string|regex:/^GTM-[A-Z0-9]+$/i',
        'facebookPixelId' => 'nullable|string|regex:/^[0-9]+$/',
    ];

    public function mount()
    {
        $this->loadSettings();
    }

    public function loadSettings()
    {
        // Load settings from cache or database
        $settings = Cache::get('page_manager_settings', []);
        
        foreach ($settings as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
        
        // Set defaults if not in cache
        $this->notificationEmail = $this->notificationEmail ?: config('mail.from.address');
    }

    public function save()
    {
        $this->validate();

        try {
            $settings = [
                'autoGenerateSlugs' => $this->autoGenerateSlugs,
                'enableSeoAnalysis' => $this->enableSeoAnalysis,
                'enableVersioning' => $this->enableVersioning,
                'defaultTemplate' => $this->defaultTemplate,
                'defaultStatus' => $this->defaultStatus,
                'cachePages' => $this->cachePages,
                'optimizeImages' => $this->optimizeImages,
                'minifyCss' => $this->minifyCss,
                'minifyJs' => $this->minifyJs,
                'enableCdn' => $this->enableCdn,
                'cdnUrl' => $this->cdnUrl,
                'autoGenerateMetaDescriptions' => $this->autoGenerateMetaDescriptions,
                'autoGenerateOgImages' => $this->autoGenerateOgImages,
                'enableBreadcrumbs' => $this->enableBreadcrumbs,
                'enableSitemap' => $this->enableSitemap,
                'sitemapFrequency' => $this->sitemapFrequency,
                'enableComments' => $this->enableComments,
                'enableSocialSharing' => $this->enableSocialSharing,
                'enableReadingTime' => $this->enableReadingTime,
                'enableTableOfContents' => $this->enableTableOfContents,
                'maxRevisions' => $this->maxRevisions,
                'enableCsrfProtection' => $this->enableCsrfProtection,
                'allowHtmlInContent' => $this->allowHtmlInContent,
                'sanitizeContent' => $this->sanitizeContent,
                'enableRateLimiting' => $this->enableRateLimiting,
                'emailOnPagePublished' => $this->emailOnPagePublished,
                'emailOnPageUpdated' => $this->emailOnPageUpdated,
                'notificationEmail' => $this->notificationEmail,
                'customCssGlobal' => $this->customCssGlobal,
                'customJsGlobal' => $this->customJsGlobal,
                'googleAnalyticsId' => $this->googleAnalyticsId,
                'googleTagManagerId' => $this->googleTagManagerId,
                'facebookPixelId' => $this->facebookPixelId,
            ];

            // Cache the settings
            Cache::put('page_manager_settings', $settings);
            
            // Also save to config file for persistence
            $this->updateConfigFile($settings);
            
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Settings saved successfully'
            ]);

        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Failed to save settings: ' . $e->getMessage()
            ]);
        }
    }

    public function resetToDefaults()
    {
        try {
            // Reset all settings to defaults
            $this->autoGenerateSlugs = true;
            $this->enableSeoAnalysis = true;
            $this->enableVersioning = false;
            $this->defaultTemplate = 'default';
            $this->defaultStatus = 'draft';
            $this->cachePages = true;
            $this->optimizeImages = true;
            $this->minifyCss = false;
            $this->minifyJs = false;
            $this->enableCdn = false;
            $this->cdnUrl = '';
            $this->autoGenerateMetaDescriptions = true;
            $this->autoGenerateOgImages = false;
            $this->enableBreadcrumbs = true;
            $this->enableSitemap = true;
            $this->sitemapFrequency = 'daily';
            $this->enableComments = false;
            $this->enableSocialSharing = true;
            $this->enableReadingTime = true;
            $this->enableTableOfContents = false;
            $this->maxRevisions = 10;
            $this->enableCsrfProtection = true;
            $this->allowHtmlInContent = true;
            $this->sanitizeContent = true;
            $this->enableRateLimiting = true;
            $this->emailOnPagePublished = false;
            $this->emailOnPageUpdated = false;
            $this->notificationEmail = config('mail.from.address');
            $this->customCssGlobal = '';
            $this->customJsGlobal = '';
            $this->googleAnalyticsId = '';
            $this->googleTagManagerId = '';
            $this->facebookPixelId = '';

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Settings reset to defaults'
            ]);

        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Failed to reset settings'
            ]);
        }
    }

    public function clearCache()
    {
        try {
            // Clear various caches
            Cache::flush();
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('view:clear');
            
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'All caches cleared successfully'
            ]);

        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Failed to clear cache'
            ]);
        }
    }

    public function testEmailSettings()
    {
        try {
            if (!$this->notificationEmail) {
                throw new \Exception('Please enter a notification email address');
            }

            // Send test email (simplified - you'd create a proper Mailable class)
            \Mail::raw('This is a test email from your Page Manager settings.', function ($message) {
                $message->to($this->notificationEmail)
                        ->subject('Page Manager - Test Email');
            });
            
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Test email sent successfully to ' . $this->notificationEmail
            ]);

        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Failed to send test email: ' . $e->getMessage()
            ]);
        }
    }

    public function optimizeDatabase()
    {
        try {
            // Optimize database tables
            \DB::statement('OPTIMIZE TABLE pages, page_media, page_media_attachments');
            
            // Clean up orphaned records
            \DB::statement('DELETE FROM page_media_attachments WHERE page_id NOT IN (SELECT id FROM pages)');
            
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Database optimized successfully'
            ]);

        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Database optimization failed'
            ]);
        }
    }

    public function exportSettings()
    {
        try {
            $settings = Cache::get('page_manager_settings', []);
            $export = [
                'exported_at' => now()->toISOString(),
                'version' => '1.0',
                'settings' => $settings
            ];
            
            $filename = 'page-manager-settings-' . now()->format('Y-m-d') . '.json';
            
            $this->dispatch('download-file', [
                'filename' => $filename,
                'content' => json_encode($export, JSON_PRETTY_PRINT),
                'type' => 'application/json'
            ]);

        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Failed to export settings'
            ]);
        }
    }

    public function getTemplateOptions()
    {
        return [
            'default' => 'Default Template',
            'landing' => 'Landing Page',
            'blog' => 'Blog Style',
            'full-width' => 'Full Width',
            'minimal' => 'Minimal',
        ];
    }

    public function getSystemInfo()
    {
        return [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'total_pages' => \App\Models\Page::count(),
            'total_media' => \App\Models\PageMedia::count(),
            'cache_driver' => config('cache.default'),
            'queue_driver' => config('queue.default'),
            'storage_disk' => config('filesystems.default'),
            'memory_limit' => ini_get('memory_limit'),
            'max_upload_size' => ini_get('upload_max_filesize'),
        ];
    }

    private function updateConfigFile($settings)
    {
        // In a real implementation, you might want to update a config file
        // or store these in a database table
        // For now, we'll just cache them
    }

    public function render()
    {
        return view('livewire.pages.partials.settings', [
            'templateOptions' => $this->getTemplateOptions(),
            'systemInfo' => $this->getSystemInfo(),
        ]);
    }
}