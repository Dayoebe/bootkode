<?php

namespace App\Livewire\Cbt;

use App\Models\CbtExam;
use App\Models\CbtResult;
use App\Models\Course;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

#[Layout('layouts.app')]
class CbtExamSelection extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedCourse = '';
    public $selectedType = '';
    public $selectedDifficulty = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';

    // Filters
    public $showAvailableOnly = true;
    public $showMyResults = false;

    // Modal states
    public $showExamDetails = false;
    public $selectedExam = null;
    public $showStartConfirmation = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'selectedCourse' => ['except' => ''],
        'selectedType' => ['except' => ''],
        'selectedDifficulty' => ['except' => ''],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
        'page' => ['except' => 1],
    ];

    public function mount()
    {
        // Any initialization logic
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedSelectedCourse()
    {
        $this->resetPage();
    }

    public function updatedSelectedType()
    {
        $this->resetPage();
    }

    public function updatedSelectedDifficulty()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->selectedCourse = '';
        $this->selectedType = '';
        $this->selectedDifficulty = '';
        $this->showAvailableOnly = true;
        $this->showMyResults = false;
        $this->resetPage();
    }

    public function showDetails($examId)
    {
        $this->selectedExam = CbtExam::with(['course', 'creator', 'results'])
            ->findOrFail($examId);
        $this->showExamDetails = true;
    }

    public function closeDetails()
    {
        $this->showExamDetails = false;
        $this->selectedExam = null;
    }

    public function confirmStart($examId)
    {
        $exam = CbtExam::findOrFail($examId);

        // Check if user can take exam
        [$canTake, $reason] = $exam->canUserTake(Auth::user());

        if (!$canTake) {
            session()->flash('error', $reason);
            return;
        }

        $this->selectedExam = $exam;
        $this->showStartConfirmation = true;
    }

    public function cancelStart()
    {
        $this->showStartConfirmation = false;
        $this->selectedExam = null;
    }

    public function startExam()
    {
        if (!$this->selectedExam) {
            return;
        }

        // Final check
        [$canTake, $reason] = $this->selectedExam->canUserTake(Auth::user());

        if (!$canTake) {
            session()->flash('error', $reason);
            $this->cancelStart();
            return;
        }

        // Redirect to exam taking component
        return redirect()->route('cbt.exam.take', ['exam' => $this->selectedExam->id]);
    }

    public function getUserExamStatus($exam)
    {
        $user = Auth::user();
        $results = CbtResult::where('cbt_exam_id', $exam->id)
            ->where('user_id', $user->id)
            ->orderBy('attempt_number', 'desc')
            ->get();

        if ($results->isEmpty()) {
            return ['status' => 'not_started', 'attempts' => 0, 'best_score' => null];
        }

        $latestResult = $results->first();
        $bestResult = $results->where('status', 'completed')->sortByDesc('percentage_score')->first();

        if ($latestResult->status === 'in_progress') {
            return [
                'status' => 'in_progress',
                'attempts' => $results->count(),
                'best_score' => $bestResult ? $bestResult->percentage_score : null,
                'session_id' => $latestResult->session_id
            ];
        }

        return [
            'status' => $latestResult->status,
            'attempts' => $results->count(),
            'best_score' => $bestResult ? $bestResult->percentage_score : null,
            'latest_score' => $latestResult->percentage_score,
            'passed' => $bestResult ? $bestResult->passed : false
        ];
    }

    public function resumeExam($sessionId)
    {
        $result = CbtResult::where('session_id', $sessionId)
            ->where('user_id', Auth::id())
            ->where('status', 'in_progress')
            ->first();

        if (!$result) {
            session()->flash('error', 'Exam session not found or already completed.');
            return;
        }

        return redirect()->route('cbt.exam.take', ['exam' => $result->cbt_exam_id]);
    }

    public function render()
    {
        $query = CbtExam::with(['course', 'creator'])
            ->withCount(['results', 'participants'])
            ->when($this->search, function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%')
                    ->orWhere('exam_code', 'like', '%' . $this->search . '%');
            })
            ->when($this->selectedCourse, function ($q) {
                $q->where('course_id', $this->selectedCourse);
            })
            ->when($this->selectedType, function ($q) {
                $q->where('exam_type', $this->selectedType);
            })
            ->when($this->selectedDifficulty, function ($q) {
                $q->where('difficulty_level', $this->selectedDifficulty);
            })
            ->when($this->showAvailableOnly, function ($q) {
                $q->available();
            })
            ->orderBy($this->sortBy, $this->sortDirection);

        $exams = $query->paginate(12);

        // Get user's exam statuses
        $examStatuses = [];
        foreach ($exams as $exam) {
            $examStatuses[$exam->id] = $this->getUserExamStatus($exam);
        }

        return view('livewire.c-b-t.cbt-exam-selection', [
            'exams' => $exams,
            'examStatuses' => $examStatuses,
            'courses' => Course::orderBy('title')->get(),
            'examTypes' => [
                'practice' => 'Practice',
                'mock' => 'Mock Exam',
                'final' => 'Final Exam',
                'certification' => 'Certification'
            ],
            'difficultyLevels' => [
                'beginner' => 'Beginner',
                'intermediate' => 'Intermediate',
                'advanced' => 'Advanced',
                'expert' => 'Expert'
            ]
        ]);
    }
}