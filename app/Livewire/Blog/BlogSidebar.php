<?php

namespace App\Livewire\Blog;

use App\Models\Content\BlogPost;
use App\Models\Content\BlogCategory;
use App\Models\Admin\NewsletterSubscriber;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;
use Illuminate\Support\Facades\Cache;

class BlogSidebar extends Component
{
    public $currentPost = null;
    public $currentCategory;
    public $subscribeEmail = '';
    public $subscribeSuccess = false;
    public $subscribeError = '';

    public function mount($currentPost = null, $currentCategory = null)
    {
        $this->currentPost = $currentPost;
        $this->currentCategory = $currentCategory;
    }

    public function subscribe()
    {
        // Validate the email
        $validator = Validator::make(
            ['email' => $this->subscribeEmail],
            ['email' => 'required|email|max:255']
        );

        if ($validator->fails()) {
            $this->subscribeError = 'Please enter a valid email address.';
            return;
        }

        try {
            // Check if already subscribed
            $existing = NewsletterSubscriber::where('email', $this->subscribeEmail)->first();

            if ($existing) {
                if ($existing->status === NewsletterSubscriber::STATUS_UNSUBSCRIBED) {
                    $existing->update([
                        'status' => NewsletterSubscriber::STATUS_ACTIVE,
                        'unsubscribed_at' => null
                    ]);
                }
                $this->subscribeSuccess = true;
                $this->subscribeEmail = '';
                $this->subscribeError = '';
                return;
            }

            // Create new subscriber
            NewsletterSubscriber::create([
                'email' => $this->subscribeEmail,
                'status' => NewsletterSubscriber::STATUS_ACTIVE,
                'source' => 'blog_sidebar',
                'subscribed_at' => now(),
                'ip_address' => request()->ip(),
            ]);

            $this->subscribeSuccess = true;
            $this->subscribeEmail = '';
            $this->subscribeError = '';

        } catch (\Exception $e) {
            $this->subscribeError = 'Sorry, there was an error subscribing. Please try again.';
            logger()->error('Newsletter subscription error: ' . $e->getMessage());
        }
    }

    public function render()
    {
        // Get categories with post counts
        $categories = Cache::remember('blog_sidebar_categories', 3600, function () {
            return BlogCategory::active()
                ->withCount(['publishedPosts'])
                ->having('published_posts_count', '>', 0)
                ->ordered()
                ->get();
        });

        // Get popular posts
        $popularPosts = Cache::remember('blog_sidebar_popular', 1800, function () {
            return BlogPost::published()
                ->orderByDesc('views_count')
                ->take(5)
                ->get(['id', 'title', 'slug', 'views_count', 'published_at']);
        });

        // Get recent posts
        $recentPosts = Cache::remember('blog_sidebar_recent', 1800, function () {
            return BlogPost::published()
                ->orderByDesc('published_at')
                ->take(5)
                ->get(['id', 'title', 'slug', 'published_at']);
        });

        // Get popular tags
        $popularTags = Cache::remember('blog_sidebar_tags', 3600, function () {
            return BlogPost::published()
                ->whereNotNull('tags')
                ->get()
                ->pluck('tags')
                ->flatten()
                ->countBy()
                ->sortDesc()
                ->take(20)
                ->keys()
                ->toArray();
        });

        return view('livewire.blog.blog-sidebar', [
            'categories' => $categories,
            'popularPosts' => $popularPosts,
            'recentPosts' => $recentPosts,
            'popularTags' => $popularTags,
        ]);
    }
}