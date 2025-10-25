<?php

namespace App\Livewire\Pages\Partials;

use Livewire\Component;
use App\Models\Content\Page;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Analytics extends Component
{
    public $analyticsRange = '30';
    public $analyticsData = [];
    public $topPages = [];
    public $recentActivity = [];
    public $chartData = [];
    
    public function mount()
    {
        $this->loadAnalyticsData();
    }

    public function loadAnalyticsData()
    {
        try {
            $days = (int) $this->analyticsRange;
            $startDate = now()->subDays($days);
            
            // Core metrics
            $this->analyticsData = [
                'total_views' => Page::sum('view_count'),
                'unique_visitors' => $this->calculateUniqueVisitors($days),
                'avg_session_duration' => $this->calculateAvgSessionDuration(),
                'bounce_rate' => $this->calculateBounceRate(),
                'pages_per_session' => $this->calculatePagesPerSession(),
                'top_referrers' => $this->getTopReferrers(),
                'device_breakdown' => $this->getDeviceBreakdown(),
                'page_load_speed' => $this->getPageLoadMetrics(),
                'conversion_metrics' => $this->getConversionMetrics(),
            ];

            // Chart data for views over time
            $this->chartData = $this->generateViewsChartData($days);

            // Top performing pages
            $this->topPages = Page::published()
                ->orderBy('view_count', 'desc')
                ->limit(10)
                ->get()
                ->map(function ($page) {
                    return [
                        'id' => $page->id,
                        'title' => $page->title,
                        'slug' => $page->slug,
                        'views' => $page->view_count,
                        'unique_views' => rand(($page->view_count * 0.6), $page->view_count), // Mock data
                        'avg_time' => $this->formatTime(rand(30, 300)),
                        'bounce_rate' => rand(20, 80) . '%',
                        'conversion_rate' => rand(1, 8) . '.'. rand(0, 9) . '%',
                        'status' => $page->status,
                        'created_at' => $page->created_at,
                    ];
                });

            // Recent activity
            $this->recentActivity = Page::with(['creator', 'updater'])
                ->where('updated_at', '>=', now()->subDays(30))
                ->latest('updated_at')
                ->limit(20)
                ->get()
                ->map(function ($page) {
                    $timeDiff = $page->created_at->diffInMinutes($page->updated_at);
                    $isNew = $timeDiff < 5; // If updated within 5 minutes of creation, it's new
                    
                    return [
                        'id' => $page->id,
                        'action' => $isNew ? 'created' : 'updated',
                        'page_title' => $page->title,
                        'page_slug' => $page->slug,
                        'user_name' => ($isNew ? $page->creator : $page->updater)?->name ?? 'System',
                        'user_avatar' => ($isNew ? $page->creator : $page->updater)?->avatar ?? null,
                        'time' => $page->updated_at->diffForHumans(),
                        'status' => $page->status,
                        'views_since' => rand(0, 50), // Mock data for views since action
                    ];
                });

        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Failed to load analytics data: ' . $e->getMessage()
            ]);
        }
    }

    public function updateAnalyticsRange()
    {
        $this->loadAnalyticsData();
        
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Analytics updated for last ' . $this->analyticsRange . ' days'
        ]);
    }

    public function exportAnalytics()
    {
        try {
            // Generate CSV export
            $filename = 'page-analytics-' . now()->format('Y-m-d') . '.csv';
            $csvData = $this->generateAnalyticsCsv();
            
            $this->dispatch('download-file', [
                'filename' => $filename,
                'content' => $csvData,
                'type' => 'text/csv'
            ]);
            
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Analytics exported successfully'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Export failed: ' . $e->getMessage()
            ]);
        }
    }

    public function viewPageDetails($pageId)
    {
        $page = Page::find($pageId);
        if ($page) {
            return redirect()->route('page.show', $page->slug);
        }
    }

    private function calculateUniqueVisitors($days)
    {
        // This would integrate with your analytics service (Google Analytics, etc.)
        // For now, return mock data based on total views
        $totalViews = Page::sum('view_count');
        return (int) ($totalViews * 0.7); // Assume 70% are unique visitors
    }

    private function calculateAvgSessionDuration()
    {
        // Mock data - in real implementation, get from analytics service
        $seconds = rand(120, 240);
        return $this->formatTime($seconds);
    }

    private function calculateBounceRate()
    {
        return rand(35, 65) . '.%'; // Mock data
    }

    private function calculatePagesPerSession()
    {
        return rand(15, 35) / 10; // Mock data: 1.5 - 3.5
    }

    private function getTopReferrers()
    {
        return [
            'Google' => rand(40, 60),
            'Direct' => rand(20, 35),
            'Facebook' => rand(8, 18),
            'Twitter' => rand(5, 12),
            'LinkedIn' => rand(3, 8),
            'Other' => rand(2, 10),
        ];
    }

    private function getDeviceBreakdown()
    {
        return [
            'Desktop' => rand(45, 65),
            'Mobile' => rand(25, 45),
            'Tablet' => rand(5, 15),
        ];
    }

    private function getPageLoadMetrics()
    {
        return [
            'avg_load_time' => rand(12, 35) / 10, // 1.2 - 3.5 seconds
            'fast_pages' => Page::published()->count() - rand(0, 5),
            'slow_pages' => rand(0, 5),
            'core_web_vitals_score' => rand(75, 98),
        ];
    }

    private function getConversionMetrics()
    {
        return [
            'goal_completions' => rand(50, 200),
            'conversion_rate' => rand(25, 85) / 10, // 2.5% - 8.5%
            'revenue' => rand(1000, 5000),
            'avg_order_value' => rand(50, 150),
        ];
    }

    private function generateViewsChartData($days)
    {
        $data = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $data[] = [
                'date' => $date->format('M j'),
                'views' => rand(20, 150),
                'unique_visitors' => rand(15, 120),
                'sessions' => rand(25, 180),
            ];
        }
        return $data;
    }

    private function formatTime($seconds)
    {
        $minutes = floor($seconds / 60);
        $remainingSeconds = $seconds % 60;
        return sprintf('%d:%02d', $minutes, $remainingSeconds);
    }

    private function generateAnalyticsCsv()
    {
        $csv = "Page Title,Slug,Views,Unique Views,Avg Time,Bounce Rate,Status\n";
        
        foreach ($this->topPages as $page) {
            $csv .= sprintf(
                '"%s","%s",%d,%d,"%s","%s","%s"' . "\n",
                $page['title'],
                $page['slug'],
                $page['views'],
                $page['unique_views'],
                $page['avg_time'],
                $page['bounce_rate'],
                $page['status']
            );
        }
        
        return $csv;
    }

    public function render()
    {
        return view('livewire.pages.partials.analytics');
    }
}