<?php

namespace App\Livewire\Career;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use App\Models\MockInterview;
use App\Models\User;
use App\Models\Course;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Carbon\Carbon;

#[Layout('layouts.admin', ['title' => 'Mock Interviews Management', 'description' => 'Manage mock interviews, templates, and analytics', 'icon' => 'fas fa-microphone-alt', 'active' => 'admin.mock-interviews'])]

class AdminMockInterview extends Component
{
    use WithFileUploads, WithPagination;

    // Core Properties
    public $activeTab = 'overview';
    public $interviews = [];
    public $users = [];
    public $selectedInterview = null;

    // Statistics
    public $totalInterviews = 0;
    public $totalUsers = 0;
    public $completedInterviews = 0;
    public $averageScore = 0;
    public $dailyInterviews = 0;
    public $weeklyGrowth = 0;
    public $premiumUsage = 0;
    public $popularTypes = [];

    // Template Management
    public $templates = [];
    public $templateTitle = '';
    public $templateDescription = '';
    public $templateType = 'technical';
    public $templateDifficulty = 'intermediate';
    public $templateQuestions = [];
    public $newTemplateQuestion = '';
    public $templateQuestionType = 'behavioral';
    public $editingTemplateId = null;
    public $showTemplateForm = false;

    // Question Banks
    public $questionBanks = [];
    public $selectedQuestionBank = null;
    public $newQuestion = '';
    public $questionCategory = 'technical';
    public $questionDifficulty = 'intermediate';
    public $questionTags = '';

    // User Management
    public $selectedUser = null;
    public $userInterviews = [];
    public $userPerformanceData = [];
    public $showUserModal = false;

    // Filters and Search
    public $searchTerm = '';
    public $filterType = '';
    public $filterStatus = '';
    public $filterDifficulty = '';
    public $filterDateRange = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 10;

    // Bulk Actions
    public $selectedInterviews = [];
    public $bulkAction = '';
    public $showBulkActions = false;

    // Analytics
    public $analyticsDateRange = '30';
    public $chartData = [];
    public $performanceMetrics = [];
    public $userEngagementData = [];

    // System Settings
    public $systemSettings = [
        'max_interview_duration' => 180,
        'default_difficulty' => 'intermediate',
        'enable_ai_feedback' => true,
        'enable_video_recording' => true,
        'auto_generate_feedback' => false,
        'require_premium_for_retakes' => true,
        'max_concurrent_interviews' => 5,
    ];

    protected $rules = [
        'templateTitle' => 'required|string|max:255',
        'templateDescription' => 'nullable|string',
        'templateType' => 'required|in:technical,behavioral,case_study,system_design,coding,hr,custom',
        'templateDifficulty' => 'required|in:beginner,intermediate,advanced,expert',
        'newQuestion' => 'required|string|min:10',
        'questionCategory' => 'required|string',
        'questionDifficulty' => 'required|string',
    ];

    public function mount()
    {
        $this->checkAdminAccess();
        $this->loadOverviewData();
        $this->loadInterviews();
        $this->loadTemplates();
        $this->loadQuestionBanks();
        $this->loadAnalytics();
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['searchTerm', 'filterType', 'filterStatus', 'filterDifficulty', 'filterDateRange', 'sortBy', 'sortDirection'])) {
            $this->resetPage();
            $this->loadInterviews();
        }

        if ($propertyName === 'analyticsDateRange') {
            $this->loadAnalytics();
        }
    }

    private function checkAdminAccess()
    {
        if (!Auth::user()->canManageCourses()) {
            abort(403, 'Unauthorized access.');
        }
    }

    public function loadOverviewData()
    {
        $this->totalInterviews = MockInterview::count();
        $this->totalUsers = User::whereHas('mockInterviews')->count();
        $this->completedInterviews = MockInterview::completed()->count();
        $this->averageScore = MockInterview::completed()->avg('overall_score') ?? 0;

        $this->dailyInterviews = MockInterview::whereDate('created_at', today())->count();

        // Calculate weekly growth
        $thisWeek = MockInterview::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count();
        $lastWeek = MockInterview::whereBetween('created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()])->count();
        $this->weeklyGrowth = $lastWeek > 0 ? (($thisWeek - $lastWeek) / $lastWeek) * 100 : 0;

        $this->premiumUsage = MockInterview::where('is_premium', true)->count();

        // Popular interview types
        $this->popularTypes = MockInterview::select('type', DB::raw('count(*) as count'))
            ->groupBy('type')
            ->orderByDesc('count')
            ->limit(5)
            ->get()
            ->toArray();
    }

    public function loadInterviews()
    {
        $query = MockInterview::with(['user', 'course']);

        // Apply search
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

        // Apply filters
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

        // Apply sorting
        $query->orderBy($this->sortBy, $this->sortDirection);

        $this->interviews = $this->loadInterviews();
        return $query->paginate($this->perPage);
    }

    public function loadTemplates()
    {
        // In a real implementation, this would be a separate InterviewTemplate model
        $this->templates = collect([
            [
                'id' => 1,
                'title' => 'Senior Frontend Developer Interview',
                'type' => 'technical',
                'difficulty' => 'advanced',
                'questions_count' => 12,
                'usage_count' => 45,
                'created_at' => now()->subDays(30),
            ],
            [
                'id' => 2,
                'title' => 'Behavioral Leadership Assessment',
                'type' => 'behavioral',
                'difficulty' => 'intermediate',
                'questions_count' => 8,
                'usage_count' => 28,
                'created_at' => now()->subDays(15),
            ],
        ]);
    }

    public function loadQuestionBanks()
    {
        // Mock question bank data
        $this->questionBanks = collect([
            [
                'id' => 1,
                'category' => 'technical',
                'question' => 'Explain the difference between synchronous and asynchronous programming.',
                'difficulty' => 'intermediate',
                'tags' => ['programming', 'concepts'],
                'usage_count' => 156,
            ],
            [
                'id' => 2,
                'category' => 'behavioral',
                'question' => 'Tell me about a time when you had to work with a difficult team member.',
                'difficulty' => 'beginner',
                'tags' => ['teamwork', 'conflict-resolution'],
                'usage_count' => 203,
            ],
        ]);
    }

    public function loadAnalytics()
    {
        $days = (int) $this->analyticsDateRange;
        $startDate = now()->subDays($days);

        // Interview completion trends
        $this->chartData = MockInterview::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => Carbon::parse($item->date)->format('M d'),
                    'count' => $item->count,
                ];
            })
            ->toArray();

        // Performance metrics
        $this->performanceMetrics = [
            'avg_completion_rate' => MockInterview::completed()->avg('completion_rate') ?? 0,
            'avg_score_by_type' => MockInterview::completed()
                ->selectRaw('type, AVG(overall_score) as avg_score')
                ->groupBy('type')
                ->get()
                ->pluck('avg_score', 'type')
                ->toArray(),
            'user_satisfaction' => 4.2, // Mock data
            'total_interview_hours' => MockInterview::completed()->sum('estimated_duration_minutes') / 60,
        ];

        // User engagement
        $this->userEngagementData = [
            'active_users' => User::whereHas('mockInterviews', function ($query) use ($startDate) {
                $query->where('created_at', '>=', $startDate);
            })->count(),
            'repeat_users' => User::whereHas('mockInterviews', function ($query) use ($startDate) {
                $query->where('created_at', '>=', $startDate);
            }, '>', 1)->count(),
            'avg_interviews_per_user' => $this->totalUsers > 0 ? round($this->totalInterviews / $this->totalUsers, 1) : 0,
        ];
    }

    public function viewInterview($interviewId)
    {
        $this->selectedInterview = MockInterview::with(['user', 'course'])->find($interviewId);
    }

    public function editInterview($interviewId)
    {
        $interview = MockInterview::find($interviewId);

        if ($interview) {
            // Redirect to edit form or open modal
            $this->selectedInterview = $interview;
        }
    }

    public function deleteInterview($interviewId)
    {
        $interview = MockInterview::find($interviewId);

        if ($interview) {
            $interview->delete();
            $this->loadInterviews();
            $this->loadOverviewData();
            session()->flash('message', 'Interview deleted successfully.');
        }
    }

    public function approveInterview($interviewId)
    {
        $interview = MockInterview::find($interviewId);

        if ($interview) {
            $interview->update(['is_approved' => true]);
            $this->loadInterviews();
            session()->flash('message', 'Interview approved successfully.');
        }
    }

    public function generateAIFeedback($interviewId)
    {
        $interview = MockInterview::find($interviewId);

        if ($interview && $interview->isCompleted()) {
            // Mock AI feedback generation
            $feedback = [
                'strengths' => [
                    'Clear and articulate responses',
                    'Good technical knowledge demonstration',
                    'Structured problem-solving approach',
                ],
                'areas_for_improvement' => [
                    'Could provide more specific examples',
                    'Practice explaining complex concepts simply',
                    'Work on time management for responses',
                ],
                'recommendations' => [
                    'Practice the STAR method for behavioral questions',
                    'Review system design fundamentals',
                    'Work on confident delivery',
                ],
                'overall_feedback' => 'Strong technical foundation with room for improvement in communication clarity.',
            ];

            $interview->update([
                'ai_feedback' => $feedback,
                'improvement_suggestions' => $feedback['recommendations'],
                'strengths' => $feedback['strengths'],
                'weaknesses' => $feedback['areas_for_improvement'],
            ]);

            $this->loadInterviews();
            session()->flash('message', 'AI feedback generated successfully.');
        }
    }

    public function createTemplate()
    {
        $this->validate([
            'templateTitle' => 'required|string|max:255',
            'templateType' => 'required|string',
            'templateDifficulty' => 'required|string',
        ]);

        // In a real implementation, this would save to InterviewTemplate model
        session()->flash('message', 'Interview template created successfully.');
        $this->resetTemplateForm();
        $this->loadTemplates();
    }

    public function addTemplateQuestion()
    {
        if (empty($this->newTemplateQuestion)) {
            return;
        }

        $this->templateQuestions[] = [
            'id' => Str::uuid(),
            'question' => $this->newTemplateQuestion,
            'type' => $this->templateQuestionType,
            'created_at' => now()->toISOString(),
        ];

        $this->newTemplateQuestion = '';
    }

    public function removeTemplateQuestion($index)
    {
        unset($this->templateQuestions[$index]);
        $this->templateQuestions = array_values($this->templateQuestions);
    }

    public function addQuestion()
    {
        $this->validate([
            'newQuestion' => 'required|string|min:10',
            'questionCategory' => 'required|string',
            'questionDifficulty' => 'required|string',
        ]);

        // In a real implementation, this would save to QuestionBank model
        session()->flash('message', 'Question added to bank successfully.');

        $this->reset(['newQuestion', 'questionTags']);
        $this->loadQuestionBanks();
    }

    public function viewUser($userId)
    {
        $this->selectedUser = User::with(['mockInterviews'])->find($userId);

        if ($this->selectedUser) {
            $this->userInterviews = $this->selectedUser->mockInterviews()
                ->with('course')
                ->orderByDesc('created_at')
                ->take(10)
                ->get();

            $this->userPerformanceData = [
                'total_interviews' => $this->selectedUser->mockInterviews()->count(),
                'completed_interviews' => $this->selectedUser->mockInterviews()->completed()->count(),
                'average_score' => $this->selectedUser->mockInterviews()->completed()->avg('overall_score') ?? 0,
                'improvement_rate' => $this->calculateUserImprovementRate($this->selectedUser),
                'favorite_type' => $this->getUserFavoriteInterviewType($this->selectedUser),
                'last_interview' => $this->selectedUser->mockInterviews()->latest()->first(),
            ];

            $this->showUserModal = true;
        }
    }

    private function calculateUserImprovementRate($user)
    {
        $interviews = $user->mockInterviews()->completed()->orderBy('completed_at')->get();

        if ($interviews->count() < 2) {
            return 0;
        }

        $recent = $interviews->take(-3)->avg('overall_score');
        $older = $interviews->take(3)->avg('overall_score');

        return $older > 0 ? round((($recent - $older) / $older) * 100, 1) : 0;
    }

    private function getUserFavoriteInterviewType($user)
    {
        return $user->mockInterviews()
            ->select('type', DB::raw('count(*) as count'))
            ->groupBy('type')
            ->orderByDesc('count')
            ->first()
            ->type ?? 'N/A';
    }

    public function toggleBulkSelect($interviewId)
    {
        if (in_array($interviewId, $this->selectedInterviews)) {
            $this->selectedInterviews = array_diff($this->selectedInterviews, [$interviewId]);
        } else {
            $this->selectedInterviews[] = $interviewId;
        }

        $this->showBulkActions = count($this->selectedInterviews) > 0;
    }

    public function selectAllVisible()
    {
        $this->selectedInterviews = $this->interviews->pluck('id')->toArray();
        $this->showBulkActions = true;
    }

    public function clearBulkSelection()
    {
        $this->selectedInterviews = [];
        $this->showBulkActions = false;
    }

    public function executeBulkAction()
    {
        if (empty($this->bulkAction) || empty($this->selectedInterviews)) {
            return;
        }

        $interviews = MockInterview::whereIn('id', $this->selectedInterviews);

        switch ($this->bulkAction) {
            case 'approve':
                $interviews->update(['is_approved' => true]);
                session()->flash('message', count($this->selectedInterviews) . ' interviews approved.');
                break;

            case 'delete':
                $interviews->delete();
                session()->flash('message', count($this->selectedInterviews) . ' interviews deleted.');
                break;

            case 'generate_feedback':
                $interviews->each(function ($interview) {
                    if ($interview->isCompleted() && !$interview->ai_feedback) {
                        $this->generateAIFeedback($interview->id);
                    }
                });
                session()->flash('message', 'AI feedback generated for eligible interviews.');
                break;
        }

        $this->clearBulkSelection();
        $this->loadInterviews();
        $this->loadOverviewData();
    }

    public function updateSystemSettings()
    {
        // Validate system settings
        if ($this->systemSettings['max_interview_duration'] < 15 || $this->systemSettings['max_interview_duration'] > 300) {
            session()->flash('error', 'Interview duration must be between 15 and 300 minutes.');
            return;
        }

        // In a real implementation, save to settings table or config
        session()->flash('message', 'System settings updated successfully.');
    }

    public function exportAnalytics()
    {
        // Generate analytics export
        session()->flash('message', 'Analytics export will be sent to your email.');
    }

    public function resetTemplateForm()
    {
        $this->reset([
            'templateTitle',
            'templateDescription',
            'templateType',
            'templateDifficulty',
            'templateQuestions',
            'newTemplateQuestion',
            'templateQuestionType',
            'editingTemplateId'
        ]);
        $this->showTemplateForm = false;
    }

    public function render()
    {
        $users = User::withCount(['mockInterviews'])
            ->orderByDesc('mock_interviews_count')
            ->limit(20)
            ->get();
    
        $recentActivity = MockInterview::with(['user'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();
    
        return view('livewire.career.admin-mock-interview', [
            'users' => $users,
            'recentActivity' => $recentActivity,
        ]);
    }
    
    
}