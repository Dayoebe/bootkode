<?php

namespace App\Livewire\Community\Partial;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\CommunityReport;
use App\Models\CommunityFeedback;
use App\Models\ForumThread;
use App\Models\ForumReply;
use App\Models\User;

class Moderation extends Component
{
    use WithPagination;
    
    public $activeTab = 'reports';
    public $statusFilter = 'pending';
    public $search = '';
    
    // Add these properties for the moderation modal
    public $showModerationModal = false;
    public $moderationAction = '';
    public $selectedItem = [];
    public $moderationReason = '';
    
    // Report handling
    public $selectedReport = null;
    public $moderatorNotes = '';
    
    // Feedback management
    public $selectedFeedback = null;
    public $adminResponse = '';
    public $assignedAdmin = '';

    public function mount()
    {
        // Check if user has moderation permissions
        if (!auth()->user()->canManageUsers()) {
            abort(403, 'Unauthorized access to moderation panel.');
        }
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function selectReport($reportId)
    {
        $this->selectedReport = CommunityReport::with(['reporter', 'reportable', 'moderator'])->find($reportId);
        $this->moderatorNotes = $this->selectedReport?->moderator_notes ?? '';
    }

    public function resolveReport($action = 'resolved')
    {
        if (!$this->selectedReport) return;

        if ($action === 'resolved') {
            $this->selectedReport->resolve($this->moderatorNotes);
        } else {
            $this->selectedReport->dismiss($this->moderatorNotes);
        }

        $this->reset(['selectedReport', 'moderatorNotes']);
        session()->flash('message', 'Report ' . $action . ' successfully.');
    }

    public function selectFeedback($feedbackId)
    {
        $this->selectedFeedback = CommunityFeedback::with(['user', 'assignedTo'])->find($feedbackId);
        $this->adminResponse = $this->selectedFeedback?->admin_response ?? '';
        $this->assignedAdmin = $this->selectedFeedback?->assigned_to ?? '';
    }

    public function assignFeedback()
    {
        if (!$this->selectedFeedback || !$this->assignedAdmin) return;

        $this->selectedFeedback->assignTo($this->assignedAdmin);
        session()->flash('message', 'Feedback assigned successfully.');
    }

    public function resolveFeedback()
    {
        if (!$this->selectedFeedback || !$this->adminResponse) {
            session()->flash('error', 'Please provide a response before resolving.');
            return;
        }

        $this->selectedFeedback->resolve($this->adminResponse);
        $this->reset(['selectedFeedback', 'adminResponse', 'assignedAdmin']);
        session()->flash('message', 'Feedback resolved successfully.');
    }

    public function lockThread($threadId)
    {
        $thread = ForumThread::find($threadId);
        if ($thread) {
            $thread->update(['is_locked' => true]);
            session()->flash('message', 'Thread locked successfully.');
        }
    }

    public function unlockThread($threadId)
    {
        $thread = ForumThread::find($threadId);
        if ($thread) {
            $thread->update(['is_locked' => false]);
            session()->flash('message', 'Thread unlocked successfully.');
        }
    }

    public function deleteContent($type, $id)
    {
        switch ($type) {
            case 'thread':
                ForumThread::find($id)?->delete();
                break;
            case 'reply':
                ForumReply::find($id)?->delete();
                break;
        }
        
        session()->flash('message', ucfirst($type) . ' deleted successfully.');
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

      // Add this method to handle moderation actions
      public function moderateItem($type, $id, $action)
      {
          $this->selectedItem = [
              'type' => $type,
              'id' => $id
          ];
          $this->moderationAction = $action;
          $this->showModerationModal = true;
      }
  
      // Add this method to execute the moderation action
      public function executeModerationAction()
      {
          if (!$this->selectedItem) {
              return;
          }
  
          $type = $this->selectedItem['type'];
          $id = $this->selectedItem['id'];
          $action = $this->moderationAction;
  
          switch ($type) {
              case 'thread':
                  $this->handleThreadAction($id, $action);
                  break;
              case 'reply':
                  $this->handleReplyAction($id, $action);
                  break;
              case 'study_group':
                  $this->handleStudyGroupAction($id, $action);
                  break;
              case 'challenge':
                  $this->handleChallengeAction($id, $action);
                  break;
              case 'event':
                  $this->handleEventAction($id, $action);
                  break;
          }
  
          $this->reset(['showModerationModal', 'selectedItem', 'moderationAction', 'moderationReason']);
          session()->flash('message', ucfirst($action) . ' action completed successfully.');
      }
  
      // Add helper methods for different actions
      private function handleThreadAction($id, $action)
      {
          $thread = ForumThread::find($id);
          if (!$thread) return;
  
          switch ($action) {
              case 'pin':
                  $thread->update(['is_pinned' => true]);
                  break;
              case 'unpin':
                  $thread->update(['is_pinned' => false]);
                  break;
              case 'lock':
                  $thread->update(['is_locked' => true]);
                  break;
              case 'unlock':
                  $thread->update(['is_locked' => false]);
                  break;
              case 'delete':
                  $thread->delete();
                  break;
          }
      }
  
      private function handleReplyAction($id, $action)
      {
          if ($action === 'delete') {
              ForumReply::find($id)?->delete();
          }
      }
  
      private function handleStudyGroupAction($id, $action)
      {
          // You'll need to implement study group actions
          // For now, just show a message
          session()->flash('message', "Study group {$action} action would be implemented here.");
      }
  
      private function handleChallengeAction($id, $action)
      {
          // You'll need to implement challenge actions
          // For now, just show a message
          session()->flash('message', "Challenge {$action} action would be implemented here.");
      }
  
      private function handleEventAction($id, $action)
      {
          // You'll need to implement event actions
          // For now, just show a message
          session()->flash('message', "Event {$action} action would be implemented here.");
      }
  
    public function render()
    {
        $reports = collect();
        $feedback = collect();
        $flaggedContent = collect();
        $stats = [];

        if ($this->activeTab === 'reports') {
            $reports = CommunityReport::with(['reporter', 'reportable', 'moderator'])
                ->when($this->statusFilter !== 'all', function($query) {
                    return $query->where('status', $this->statusFilter);
                })
                ->when($this->search, function($query) {
                    return $query->where('description', 'like', '%' . $this->search . '%');
                })
                ->latest()
                ->paginate(15);

            $stats = [
                'pending' => CommunityReport::where('status', 'pending')->count(),
                'resolved' => CommunityReport::where('status', 'resolved')->count(),
                'dismissed' => CommunityReport::where('status', 'dismissed')->count(),
            ];
        }

        if ($this->activeTab === 'feedback') {
            $feedback = CommunityFeedback::with(['user', 'assignedTo'])
                ->when($this->statusFilter !== 'all', function($query) {
                    return $query->where('status', $this->statusFilter);
                })
                ->when($this->search, function($query) {
                    return $query->where('title', 'like', '%' . $this->search . '%')
                        ->orWhere('message', 'like', '%' . $this->search . '%');
                })
                ->latest()
                ->paginate(15);

            $stats = [
                'open' => CommunityFeedback::where('status', 'open')->count(),
                'in_progress' => CommunityFeedback::where('status', 'in_progress')->count(),
                'resolved' => CommunityFeedback::where('status', 'resolved')->count(),
            ];
        }

        if ($this->activeTab === 'content') {
            // Get flagged content (threads with reports)
            $flaggedContent = ForumThread::with(['user', 'category', 'reports'])
                ->whereHas('reports')
                ->when($this->search, function($query) {
                    return $query->where('title', 'like', '%' . $this->search . '%');
                })
                ->latest()
                ->paginate(15);
        }

        $admins = User::whereIn('role', [User::ROLE_SUPER_ADMIN, User::ROLE_ACADEMY_ADMIN])
            ->get();

        return view('livewire.community.partial.moderation', [
            'reports' => $reports,
            'feedback' => $feedback,
            'flaggedContent' => $flaggedContent,
            'stats' => $stats,
            'admins' => $admins,
        ]);
    }
}