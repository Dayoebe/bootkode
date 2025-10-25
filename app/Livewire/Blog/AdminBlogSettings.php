<?php

namespace App\Livewire\Blog;

use App\Models\Content\BlogSetting;
use Livewire\Component;

class AdminBlogSettings extends Component
{
    public $activeTab = 'general';
    public $isSeoMode = false;
    
    // General Settings
    public $blog_title = '';
    public $blog_description = '';
    public $posts_per_page = 12;
    public $allow_guest_comments = true;
    public $auto_approve_comments = false;
    public $show_author_bio = true;
    public $show_reading_time = true;
    public $enable_reactions = true;
    public $enable_bookmarks = true;
    
    // SEO Settings
    public $default_meta_title = '';
    public $default_meta_description = '';
    public $default_meta_keywords = [];
    public $og_image = '';
    public $enable_sitemap = true;
    public $enable_rss = true;
    public $robots_txt_additions = '';
    
    // Social Settings
    public $facebook_url = '';
    public $twitter_url = '';
    public $linkedin_url = '';
    public $instagram_url = '';
    public $enable_social_sharing = true;
    
    // Email Settings
    public $notify_admin_new_comment = true;
    public $notify_admin_new_post = false;
    public $admin_email = '';
    public $from_email = '';
    public $from_name = '';
    
    // UI state
    public $newKeyword = '';

    public function mount()
    {
        $this->isSeoMode = request()->routeIs('admin.blog.seo');
        $this->activeTab = $this->isSeoMode ? 'seo' : 'general';
        $this->loadSettings();
    }

    public function loadSettings()
    {
        // General Settings
        $this->blog_title = BlogSetting::get('blog_title', config('app.name') . ' Blog');
        $this->blog_description = BlogSetting::get('blog_description', 'Discover insights, stories, and knowledge from our experts');
        $this->posts_per_page = BlogSetting::get('posts_per_page', 12);
        $this->allow_guest_comments = BlogSetting::get('allow_guest_comments', true);
        $this->auto_approve_comments = BlogSetting::get('auto_approve_comments', false);
        $this->show_author_bio = BlogSetting::get('show_author_bio', true);
        $this->show_reading_time = BlogSetting::get('show_reading_time', true);
        $this->enable_reactions = BlogSetting::get('enable_reactions', true);
        $this->enable_bookmarks = BlogSetting::get('enable_bookmarks', true);

        // SEO Settings
        $this->default_meta_title = BlogSetting::get('default_meta_title', '');
        $this->default_meta_description = BlogSetting::get('default_meta_description', '');
        $this->default_meta_keywords = BlogSetting::get('default_meta_keywords', []);
        $this->og_image = BlogSetting::get('og_image', '');
        $this->enable_sitemap = BlogSetting::get('enable_sitemap', true);
        $this->enable_rss = BlogSetting::get('enable_rss', true);
        $this->robots_txt_additions = BlogSetting::get('robots_txt_additions', '');

        // Social Settings
        $this->facebook_url = BlogSetting::get('facebook_url', '');
        $this->twitter_url = BlogSetting::get('twitter_url', '');
        $this->linkedin_url = BlogSetting::get('linkedin_url', '');
        $this->instagram_url = BlogSetting::get('instagram_url', '');
        $this->enable_social_sharing = BlogSetting::get('enable_social_sharing', true);

        // Email Settings
        $this->notify_admin_new_comment = BlogSetting::get('notify_admin_new_comment', true);
        $this->notify_admin_new_post = BlogSetting::get('notify_admin_new_post', false);
        $this->admin_email = BlogSetting::get('admin_email', '');
        $this->from_email = BlogSetting::get('from_email', '');
        $this->from_name = BlogSetting::get('from_name', '');
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function addKeyword()
    {
        if ($this->newKeyword && !in_array($this->newKeyword, $this->default_meta_keywords)) {
            $this->default_meta_keywords[] = trim($this->newKeyword);
            $this->newKeyword = '';
        }
    }

    public function removeKeyword($index)
    {
        unset($this->default_meta_keywords[$index]);
        $this->default_meta_keywords = array_values($this->default_meta_keywords);
    }

    public function save()
    {
        $this->validate([
            'blog_title' => 'required|max:100',
            'blog_description' => 'required|max:500',
            'posts_per_page' => 'required|integer|min:6|max:50',
            'admin_email' => 'nullable|email',
            'from_email' => 'nullable|email',
        ]);

        // Save all settings
        $settings = [
            // General
            'blog_title' => $this->blog_title,
            'blog_description' => $this->blog_description,
            'posts_per_page' => $this->posts_per_page,
            'allow_guest_comments' => $this->allow_guest_comments,
            'auto_approve_comments' => $this->auto_approve_comments,
            'show_author_bio' => $this->show_author_bio,
            'show_reading_time' => $this->show_reading_time,
            'enable_reactions' => $this->enable_reactions,
            'enable_bookmarks' => $this->enable_bookmarks,
            
            // SEO
            'default_meta_title' => $this->default_meta_title,
            'default_meta_description' => $this->default_meta_description,
            'default_meta_keywords' => $this->default_meta_keywords,
            'og_image' => $this->og_image,
            'enable_sitemap' => $this->enable_sitemap,
            'enable_rss' => $this->enable_rss,
            'robots_txt_additions' => $this->robots_txt_additions,
            
            // Social
            'facebook_url' => $this->facebook_url,
            'twitter_url' => $this->twitter_url,
            'linkedin_url' => $this->linkedin_url,
            'instagram_url' => $this->instagram_url,
            'enable_social_sharing' => $this->enable_social_sharing,
            
            // Email
            'notify_admin_new_comment' => $this->notify_admin_new_comment,
            'notify_admin_new_post' => $this->notify_admin_new_post,
            'admin_email' => $this->admin_email,
            'from_email' => $this->from_email,
            'from_name' => $this->from_name,
        ];

        foreach ($settings as $key => $value) {
            BlogSetting::set($key, $value);
        }

        session()->flash('message', 'Settings saved successfully!');
    }

    public function render()
    {
        return view('livewire.blog.admin-blog-settings')->layout('layouts.dashboard');
    }
}