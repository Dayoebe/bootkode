<?php

namespace App\Livewire\Component\CourseManagement;

use App\Models\CourseCategory;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

#[Layout('layouts.dashboard', ['title' => 'Course Categories', 'description' => 'Manage course categories including creation, editing, and deletion', 'icon' => 'fas fa-tags', 'active' => 'admin.course-categories'])]
class CourseCategories extends Component
{
    use WithPagination;

    #[Rule('required|string|max:255')]
    public $name = '';

    #[Rule('nullable|string|max:1000')]
    public $description = '';

    public $categoryId = null;
    public $isModalOpen = false;
    public $search = '';
    public $showConfirmDelete = false;
    public $categoryToDelete = null;

    /**
     * Render the component with paginated categories.
     */
    public function render()
    {
        $currentPage = $this->getPage();
        $categories = Cache::remember('course_categories_paginated_' . md5($this->search . $currentPage), 600, function () {
            return CourseCategory::when($this->search, function ($query) {
                return $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            })
                ->withCount('courses')
                ->orderBy('name')
                ->paginate(10);
        });

        return view('livewire.component.course-management.course-categories', [
            'categories' => $categories,
        ]);
    }

    /**
     * Open modal for creating a new category.
     */
    public function create()
    {
        if (!auth()->check() || !auth()->user()->hasAnyRole(['super_admin', 'academy_admin'])) {
            $this->flashMessage('You are not authorized to create categories.', 'error');
            return;
        }
        $this->resetInputFields();
        $this->openModal();
    }

    /**
     * Open the modal for editing/creating.
     */
    public function openModal()
    {
        $this->isModalOpen = true;
    }

    /**
     * Close the modal and reset validation.
     */
    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetValidation();
        $this->resetInputFields();
    }

    /**
     * Reset input fields.
     */
    private function resetInputFields()
    {
        $this->categoryId = null;
        $this->name = '';
        $this->description = '';
    }

    /**
     * Save or update the category.
     */
    public function store()
    {
        if (!auth()->check() || !auth()->user()->hasAnyRole(['super_admin', 'academy_admin'])) {
            $this->flashMessage('You are not authorized to save categories.', 'error');
            return;
        }

        $this->validate([
            'name' => $this->categoryId
                ? 'required|string|max:255|unique:course_categories,name,' . $this->categoryId
                : 'required|string|max:255|unique:course_categories,name',
            'description' => 'nullable|string|max:1000',
        ]);

        try {
            CourseCategory::updateOrCreate(
                ['id' => $this->categoryId],
                [
                    'name' => $this->name,
                    'description' => strip_tags($this->description),
                    'slug' => Str::slug($this->name),
                ]
            );

            // Invalidate cache for all pages and search terms
            Cache::forget('course_categories_paginated_' . md5($this->search . $this->getPage()));
            foreach (range(1, 10) as $page) { // Clear first 10 pages as a reasonable limit
                Cache::forget('course_categories_paginated_' . md5($this->search . $page));
                Cache::forget('course_categories_paginated_' . md5('' . $page)); // Clear empty search too
            }

            // Reset pagination to first page for new categories
            if (!$this->categoryId) {
                $this->resetPage();
            }

            // Dispatch event to force re-render
            $this->dispatch('category-updated');

            $this->flashMessage($this->categoryId ? 'Category updated successfully.' : 'Category created successfully.');
            $this->closeModal();
        } catch (\Exception $e) {
            $this->flashMessage('Error saving category: ' . $e->getMessage(), 'error');
        }
    }

    /**
     * Edit an existing category.
     */
    public function edit($id)
    {
        if (!auth()->check() || !auth()->user()->hasAnyRole(['super_admin', 'academy_admin'])) {
            $this->flashMessage('You are not authorized to edit categories.', 'error');
            return;
        }

        try {
            $category = CourseCategory::findOrFail($id);
            $this->categoryId = $id;
            $this->name = $category->name;
            $this->description = $category->description;
            $this->openModal();
        } catch (\Exception $e) {
            $this->flashMessage('Error: ' . $e->getMessage(), 'error');
        }
    }

    /**
     * Show confirmation for deletion.
     */
    public function confirmDelete($id)
    {
        if (!auth()->check() || !auth()->user()->hasAnyRole(['super_admin', 'academy_admin'])) {
            $this->flashMessage('You are not authorized to delete categories.', 'error');
            return;
        }
        $this->categoryToDelete = $id;
        $this->showConfirmDelete = true;
    }

    /**
     * Delete a category.
     */
    public function delete()
    {
        if (!auth()->check() || !auth()->user()->hasAnyRole(['super_admin', 'academy_admin'])) {
            $this->flashMessage('You are not authorized to delete categories.', 'error');
            $this->showConfirmDelete = false;
            return;
        }

        try {
            $category = CourseCategory::find($this->categoryToDelete);

            if ($category) {
                if ($category->courses()->count() > 0) {
                    $this->flashMessage('Cannot delete category with associated courses.', 'error');
                    $this->showConfirmDelete = false;
                    return;
                }

                $category->delete();
                Cache::forget('course_categories_paginated_' . md5($this->search . $this->getPage()));
                foreach (range(1, 10) as $page) {
                    Cache::forget('course_categories_paginated_' . md5($this->search . $page));
                    Cache::forget('course_categories_paginated_' . md5('' . $page));
                }

                $this->flashMessage('Category deleted successfully.');
            }

            $this->showConfirmDelete = false;
            $this->categoryToDelete = null;
        } catch (\Exception $e) {
            $this->flashMessage('Error: ' . $e->getMessage(), 'error');
            $this->showConfirmDelete = false;
        }
    }

    /**
     * Suggest AI-generated category name/description.
     */
    public function suggestAiContent()
    {
        $this->dispatch('notify', ['message' => 'AI suggestion coming soon!', 'type' => 'info']);
    }

    /**
     * Centralized flash message handler.
     */
    private function flashMessage(string $message, string $type = 'success')
    {
        session()->flash($type === 'success' ? 'message' : 'error', $message);
    }
}