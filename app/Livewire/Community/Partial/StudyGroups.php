<?php

namespace App\Livewire\Community\Partial;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\CommunityActivity;
use App\Models\ActivityParticipant;

class StudyGroups extends Component
{
    use WithPagination;
    
    public $showCreateForm = false;
    public $search = '';
    public $statusFilter = 'active';
    
    // Form properties
    public $title = '';
    public $description = '';
    public $tags = '';
    public $maxParticipants = '';
    public $startDate = '';
    public $endDate = '';
    public $requirements = '';

    protected $rules = [
        'title' => 'required|min:5|max:255',
        'description' => 'required|min:20',
        'tags' => 'nullable|string',
        'maxParticipants' => 'nullable|integer|min:2|max:50',
        'startDate' => 'nullable|date|after:today',
        'endDate' => 'nullable|date|after:startDate',
        'requirements' => 'nullable|string|max:1000',
    ];

    public function createStudyGroup()
    {
        $this->validate();

        $tags = $this->tags ? array_map('trim', explode(',', $this->tags)) : [];

        CommunityActivity::create([
            'creator_id' => auth()->id(),
            'type' => 'study_group',
            'title' => $this->title,
            'description' => $this->description,
            'tags' => $tags,
            'max_participants' => $this->maxParticipants,
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'requirements' => $this->requirements,
            'status' => 'active',
        ]);

        $this->reset(['title', 'description', 'tags', 'maxParticipants', 'startDate', 'endDate', 'requirements', 'showCreateForm']);
        
        session()->flash('message', 'Study group created successfully!');
    }

    public function joinGroup($groupId)
    {
        $group = CommunityActivity::findOrFail($groupId);
        
        if ($group->join()) {
            session()->flash('message', 'Successfully joined the study group!');
        } else {
            session()->flash('error', 'Unable to join this study group.');
        }
    }

    public function leaveGroup($groupId)
    {
        $group = CommunityActivity::findOrFail($groupId);
        
        if ($group->leave()) {
            session()->flash('message', 'Left the study group successfully.');
        } else {
            session()->flash('error', 'Unable to leave this study group.');
        }
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function render()
    {
        $studyGroups = CommunityActivity::studyGroups()
            ->with(['creator', 'activeParticipants.user'])
            ->when($this->search, function($query) {
                return $query->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->when($this->statusFilter !== 'all', function($query) {
                return $query->where('status', $this->statusFilter);
            })
            ->latest()
            ->paginate(12);

        $myGroups = CommunityActivity::studyGroups()
            ->with(['creator', 'activeParticipants.user'])
            ->whereHas('participants', function($query) {
                $query->where('user_id', auth()->id())
                      ->whereIn('status', ['joined', 'completed']);
            })
            ->limit(3)
            ->get();

        return view('livewire.community.partial.study-groups', [
            'studyGroups' => $studyGroups,
            'myGroups' => $myGroups,
        ]);
    }
}