<?php

namespace App\Livewire\Community\Partial;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\CommunityFeedback;
use App\Models\User;

class Feedback extends Component
{
    use WithPagination, WithFileUploads;
    
    public $showCreateForm = false;
    public $showResponseModal = false;
    public $selectedFeedback;
    public $adminResponse;
    public $assignTo;
    public $search = '';
    public $statusFilter = 'all';
    public $categoryFilter = 'all';
    
    // Form properties
    public $category = 'general';
    public $subject = '';
    public $message = '';
    public $priority = 'medium';
    
    // Admin properties
    public $isAdmin = false;
    public $admins = [];

    public function mount()
    {
        // Check if user is admin (super admin or academy admin)
        $user = auth()->user();
        $this->isAdmin = $user && ($user->isSuperAdmin() || $user->isAcademyAdmin());
        
        // Load admins for assignment if user is admin
        if ($this->isAdmin) {
            $this->admins = User::whereIn('role', [User::ROLE_SUPER_ADMIN, User::ROLE_ACADEMY_ADMIN])->get();
        }
    }

    public function submitFeedback()
    {
        $this->validate([
            'category' => 'required|in:general,feature_request,bug_report,course_feedback',
            'subject' => 'required|min:5|max:255',
            'message' => 'required|min:10',
            'priority' => 'required|in:low,medium,high',
        ]);

        CommunityFeedback::create([
            'user_id' => auth()->id(),
            'category' => $this->category,
            'subject' => $this->subject,
            'message' => $this->message,
            'priority' => $this->priority,
            'status' => 'open',
        ]);

        $this->reset(['category', 'subject', 'message', 'priority', 'showCreateForm']);
        session()->flash('message', 'Feedback submitted successfully!');
    }

    public function openResponseModal($feedbackId)
    {
        $this->selectedFeedback = CommunityFeedback::find($feedbackId);
        $this->adminResponse = $this->selectedFeedback->admin_response;
        $this->assignTo = $this->selectedFeedback->assigned_to;
        $this->showResponseModal = true;
    }

    public function respondToFeedback()
    {
        $this->validate([
            'adminResponse' => 'required|min:10',
        ]);

        $this->selectedFeedback->update([
            'admin_response' => $this->adminResponse,
            'assigned_to' => $this->assignTo,
            'responded_at' => now(),
            'status' => 'resolved',
        ]);

        $this->showResponseModal = false;
        $this->reset(['adminResponse', 'assignTo', 'selectedFeedback']);
    }

    public function updateFeedbackStatus($feedbackId, $status)
    {
        $feedback = CommunityFeedback::find($feedbackId);
        if ($feedback) {
            $feedback->update(['status' => $status]);
        }
    }

    public function render()
    {
        $feedbackQuery = CommunityFeedback::with(['user', 'assignedTo']);
        
        // Non-admins can only see their own feedback
        if (!$this->isAdmin) {
            $feedbackQuery->where('user_id', auth()->id());
        }
        
        // Apply filters
        $feedbackQuery->when($this->search, function($query) {
                return $query->where('subject', 'like', '%' . $this->search . '%')
                    ->orWhere('message', 'like', '%' . $this->search . '%');
            })
            ->when($this->statusFilter !== 'all', function($query) {
                return $query->where('status', $this->statusFilter);
            })
            ->when($this->categoryFilter !== 'all', function($query) {
                return $query->where('category', $this->categoryFilter);
            })
            ->latest();

        $feedback = $feedbackQuery->paginate(10);

        // Get stats based on user role
        if ($this->isAdmin) {
            $stats = [
                'total' => CommunityFeedback::count(),
                'open' => CommunityFeedback::where('status', 'open')->count(),
                'in_progress' => CommunityFeedback::where('status', 'in_progress')->count(),
                'resolved' => CommunityFeedback::where('status', 'resolved')->count(),
            ];
        } else {
            $stats = [
                'total' => CommunityFeedback::where('user_id', auth()->id())->count(),
                'open' => CommunityFeedback::where('user_id', auth()->id())->where('status', 'open')->count(),
                'in_progress' => CommunityFeedback::where('user_id', auth()->id())->where('status', 'in_progress')->count(),
                'resolved' => CommunityFeedback::where('user_id', auth()->id())->where('status', 'resolved')->count(),
            ];
        }

        return view('livewire.community.partial.feedback', [
            'feedback' => $feedback,
            'stats' => $stats,
            'isAdmin' => $this->isAdmin,
            'admins' => $this->admins,
        ]);
    }
}