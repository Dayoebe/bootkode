<?php

namespace App\Livewire\Pages;

use Livewire\Component;
use App\Models\Page;
use Livewire\Attributes\Layout;

#[Layout('layouts.dashboard', [
    'title' => 'Page Management', 
    'description' => 'Create and manage SEO-friendly pages', 
    'icon' => 'fas fa-file-alt', 
    'active' => 'pages'
])]
class PageManager extends Component
{
    public $activeTab = 'all-pages';
    
    // Statistics for header display
    public $stats = [
        'total_pages' => 0,
        'published_pages' => 0,
        'draft_pages' => 0,
        'scheduled_pages' => 0,
        'total_views' => 0,
    ];

    public function mount($activeTab = null)
    {
        // Set active tab from route parameter or URL
        $this->activeTab = $activeTab ?? $this->getActiveTabFromRoute();
        $this->loadStatistics();
    }

    private function getActiveTabFromRoute()
    {
        $routeName = request()->route()->getName();
        
        return match($routeName) {
            'pages.create' => 'create-page',
            'pages.analytics' => 'analytics',
            'pages.templates' => 'templates',
            'pages.media' => 'media',
            'pages.seo' => 'seo',
            'pages.settings' => 'settings',
            default => 'all-pages'
        };
    }

    public function setActiveTab($tab)
    {
        // Navigate to appropriate route instead of just changing tab
        $routeMap = [
            'all-pages' => 'pages.index',
            'create-page' => 'pages.create',
            'analytics' => 'pages.analytics',
            'templates' => 'pages.templates',
            'media' => 'pages.media',
            'seo' => 'pages.seo',
            'settings' => 'pages.settings',
        ];

        if (isset($routeMap[$tab])) {
            return redirect()->route($routeMap[$tab]);
        }

        $this->activeTab = $tab;
    }

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

    public function render()
    {
        return view('livewire.pages.page-manager');
    }
}