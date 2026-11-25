<?php

namespace App\Livewire\Pages\Partials;

use App\Livewire\Concerns\AuthorizesPublishingAdministration;
use App\Models\Content\Page;
use App\Services\Authorization\PublishingAccess;
use App\Services\Content\PageAnalyticsProjection;
use Livewire\Component;
use Symfony\Component\HttpFoundation\StreamedResponse;

class Analytics extends Component
{
    use AuthorizesPublishingAdministration;

    public string $analyticsRange = '30';

    public array $analyticsData = [];

    public array $topPages = [];

    public array $recentActivity = [];

    protected function publishingAbility(): string
    {
        return PublishingAccess::PAGE_ANALYTICS;
    }

    public function mount(PageAnalyticsProjection $projection): void
    {
        $this->loadAnalyticsData($projection);
    }

    public function loadAnalyticsData(PageAnalyticsProjection $projection): void
    {
        $days = $this->validatedDays();

        $this->analyticsData = $projection->summary();
        $this->topPages = $projection->topPages()->all();
        $this->recentActivity = $projection->recentActivity($days)->all();
    }

    public function updateAnalyticsRange(PageAnalyticsProjection $projection): void
    {
        $this->loadAnalyticsData($projection);
    }

    public function exportAnalytics(PageAnalyticsProjection $projection): StreamedResponse
    {
        $pages = $projection->topPages(1000);

        return response()->streamDownload(function () use ($pages): void {
            $stream = fopen('php://output', 'wb');
            fputcsv($stream, ['Page title', 'Slug', 'Recorded views', 'Status']);

            foreach ($pages as $page) {
                fputcsv($stream, [$page['title'], $page['slug'], $page['views'], $page['status']]);
            }

            fclose($stream);
        }, 'page-analytics-'.now()->format('Y-m-d').'.csv', ['Content-Type' => 'text/csv']);
    }

    public function viewPageDetails(int $pageId): mixed
    {
        $page = Page::findOrFail($pageId);

        return redirect()->route('page.show', $page->slug);
    }

    private function validatedDays(): int
    {
        $this->validate(['analyticsRange' => 'required|in:7,30,90,365']);

        return (int) $this->analyticsRange;
    }

    public function render()
    {
        return view('livewire.pages.partials.analytics');
    }
}
