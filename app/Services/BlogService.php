<?php

namespace App\Services;

use App\Models\BlogPost;
use App\Models\BlogCategory;
use App\Models\BlogSetting;
use Illuminate\Support\Facades\Cache;

class BlogService
{
    public static function getFeaturedPosts($limit = 3)
    {
        return Cache::remember("featured_posts_{$limit}", 1800, function () use ($limit) {
            return BlogPost::with(['author', 'category'])
                ->published()
                ->featured()
                ->orderByDesc('published_at')
                ->take($limit)
                ->get();
        });
    }

    public static function getLatestPosts($limit = 10, $excludeIds = [])
    {
        return Cache::remember("latest_posts_{$limit}_" . md5(serialize($excludeIds)), 1800, function () use ($limit, $excludeIds) {
            return BlogPost::with(['author', 'category'])
                ->published()
                ->when(!empty($excludeIds), function ($query) use ($excludeIds) {
                    return $query->whereNotIn('id', $excludeIds);
                })
                ->orderByDesc('published_at')
                ->take($limit)
                ->get();
        });
    }

    public static function getRelatedPosts(BlogPost $post, $limit = 4)
    {
        return Cache::remember("related_posts_{$post->id}_{$limit}", 3600, function () use ($post, $limit) {
            $query = BlogPost::published()
                ->where('id', '!=', $post->id);

            // Priority: same category
            if ($post->category_id) {
                $query->where('category_id', $post->category_id);
            }

            // Secondary: similar tags
            if ($post->tags && count($post->tags) > 0) {
                $query->orWhere(function ($q) use ($post) {
                    foreach ($post->tags as $tag) {
                        $q->orWhereJsonContains('tags', $tag);
                    }
                });
            }

            return $query->orderByDesc('views_count')
                ->take($limit)
                ->get();
        });
    }

    public static function getBlogStats()
    {
        return Cache::remember('blog_stats', 3600, function () {
            return [
                'total_posts' => BlogPost::published()->count(),
                'total_categories' => BlogCategory::active()->count(),
                'total_views' => BlogPost::sum('views_count'),
                'total_likes' => BlogPost::sum('likes_count'),
            ];
        });
    }

    public static function generateSitemap()
    {
        $posts = BlogPost::published()
            ->select('slug', 'updated_at')
            ->orderByDesc('updated_at')
            ->get();

        $categories = BlogCategory::active()
            ->select('slug', 'updated_at')
            ->orderByDesc('updated_at')
            ->get();

        return [
            'posts' => $posts,
            'categories' => $categories,
            'last_modified' => $posts->first()?->updated_at ?? now(),
        ];
    }

    public static function getPopularTags($limit = 20)
    {
        return Cache::remember("popular_tags_{$limit}", 3600, function () use ($limit) {
            return BlogPost::published()
                ->whereNotNull('tags')
                ->get()
                ->pluck('tags')
                ->flatten()
                ->countBy()
                ->sortDesc()
                ->take($limit)
                ->map(function ($count, $tag) {
                    return [
                        'name' => $tag,
                        'count' => $count,
                        'slug' => \Str::slug($tag),
                    ];
                })
                ->values()
                ->toArray();
        });
    }

    public static function clearBlogCache()
    {
        $cacheKeys = [
            'blog_categories_active',
            'blog_popular_tags',
            'blog_recent_posts',
            'featured_posts_3',
            'latest_posts_10_*',
            'blog_sidebar_*',
            'blog_stats',
            'popular_tags_*',
        ];

        foreach ($cacheKeys as $key) {
            if (str_contains($key, '*')) {
                // Clear pattern-based cache keys
                Cache::flush(); // For simplicity, you might want a more selective approach
            } else {
                Cache::forget($key);
            }
        }
    }
}