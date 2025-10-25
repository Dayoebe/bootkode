<?php

namespace App\Livewire\Content\Partial;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Learning\Document;

class DocumentReviews extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedStatus = 'pending_review';
    public $sortBy = 'updated_at';
    public $sortDirection = 'desc';
    
    public $showReviewModal = false;
    public $selectedDocument = null;
    public $reviewAction = '';
    public $reviewComments = '';

    protected $paginationTheme = 'tailwind';

    protected $rules = [
        'reviewComments' => 'nullable|string|max:1000',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openReviewModal($documentId, $action)
    {
        $this->selectedDocument = Document::findOrFail($documentId);
        $this->reviewAction = $action;
        $this->reviewComments = '';
        $this->showReviewModal = true;
    }

    public function closeReviewModal()
    {
        $this->showReviewModal = false;
        $this->selectedDocument = null;
        $this->reviewAction = '';
        $this->reviewComments = '';
    }

    public function submitReview()
    {
        $this->validate();

        try {
            if ($this->reviewAction === 'approve') {
                $this->selectedDocument->approve(auth()->id());
                $message = 'Document approved successfully!';
            } elseif ($this->reviewAction === 'reject') {
                $this->selectedDocument->reject(auth()->id(), $this->reviewComments);
                $message = 'Document rejected successfully!';
            }

            $this->closeReviewModal();
            session()->flash('message', $message);
            
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to process review: ' . $e->getMessage());
        }
    }

    public function quickApprove($documentId)
    {
        try {
            $document = Document::findOrFail($documentId);
            $document->approve(auth()->id());
            session()->flash('message', 'Document approved successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to approve document: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $documents = Document::query()
            ->when($this->search, function($query) {
                $query->where(function($q) {
                    $q->where('title', 'like', '%' . $this->search . '%')
                      ->orWhere('content', 'like', '%' . $this->search . '%')
                      ->orWhereHas('creator', function($userQuery) {
                          $userQuery->where('name', 'like', '%' . $this->search . '%');
                      });
                });
            })
            ->when($this->selectedStatus, function($query) {
                $query->where('status', $this->selectedStatus);
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->with(['creator', 'category', 'reviewer'])
            ->paginate(15);

        $statuses = [
            'pending_review' => 'Pending Review',
            'published' => 'Published',
            'draft' => 'Draft',
            'archived' => 'Archived',
            'deprecated' => 'Deprecated',
        ];

        return view('livewire.content.partial.document-reviews', compact('documents', 'statuses'));
    }
}