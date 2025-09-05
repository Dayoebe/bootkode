<?php

namespace App\Livewire\Blog;

use App\Models\BlogPost;
use App\Models\BlogCategory;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminBlogPostForm extends Component
{
    use WithFileUploads;

    public $post;
    public $isEdit = false;

    // Form fields
    public $title = '';
    public $slug = '';
    public $excerpt = '';
    public $content = '';
    public $category_id = '';
    public $status = 'draft';
    public $published_at = '';
    public $featured_image;
    public $existing_image = '';
    public $meta_title = '';
    public $meta_description = '';
    public $meta_keywords = [];
    public $tags = [];
    public $allow_comments = true;
    public $is_featured = false;

    // UI state
    public $showSeoSection = false;
    public $showScheduling = false;
    public $newTag = '';
    public $newKeyword = '';

    protected $rules = [
        'title' => 'required|min:5|max:255',
        'slug' => 'required|min:5|max:255',
        'excerpt' => 'nullable|max:500',
        'content' => 'required|min:100',
        'category_id' => 'nullable|exists:blog_categories,id',
        'status' => 'required|in:draft,published,scheduled',
        'published_at' => 'nullable|date|after_or_equal:now',
        'featured_image' => 'nullable|image|max:2048',
        'meta_title' => 'nullable|max:60',
        'meta_description' => 'nullable|max:500',
        'allow_comments' => 'boolean',
        'is_featured' => 'boolean',
    ];

    public function mount($post = null)
    {
        if ($post) {
            if (is_string($post)) {
                $this->post = BlogPost::where('slug', $post)->firstOrFail();
            } else {
                $this->post = BlogPost::findOrFail($post);
            }

            $this->isEdit = true;
            $this->fill($this->post->toArray());
            
            // FIXED: Properly set and maintain existing image
            $this->existing_image = $this->post->featured_image;
            
            $this->meta_keywords = $this->post->meta_keywords ?? [];
            $this->tags = $this->post->tags ?? [];
            $this->published_at = $this->post->published_at?->format('Y-m-d\TH:i');
        } else {
            // Only set default published_at for new posts when status is published or scheduled
            $this->published_at = '';
        }
    }

    // ADDED: Method to ensure existing image persists across requests
    public function hydrate()
    {
        if ($this->isEdit && $this->post && !$this->existing_image) {
            $this->existing_image = $this->post->featured_image;
        }
    }

    public function updatedTitle()
    {
        if (!$this->isEdit || empty($this->slug)) {
            $this->slug = Str::slug($this->title);
        }
    }

    // Updated method to handle status changes and set appropriate published_at
    public function updatedStatus()
    {
        if ($this->status === 'scheduled' && empty($this->published_at)) {
            $this->published_at = now()->addDay()->format('Y-m-d\TH:i');
        } elseif ($this->status === 'draft') {
            // Keep the published_at if it exists, but don't require it
        }
        // REMOVED: Auto-setting published_at for 'published' status to avoid showing time field
    }

    public function generateSlug()
    {
        $this->slug = Str::slug($this->title);
    }

    public function addTag()
    {
        if ($this->newTag && !in_array($this->newTag, $this->tags)) {
            $this->tags[] = trim($this->newTag);
            $this->newTag = '';
        }
    }

    public function removeTag($index)
    {
        unset($this->tags[$index]);
        $this->tags = array_values($this->tags);
    }

    public function addKeyword()
    {
        if ($this->newKeyword && !in_array($this->newKeyword, $this->meta_keywords)) {
            $this->meta_keywords[] = trim($this->newKeyword);
            $this->newKeyword = '';
        }
    }

    public function removeKeyword($index)
    {
        unset($this->meta_keywords[$index]);
        $this->meta_keywords = array_values($this->meta_keywords);
    }

    public function save($action = 'save')
    {
        // Custom validation for scheduled posts
        if ($this->status === 'scheduled' && empty($this->published_at)) {
            $this->addError('published_at', 'Scheduled posts must have a publish date.');
            return;
        }

        $this->validate();

        // Prepare data
        $data = [
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => $this->excerpt,
            'content' => $this->content,
            'category_id' => $this->category_id ?: null,
            'status' => $this->status,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'meta_keywords' => $this->meta_keywords,
            'tags' => $this->tags,
            'allow_comments' => $this->allow_comments,
            'is_featured' => $this->is_featured,
        ];

        // Handle published_at based on status
        if ($this->status === 'published') {
            // FIXED: Always set to current time for published posts (no user input)
            $data['published_at'] = now();
        } elseif ($this->status === 'scheduled') {
            $data['published_at'] = $this->published_at;
        } else {
            // For draft status, keep existing published_at if editing, otherwise null
            $data['published_at'] = $this->isEdit ? $this->post->published_at : null;
        }

        // FIXED: Improved image handling logic
        if ($this->featured_image) {
            // Delete old image if exists and we're uploading a new one
            if ($this->existing_image) {
                Storage::disk('public')->delete($this->existing_image);
            }

            $filename = time() . '_' . Str::random(10) . '.' . $this->featured_image->getClientOriginalExtension();
            $path = $this->featured_image->storeAs('blog/images', $filename, 'public');
            $data['featured_image'] = $path;
            
            // Update existing_image to new path for consistency
            $this->existing_image = $path;
        } else {
            // FIXED: Always preserve existing image if no new image uploaded
            if ($this->isEdit && $this->existing_image) {
                $data['featured_image'] = $this->existing_image;
            }
        }

        if ($this->isEdit) {
            $this->post->update($data);
            $message = 'Post updated successfully!';
        } else {
            $data['author_id'] = auth()->id();
            $this->post = BlogPost::create($data);
            $this->existing_image = $this->post->featured_image;
            $message = 'Post created successfully!';
        }

        session()->flash('message', $message);

        if ($action === 'save_and_continue') {
            return redirect()->route('admin.blog.posts.edit', $this->post);
        }

        return redirect()->route('admin.blog.posts.index');
    }

    public function saveDraft()
    {
        $this->status = 'draft';
        $this->save();
    }

    public function publish()
    {
        $this->status = 'published';
        // No need to set published_at here - it's handled in save() method
        $this->save();
    }

    public function render()
    {
        $categories = BlogCategory::active()->ordered()->get();

        return view('livewire.blog.admin-blog-post-form', [
            'categories' => $categories
        ])->layout('layouts.dashboard');
    }
}