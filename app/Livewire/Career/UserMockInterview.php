<?php

namespace App\Livewire\Career;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\MockInterview;
use App\Models\InterviewQuestionSet;
use App\Models\Course;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;

#[Layout('layouts.dashboard', [
    'title' => 'Mock Interviews',
    'description' => 'Practice and improve your interview skills',
    'icon' => 'fas fa-microphone-alt',
    'active' => 'student.mock-interviews'
])]
class UserMockInterview extends Component
{
    use WithPagination;

    // ============================================
    // IMPORTANT: STUDENTS CANNOT CREATE QUESTIONS
    // They can only:
    // 1. Take interviews
    // 2. View their results
    // 3. Schedule interviews from available sets
    // ============================================

    // Core Properties
    public $activeTab = 'dashboard';
    
    // Search & Filters
    public $searchTerm = '';
    public $filterType = '';
    public $filterStatus = '';
    public $filterDifficulty = '';
    
    // Interview Creation (from existing sets only)
    public $showCreateForm = false;
    public $title = '';
    public $description = '';
    public $selectedQuestionSetId = null;
    public $selectedCourseId = null;
    public $scheduled_at = null;
    public $difficulty_level = 'intermediate';
    public $type = 'technical';
    
    // Interview Taking
    public $showInterviewModal = false;
    public $currentInterview = null;
    public $currentQuestionIndex = 0;
    public $currentAnswer = '';
    public $timeRemaining = 0;
    
    // Results
    public $showResultsModal = false;
    public $selectedInterview = null;

    protected $queryString = [
        'searchTerm' => ['except' => ''],
        'filterType' => ['except' => ''],
        'filterStatus' => ['except' => ''],
        'activeTab' => ['except' => 'dashboard']
    ];

    protected $rules = [
        'title' => 'required|string|max:255',
        'selectedQuestionSetId' => 'required|exists:interview_question_sets,id',
    ];

    // ============================================
    // MOUNT & AUTHORIZATION
    // ============================================
    
    public function mount()
    {
        // Any authenticated user can access
        // but they CANNOT create questions or question sets
    }

    // ============================================
    // COMPUTED PROPERTIES
    // ============================================
    
    #[Computed]
    public function mockInterviews()
    {
        try {
            $userId = Auth::id();
            
            // If user is not authenticated, return empty collection
            if (!$userId) {
                return collect();
            }
            
            $query = MockInterview::where('user_id', $userId)
                ->with(['course', 'questionSet']);
    
            if ($this->searchTerm) {
                $query->where(function ($q) {
                    $q->where('title', 'like', '%' . $this->searchTerm . '%')
                        ->orWhere('description', 'like', '%' . $this->searchTerm . '%');
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
    
            return $query->orderBy('created_at', 'desc')->get() ?? collect();
        } catch (\Exception $e) {
            \Log::error('Error fetching mock interviews: ' . $e->getMessage(), [
                'user_id' => Auth::id() ?? 'unknown'
            ]);
            return collect(); // Always return a collection, never null
        }
    }

    #[Computed]
    public function statistics()
    {
        $userId = Auth::id();
        
        try {
            $totalInterviews = MockInterview::where('user_id', $userId)->count();
            $completedInterviews = MockInterview::where('user_id', $userId)
                ->where('status', 'completed')->count();
            $averageScore = MockInterview::where('user_id', $userId)
                ->where('status', 'completed')
                ->avg('overall_score') ?? 0;
            $upcomingInterviews = MockInterview::where('user_id', $userId)
                ->where('status', 'scheduled')
                ->where('scheduled_at', '>', now())
                ->count();
    
            return [
                'totalInterviews' => $totalInterviews,
                'completedInterviews' => $completedInterviews,
                'averageScore' => (float) $averageScore,
                'upcomingInterviews' => $upcomingInterviews,
            ];
        } catch (\Exception $e) {
            // Return default values if there's an error
            return [
                'totalInterviews' => 0,
                'completedInterviews' => 0,
                'averageScore' => 0,
                'upcomingInterviews' => 0,
            ];
        }
    }

    #[Computed]
    public function availableQuestionSets()
    {
        // Students can only select from available question sets
        // They CANNOT create their own questions
        return InterviewQuestionSet::where('is_active', true)
            ->where(function($query) {
                $query->where('is_public', true)
                    ->orWhere('created_by', Auth::id());
            })
            ->with('questions')
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function availableCourses()
    {
        return Course::whereHas('enrollments', function($query) {
            $query->where('user_id', Auth::id());
        })
        ->orWhere('instructor_id', Auth::id())
        ->orderBy('title')
        ->get();
    }

    // ============================================
    // INTERVIEW CREATION (FROM SETS ONLY)
    // ============================================
    
    public function createInterview()
    {
        $this->validate();

        try {
            $questionSet = InterviewQuestionSet::with('questions')->findOrFail($this->selectedQuestionSetId);
            
            // Get questions from the selected set
            $questions = $questionSet->questions->map(function($q) {
                return [
                    'id' => $q->id,
                    'question' => $q->question,
                    'type' => $q->type,
                    'difficulty_level' => $q->difficulty_level,
                    'max_points' => $q->max_points,
                    'time_limit' => $q->time_limit,
                ];
            })->toArray();

            $interview = MockInterview::create([
                'user_id' => Auth::id(),
                'question_set_id' => $this->selectedQuestionSetId,
                'course_id' => $this->selectedCourseId,
                'title' => $this->title,
                'description' => $this->description,
                'type' => $questionSet->type,
                'format' => 'text', // Default format
                'status' => $this->scheduled_at ? 'scheduled' : 'scheduled',
                'difficulty_level' => $questionSet->difficulty_level,
                'estimated_duration_minutes' => $questionSet->estimated_duration,
                'scheduled_at' => $this->scheduled_at ?? now(),
                'questions' => $questions,
                'is_practice' => true,
                'allow_retakes' => true,
                'max_retakes' => 3,
            ]);

            session()->flash('message', 'Interview created successfully!');
            $this->resetCreateForm();
            $this->dispatch('interview-created');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to create interview: ' . $e->getMessage());
        }
    }

    private function resetCreateForm()
    {
        $this->reset([
            'showCreateForm',
            'title',
            'description',
            'selectedQuestionSetId',
            'selectedCourseId',
            'scheduled_at',
        ]);
    }

    // ============================================
    // INTERVIEW TAKING
    // ============================================
    
    public function startInterview($interviewId)
    {
        $interview = MockInterview::where('user_id', Auth::id())
            ->findOrFail($interviewId);

        if (!$interview->isScheduled()) {
            session()->flash('error', 'This interview cannot be started.');
            return;
        }

        $interview->start();
        
        $this->currentInterview = $interview;
        $this->currentQuestionIndex = 0;
        $this->currentAnswer = '';
        $this->timeRemaining = $interview->estimated_duration_minutes * 60;
        $this->showInterviewModal = true;
    }

    public function submitAnswer()
    {
        if (!$this->currentInterview || !isset($this->currentInterview->questions[$this->currentQuestionIndex])) {
            return;
        }

        $question = $this->currentInterview->questions[$this->currentQuestionIndex];
        $responses = $this->currentInterview->user_responses ?? [];
        
        $responses[] = [
            'question_id' => $question['id'],
            'answer' => $this->currentAnswer,
            'response_time' => 0, // Calculate this properly with timer
            'timestamp' => now()->toISOString(),
        ];

        $this->currentInterview->update(['user_responses' => $responses]);

        // Move to next question or complete
        $this->currentQuestionIndex++;
        $this->currentAnswer = '';

        if ($this->currentQuestionIndex >= count($this->currentInterview->questions)) {
            $this->completeInterview();
        }
    }

    public function completeInterview()
    {
        if (!$this->currentInterview) {
            return;
        }

        $responses = $this->currentInterview->user_responses ?? [];
        $questionCount = count($this->currentInterview->questions);
        $responseCount = count($responses);
        
        $completionRate = $questionCount > 0 ? ($responseCount / $questionCount) * 100 : 0;
        
        // Basic scoring
        $scores = [
            'overall_score' => $completionRate * 0.7 + rand(15, 30),
            'technical_score' => $completionRate * 0.8 + rand(10, 20),
            'communication_score' => rand(70, 95),
            'confidence_score' => rand(65, 90),
            'completion_rate' => $completionRate,
            'avg_response_time' => collect($responses)->avg('response_time') ?? 0,
        ];

        $this->currentInterview->complete($responses, $scores);

        $this->showInterviewModal = false;
        $this->viewResults($this->currentInterview->id);
        
        session()->flash('message', 'Interview completed successfully!');
    }

    // ============================================
    // RESULTS & ANALYTICS
    // ============================================
    
    public function viewResults($interviewId)
    {
        $this->selectedInterview = MockInterview::where('user_id', Auth::id())
            ->with(['questionSet', 'course'])
            ->findOrFail($interviewId);
            
        $this->showResultsModal = true;
    }

    public function retakeInterview($interviewId)
    {
        $originalInterview = MockInterview::where('user_id', Auth::id())
            ->findOrFail($interviewId);

        if (!$originalInterview->allow_retakes || 
            $originalInterview->retake_count >= $originalInterview->max_retakes) {
            session()->flash('error', 'Maximum retakes reached.');
            return;
        }

        // Create a new interview based on the original
        $newInterview = MockInterview::create([
            'user_id' => Auth::id(),
            'original_interview_id' => $originalInterview->id,
            'question_set_id' => $originalInterview->question_set_id,
            'course_id' => $originalInterview->course_id,
            'title' => $originalInterview->title . ' (Retake ' . ($originalInterview->retake_count + 1) . ')',
            'description' => $originalInterview->description,
            'type' => $originalInterview->type,
            'format' => $originalInterview->format,
            'status' => 'scheduled',
            'difficulty_level' => $originalInterview->difficulty_level,
            'estimated_duration_minutes' => $originalInterview->estimated_duration_minutes,
            'questions' => $originalInterview->questions,
            'scheduled_at' => now(),
            'is_practice' => true,
            'allow_retakes' => true,
            'max_retakes' => $originalInterview->max_retakes,
        ]);

        $originalInterview->increment('retake_count');

        session()->flash('message', 'Retake interview created!');
        $this->startInterview($newInterview->id);
    }

    public function deleteInterview($interviewId)
    {
        $interview = MockInterview::where('user_id', Auth::id())
            ->findOrFail($interviewId);

        if ($interview->isCompleted()) {
            session()->flash('error', 'Cannot delete completed interviews.');
            return;
        }

        $interview->delete();
        session()->flash('message', 'Interview deleted successfully.');
    }

    // ============================================
    // UI HELPERS
    // ============================================
    
    public function updated($propertyName)
    {
        if (in_array($propertyName, ['searchTerm', 'filterType', 'filterStatus', 'filterDifficulty'])) {
            $this->resetPage();
        }
    }

    #[On('interview-created')]
    public function refreshInterviews()
    {
        $this->resetPage();
    }

    #[Computed]
public function analytics()
{
    try {
        $userId = Auth::id();
        if (!$userId) {
            return $this->getDefaultAnalytics();
        }

        $completedInterviews = MockInterview::where('user_id', $userId)
            ->where('status', 'completed')
            ->get();

        $averageScore = $completedInterviews->avg('overall_score') ?? 0;
        $previousAverage = $completedInterviews->skip(1)->avg('overall_score') ?? 0;
        $improvementRate = $previousAverage > 0 ? (($averageScore - $previousAverage) / $previousAverage) * 100 : 0;

        return [
            'averageScore' => $averageScore,
            'improvementRate' => max(0, round($improvementRate, 1)),
            'streakCount' => $this->calculateStreak(),
        ];
    } catch (\Exception $e) {
        return $this->getDefaultAnalytics();
    }
}

private function getDefaultAnalytics()
{
    return [
        'averageScore' => 0,
        'improvementRate' => 0,
        'streakCount' => 0,
    ];
}

private function calculateStreak()
{
    // Calculate consecutive days with completed interviews
    $userId = Auth::id();
    $interviews = MockInterview::where('user_id', $userId)
        ->where('status', 'completed')
        ->orderBy('completed_at', 'desc')
        ->get();

    if ($interviews->isEmpty()) return 0;

    $streak = 0;
    $today = now()->startOfDay();

    foreach ($interviews as $interview) {
        $interviewDate = $interview->completed_at->startOfDay();
        if ($interviewDate->diffInDays($today) == $streak) {
            $streak++;
            $today = $interviewDate;
        } else {
            break;
        }
    }

    return $streak;
}

    // ============================================
    // RENDER
    // ============================================
    
    public function startQuickPractice()
{
    try {
        $questionSet = InterviewQuestionSet::where('is_active', true)
            ->where('type', $this->type)
            ->where('difficulty_level', $this->difficulty_level)
            ->first();

        if (!$questionSet) {
            session()->flash('error', 'No question sets available for this selection.');
            return;
        }

        $this->createInterview();
    } catch (\Exception $e) {
        session()->flash('error', 'Failed to start practice: ' . $e->getMessage());
    }
}
    public function render()
    {
        return view('livewire.career.user-mock-interview');
    }
}