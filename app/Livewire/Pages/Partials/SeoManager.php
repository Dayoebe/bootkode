<?php

namespace App\Livewire\Pages\Partials;

use Livewire\Component;
use App\Models\Content\Page;
use Illuminate\Support\Facades\File;

class SeoManager extends Component
{
    public $seoIssues = [];
    public $seoSuggestions = [];
    public $crawlErrors = [];
    public $sitemapStatus = [];
    public $seoScore = 0;
    public $selectedIssueType = '';

    public function mount()
    {
        $this->loadSeoData();
    }

    public function loadSeoData()
    {
        $this->seoIssues = $this->analyzeSeoIssues();
        $this->seoSuggestions = $this->generateSeoSuggestions();
        $this->crawlErrors = $this->getCrawlErrors();
        $this->sitemapStatus = $this->getSitemapStatus();
        $this->seoScore = $this->calculateOverallSeoScore();
    }

    private function analyzeSeoIssues()
    {
        $issues = [];
        
        // Missing meta descriptions
        $missingMetaDesc = Page::whereNull('meta_description')->orWhere('meta_description', '')->get();
        if ($missingMetaDesc->count() > 0) {
            $issues[] = [
                'type' => 'critical',
                'title' => 'Missing Meta Descriptions',
                'count' => $missingMetaDesc->count(),
                'description' => $missingMetaDesc->count() . ' pages are missing meta descriptions',
                'pages' => $missingMetaDesc->take(5)->pluck('title', 'slug')->toArray(),
                'action' => 'fix_meta_descriptions'
            ];
        }

        // Duplicate titles
        $duplicateTitles = Page::select('title', \DB::raw('COUNT(*) as count'))
            ->groupBy('title')
            ->having('count', '>', 1)
            ->get();
        
        if ($duplicateTitles->count() > 0) {
            $issues[] = [
                'type' => 'warning',
                'title' => 'Duplicate Titles',
                'count' => $duplicateTitles->sum('count'),
                'description' => 'Multiple pages have identical titles',
                'pages' => $duplicateTitles->pluck('title')->toArray(),
                'action' => 'review_duplicate_titles'
            ];
        }

        // Long meta descriptions
        $longMetaDesc = Page::whereRaw('LENGTH(meta_description) > 160')->get();
        if ($longMetaDesc->count() > 0) {
            $issues[] = [
                'type' => 'warning',
                'title' => 'Long Meta Descriptions',
                'count' => $longMetaDesc->count(),
                'description' => $longMetaDesc->count() . ' pages have meta descriptions longer than 160 characters',
                'pages' => $longMetaDesc->take(5)->pluck('title', 'slug')->toArray(),
                'action' => 'trim_meta_descriptions'
            ];
        }

        // Missing alt text for images
        $pagesWithImages = Page::whereHas('media')->get();
        $missingAltText = 0;
        foreach ($pagesWithImages as $page) {
            $missingAltText += $page->media()->whereNull('alt_text')->count();
        }
        
        if ($missingAltText > 0) {
            $issues[] = [
                'type' => 'warning',
                'title' => 'Missing Image Alt Text',
                'count' => $missingAltText,
                'description' => $missingAltText . ' images are missing alt text',
                'action' => 'add_alt_text'
            ];
        }

        return $issues;
    }

    public function fixSeoIssue($issueIndex)
    {
        try {
            $issue = $this->seoIssues[$issueIndex];
            
            switch ($issue['action']) {
                case 'fix_meta_descriptions':
                    Page::whereNull('meta_description')->orWhere('meta_description', '')
                        ->each(function ($page) {
                            $page->update([
                                'meta_description' => \Str::limit(strip_tags($page->content), 155)
                            ]);
                        });
                    break;
                    
                case 'trim_meta_descriptions':
                    Page::whereRaw('LENGTH(meta_description) > 160')
                        ->each(function ($page) {
                            $page->update([
                                'meta_description' => \Str::limit($page->meta_description, 155)
                            ]);
                        });
                    break;
            }
            
            $this->loadSeoData();
            
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'SEO issue fixed successfully'
            ]);
            
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Failed to fix SEO issue: ' . $e->getMessage()
            ]);
        }
    }

    public function generateSitemap()
    {
        try {
            $pages = Page::published()->get();
            
            $sitemap = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
            $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;
            
            foreach ($pages as $page) {
                $sitemap .= '  <url>' . PHP_EOL;
                $sitemap .= '    <loc>' . url($page->slug) . '</loc>' . PHP_EOL;
                $sitemap .= '    <lastmod>' . $page->updated_at->toAtomString() . '</lastmod>' . PHP_EOL;
                $sitemap .= '    <changefreq>weekly</changefreq>' . PHP_EOL;
                $sitemap .= '    <priority>0.8</priority>' . PHP_EOL;
                $sitemap .= '  </url>' . PHP_EOL;
            }
            
            $sitemap .= '</urlset>';
            
            File::put(public_path('sitemap.xml'), $sitemap);
            
            $this->sitemapStatus['last_generated'] = now();
            $this->sitemapStatus['total_urls'] = $pages->count();
            
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Sitemap generated successfully with ' . $pages->count() . ' URLs'
            ]);
            
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Failed to generate sitemap: ' . $e->getMessage()
            ]);
        }
    }

    private function generateSeoSuggestions()
    {
        return [
            'Add structured data (Schema.org) markup to improve search appearance',
            'Optimize page loading speed - compress images and minify CSS/JS',
            'Improve internal linking between related pages',
            'Add breadcrumbs for better navigation and SEO',
            'Optimize images with descriptive alt text and proper file names',
            'Create topic clusters around your main keywords',
            'Add FAQ sections to target long-tail keywords',
            'Improve mobile page speed and Core Web Vitals scores',
        ];
    }

    private function getCrawlErrors()
    {
        // In a real implementation, this would integrate with Google Search Console API
        return [
            ['url' => '/old-page', 'error' => '404 Not Found', 'last_seen' => '2 days ago', 'severity' => 'high'],
            ['url' => '/broken-link', 'error' => '500 Server Error', 'last_seen' => '1 week ago', 'severity' => 'critical'],
            ['url' => '/slow-page', 'error' => 'Slow Loading', 'last_seen' => '3 days ago', 'severity' => 'medium'],
        ];
    }

    private function getSitemapStatus()
    {
        $sitemapPath = public_path('sitemap.xml');
        
        return [
            'exists' => File::exists($sitemapPath),
            'last_generated' => File::exists($sitemapPath) ? 
                \Carbon\Carbon::createFromTimestamp(File::lastModified($sitemapPath)) : null,
            'total_urls' => Page::published()->count(),
            'file_size' => File::exists($sitemapPath) ? File::size($sitemapPath) : 0,
            'status' => File::exists($sitemapPath) ? 'healthy' : 'missing',
        ];
    }

    private function calculateOverallSeoScore()
    {
        $score = 100;
        $totalPages = Page::count();
        
        if ($totalPages == 0) return 0;
        
        // Deduct for missing meta descriptions
        $missingMeta = Page::whereNull('meta_description')->orWhere('meta_description', '')->count();
        $score -= ($missingMeta / $totalPages) * 30;
        
        // Deduct for missing titles
        $missingTitles = Page::whereNull('meta_title')->orWhere('meta_title', '')->count();
        $score -= ($missingTitles / $totalPages) * 20;
        
        // Deduct for duplicate titles
        $duplicates = Page::select('title')->groupBy('title')->havingRaw('COUNT(*) > 1')->count();
        $score -= ($duplicates / $totalPages) * 15;
        
        // Deduct if no sitemap
        if (!$this->sitemapStatus['exists']) {
            $score -= 10;
        }
        
        return max(0, round($score));
    }

    public function runSeoAudit()
    {
        $this->loadSeoData();
        
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'SEO audit completed successfully'
        ]);
    }

    public function exportSeoReport()
    {
        try {
            $report = [
                'generated_at' => now()->toDateTimeString(),
                'overall_score' => $this->seoScore,
                'total_pages' => Page::count(),
                'issues' => $this->seoIssues,
                'suggestions' => $this->seoSuggestions,
                'crawl_errors' => $this->crawlErrors,
            ];
            
            $filename = 'seo-report-' . now()->format('Y-m-d') . '.json';
            
            $this->dispatch('download-file', [
                'filename' => $filename,
                'content' => json_encode($report, JSON_PRETTY_PRINT),
                'type' => 'application/json'
            ]);
            
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Failed to export SEO report'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.pages.partials.seo-manager');
    }
}