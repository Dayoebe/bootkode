<?php

namespace App\Livewire\Career;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\MockInterview;
use App\Models\Course;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Carbon\Carbon;

#[Layout('layouts.dashboard', ['title' => 'Mock Interviews', 'description' => 'Practice and improve your interview skills', 'icon' => 'fas fa-microphone-alt', 'active' => 'mock-interviews'])]

class UserMockInterview extends Component
{
    use WithFileUploads;

    // Core Properties
    public $mockInterviews = [];
    public $currentInterview = null;
    public $activeTab = 'dashboard';
    
    // Interview Creation/Editing
    public $title = '';
    public $description = '';
    public $type = 'technical';
    public $format = 'text';
    public $difficulty_level = 'intermediate';
    public $industry = '';
    public $job_role = '';
    public $company_type = '';
    public $estimated_duration_minutes = 60;
    public $scheduled_at = '';
    public $course_id = null;
    public $is_premium = false;
    public $premium_features = [];
    public $custom_questions = [];
    public $newQuestion = '';
    public $questionType = 'behavioral';
    
    // Interview Taking
    public $currentQuestionIndex = 0;
    public $currentAnswer = '';
    public $responses = [];
    public $startTime = null;
    public $endTime = null;
    public $recordingEnabled = false;
    public $audioRecording = null;
    public $videoRecording = null;
    public $interviewInProgress = false;
    public $timeRemaining = 0;
    
    // UI State
    public $showCreateForm = false;
    public $showInterviewModal = false;
    public $showResultsModal = false;
    public $editingInterviewId = null;
    public $viewMode = 'grid';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $filterType = '';
    public $filterStatus = '';
    public $filterDifficulty = '';
    public $searchTerm = '';
    
    // Statistics
    public $totalInterviews = 0;
    public $completedInterviews = 0;
    public $averageScore = 0;
    public $upcomingInterviews = 0;
    public $streakCount = 0;
    public $improvementRate = 0;
    
    // Premium Features State
    public $aiAnalysisEnabled = false;
    public $videoAnalysisEnabled = false;
    public $speechAnalysisEnabled = false;
    public $emotionAnalysisEnabled = false;
    public $confidenceTrackingEnabled = false;
    
    // Question Banks
    public $technicalQuestions = [
        'Explain the difference between synchronous and asynchronous programming.',
        'How would you optimize a slow database query?',
        'Describe the MVC architecture pattern.',
        'What are the principles of RESTful API design?',
        'Explain the concept of Big O notation with examples.',
    ];
    
    public $behavioralQuestions = [
        'Tell me about a time when you had to work with a difficult team member.',
        'Describe a situation where you had to meet a tight deadline.',
        'Give an example of a time you showed leadership.',
        'Tell me about a mistake you made and how you handled it.',
        'Describe your greatest professional achievement.',
    ];
    
    public $systemDesignQuestions = [
        'Design a URL shortening service like bit.ly',
        'How would you design a chat application like WhatsApp?',
        'Design a social media news feed system.',
        'How would you design a video streaming platform?',
        'Design a distributed cache system.',
    ];

    protected $rules = [
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'type' => 'required|in:technical,behavioral,case_study,system_design,coding,hr,custom',
        'format' => 'required|in:text,voice,video,mixed',
        'difficulty_level' => 'required|in:beginner,intermediate,advanced,expert',
        'estimated_duration_minutes' => 'required|integer|min:15|max:180',
        'scheduled_at' => 'nullable|date|after:now',
        'industry' => 'nullable|string|max:100',
        'job_role' => 'nullable|string|max:100',
        'company_type' => 'nullable|string|max:100',
    ];

    public function mount()
    {
        $this->loadInterviews();
        $this->calculateStatistics();
        $this->checkPremiumAccess();
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['sortBy', 'sortDirection', 'filterType', 'filterStatus', 'filterDifficulty', 'searchTerm'])) {
            $this->loadInterviews();
        }
        
        if ($propertyName === 'type') {
            $this->generateQuestionsForType();
        }
    }

    public function loadInterviews()
    {
        $query = Auth::user()->mockInterviews()->with(['course', 'interviewer']);

        // Apply search
        if ($this->searchTerm) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->searchTerm . '%')
                  ->orWhere('description', 'like', '%' . $this->searchTerm . '%')
                  ->orWhere('job_role', 'like', '%' . $this->searchTerm . '%')
                  ->orWhere('industry', 'like', '%' . $this->searchTerm . '%');
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

        // Apply sorting
        $query->orderBy($this->sortBy, $this->sortDirection);

        $this->mockInterviews = $query->get();
    }

    public function calculateStatistics()
    {
        $user = Auth::user();
        $interviews = $user->mockInterviews();
        
        $this->totalInterviews = $interviews->count();
        $this->completedInterviews = $interviews->completed()->count();
        $this->upcomingInterviews = $interviews->upcoming()->count();
        
        $completedInterviewsData = $interviews->completed()->get();
        $this->averageScore = $completedInterviewsData->avg('overall_score') ?? 0;
        
        // Calculate streak (consecutive days with completed interviews)
        $this->streakCount = $this->calculateStreakCount($completedInterviewsData);
        
        // Calculate improvement rate (comparing last 5 interviews to previous 5)
        $this->improvementRate = $this->calculateImprovementRate($completedInterviewsData);
    }

    private function calculateStreakCount($interviews)
    {
        $streak = 0;
        $currentDate = now();
        $interviewDates = $interviews->pluck('completed_at')->map(fn($date) => $date->format('Y-m-d'))->unique()->sort()->values();
        
        for ($i = $interviewDates->count() - 1; $i >= 0; $i--) {
            $interviewDate = Carbon::parse($interviewDates[$i]);
            $daysDiff = $currentDate->diffInDays($interviewDate);
            
            if ($daysDiff <= $streak + 1) {
                $streak++;
                $currentDate = $interviewDate;
            } else {
                break;
            }
        }
        
        return $streak;
    }

    private function calculateImprovementRate($interviews)
    {
        $recent = $interviews->sortByDesc('completed_at')->take(5)->avg('overall_score') ?? 0;
        $previous = $interviews->sortByDesc('completed_at')->skip(5)->take(5)->avg('overall_score') ?? 0;
        
        if ($previous == 0) return 0;
        
        return round((($recent - $previous) / $previous) * 100, 1);
    }

    public function checkPremiumAccess()
    {
        $user = Auth::user();
        $this->aiAnalysisEnabled = $user->hasRole(['premium', 'instructor', 'admin']);
        $this->videoAnalysisEnabled = $this->aiAnalysisEnabled;
        $this->speechAnalysisEnabled = $this->aiAnalysisEnabled;
        $this->emotionAnalysisEnabled = $this->aiAnalysisEnabled;
        $this->confidenceTrackingEnabled = $this->aiAnalysisEnabled;
    }

    public function createInterview()
    {
        $this->validate();

        try {
            $questions = $this->generateQuestionsForType();
            
            $interviewData = [
                'title' => $this->title,
                'description' => $this->description,
                'type' => $this->type,
                'format' => $this->format,
                'difficulty_level' => $this->difficulty_level,
                'industry' => $this->industry,
                'job_role' => $this->job_role,
                'company_type' => $this->company_type,
                'estimated_duration_minutes' => $this->estimated_duration_minutes,
                'scheduled_at' => $this->scheduled_at ? Carbon::parse($this->scheduled_at) : null,
                'course_id' => $this->course_id,
                'questions' => $questions,
                'custom_questions' => $this->custom_questions,
                'is_premium' => $this->is_premium,
                'premium_features' => $this->premium_features,
                'ai_feedback_enabled' => in_array('ai_feedback', $this->premium_features),
                'video_recording_enabled' => in_array('video_recording', $this->premium_features),
                'detailed_analytics_enabled' => in_array('detailed_analytics', $this->premium_features),
                'slug' => Str::slug($this->title . '-' . Str::random(6)),
            ];

            if ($this->editingInterviewId) {
                $interview = MockInterview::find($this->editingInterviewId);
                $interview->update($interviewData);
                session()->flash('message', 'Mock interview updated successfully!');
            } else {
                Auth::user()->mockInterviews()->create($interviewData);
                session()->flash('message', 'Mock interview created successfully!');
            }

            $this->resetForm();
            $this->loadInterviews();
            $this->calculateStatistics();

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to save interview: ' . $e->getMessage());
        }
    }

    private function generateQuestionsForType()
    {
        $questionCount = match($this->difficulty_level) {
            'beginner' => 5,
            'intermediate' => 8,
            'advanced' => 12,
            'expert' => 15,
            default => 8
        };

        $questions = [];
        $sourceQuestions = match($this->type) {
            'technical' => $this->technicalQuestions,
            'behavioral' => $this->behavioralQuestions,
            'system_design' => $this->systemDesignQuestions,
            'coding' => $this->technicalQuestions, // Could be separate array
            'hr' => $this->behavioralQuestions, // Could be separate array
            default => array_merge($this->technicalQuestions, $this->behavioralQuestions)
        };

        $selectedQuestions = collect($sourceQuestions)->random(min($questionCount, count($sourceQuestions)));
        
        foreach ($selectedQuestions as $index => $question) {
            $questions[] = [
                'id' => Str::uuid(),
                'question' => $question,
                'type' => $this->type,
                'order' => $index + 1,
                'time_limit' => $this->calculateTimePerQuestion(),
                'points' => 10
            ];
        }

        return $questions;
    }

    private function calculateTimePerQuestion()
    {
        return match($this->type) {
            'technical' => 300, // 5 minutes
            'behavioral' => 180, // 3 minutes
            'system_design' => 600, // 10 minutes
            'coding' => 900, // 15 minutes
            default => 240 // 4 minutes
        };
    }

    public function startInterview($interviewId)
    {
        $interview = MockInterview::find($interviewId);
        
        if (!$interview || $interview->user_id !== Auth::id()) {
            session()->flash('error', 'Interview not found or access denied.');
            return;
        }

        $interview->start();
        $this->currentInterview = $interview;
        $this->interviewInProgress = true;
        $this->currentQuestionIndex = 0;
        $this->responses = [];
        $this->startTime = now();
        $this->showInterviewModal = true;
        
        $this->loadInterviews();
    }

    public function submitAnswer()
    {
        if (!$this->currentInterview || !isset($this->currentInterview->questions[$this->currentQuestionIndex])) {
            return;
        }

        $question = $this->currentInterview->questions[$this->currentQuestionIndex];
        $responseTime = now()->diffInSeconds($this->startTime);

        $this->responses[$question['id']] = [
            'question_id' => $question['id'],
            'answer' => $this->currentAnswer,
            'response_time' => $responseTime,
            'timestamp' => now()->toISOString(),
        ];

        $this->currentAnswer = '';
        $this->currentQuestionIndex++;

        // Check if interview is complete
        if ($this->currentQuestionIndex >= count($this->currentInterview->questions)) {
            $this->completeInterview();
        }
    }

    public function completeInterview()
    {
        if (!$this->currentInterview) {
            return;
        }

        $this->endTime = now();
        $totalDuration = $this->startTime->diffInMinutes($this->endTime);

        // Calculate basic scores
        $scores = $this->calculateScores();

        $this->currentInterview->complete($this->responses, $scores);
        
        // Generate AI feedback if enabled
        if ($this->currentInterview->ai_feedback_enabled && $this->aiAnalysisEnabled) {
            $this->generateAIFeedback();
        }

        $this->interviewInProgress = false;
        $this->showInterviewModal = false;
        $this->showResultsModal = true;
        
        $this->loadInterviews();
        $this->calculateStatistics();
    }

    private function calculateScores()
    {
        // Basic scoring algorithm - in production, this would be more sophisticated
        $totalQuestions = count($this->currentInterview->questions);
        $answeredQuestions = count($this->responses);
        
        $completionRate = ($answeredQuestions / $totalQuestions) * 100;
        $averageResponseTime = collect($this->responses)->avg('response_time') ?? 0;
        
        // Mock scoring based on completion and response quality
        $technicalScore = min(100, $completionRate * 0.8 + (120 - min($averageResponseTime, 120)) / 120 * 20);
        $communicationScore = rand(70, 95); // Would be based on speech analysis
        $confidenceScore = rand(65, 90); // Would be based on voice/video analysis
        
        return [
            'overall_score' => ($technicalScore + $communicationScore + $confidenceScore) / 3,
            'technical_score' => $technicalScore,
            'communication_score' => $communicationScore,
            'confidence_score' => $confidenceScore,
            'completion_rate' => $completionRate,
            'avg_response_time' => $averageResponseTime,
        ];
    }

    private function generateAIFeedback()
    {
        // Mock AI feedback - in production, this would call actual AI services
        $feedback = [
            'strengths' => [
                'Clear and concise responses',
                'Good technical knowledge demonstration',
                'Appropriate use of examples',
            ],
            'areas_for_improvement' => [
                'Could provide more specific examples',
                'Consider structuring responses using STAR method',
                'Practice speaking with more confidence',
            ],
            'recommendations' => [
                'Practice technical concepts in ' . $this->currentInterview->industry,
                'Work on reducing filler words',
                'Prepare more behavioral examples',
            ],
            'overall_feedback' => 'Good performance overall. Focus on providing more concrete examples and maintaining consistent energy throughout the interview.',
        ];

        $this->currentInterview->update([
            'ai_feedback' => $feedback,
            'improvement_suggestions' => $feedback['recommendations'],
            'strengths' => $feedback['strengths'],
            'weaknesses' => $feedback['areas_for_improvement'],
        ]);
    }

    public function editInterview($interviewId)
    {
        $interview = MockInterview::find($interviewId);
        
        if (!$interview || $interview->user_id !== Auth::id()) {
            session()->flash('error', 'Interview not found or access denied.');
            return;
        }

        $this->editingInterviewId = $interview->id;
        $this->title = $interview->title;
        $this->description = $interview->description;
        $this->type = $interview->type;
        $this->format = $interview->format;
        $this->difficulty_level = $interview->difficulty_level;
        $this->industry = $interview->industry;
        $this->job_role = $interview->job_role;
        $this->company_type = $interview->company_type;
        $this->estimated_duration_minutes = $interview->estimated_duration_minutes;
        $this->scheduled_at = $interview->scheduled_at ? $interview->scheduled_at->format('Y-m-d\TH:i') : '';
        $this->course_id = $interview->course_id;
        $this->custom_questions = $interview->custom_questions ?? [];
        $this->is_premium = $interview->is_premium;
        $this->premium_features = $interview->premium_features ?? [];
        
        $this->showCreateForm = true;
    }

    public function deleteInterview($interviewId)
    {
        $interview = MockInterview::find($interviewId);
        
        if ($interview && $interview->user_id === Auth::id()) {
            $interview->delete();
            $this->loadInterviews();
            $this->calculateStatistics();
            session()->flash('message', 'Interview deleted successfully.');
        }
    }

    public function retakeInterview($interviewId)
    {
        $interview = MockInterview::find($interviewId);
        
        if (!$interview || $interview->user_id !== Auth::id()) {
            session()->flash('error', 'Interview not found or access denied.');
            return;
        }

        if (!$interview->allow_retakes || $interview->retake_count >= $interview->max_retakes) {
            session()->flash('error', 'Retakes not allowed or maximum retakes reached.');
            return;
        }

        // Create a new interview instance for retake
        $retakeData = $interview->toArray();
        unset($retakeData['id'], $retakeData['created_at'], $retakeData['updated_at']);
        $retakeData['title'] = $interview->title . ' (Retake ' . ($interview->retake_count + 1) . ')';
        $retakeData['original_interview_id'] = $interview->id;
        $retakeData['status'] = MockInterview::STATUS_SCHEDULED;
        $retakeData['started_at'] = null;
        $retakeData['completed_at'] = null;
        $retakeData['user_responses'] = null;
        $retakeData['overall_score'] = null;

        $retake = Auth::user()->mockInterviews()->create($retakeData);
        $interview->increment('retake_count');

        $this->loadInterviews();
        session()->flash('message', 'Retake interview created successfully!');
    }

    public function addCustomQuestion()
    {
        if (empty($this->newQuestion)) {
            return;
        }

        $this->custom_questions[] = [
            'id' => Str::uuid(),
            'question' => $this->newQuestion,
            'type' => $this->questionType,
            'created_at' => now()->toISOString(),
        ];

        $this->newQuestion = '';
    }

    public function removeCustomQuestion($index)
    {
        unset($this->custom_questions[$index]);
        $this->custom_questions = array_values($this->custom_questions);
    }

    public function viewResults($interviewId)
    {
        $interview = MockInterview::find($interviewId);
        
        if (!$interview || $interview->user_id !== Auth::id()) {
            session()->flash('error', 'Interview not found or access denied.');
            return;
        }

        $this->currentInterview = $interview;
        $this->showResultsModal = true;
    }

    public function resetForm()
    {
        $this->reset([
            'title', 'description', 'type', 'format', 'difficulty_level',
            'industry', 'job_role', 'company_type', 'estimated_duration_minutes',
            'scheduled_at', 'course_id', 'custom_questions', 'newQuestion',
            'questionType', 'is_premium', 'premium_features', 'editingInterviewId'
        ]);
        
        $this->showCreateForm = false;
    }

    public function togglePremiumFeature($feature)
    {
        if (in_array($feature, $this->premium_features)) {
            $this->premium_features = array_diff($this->premium_features, [$feature]);
        } else {
            $this->premium_features[] = $feature;
        }
        
        $this->is_premium = !empty($this->premium_features);
    }

    public function render()
    {
        $courses = Course::published()->approved()->get(['id', 'title']);
        
        return view('livewire.career.user-mock-interview', [
            'courses' => $courses,
        ]);
    }
}