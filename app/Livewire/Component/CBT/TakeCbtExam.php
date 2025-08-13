<?php

namespace App\Livewire\Component\CBT;

use Livewire\Component;
use App\Models\CbtExam;
use App\Models\CbtQuestion;
use App\Models\CbtAnswer;
use App\Models\CbtResult;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;

class TakeCbtExam extends Component
{
    public $examId;
    public $exam;
    public $questions = [];
    public $currentQuestionIndex = 0;
    public $answers = [];
    public $timeRemaining;
    public $isExamStarted = false;
    public $isFullScreen = false;

    protected $listeners = [
        'startExam' => 'startExam',
        'submitAnswer' => 'submitAnswer',
        'submitExam' => 'submitExam',
        'tick' => 'updateTimer',
    ];

    public function mount($examId)
    {
        if (!Auth::user()->hasRole(User::ROLE_STUDENT)) {
            abort(403, 'Unauthorized access to CBT Exam.');
        }

        $this->examId = $examId;
        $this->exam = CbtExam::with('questions')->findOrFail($examId);
        $this->questions = $this->exam->questions->toArray();
        $this->timeRemaining = $this->exam->duration_minutes * 60;
    }

    public function startExam()
    {
        $this->isExamStarted = true;
        $this->isFullScreen = true;
        $this->dispatch('startTimer');
    }

    public function submitAnswer($questionId, $selectedOptionIndex)
    {
        $question = CbtQuestion::findOrFail($questionId);
        $isCorrect = $selectedOptionIndex === $question->correct_option_index;

        CbtAnswer::updateOrCreate(
            [
                'cbt_exam_id' => $this->examId,
                'cbt_question_id' => $questionId,
                'user_id' => Auth::id(),
            ],
            [
                'selected_option_index' => $selectedOptionIndex,
                'is_correct' => $isCorrect,
            ]
        );

        $this->answers[$questionId] = $selectedOptionIndex;
        $this->dispatch('notify', type: 'success', message: 'Answer saved!');
    }

    public function submitExam()
    {
        $score = CbtAnswer::where('cbt_exam_id', $this->examId)
            ->where('user_id', Auth::id())
            ->where('is_correct', true)
            ->sum('marks');

        $totalMarks = $this->exam->total_marks;

        CbtResult::create([
            'cbt_exam_id' => $this->examId,
            'user_id' => Auth::id(),
            'score' => $score,
            'total_marks' => $totalMarks,
            'passed' => $score >= ($totalMarks * 0.5), // Pass mark: 50%
            'completed_at' => now(),
        ]);

        $this->isExamStarted = false;
        $this->dispatch('notify', type: 'success', message: 'Exam submitted successfully!');
        $this->redirect(route('cbt.results'));
    }

    public function updateTimer()
    {
        if ($this->isExamStarted && $this->timeRemaining > 0) {
            $this->timeRemaining--;
            if ($this->timeRemaining <= 0) {
                $this->submitExam();
            }
        }
    }

    public function nextQuestion()
    {
        if ($this->currentQuestionIndex < count($this->questions) - 1) {
            $this->currentQuestionIndex++;
        }
    }

    public function previousQuestion()
    {
        if ($this->currentQuestionIndex > 0) {
            $this->currentQuestionIndex--;
        }
    }

    public function render()
    {
        return view('livewire.component.cbt.take-cbt-exam')
            ->layout('layouts.dashboard', [
                'title' => 'Take CBT Exam',
                'description' => 'Computer-Based Test for ' . $this->exam->title,
                'icon' => 'fas fa-pencil-alt',
                'active' => 'cbt.exam',
            ]);
    }
}