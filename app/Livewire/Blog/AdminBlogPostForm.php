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
    public $category_ids = []; // Changed to array for multiple categories
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
        return [
            'title' => 'required|min:5|max:255',
            'slug' => 'required|min:5|max:255',
            'excerpt' => 'nullable|max:500',
            'content' => 'required|min:100',
            'category_ids' => 'array',
            'category_ids.*' => 'exists:blog_categories,id',
            'status' => 'required|in:draft,published,scheduled',
            'published_at' => 'nullable|date',
            'featured_image' => 'nullable|image|max:2048',
            'meta_title' => 'nullable|max:60',
            'meta_description' => 'nullable|max:500',
            'allow_comments' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

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
            
            // FIXED: Ensure existing_image is always properly set
            $this->existing_image = $this->post->featured_image;
            
            // Handle categories (convert single category to array for backward compatibility)
            if ($this->post->category_id) {
                $this->category_ids = [$this->post->category_id];
            }
            
            $this->meta_keywords = $this->post->meta_keywords ?? [];
            $this->tags = $this->post->tags ?? [];
            $this->published_at = $this->post->published_at?->format('Y-m-d\TH:i');
            
            // Set meta_title to title if empty
            if (empty($this->meta_title)) {
                $this->meta_title = $this->title;
            }
            
            // FIXED: Reset featured_image to null to prevent conflict with existing_image
            $this->featured_image = null;
        }
    }

    public function updatedTitle()
    {
        if (!$this->isEdit || empty($this->slug)) {
            $this->slug = Str::slug($this->title);
        }
        
        // Auto-populate meta_title with title if it's empty
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

        $this->validate();

        // Prepare data
        $data = [
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => $this->excerpt,
            'content' => $this->content,
            'category_id' => !empty($this->category_ids) ? $this->category_ids[0] : null, // Use first category for backward compatibility
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'meta_keywords' => $this->meta_keywords,
            'tags' => array_merge($this->tags, array_slice($this->category_ids, 1)), // Add additional categories as tags
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

        // FIXED: Handle image upload and removal properly
        if ($this->removeExistingImage && $this->existing_image) {
            Storage::disk('public')->delete($this->existing_image);
            $data['featured_image'] = null;
            $this->existing_image = '';
        } elseif ($this->featured_image && is_object($this->featured_image)) {
            // Only handle new uploads here
            if ($this->existing_image) {
                Storage::disk('public')->delete($this->existing_image);
            }

            $filename = time() . '_' . Str::random(10) . '.' . $this->featured_image->getClientOriginalExtension();
            $path = $this->featured_image->storeAs('blog/images', $filename, 'public');
            $data['featured_image'] = $path;
            $this->existing_image = $path;
        } else {
            // FIXED: Keep existing image - don't include in update data if no changes
            if ($this->isEdit && $this->existing_image && !$this->removeExistingImage) {
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

        // Reset upload states
        $this->featured_image = null;
        $this->removeExistingImage = false;

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