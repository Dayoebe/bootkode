<?php

namespace App\Livewire\Community\Partial;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\CommunityActivity;
use App\Models\ActivityParticipant;
use App\Models\User;

class CodeChallenges extends Component
{
    
    use WithPagination;
    
    public $showCreateForm = false;
    public $showSubmissionForm = false; // Add this
    public $search = '';
    public $difficultyFilter = 'all';
    public $statusFilter = 'all'; // Add this if needed
    public $selectedChallenge = null; // Add this
    
    // Form properties
    public $title = '';
    public $description = '';
    public $requirements = '';
    public $tags = '';
    public $difficulty = 'medium';
    public $maxParticipants = '';
    public $endDate = '';
    public $testCases = '';
    public $sampleInput = '';
    public $sampleOutput = '';  
    

   // Submission form properties
   public $language = 'javascript'; // Add this
   public $code = ''; // Add this

   protected $rules = [
       'title' => 'required|min:5|max:255',
       'description' => 'required|min:20',
       'requirements' => 'required|min:10',
       'tags' => 'nullable|string',
       'difficulty' => 'required|in:easy,medium,hard',
       'maxParticipants' => 'nullable|integer|min:1|max:1000',
       'endDate' => 'nullable|date|after:today',
       'testCases' => 'nullable|string',
       'sampleInput' => 'nullable|string',
       'sampleOutput' => 'nullable|string',
       'code' => 'required|min:10', // Add this if needed
   ];
    public function createChallenge()
    {
        $this->validate();

        $tags = $this->tags ? array_map('trim', explode(',', $this->tags)) : [];
        
        $metadata = [
            'difficulty' => $this->difficulty,
            'test_cases' => $this->testCases,
            'sample_input' => $this->sampleInput,
            'sample_output' => $this->sampleOutput,
        ];

        CommunityActivity::create([
            'creator_id' => auth()->id(),
            'type' => 'code_challenge',
            'title' => $this->title,
            'description' => $this->description,
            'requirements' => $this->requirements,
            'tags' => $tags,
            'max_participants' => $this->maxParticipants,
            'end_date' => $this->endDate,
            'metadata' => $metadata,
            'status' => 'active',
        ]);

        $this->reset(['title', 'description', 'requirements', 'tags', 'difficulty', 'maxParticipants', 'endDate', 'testCases', 'sampleInput', 'sampleOutput', 'showCreateForm']);
        
        session()->flash('message', 'Code challenge created successfully!');
    }

    public function selectChallenge($challengeId)
    {
        $this->selectedChallenge = CommunityActivity::find($challengeId);
        $this->showSubmissionForm = true;
    }

    // Add this method to handle solution submission
    public function submitSolution()
    {
        $this->validate([
            'language' => 'required|string',
            'code' => 'required|min:10',
        ]);

        if ($this->selectedChallenge) {
            $this->submitSolutionToChallenge($this->selectedChallenge->id, $this->code, $this->language);
            $this->reset(['language', 'code', 'showSubmissionForm', 'selectedChallenge']);
            session()->flash('message', 'Solution submitted successfully!');
        }
    }
    public function submitSolutionToChallenge($challengeId, $solution, $language = 'javascript')
{
    $participant = ActivityParticipant::where('activity_id', $challengeId)
        ->where('user_id', auth()->id())
        ->first();

    if ($participant) {
        $submissionData = [
            'solution' => $solution,
            'language' => $language,
            'submitted_at' => now(),
        ];

        $participant->update([
            'submission_data' => $submissionData,
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        return true;
    }
    
    return false;
}
    public function joinChallenge($challengeId)
    {
        $challenge = CommunityActivity::findOrFail($challengeId);
        
        if ($challenge->join()) {
            session()->flash('message', 'Successfully joined the challenge!');
        } else {
            session()->flash('error', 'Unable to join this challenge.');
        }
    }   

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedDifficultyFilter()
    {
        $this->resetPage();
    }

    public function render()
    {
        $challenges = CommunityActivity::codeChallenges()
            ->with(['creator', 'activeParticipants.user'])
            ->when($this->search, function($query) {
                return $query->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->when($this->difficultyFilter !== 'all', function($query) {
                return $query->whereJsonContains('metadata->difficulty', $this->difficultyFilter);
            })
            ->latest()
            ->paginate(12);
    
        $mySubmissions = CommunityActivity::codeChallenges()
            ->with(['creator', 'activeParticipants.user'])
            ->whereHas('participants', function($query) {
                $query->where('user_id', auth()->id());
            })
            ->limit(3)
            ->get();
    
        $completedChallenges = ActivityParticipant::where('user_id', auth()->id())
            ->where('status', 'completed')
            ->whereHas('activity', function($query) {
                $query->where('type', 'code_challenge');
            })
            ->count();



            $leaderboard = User::selectRaw('
    users.id,
    users.name,
    users.email,
    users.profile_picture,
    COUNT(activity_participants.id) as submissions_count,
    COALESCE(SUM(activity_participants.score), 0) as total_score
')
->leftJoin('activity_participants', function($join) {
    $join->on('users.id', '=', 'activity_participants.user_id')
        ->where('activity_participants.status', 'completed');
})
->leftJoin('community_activities', 'activity_participants.activity_id', '=', 'community_activities.id')
->where('community_activities.type', 'code_challenge')
->groupBy('users.id', 'users.name', 'users.email', 'users.profile_picture')
->orderBy('total_score', 'desc')
->orderBy('submissions_count', 'desc')
->limit(10)
->get();
    

$myChallenges = CommunityActivity::codeChallenges()
        ->with(['creator', 'activeParticipants.user'])
        ->whereHas('participants', function($query) {
            $query->where('user_id', auth()->id());
        })
        ->limit(3)
        ->get();

    return view('livewire.community.partial.code-challenges', [
        'challenges' => $challenges,
        'myChallenges' => $myChallenges, // Keep the original name
        'completedChallenges' => $completedChallenges,
        'leaderboard' => $leaderboard,
    ]);

    }
}