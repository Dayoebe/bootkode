<?php

namespace App\Livewire\Blog;

use App\Models\BlogCategory;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Str;

class AdminBlogCategories extends Component
{
    use WithPagination;

    public $search = '';
    public $showModal = false;
    public $showDeleteModal = false;
    public $editMode = false;
    public $categoryToDelete = null;
    
    // Form fields
    public $categoryId = null;
    public $name = '';
    public $slug = '';
    public $description = '';
    public $color = '#3B82F6';
    public $meta_title = '';
    public $meta_description = '';
    public $is_active = true;
    public $sort_order = 0;

    protected $rules = [
        'name' => 'required|min:2|max:100',
        'slug' => 'required|min:2|max:100',
        'description' => 'nullable|max:500',
        'color' => 'required|regex:/^#[0-9A-Fa-f]{6}$/',
        'meta_title' => 'nullable|max:60',
        'meta_description' => 'nullable|max:500',
        'is_active' => 'boolean',
        'sort_order' => 'integer|min:0',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedName()
    {
        if (!$this->editMode || empty($this->slug)) {
            $this->slug = Str::slug($this->name);
        }
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEditModal($categoryId)
    {
        $category = BlogCategory::findOrFail($categoryId);
        
        $this->categoryId = $category->id;
        $this->name = $category->name;
        $this->slug = $category->slug;
        $this->description = $category->description;
        $this->color = $category->color;
        $this->meta_title = $category->meta_title;
        $this->meta_description = $category->meta_description;
        $this->is_active = $category->is_active;
        $this->sort_order = $category->sort_order;
        
        $this->editMode = true;
        $this->showModal = true;
    }

    public function confirmDelete($categoryId)
    {
        $this->categoryToDelete = $categoryId;
        $this->showDeleteModal = true;
    }

    public function deleteCategory()
    {
        if ($this->categoryToDelete) {
            $category = BlogCategory::find($this->categoryToDelete);
            
            if ($category) {
                // Check if category has posts
                if ($category->posts()->count() > 0) {
                    session()->flash('error', 'Cannot delete category with existing posts. Please move or delete the posts first.');
                } else {
                    $category->delete();
                    session()->flash('message', 'Category deleted successfully!');
                }
            }
        }
        
        $this->showDeleteModal = false;
        $this->categoryToDelete = null;
    }

    public function saveCategory()
    {
        $this->validate();

        // Check for unique slug
        $slugExists = BlogCategory::where('slug', $this->slug)
            ->when($this->categoryId, function ($query) {
                return $query->where('id', '!=', $this->categoryId);
            })
            ->exists();

        if ($slugExists) {
            $this->addError('slug', 'This slug is already taken.');
            return;
        }

        $data = [
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'color' => $this->color,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'is_active' => $this->is_active,
            'sort_order' => $this->sort_order,
        ];

        if ($this->categoryId) {
            BlogCategory::find($this->categoryId)->update($data);
            session()->flash('message', 'Category updated successfully!');
        } else {
            BlogCategory::create($data);
            session()->flash('message', 'Category created successfully!');
        }

        $this->closeModal();
    }

    public function toggleStatus($categoryId)
    {
        $category = BlogCategory::find($categoryId);
        
        if ($category) {
            $category->update(['is_active' => !$category->is_active]);
            $status = $category->is_active ? 'activated' : 'deactivated';
            session()->flash('message', "Category {$status} successfully!");
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->reset([
            'categoryId', 'name', 'slug', 'description', 'color', 
            'meta_title', 'meta_description', 'is_active', 'sort_order'
        ]);
        $this->color = '#3B82F6';
        $this->is_active = true;
        $this->sort_order = 0;
        $this->editMode = false;
    }

    public function render()
    {
        $query = BlogCategory::withCount('posts');

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
        }

        $categories = $query->ordered()->paginate(15);

        return view('livewire.blog.admin-blog-categories', [
            'categories' => $categories
        ])->layout('layouts.dashboard');
    }
}
