<?php

namespace App\Livewire\Career;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\MockInterview;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;

#[Layout('layouts.dashboard', [
    'title' => 'Mock Interviews Management',
    'description' => 'Manage mock interviews, templates, and analytics',
    'icon' => 'fas fa-microphone-alt',
    'active' => 'admin.mock-interviews'
])]
class AdminMockInterview extends Component
{
    use WithPagination;

    // Core Properties
    public $activeTab = 'overview';
    public $selectedInterview = null;
    
    // Filters
    public $searchTerm = '';
    public $filterType = '';
    public $filterStatus = '';
    public $filterDifficulty = '';
    public $filterDateRange = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 15;
    
    // Bulk Actions
    public $selectedInterviews = [];
    public $bulkAction = '';
    
    // Analytics
    public $analyticsDateRange = '30';
    
    // UI State
    public $showUserModal = false;
    public $selectedUser = null;

    protected $queryString = [
        'searchTerm' => ['except' => ''],
        'filterType' => ['except' => ''],
        'filterStatus' => ['except' => ''],
        'activeTab' => ['except' => 'overview']
    ];

    public function mount()
    {
        $this->checkAdminAccess();
    }

    private function checkAdminAccess()
    {
        if (!Auth::user()->canManageCourses()) {
            abort(403, 'Unauthorized access.');
        }
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['searchTerm', 'filterType', 'filterStatus', 'filterDifficulty', 'filterDateRange', 'sortBy', 'sortDirection'])) {
            $this->resetPage();
        }
    }

    // Computed Properties for Performance
    #[Computed]
    public function statistics()
    {
        return Cache::remember('admin_mock_interview_stats', 300, function () {
            return [
                'totalInterviews' => MockInterview::count(),
                'totalUsers' => User::whereHas('mockInterviews')->count(),
                'completedInterviews' => MockInterview::completed()->count(),
                'averageScore' => MockInterview::completed()->avg('overall_score') ?? 0,
                'dailyInterviews' => MockInterview::whereDate('created_at', today())->count(),
                'weeklyGrowth' => $this->calculateWeeklyGrowth(),
                'premiumUsage' => MockInterview::where('is_premium', true)->count(),
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

    #[Computed]
    public function interviews()
    {
        $query = MockInterview::with(['user:id,name,email', 'course:id,title']);

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

        return $query->paginate($this->perPage);
    }

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

    // Actions
    #[On('interview-updated')]
    public function refreshInterviews()
    {
        $this->resetPage();
        Cache::forget('admin_mock_interview_stats');
        Cache::forget('admin_mock_popular_types');
    }

    public function viewInterview($interviewId)
    {
        $this->selectedInterview = MockInterview::with(['user', 'course'])->find($interviewId);
    }

    public function deleteInterview($interviewId)
    {
        $interview = MockInterview::find($interviewId);

        if ($interview) {
            $interview->delete();
            $this->dispatch('interview-updated');
            session()->flash('message', 'Interview deleted successfully.');
        }
    }

    public function approveInterview($interviewId)
    {
        $interview = MockInterview::find($interviewId);

        if ($interview) {
            $interview->update(['is_approved' => true]);
            $this->dispatch('interview-updated');
            session()->flash('message', 'Interview approved successfully.');
        }
    }

    public function generateAIFeedback($interviewId)
    {
        $interview = MockInterview::find($interviewId);

        if ($interview && $interview->isCompleted()) {
            // Generate feedback directly (no job)
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
        
        // Analyze completion rate
        if ($completionRate >= 90) {
            $strengths[] = 'Excellent interview completion rate';
        } elseif ($completionRate < 70) {
            $improvements[] = 'Consider completing all interview questions';
            $recommendations[] = 'Practice time management to answer all questions';
        }
        
        // Analyze response times
        $avgResponseTime = collect($responses)->avg('response_time') ?? 0;
        
        if ($avgResponseTime > 0 && $avgResponseTime < 120) {
            $strengths[] = 'Good response time management';
        } elseif ($avgResponseTime > 180) {
            $improvements[] = 'Response times could be improved';
            $recommendations[] = 'Practice answering questions more concisely';
        }
        
        // Analyze response quality
        foreach ($responses as $response) {
            $answerLength = strlen($response['answer'] ?? '');
            
            if ($answerLength < 50) {
                $improvements[] = 'Some responses were too brief';
                $recommendations[] = 'Provide more detailed explanations with examples';
                break;
            }
        }
        
        // Add type-specific feedback
        switch ($interview->type) {
            case 'technical':
                $strengths[] = 'Demonstrated technical knowledge';
                $recommendations[] = 'Review core technical concepts regularly';
                break;
            case 'behavioral':
                $strengths[] = 'Shared relevant experiences';
                $recommendations[] = 'Use the STAR method for behavioral questions';
                break;
            case 'system_design':
                $recommendations[] = 'Practice designing scalable systems';
                break;
        }
        
        // Ensure we have at least some feedback
        if (empty($strengths)) {
            $strengths[] = 'Participated in the interview process';
        }
        
        if (empty($improvements)) {
            $improvements[] = 'Continue practicing to refine your skills';
        }
        
        if (empty($recommendations)) {
            $recommendations[] = 'Keep practicing mock interviews regularly';
        }
        
        // Generate overall feedback
        $overallFeedback = 'Good performance overall. ';
        if ($completionRate >= 90 && $avgResponseTime > 0 && $avgResponseTime < 150) {
            $overallFeedback = 'Excellent performance! You demonstrated strong preparation and effective communication. Keep up the great work!';
        } elseif ($completionRate >= 70) {
            $overallFeedback = 'Good performance with room for improvement. Focus on providing more detailed responses and examples.';
        } else {
            $overallFeedback = 'There is room for improvement. Consider practicing more and working on time management.';
        }
        
        return [
            'strengths' => array_unique($strengths),
            'areas_for_improvement' => array_unique($improvements),
            'recommendations' => array_unique($recommendations),
            'overall_feedback' => $overallFeedback,
            'generated_at' => now()->toISOString(),
        ];
    }

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
                // Generate feedback for each (limit to prevent timeout)
                $count = 0;
                $limit = 10; // Process max 10 at a time
                
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

    public function viewUser($userId)
    {
        $this->selectedUser = User::with(['mockInterviews' => function($query) {
            $query->latest()->limit(10);
        }])->find($userId);

        if ($this->selectedUser) {
            $this->showUserModal = true;
        }
    }

    public function exportAnalytics()
    {
        // Simple CSV export without job
        try {
            $startDate = now()->subDays((int) $this->analyticsDateRange);
            
            $interviews = MockInterview::where('created_at', '>=', $startDate)
                ->with(['user:id,name,email'])
                ->get();

            $filename = 'interview-analytics-' . now()->format('Y-m-d-His') . '.csv';
            $filepath = storage_path('app/public/' . $filename);
            
            $csv = fopen($filepath, 'w');
            
            // Headers
            fputcsv($csv, [
                'ID', 'Title', 'User Name', 'User Email', 'Type', 'Status', 
                'Difficulty', 'Overall Score', 'Created At', 'Completed At'
            ]);
            
            // Data
            foreach ($interviews as $interview) {
                fputcsv($csv, [
                    $interview->id,
                    $interview->title,
                    $interview->user->name,
                    $interview->user->email,
                    $interview->type,
                    $interview->status,
                    $interview->difficulty_level,
                    $interview->overall_score ?? 'N/A',
                    $interview->created_at->format('Y-m-d H:i:s'),
                    $interview->completed_at ? $interview->completed_at->format('Y-m-d H:i:s') : 'N/A',
                ]);
            }
            
            fclose($csv);
            
            session()->flash('message', 'Analytics exported successfully. Download from: ' . asset('storage/' . $filename));
            
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to export analytics: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.career.admin-mock-interview');
    }
}