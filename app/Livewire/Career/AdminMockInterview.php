<?php

namespace App\Livewire\Career;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Mentorship\Mentorship\MockInterview;
use App\Models\Core\User;
use App\Models\Mentorship\Mentorship\InterviewQuestion;
use App\Models\Mentorship\Mentorship\InterviewQuestionSet;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;

#[Layout('layouts.dashboard', [
    'title' => 'Mock Interviews Management',
    'description' => 'Manage mock interviews, questions, and analytics',
    'icon' => 'fas fa-microphone-alt',
    'active' => 'admin.mock-interviews'
])]
class AdminMockInterview extends Component
{
    use WithPagination;

    // ============================================
    // AUTHORIZATION CHECK
    // ============================================
    
    public function mount()
    {
        // Only allow instructors, academy admins, and super admins
        if (!$this->canManageInterviews()) {
            abort(403, 'Unauthorized. Only instructors and administrators can manage mock interviews.');
        }
        
        $this->loadAvailableQuestions();
    }
    
    private function canManageInterviews(): bool
    {
        return Auth::user()->hasRole([
            'super_admin',
            'academy_admin', 
            'instructor',
            'content_editor'
        ]);
    }

    // ============================================
    // CORE PROPERTIES
    // ============================================
    
    public $activeTab = 'overview';
    public $selectedInterview = null;
    
    // ============================================
    // FILTERS - INTERVIEWS
    // ============================================
    
    public $searchTerm = '';
    public $filterType = '';
    public $filterStatus = '';
    public $filterDifficulty = '';
    public $filterDateRange = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 15;
    
    // ============================================
    // FILTERS - QUESTIONS
    // ============================================
    
    public $questionSearch = '';
    public $questionFilterType = '';
    public $questionFilterDifficulty = '';
    public $questionFilterStatus = '';
    
    // ============================================
    // QUESTION CRUD PROPERTIES
    // ============================================
    
    public $showCreateQuestionModal = false;
    public $editingQuestionId = null;
    public $question = '';
    public $type = 'technical';
    public $difficulty_level = 'intermediate';
    public $answer_type = 'text';
    public $correct_answer = '';
    public $keywords = '';
    public $max_points = 10;
    public $time_limit = 300;
    public $category = '';
    public $industry = '';
    public $job_role = '';
    
    // ============================================
    // QUESTION SET CRUD PROPERTIES
    // ============================================
    
    public $showCreateSetModal = false;
    public $editingSetId = null;
    public $name = '';
    public $description = '';
    public $setType = 'technical';
    public $setDifficulty = 'intermediate';
    public $estimated_duration = 60;
    public $selectedQuestions = [];
    public $availableQuestions = [];
    
    // ============================================
    // BULK ACTIONS
    // ============================================
    
    public $selectedInterviews = [];
    public $bulkAction = '';
    
    // ============================================
    // ANALYTICS
    // ============================================
    
    public $analyticsDateRange = '30';
    
    // ============================================
    // UI STATE
    // ============================================
    
    public $showUserModal = false;
    public $selectedUser = null;
    public $showInterviewDetailsModal = false;

    // ============================================
    // QUERY STRING
    // ============================================
    
    protected $queryString = [
        'searchTerm' => ['except' => ''],
        'filterType' => ['except' => ''],
        'filterStatus' => ['except' => ''],
        'activeTab' => ['except' => 'overview']
    ];

    // ============================================
    // VALIDATION RULES
    // ============================================
    
    protected $rules = [
        'question' => 'required|string|min:10',
        'type' => 'required|in:technical,behavioral,case_study,system_design,coding,hr',
        'difficulty_level' => 'required|in:beginner,intermediate,advanced,expert',
        'answer_type' => 'required|in:text,multiple_choice,coding',
        'max_points' => 'required|integer|min:1|max:100',
        'time_limit' => 'required|integer|min:30|max:3600',
    ];

    // ============================================
    // LIFECYCLE HOOKS
    // ============================================

    public function loadAvailableQuestions()
    {
        if (class_exists(InterviewQuestion::class)) {
            $this->availableQuestions = InterviewQuestion::where('is_active', true)
                ->where('is_approved', true)
                ->orderBy('type')
                ->orderBy('difficulty_level')
                ->get();
        } else {
            $this->availableQuestions = collect();
        }
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['searchTerm', 'filterType', 'filterStatus', 'filterDifficulty', 'filterDateRange', 'sortBy', 'sortDirection'])) {
            $this->resetPage();
        }
        
        if (in_array($propertyName, ['questionSearch', 'questionFilterType', 'questionFilterDifficulty', 'questionFilterStatus'])) {
            $this->resetPage();
        }
    }

    // ============================================
    // COMPUTED PROPERTIES - STATISTICS
    // ============================================
    
    #[Computed]
    public function statistics()
    {
        return Cache::remember('admin_mock_interview_stats', 300, function () {
            return [
                'totalInterviews' => MockInterview::count(),
                'totalUsers' => User::whereHas('mockInterviews')->count(),
                'completedInterviews' => MockInterview::where('status', 'completed')->count(),
                'averageScore' => MockInterview::where('status', 'completed')->avg('overall_score') ?? 0,
                'dailyInterviews' => MockInterview::whereDate('created_at', today())->count(),
                'weeklyGrowth' => $this->calculateWeeklyGrowth(),
                'premiumUsage' => MockInterview::where('is_premium', true)->count(),
                'totalQuestions' => InterviewQuestion::count(),
                'approvedQuestions' => InterviewQuestion::where('is_approved', true)->count(),
                'totalQuestionSets' => InterviewQuestionSet::count(),
            ];
        });
    }

    #[Computed]
    public function popularTypes()
    {
        return Cache::remember('admin_mock_popular_types', 300, function () {
            return MockInterview::select('type', DB::raw('count(*) as count'))
                ->groupBy('type')
                ->orderByDesc('count')
                ->limit(5)
                ->get()
                ->toArray();
        });
    }

    // ============================================
    // COMPUTED PROPERTIES - INTERVIEWS
    // ============================================
    
    #[Computed]
    public function interviews()
    {
        $query = MockInterview::with(['user:id,name,email', 'course:id,title']);

        if ($this->searchTerm) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->searchTerm . '%')
                    ->orWhere('description', 'like', '%' . $this->searchTerm . '%')
                    ->orWhereHas('user', function ($userQuery) {
                        $userQuery->where('name', 'like', '%' . $this->searchTerm . '%')
                            ->orWhere('email', 'like', '%' . $this->searchTerm . '%');
                    });
            });
        }

        if ($this->filterType) {
            $query->where('type', $this->filterType);
        }

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        if ($this->filterDifficulty) {
            $query->where('difficulty_level', $this->filterDifficulty);
        }

        if ($this->filterDateRange) {
            $days = (int) $this->filterDateRange;
            $query->where('created_at', '>=', now()->subDays($days));
        }

        $query->orderBy($this->sortBy, $this->sortDirection);

        return $query->paginate($this->perPage);
    }

    // ============================================
    // COMPUTED PROPERTIES - QUESTIONS
    // ============================================
    
    #[Computed]
    public function questions()
    {
        if (!class_exists(InterviewQuestion::class)) {
            return collect();
        }

        $query = InterviewQuestion::with('creator');

        if ($this->questionSearch) {
            $query->where('question', 'like', '%' . $this->questionSearch . '%');
        }

        if ($this->questionFilterType) {
            $query->where('type', $this->questionFilterType);
        }

        if ($this->questionFilterDifficulty) {
            $query->where('difficulty_level', $this->questionFilterDifficulty);
        }

        if ($this->questionFilterStatus === 'active') {
            $query->where('is_active', true);
        } elseif ($this->questionFilterStatus === 'inactive') {
            $query->where('is_active', false);
        } elseif ($this->questionFilterStatus === 'approved') {
            $query->where('is_approved', true);
        } elseif ($this->questionFilterStatus === 'pending') {
            $query->where('is_approved', false);
        }

        return $query->orderBy('created_at', 'desc')->paginate(20);
    }

    #[Computed]
    public function questionSets()
    {
        if (!class_exists(InterviewQuestionSet::class)) {
            return collect();
        }

        return InterviewQuestionSet::with('creator', 'questions')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    // ============================================
    // COMPUTED PROPERTIES - USERS & ANALYTICS
    // ============================================
    
    #[Computed]
    public function recentActivity()
    {
        return MockInterview::with(['user:id,name'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();
    }

    #[Computed]
    public function topUsers()
    {
        return User::withCount(['mockInterviews'])
            ->orderByDesc('mock_interviews_count')
            ->limit(20)
            ->get();
    }

    #[Computed]
    public function chartData()
    {
        $days = (int) $this->analyticsDateRange;
        $startDate = now()->subDays($days);

        return MockInterview::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => \Carbon\Carbon::parse($item->date)->format('M d'),
                    'count' => $item->count,
                ];
            })
            ->toArray();
    }

    private function calculateWeeklyGrowth()
    {
        $thisWeek = MockInterview::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();
        $lastWeek = MockInterview::whereBetween('created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()])->count();
        
        return $lastWeek > 0 ? round((($thisWeek - $lastWeek) / $lastWeek) * 100, 1) : 0;
    }

    // ============================================
    // QUESTION MANAGEMENT METHODS
    // ============================================
    
    public function createQuestion()
    {
        // Double-check authorization
        if (!$this->canManageInterviews()) {
            session()->flash('error', 'Unauthorized action.');
            return;
        }
        
        $this->validate();

        try {
            $keywords = array_filter(array_map('trim', explode(',', $this->keywords)));

            $question = InterviewQuestion::create([
                'created_by' => Auth::id(),
                'question' => $this->question,
                'type' => $this->type,
                'difficulty_level' => $this->difficulty_level,
                'answer_type' => $this->answer_type,
                'correct_answer' => $this->correct_answer,
                'keywords' => $keywords,
                'max_points' => $this->max_points,
                'time_limit' => $this->time_limit,
                'category' => $this->category,
                'industry' => $this->industry,
                'job_role' => $this->job_role,
                'is_active' => true,
                'is_approved' => Auth::user()->hasRole(['super_admin', 'academy_admin']),
            ]);

            $this->clearCache();
            session()->flash('message', 'Question created successfully!');
            $this->resetQuestionForm();
            $this->loadAvailableQuestions();
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to create question: ' . $e->getMessage());
        }
    }

    public function editQuestion($questionId)
    {
        if (!$this->canManageInterviews()) {
            session()->flash('error', 'Unauthorized action.');
            return;
        }
        
        $question = InterviewQuestion::findOrFail($questionId);

        $this->editingQuestionId = $question->id;
        $this->question = $question->question;
        $this->type = $question->type;
        $this->difficulty_level = $question->difficulty_level;
        $this->answer_type = $question->answer_type;
        $this->correct_answer = $question->correct_answer;
        $this->keywords = implode(', ', $question->keywords ?? []);
        $this->max_points = $question->max_points;
        $this->time_limit = $question->time_limit;
        $this->category = $question->category;
        $this->industry = $question->industry;
        $this->job_role = $question->job_role;

        $this->showCreateQuestionModal = true;
    }
    
    public function updateQuestion()
    {
        if (!$this->canManageInterviews() || !$this->editingQuestionId) {
            session()->flash('error', 'Unauthorized action.');
            return;
        }
        
        $this->validate();

        try {
            $question = InterviewQuestion::findOrFail($this->editingQuestionId);
            $keywords = array_filter(array_map('trim', explode(',', $this->keywords)));

            $question->update([
                'question' => $this->question,
                'type' => $this->type,
                'difficulty_level' => $this->difficulty_level,
                'answer_type' => $this->answer_type,
                'correct_answer' => $this->correct_answer,
                'keywords' => $keywords,
                'max_points' => $this->max_points,
                'time_limit' => $this->time_limit,
                'category' => $this->category,
                'industry' => $this->industry,
                'job_role' => $this->job_role,
            ]);

            $this->clearCache();
            session()->flash('message', 'Question updated successfully!');
            $this->resetQuestionForm();
            $this->loadAvailableQuestions();
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update question: ' . $e->getMessage());
        }
    }

    public function deleteQuestion($questionId)
    {
        if (!$this->canManageInterviews()) {
            session()->flash('error', 'Unauthorized action.');
            return;
        }
        
        $question = InterviewQuestion::findOrFail($questionId);
        $question->delete();
        
        $this->clearCache();
        session()->flash('message', 'Question deleted successfully.');
    }

    public function approveQuestion($questionId)
    {
        if (!Auth::user()->hasRole(['super_admin', 'academy_admin'])) {
            session()->flash('error', 'Only administrators can approve questions.');
            return;
        }
        
        $question = InterviewQuestion::findOrFail($questionId);
        $question->approve(Auth::id());
        
        $this->clearCache();
        session()->flash('message', 'Question approved successfully.');
    }

    public function toggleQuestionStatus($questionId)
    {
        if (!$this->canManageInterviews()) {
            session()->flash('error', 'Unauthorized action.');
            return;
        }
        
        $question = InterviewQuestion::findOrFail($questionId);
        $question->update(['is_active' => !$question->is_active]);
        
        $this->clearCache();
        session()->flash('message', 'Question status updated.');
    }

    public function viewQuestion($questionId)
    {
        $this->editingQuestionId = $questionId;
        // This would open a view-only modal
    }

    private function resetQuestionForm()
    {
        $this->reset([
            'showCreateQuestionModal',
            'editingQuestionId',
            'question',
            'type',
            'difficulty_level',
            'answer_type',
            'correct_answer',
            'keywords',
            'max_points',
            'time_limit',
            'category',
            'industry',
            'job_role',
        ]);
    }

    // ============================================
    // QUESTION SET MANAGEMENT METHODS
    // ============================================
    
    public function toggleQuestion($questionId)
    {
        if (in_array($questionId, $this->selectedQuestions)) {
            $this->selectedQuestions = array_diff($this->selectedQuestions, [$questionId]);
        } else {
            $this->selectedQuestions[] = $questionId;
        }
    }

    public function createSet()
    {
        if (!$this->canManageInterviews()) {
            session()->flash('error', 'Unauthorized action.');
            return;
        }
        
        $this->validate([
            'name' => 'required|string|max:255',
            'setType' => 'required',
            'setDifficulty' => 'required',
            'estimated_duration' => 'required|integer|min:15',
        ]);

        try {
            $set = InterviewQuestionSet::create([
                'created_by' => Auth::id(),
                'name' => $this->name,
                'description' => $this->description,
                'type' => $this->setType,
                'difficulty_level' => $this->setDifficulty,
                'estimated_duration' => $this->estimated_duration,
                'is_active' => true,
            ]);

            foreach ($this->selectedQuestions as $index => $questionId) {
                $set->addQuestion($questionId, $index + 1);
            }

            $this->clearCache();
            session()->flash('message', 'Question set created successfully!');
            $this->resetSetForm();
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to create question set: ' . $e->getMessage());
        }
    }
    
    public function editQuestionSet($setId)
    {
        if (!$this->canManageInterviews()) {
            session()->flash('error', 'Unauthorized action.');
            return;
        }
        
        $set = InterviewQuestionSet::with('questions')->findOrFail($setId);
        
        $this->editingSetId = $set->id;
        $this->name = $set->name;
        $this->description = $set->description;
        $this->setType = $set->type;
        $this->setDifficulty = $set->difficulty_level;
        $this->estimated_duration = $set->estimated_duration;
        $this->selectedQuestions = $set->questions->pluck('id')->toArray();
        
        $this->showCreateSetModal = true;
    }

    public function deleteQuestionSet($setId)
    {
        if (!$this->canManageInterviews()) {
            session()->flash('error', 'Unauthorized action.');
            return;
        }
        
        $set = InterviewQuestionSet::findOrFail($setId);
        $set->delete();
        
        $this->clearCache();
        session()->flash('message', 'Question set deleted successfully.');
    }

    private function resetSetForm()
    {
        $this->reset([
            'showCreateSetModal',
            'editingSetId',
            'name',
            'description',
            'setType',
            'setDifficulty',
            'estimated_duration',
            'selectedQuestions'
        ]);
    }

    // ============================================
    // INTERVIEW MANAGEMENT METHODS
    // ============================================
    
    #[On('interview-updated')]
    public function refreshInterviews()
    {
        $this->resetPage();
        $this->clearCache();
    }

    public function viewInterview($interviewId)
    {
        $this->selectedInterview = MockInterview::with(['user', 'course', 'questionSet'])->find($interviewId);
        $this->showInterviewDetailsModal = true;
    }

    public function deleteInterview($interviewId)
    {
        if (!$this->canManageInterviews()) {
            session()->flash('error', 'Unauthorized action.');
            return;
        }
        
        $interview = MockInterview::find($interviewId);

        if ($interview) {
            $interview->delete();
            $this->dispatch('interview-updated');
            session()->flash('message', 'Interview deleted successfully.');
        }
    }

    public function generateAIFeedback($interviewId)
    {
        if (!$this->canManageInterviews()) {
            session()->flash('error', 'Unauthorized action.');
            return;
        }
        
        $interview = MockInterview::find($interviewId);

        if ($interview && $interview->isCompleted()) {
            $feedback = $this->createAIFeedback($interview);
            
            $interview->update([
                'ai_feedback' => $feedback,
                'improvement_suggestions' => $feedback['recommendations'] ?? [],
                'strengths' => $feedback['strengths'] ?? [],
                'weaknesses' => $feedback['areas_for_improvement'] ?? [],
            ]);

            $this->dispatch('interview-updated');
            session()->flash('message', 'AI feedback generated successfully.');
        }
    }

    private function createAIFeedback($interview)
    {
        $responses = $interview->user_responses ?? [];
        $questionCount = count($interview->questions ?? []);
        $responseCount = count($responses);
        
        $completionRate = $questionCount > 0 ? ($responseCount / $questionCount) * 100 : 0;
        
        $strengths = [];
        $improvements = [];
        $recommendations = [];
        
        if ($completionRate >= 90) {
            $strengths[] = 'Excellent interview completion rate';
        } elseif ($completionRate < 70) {
            $improvements[] = 'Consider completing all interview questions';
            $recommendations[] = 'Practice time management to answer all questions';
        }
        
        $avgResponseTime = collect($responses)->avg('response_time') ?? 0;
        
        if ($avgResponseTime > 0 && $avgResponseTime < 120) {
            $strengths[] = 'Good response time management';
        } elseif ($avgResponseTime > 180) {
            $improvements[] = 'Response times could be improved';
            $recommendations[] = 'Practice answering questions more concisely';
        }
        
        if (empty($strengths)) {
            $strengths[] = 'Participated in the interview process';
        }
        
        if (empty($improvements)) {
            $improvements[] = 'Continue practicing to refine your skills';
        }
        
        if (empty($recommendations)) {
            $recommendations[] = 'Keep practicing mock interviews regularly';
        }
        
        $overallFeedback = 'Good performance overall.';
        if ($completionRate >= 90 && $avgResponseTime > 0 && $avgResponseTime < 150) {
            $overallFeedback = 'Excellent performance! You demonstrated strong preparation and effective communication.';
        }
        
        return [
            'strengths' => array_unique($strengths),
            'areas_for_improvement' => array_unique($improvements),
            'recommendations' => array_unique($recommendations),
            'overall_feedback' => $overallFeedback,
            'generated_at' => now()->toISOString(),
        ];
    }

    // ============================================
    // BULK ACTIONS
    // ============================================
    
    public function toggleBulkSelect($interviewId)
    {
        if (in_array($interviewId, $this->selectedInterviews)) {
            $this->selectedInterviews = array_diff($this->selectedInterviews, [$interviewId]);
        } else {
            $this->selectedInterviews[] = $interviewId;
        }
    }

    public function selectAllVisible()
    {
        $this->selectedInterviews = $this->interviews->pluck('id')->toArray();
    }

    public function clearBulkSelection()
    {
        $this->selectedInterviews = [];
    }

    public function executeBulkAction()
    {
        if (!$this->canManageInterviews()) {
            session()->flash('error', 'Unauthorized action.');
            return;
        }
        
        if (empty($this->bulkAction) || empty($this->selectedInterviews)) {
            return;
        }

        $interviews = MockInterview::whereIn('id', $this->selectedInterviews);

        switch ($this->bulkAction) {
            case 'delete':
                $interviews->delete();
                session()->flash('message', count($this->selectedInterviews) . ' interviews deleted.');
                break;

            case 'generate_feedback':
                $count = 0;
                $limit = 10;
                
                foreach ($interviews->get() as $interview) {
                    if ($count >= $limit) break;
                    
                    if ($interview->isCompleted() && !$interview->ai_feedback) {
                        $feedback = $this->createAIFeedback($interview);
                        $interview->update([
                            'ai_feedback' => $feedback,
                            'improvement_suggestions' => $feedback['recommendations'] ?? [],
                            'strengths' => $feedback['strengths'] ?? [],
                            'weaknesses' => $feedback['areas_for_improvement'] ?? [],
                        ]);
                        $count++;
                    }
                }
                
                session()->flash('message', "AI feedback generated for {$count} interviews.");
                break;
        }

        $this->clearBulkSelection();
        $this->dispatch('interview-updated');
    }

    // ============================================
    // USER MANAGEMENT
    // ============================================
    
    public function viewUser($userId)
    {
        $this->selectedUser = User::with(['mockInterviews' => function($query) {
            $query->latest()->limit(10);
        }])->find($userId);

        if ($this->selectedUser) {
            $this->showUserModal = true;
        }
    }

    // ============================================
    // UTILITY METHODS
    // ============================================
    
    private function clearCache()
    {
        Cache::forget('admin_mock_interview_stats');
        Cache::forget('admin_mock_popular_types');
    }

    // ============================================
    // RENDER
    // ============================================
    
    public function render()
    {
        return view('livewire.career.admin-mock-interview');
    }
}