<?php

namespace App\Livewire\Content\Partial;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Document;
use App\Models\DocumentCategory;

class AllDocuments extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedType = '';
    public $selectedCategory = '';
    public $selectedStatus = '';
    public $selectedVisibility = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $showFilters = false;

    protected $paginationTheme = 'tailwind';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function toggleFilters()
    {
        $this->showFilters = !$this->showFilters;
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->selectedType = '';
        $this->selectedCategory = '';
        $this->selectedStatus = '';
        $this->selectedVisibility = '';
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
            ->when($this->selectedVisibility, function($query) {
                $query->where('visibility', $this->selectedVisibility);
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->with(['creator', 'category'])
            ->paginate(20);

        $categories = DocumentCategory::active()->ordered()->get();
        $types = Document::TYPES;
        $statuses = Document::STATUSES;
        $visibilities = Document::VISIBILITY_LEVELS;

        return view('livewire.content.partial.all-documents', compact(
            'documents', 'categories', 'types', 'statuses', 'visibilities'
        ));
    }
}