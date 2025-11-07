<?php

namespace App\Livewire\Blog;

use App\Models\Content\BlogPost;
use App\Models\Content\BlogCategory;
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
    public $category_ids = []; // FIXED: Back to multiple categories
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
    public $newTag = '';
    public $newKeyword = '';
    public $removeExistingImage = false;

    protected function rules()
    {
        $rules = [
            'title' => 'required|min:5|max:255',
            'slug' => 'required|min:5|max:255|unique:blog_posts,slug' . ($this->isEdit ? ',' . $this->post->id : ''),
            'excerpt' => 'nullable|max:500',
            'content' => 'required|min:100',
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'exists:blog_categories,id',
            'status' => 'required|in:draft,published,scheduled',
            'published_at' => 'nullable|date',
            'featured_image' => 'nullable|image|max:2048',
            'meta_title' => 'nullable|max:255',
            'meta_description' => 'nullable|max:1000',
            'allow_comments' => 'boolean',
            'is_featured' => 'boolean',
        ];

        return $rules;
    }

    protected $messages = [
        'content.required' => 'Post content is required.',
        'content.min' => 'Post content must be at least 100 characters.',
        'slug.unique' => 'This slug is already taken. Please use a different one.',
        'featured_image.max' => 'Image size must not exceed 2MB.',
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
            
            // FIXED: Properly load all fields
            $this->title = $this->post->title;
            $this->slug = $this->post->slug;
            $this->excerpt = $this->post->excerpt;
            $this->content = $this->post->content; // Critical for Trix
            $this->status = $this->post->status;
            $this->allow_comments = $this->post->allow_comments;
            $this->is_featured = $this->post->is_featured;
            
            $this->existing_image = $this->post->featured_image;
            
            // Handle category - support both single and stored as array
            if ($this->post->category_id) {
                $this->category_ids = [$this->post->category_id];
            }
            
            // Load tags stored in tags field
            $this->tags = $this->post->tags ?? [];
            
            $this->meta_title = $this->post->meta_title ?: $this->post->title;
            $this->meta_description = $this->post->meta_description;
            $this->meta_keywords = $this->post->meta_keywords ?? [];
            
            $this->published_at = $this->post->published_at?->format('Y-m-d\TH:i');
            
            $this->featured_image = null;
        }
    }

    public function updatedTitle()
    {
        if (!$this->isEdit || empty($this->slug)) {
            $this->slug = Str::slug($this->title);
        }
        
        if (empty($this->meta_title)) {
            $this->meta_title = $this->title;
        }
    }

    public function updatedStatus()
    {
        if ($this->status === 'scheduled' && empty($this->published_at)) {
            $this->published_at = now()->addHour()->format('Y-m-d\TH:i');
        }
    }

    public function generateSlug()
    {
        $this->slug = Str::slug($this->title);
    }

    public function addTag()
    {
        $tag = trim($this->newTag);
        if ($tag && !in_array($tag, $this->tags)) {
            $this->tags[] = $tag;
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
        $keyword = trim($this->newKeyword);
        if ($keyword && !in_array($keyword, $this->meta_keywords)) {
            $this->meta_keywords[] = $keyword;
            $this->newKeyword = '';
        }
    }

    public function removeKeyword($index)
    {
        unset($this->meta_keywords[$index]);
        $this->meta_keywords = array_values($this->meta_keywords);
    }

    // FIXED: Add method to remove category
    public function removeCategory($index)
    {
        unset($this->category_ids[$index]);
        $this->category_ids = array_values($this->category_ids);
    }

    public function toggleRemoveImage()
    {
        $this->removeExistingImage = !$this->removeExistingImage;
        if ($this->removeExistingImage) {
            $this->featured_image = null;
        }
    }

    public function save($action = 'save')
    {
        // Custom validation for scheduled posts
        if ($this->status === 'scheduled') {
            if (empty($this->published_at)) {
                $this->addError('published_at', 'Scheduled posts must have a publish date and time.');
                return;
            }
            
            if (now()->greaterThan($this->published_at)) {
                $this->addError('published_at', 'Scheduled publish time must be in the future.');
                return;
            }
        }

        // Validate
        try {
            $this->validate();
        } catch (\Illuminate\Validation\ValidationException $e) {
            session()->flash('error', 'Please fix the validation errors below.');
            throw $e;
        }

        // Prepare data
        $data = [
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => $this->excerpt,
            'content' => $this->content,
            'category_id' => !empty($this->category_ids) ? $this->category_ids[0] : null, // Primary category
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'meta_keywords' => $this->meta_keywords,
            'tags' => array_merge($this->tags, array_slice($this->category_ids, 1)), // Additional categories as tags
            'allow_comments' => $this->allow_comments,
            'is_featured' => $this->is_featured,
            'status' => $this->status,
        ];

        // Handle published_at based on status
        if ($this->status === 'published') {
            $data['published_at'] = now();
        } elseif ($this->status === 'scheduled') {
            $data['published_at'] = $this->published_at;
        } else {
            $data['published_at'] = $this->isEdit ? $this->post->published_at : null;
        }

        // Handle image upload and removal
        if ($this->removeExistingImage && $this->existing_image) {
            Storage::disk('public')->delete($this->existing_image);
            $data['featured_image'] = null;
            $this->existing_image = '';
        } elseif ($this->featured_image && is_object($this->featured_image)) {
            try {
                if ($this->existing_image) {
                    Storage::disk('public')->delete($this->existing_image);
                }

                $filename = time() . '_' . Str::random(10) . '.' . $this->featured_image->getClientOriginalExtension();
                $path = $this->featured_image->storeAs('blog/images', $filename, 'public');
                $data['featured_image'] = $path;
                $this->existing_image = $path;
            } catch (\Exception $e) {
                session()->flash('error', 'Error uploading image: ' . $e->getMessage());
                logger()->error('Image upload error', ['error' => $e->getMessage()]);
                return;
            }
        } else {
            if ($this->isEdit && $this->existing_image && !$this->removeExistingImage) {
                $data['featured_image'] = $this->existing_image;
            }
        }

        try {
            if ($this->isEdit) {
                $this->post->update($data);
                $message = 'Post updated successfully!';
            } else {
                $data['author_id'] = auth()->id();
                $this->post = BlogPost::create($data);
                $this->isEdit = true;
                $this->existing_image = $this->post->featured_image;
                $message = 'Post created successfully!';
            }

            // Reset upload states
            $this->featured_image = null;
            $this->removeExistingImage = false;

            session()->flash('message', $message);

            if ($action === 'save_and_continue') {
                return redirect()->route('admin.blog.posts.edit', $this->post->slug);
            }

            return redirect()->route('admin.blog.posts.index');
        } catch (\Exception $e) {
            session()->flash('error', 'Error saving post: ' . $e->getMessage());
            logger()->error('Blog post save error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
        }
    }

    public function saveDraft()
    {
        $this->status = 'draft';
        $this->save();
    }

    public function publish()
    {
        $this->status = 'published';
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