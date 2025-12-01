<?php

namespace App\Livewire\Blog;

use App\Models\Content\BlogPost;
use App\Models\Content\BlogCategory;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Traits\HasCloudinaryUpload;

class AdminBlogPostForm extends Component
{
    use WithFileUploads, HasCloudinaryUpload;

    public $post;
    public $isEdit = false;

    // Form fields
    public $title = '';
    public $slug = '';
    public $excerpt = '';
    public $content = '';
    public $category_ids = [];
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
            'category_ids' => 'required|array|min:1',
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
        'category_ids.required' => 'Please select at least one category.',
        'category_ids.min' => 'Please select at least one category.',
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

            $this->title = $this->post->title;
            $this->slug = $this->post->slug;
            $this->excerpt = $this->post->excerpt;
            $this->content = $this->post->content;
            $this->status = $this->post->status;
            $this->allow_comments = $this->post->allow_comments;
            $this->is_featured = $this->post->is_featured;

            $this->existing_image = $this->post->featured_image;

            $categoryIds = $this->post->all_category_ids;
            $this->category_ids = is_array($categoryIds) ? $categoryIds : [];

            $userTags = $this->post->user_tags;
            $this->tags = is_array($userTags) ? $userTags : [];

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

    public function updateContent($content)
    {
        $this->content = $content;
    }
    public function save($action = 'save_and_exit')
    {
        $this->validate();

        $data = [
            'title' => $this->title,
            'slug' => $this->slug ?: Str::slug($this->title),
            'excerpt' => $this->excerpt,
            'content' => $this->content,
            'status' => $this->status,
            'published_at' => $this->status === 'published' ? now() : ($this->published_at ?: null),
            'allow_comments' => $this->allow_comments,
            'is_featured' => $this->is_featured,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'meta_keywords' => $this->meta_keywords ? array_filter($this->meta_keywords) : null,
            'tags' => [
                'categories' => $this->category_ids,
                'tags' => array_filter($this->tags)
            ],
        ];

        // === UPLOAD FEATURED IMAGE TO CLOUDINARY (THIS WORKS 100%) ===
        if ($this->featured_image) {
            try {
                $uploadResult = $this->uploadToCloudinary(
                    $this->featured_image,                    // Livewire temporary uploaded file
                    'blog/featured-images',                   // Folder in Cloudinary
                    [
                        'public_id' => 'blog_post_' . ($this->isEdit ? $this->post->id : 'temp_' . time()),
                        'overwrite' => true,
                        'unique_filename' => false,
                        'width' => 1200,
                        'height' => 630,
                        'crop' => 'fill',
                        'gravity' => 'auto',
                        'quality' => 'auto',
                        'fetch_format' => 'auto',
                        'flags' => 'progressive',
                        'tags' => ['blog', 'featured', 'post_' . ($this->isEdit ? $this->post->id : 'new')]
                    ]
                );

                if ($uploadResult && isset($uploadResult['secure_url'])) {
                    $data['featured_image'] = $uploadResult['secure_url']; // Save full HTTPS URL
                } else {
                    session()->flash('error', 'Image uploaded but no URL returned.');
                    return;
                }
            } catch (\Exception $e) {
                session()->flash('error', 'Failed to upload image: ' . $e->getMessage());
                \Log::error('Cloudinary upload failed', ['error' => $e->getMessage()]);
                return;
            }
        }
        // If removing image
        elseif ($this->removeExistingImage) {
            $data['featured_image'] = null;
        }
        // Keep existing image if no new one uploaded
        elseif ($this->isEdit && $this->existing_image) {
            $data['featured_image'] = $this->existing_image;
        }

        // === SAVE THE POST ===
        try {
            if ($this->isEdit) {
                $this->post->update($data);
                session()->flash('message', 'Post updated successfully!');
            } else {
                $data['author_id'] = auth()->id();
                $this->post = BlogPost::create($data);
                $this->isEdit = true;
                session()->flash('message', 'Post created successfully!');
            }

            // Reset file input
            $this->featured_image = null;
            $this->removeExistingImage = false;

            if ($action === 'save_and_continue') {
                return redirect()->route('admin.blog.posts.edit', $this->post->slug);
            }

            return redirect()->route('admin.blog.posts.index');

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to save post: ' . $e->getMessage());
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