<?php

namespace App\Services\Content;

use App\Models\Content\Page;
use Illuminate\Support\Collection;

final class PageAnalyticsProjection
{
    /** @return array<string, int|bool|string> */
    public function summary(): array
    {
        return [
            'total_views' => (int) Page::sum('view_count'),
            'total_pages' => Page::count(),
            'published_pages' => Page::published()->count(),
            'draft_pages' => Page::where('status', 'draft')->count(),
            'detailed_analytics_available' => false,
            'unavailable_reason' => 'Visitor, session, conversion, revenue, device, referrer and Core Web Vitals events are not currently collected.',
        ];
    }

    /** @return Collection<int, array<string, mixed>> */
    public function topPages(int $limit = 10): Collection
    {
        return Page::published()
            ->orderByDesc('view_count')
            ->orderBy('title')
            ->limit($limit)
            ->get(['id', 'title', 'slug', 'view_count', 'status', 'created_at'])
            ->map(fn (Page $page): array => [
                'id' => $page->id,
                'title' => $page->title,
                'slug' => $page->slug,
                'views' => (int) $page->view_count,
                'status' => $page->status,
                'created_at' => $page->created_at,
            ]);
    }

    /** @return Collection<int, array<string, mixed>> */
    public function recentActivity(int $days, int $limit = 20): Collection
    {
        return Page::with(['creator:id,name,profile_picture', 'updater:id,name,profile_picture'])
            ->where('updated_at', '>=', now()->subDays($days))
            ->latest('updated_at')
            ->limit($limit)
            ->get()
            ->map(function (Page $page): array {
                $isNew = $page->created_at->diffInMinutes($page->updated_at) < 5;
                $actor = $isNew ? $page->creator : $page->updater;

                return [
                    'id' => $page->id,
                    'action' => $isNew ? 'created' : 'updated',
                    'page_title' => $page->title,
                    'page_slug' => $page->slug,
                    'user_name' => $actor?->name ?? 'System',
                    'user_avatar' => $actor?->profile_picture,
                    'time' => $page->updated_at->diffForHumans(),
                    'status' => $page->status,
                ];
            });
    }
}
