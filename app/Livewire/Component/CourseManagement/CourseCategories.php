<?php

namespace App\Livewire\Component\CourseManagement;

use App\Models\CourseCategory;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;

#[Layout('layouts.dashboard', ['title' => 'Course Categories'])]

class CourseCategories extends Component
{
    use WithPagination;

    public $name;
    public $description;
    public $categoryId;
    public $isModalOpen = false;
    public $search = '';

    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
    ];

    public function render()
    {
        $categories = CourseCategory::when($this->search, function ($query) {
            return $query->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%');
        })
        ->orderBy('name')
        ->paginate(10);

        return view('livewire.component.course-management.course-categories', [
            'categories' => $categories,
        ]);
    }

    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }

    public function openModal()
    {
        $this->isModalOpen = true;
    }

    public function closeModal()
    {
        $this->isModalOpen = false;
        $this->resetValidation();
    }

    private function resetInputFields()
    {
        $this->categoryId = null;
        $this->name = '';
        $this->description = '';
    }

    public function store()
    {
        $this->validate();

        CourseCategory::updateOrCreate(
            ['id' => $this->categoryId],
            [
                'name' => $this->name,
                'description' => $this->description,
            ]
        );

        session()->flash('message', 
            $this->categoryId ? 'Category updated successfully.' : 'Category created successfully.');

        $this->closeModal();
        $this->resetInputFields();
    }

    public function edit($id)
    {
        $category = CourseCategory::findOrFail($id);
        $this->categoryId = $id;
        $this->name = $category->name;
        $this->description = $category->description;
        
        $this->openModal();
    }

    public function delete($id)
    {
        $category = CourseCategory::find($id);
        
        if ($category) {
            // Check if category has courses before deleting
            if ($category->courses()->count() > 0) {
                session()->flash('error', 'Cannot delete category with associated courses.');
                return;
            }
            
            $category->delete();
            session()->flash('message', 'Category deleted successfully.');
        }
    }
}