<?php

namespace App\Livewire\Content\Partial;

use Livewire\Component;
use App\Models\DocumentCategory;

class DocumentCategories extends Component
{
    public $search = '';
    public $showCreateModal = false;
    public $showEditModal = false;
    public $showDeleteModal = false;
    public $selectedCategory = null;

    // Form fields
    public $name = '';
    public $description = '';
    public $color = '#3B82F6';
    public $icon = 'fas fa-folder';
    public $is_active = true;

    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string|max:500',
        'color' => 'required|string',
        'icon' => 'required|string',
        'is_active' => 'boolean',
    ];

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
        $this->resetForm();
    }

    public function openEditModal($categoryId)
    {
        $category = DocumentCategory::findOrFail($categoryId);
        
        $this->selectedCategory = $category;
        $this->name = $category->name;
        $this->description = $category->description;
        $this->color = $category->color ?? '#3B82F6';
        $this->icon = $category->icon ?? 'fas fa-folder';
        $this->is_active = $category->is_active;
        
        $this->showEditModal = true;
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->resetForm();
    }

    public function openDeleteModal($categoryId)
    {
        $this->selectedCategory = DocumentCategory::findOrFail($categoryId);
        $this->showDeleteModal = true;
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->selectedCategory = null;
    }

    public function save()
    {
        $this->validate();

        try {
            DocumentCategory::create([
                'name' => $this->name,
                'description' => $this->description,
                'color' => $this->color,
                'icon' => $this->icon,
                'is_active' => $this->is_active,
            ]);

            $this->closeCreateModal();
            session()->flash('message', 'Category created successfully!');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to create category: ' . $e->getMessage());
        }
    }

    public function update()
    {
        $this->validate();

        try {
            $this->selectedCategory->update([
                'name' => $this->name,
                'description' => $this->description,
                'color' => $this->color,
                'icon' => $this->icon,
                'is_active' => $this->is_active,
            ]);

            $this->closeEditModal();
            session()->flash('message', 'Category updated successfully!');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update category: ' . $e->getMessage());
        }
    }

    public function delete()
    {
        try {
            if (!$this->selectedCategory->canBeDeleted()) {
                session()->flash('error', 'Cannot delete category that contains documents.');
                return;
            }

            $this->selectedCategory->delete();
            $this->closeDeleteModal();
            session()->flash('message', 'Category deleted successfully!');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete category: ' . $e->getMessage());
        }
    }

    public function toggleStatus($categoryId)
    {
        $category = DocumentCategory::findOrFail($categoryId);
        $category->update(['is_active' => !$category->is_active]);
        
        $status = $category->is_active ? 'activated' : 'deactivated';
        session()->flash('message', "Category {$status} successfully!");
    }

    public function moveUp($categoryId)
    {
        $category = DocumentCategory::findOrFail($categoryId);
        $category->moveUp();
        session()->flash('message', 'Category moved up successfully!');
    }

    public function moveDown($categoryId)
    {
        $category = DocumentCategory::findOrFail($categoryId);
        $category->moveDown();
        session()->flash('message', 'Category moved down successfully!');
    }

    private function resetForm()
    {
        $this->name = '';
        $this->description = '';
        $this->color = '#3B82F6';
        $this->icon = 'fas fa-folder';
        $this->is_active = true;
        $this->selectedCategory = null;
    }

    public function render()
    {
        $categories = DocumentCategory::query()
            ->when($this->search, function($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->withDocumentCounts()
            ->ordered()
            ->get();

        $iconOptions = [
            'fas fa-folder' => 'Folder',
            'fas fa-book' => 'Book',
            'fas fa-file-alt' => 'Document',
            'fas fa-graduation-cap' => 'Education',
            'fas fa-code' => 'Code',
            'fas fa-cogs' => 'Settings',
            'fas fa-users' => 'Users',
            'fas fa-chart-bar' => 'Analytics',
            'fas fa-shield-alt' => 'Security',
            'fas fa-tools' => 'Tools',
        ];

        return view('livewire.content.partial.document-categories', compact('categories', 'iconOptions'));
    }
}