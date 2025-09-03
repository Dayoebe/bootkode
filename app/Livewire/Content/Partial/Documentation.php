<?php

namespace App\Livewire\Content\Partial;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\User;

class Documentation extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedType = '';
    public $selectedCategory = '';
    public $selectedStatus = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $showCreateModal = false;
    public $showEditModal = false;
    public $showDeleteModal = false;
    public $selectedDocument = null;

    // Form fields
    public $title = '';
    public $content = '';
    public $excerpt = '';
    public $type = 'guide';
    public $category_id = '';
    public $status = 'draft';
    public $visibility = 'public';
    public $tags = '';
    public $meta_title = '';
    public $meta_description = '';
    public $featured = false;
    public $difficulty_level = 'beginner';

    protected $paginationTheme = 'tailwind';

    protected $rules = [
        'title' => 'required|string|max:255',
        'content' => 'required|string',
        'excerpt' => 'nullable|string|max:500',
        'type' => 'required|string',
        'category_id' => 'nullable|exists:document_categories,id',
        'status' => 'required|string',
        'visibility' => 'required|string',
        'tags' => 'nullable|string',
        'meta_title' => 'nullable|string|max:60',
        'meta_description' => 'nullable|string|max:160',
        'featured' => 'boolean',
        'difficulty_level' => 'required|string',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
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

    public function openEditModal($documentId)
    {
        $document = Document::findOrFail($documentId);
        
        $this->selectedDocument = $document;
        $this->title = $document->title;
        $this->content = $document->content;
        $this->excerpt = $document->excerpt;
        $this->type = $document->type;
        $this->category_id = $document->category_id;
        $this->status = $document->status;
        $this->visibility = $document->visibility;
        $this->tags = $document->tags;
        $this->meta_title = $document->meta_title;
        $this->meta_description = $document->meta_description;
        $this->featured = $document->featured;
        $this->difficulty_level = $document->difficulty_level;
        
        $this->showEditModal = true;
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->resetForm();
    }

    public function openDeleteModal($documentId)
    {
        $this->selectedDocument = Document::findOrFail($documentId);
        $this->showDeleteModal = true;
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->selectedDocument = null;
    }

    public function save()
    {
        $this->validate();

        try {
            Document::create([
                'title' => $this->title,
                'content' => $this->content,
                'excerpt' => $this->excerpt,
                'type' => $this->type,
                'category_id' => $this->category_id ?: null,
                'status' => $this->status,
                'visibility' => $this->visibility,
                'tags' => $this->tags,
                'meta_title' => $this->meta_title,
                'meta_description' => $this->meta_description,
                'featured' => $this->featured,
                'difficulty_level' => $this->difficulty_level,
                'created_by' => auth()->id(),
            ]);

            $this->closeCreateModal();
            session()->flash('message', 'Document created successfully!');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to create document: ' . $e->getMessage());
        }
    }

    public function update()
    {
        $this->validate();

        try {
            $this->selectedDocument->update([
                'title' => $this->title,
                'content' => $this->content,
                'excerpt' => $this->excerpt,
                'type' => $this->type,
                'category_id' => $this->category_id ?: null,
                'status' => $this->status,
                'visibility' => $this->visibility,
                'tags' => $this->tags,
                'meta_title' => $this->meta_title,
                'meta_description' => $this->meta_description,
                'featured' => $this->featured,
                'difficulty_level' => $this->difficulty_level,
                'updated_by' => auth()->id(),
            ]);

            $this->closeEditModal();
            session()->flash('message', 'Document updated successfully!');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update document: ' . $e->getMessage());
        }
    }

    public function delete()
    {
        try {
            $this->selectedDocument->delete();
            $this->closeDeleteModal();
            session()->flash('message', 'Document deleted successfully!');
            
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete document: ' . $e->getMessage());
        }
    }

    public function toggleFeatured($documentId)
    {
        $document = Document::findOrFail($documentId);
        $document->toggleFeatured();
        session()->flash('message', 'Document featured status updated!');
    }

    public function changeStatus($documentId, $status)
    {
        $document = Document::findOrFail($documentId);
        $document->update(['status' => $status]);
        session()->flash('message', 'Document status updated!');
    }

    private function resetForm()
    {
        $this->title = '';
        $this->content = '';
        $this->excerpt = '';
        $this->type = 'guide';
        $this->category_id = '';
        $this->status = 'draft';
        $this->visibility = 'public';
        $this->tags = '';
        $this->meta_title = '';
        $this->meta_description = '';
        $this->featured = false;
        $this->difficulty_level = 'beginner';
        $this->selectedDocument = null;
    }

    public function render()
    {
        $documents = Document::query()
            ->when($this->search, function($query) {
                $query->where(function($q) {
                    $q->where('title', 'like', '%' . $this->search . '%')
                      ->orWhere('content', 'like', '%' . $this->search . '%')
                      ->orWhere('excerpt', 'like', '%' . $this->search . '%')
                      ->orWhere('tags', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->selectedType, function($query) {
                $query->where('type', $this->selectedType);
            })
            ->when($this->selectedCategory, function($query) {
                $query->where('category_id', $this->selectedCategory);
            })
            ->when($this->selectedStatus, function($query) {
                $query->where('status', $this->selectedStatus);
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->with(['creator', 'category'])
            ->paginate(15);

        $categories = DocumentCategory::active()->ordered()->get();
        $types = Document::TYPES;
        $statuses = Document::STATUSES;
        $visibilities = Document::VISIBILITY_LEVELS;
        $difficultyLevels = Document::DIFFICULTY_LEVELS;

        return view('livewire.content.partial.documentation', compact(
            'documents', 'categories', 'types', 'statuses', 'visibilities', 'difficultyLevels'
        ));
    }
}