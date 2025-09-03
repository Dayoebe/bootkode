<?php

namespace App\Livewire\Content\Partial;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\LearningMaterial;
use App\Models\Course;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class LearningMaterials extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';
    public $selectedType = '';
    public $selectedCourse = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $showCreateModal = false;
    public $showEditModal = false;
    public $showDeleteModal = false;
    public $selectedMaterial = null;

    // Form fields
    public $title = '';
    public $description = '';
    public $type = '';
    public $course_id = '';
    public $content = '';
    public $file_upload = null;
    public $tags = '';
    public $is_public = true;
    public $difficulty_level = 'beginner';

    protected $paginationTheme = 'tailwind';

    protected $rules = [
        'title' => 'required|string|max:255',
        'description' => 'nullable|string|max:1000',
        'type' => 'required|in:document,presentation,worksheet,template,guide,other',
        'course_id' => 'nullable|exists:courses,id',
        'content' => 'nullable|string',
        'file_upload' => 'nullable|file|max:50000', // 50MB max
        'tags' => 'nullable|string',
        'is_public' => 'boolean',
        'difficulty_level' => 'required|in:beginner,intermediate,advanced,expert',
    ];

    protected $listeners = ['refreshMaterials' => '$refresh'];

    public function mount()
    {
        // Initialize any needed data
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSelectedType()
    {
        $this->resetPage();
    }

    public function updatingSelectedCourse()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
    }

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

    public function openEditModal($materialId)
    {
        $material = LearningMaterial::findOrFail($materialId);

        $this->selectedMaterial = $material;
        $this->title = $material->title;
        $this->description = $material->description;
        $this->type = $material->type;
        $this->course_id = $material->course_id;
        $this->content = $material->content;
        $this->tags = $material->tags;
        $this->is_public = $material->is_public;
        $this->difficulty_level = $material->difficulty_level;
        
        $this->showEditModal = true;
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->resetForm();
    }

    public function openDeleteModal($materialId)
    {
        $this->selectedMaterial = LearningMaterial::findOrFail($materialId);
        $this->showDeleteModal = true;
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->selectedMaterial = null;
    }

    public function save()
    {
        $this->validate();

        try {
            $data = [
                'title' => $this->title,
                'description' => $this->description,
                'type' => $this->type,
                'course_id' => $this->course_id ?: null,
                'content' => $this->content,
                'tags' => $this->tags,
                'is_public' => $this->is_public,
                'difficulty_level' => $this->difficulty_level,
                'created_by' => auth()->id(),
            ];

            // Handle file upload
            if ($this->file_upload) {
                $filename = $this->file_upload->store('learning-materials', 'public');
                $data['file_path'] = $filename;
                $data['file_size'] = $this->file_upload->getSize();
                $data['file_type'] = $this->file_upload->getMimeType();
                $data['original_filename'] = $this->file_upload->getClientOriginalName();
            }

            LearningMaterial::create($data);

            $this->closeCreateModal();
            session()->flash('message', 'Learning material created successfully!');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to create learning material: ' . $e->getMessage());
        }
    }

    public function update()
    {
        $this->validate();

        try {
            $data = [
                'title' => $this->title,
                'description' => $this->description,
                'type' => $this->type,
                'course_id' => $this->course_id ?: null,
                'content' => $this->content,
                'tags' => $this->tags,
                'is_public' => $this->is_public,
                'difficulty_level' => $this->difficulty_level,
            ];

            // Handle file upload
            if ($this->file_upload) {
                // Delete old file if exists
                if ($this->selectedMaterial->file_path) {
                    Storage::disk('public')->delete($this->selectedMaterial->file_path);
                }

                $filename = $this->file_upload->store('learning-materials', 'public');
                $data['file_path'] = $filename;
                $data['file_size'] = $this->file_upload->getSize();
                $data['file_type'] = $this->file_upload->getMimeType();
                $data['original_filename'] = $this->file_upload->getClientOriginalName();
            }

            $this->selectedMaterial->update($data);

            $this->closeEditModal();
            session()->flash('message', 'Learning material updated successfully!');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update learning material: ' . $e->getMessage());
        }
    }

    public function delete()
    {
        try {
            // Delete file if exists
            if ($this->selectedMaterial->file_path) {
                Storage::disk('public')->delete($this->selectedMaterial->file_path);
            }

            $this->selectedMaterial->delete();
            
            $this->closeDeleteModal();
            session()->flash('message', 'Learning material deleted successfully!');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete learning material: ' . $e->getMessage());
        }
    }

    public function downloadFile($materialId)
    {
        $material = LearningMaterial::findOrFail($materialId);
        
        if (!$material->file_path || !Storage::disk('public')->exists($material->file_path)) {
            session()->flash('error', 'File not found.');
            return;
        }

        // Increment download count
        $material->increment('download_count');

        return Storage::disk('public')->download($material->file_path, $material->original_filename);
    }

    private function resetForm()
    {
        $this->title = '';
        $this->description = '';
        $this->type = '';
        $this->course_id = '';
        $this->content = '';
        $this->file_upload = null;
        $this->tags = '';
        $this->is_public = true;
        $this->difficulty_level = 'beginner';
        $this->selectedMaterial = null;
    }

    public function render()
    {
        $materials = LearningMaterial::query()
            ->when($this->search, function($query) {
                $query->where(function($q) {
                    $q->where('title', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%')
                      ->orWhere('tags', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->selectedType, function($query) {
                $query->where('type', $this->selectedType);
            })
            ->when($this->selectedCourse, function($query) {
                $query->where('course_id', $this->selectedCourse);
            })
            
            ->orderBy($this->sortBy, $this->sortDirection)
            ->with(['creator', 'course'])
            ->paginate(12);

        $courses = Course::select('id', 'title')->orderBy('title')->get();
        
        $types = [
            'document' => 'Document',
            'presentation' => 'Presentation',
            'worksheet' => 'Worksheet',
            'template' => 'Template',
            'guide' => 'Guide',
            'other' => 'Other'
        ];

        return view('livewire.content.partial.learning-materials', compact('materials', 'courses', 'types'));
    }
}