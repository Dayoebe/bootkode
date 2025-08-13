<?php

namespace App\Livewire\Component\CBT;

use Livewire\Component;
use App\Models\CbtExam;
use App\Models\CbtQuestion;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Layout;

#[layout('layouts.dashboard', [
         'title' => 'CBT Management',
         'description' => 'Manage CBT exams and questions',
         'icon' => 'fas fa-cog',
         'active' => 'cbt.management',
     ])]
 
class CbtManagement extends Component
{
    public $exams = [];
    public $selectedExamId = null;
    public $questions = [];
    public $showCreateExamModal = false;
    public $showCreateQuestionModal = false;

    #[Validate('required|string|max:255')]
    public $examTitle;

    #[Validate('nullable|string')]
    public $examDescription;

    #[Validate('required|integer|min:1')]
    public $examDuration;

    #[Validate('required|string')]
    public $questionText;

    #[Validate('required|array|min:4|max:4')]
    public $questionOptions = ['', '', '', ''];

    #[Validate('required|integer|min:0|max:3')]
    public $correctOptionIndex;

    #[Validate('required|integer|min:1')]
    public $questionMarks;

    public function mount()
    {
        if (!Auth::user()->hasAnyRole([User::ROLE_SUPER_ADMIN, User::ROLE_ACADEMY_ADMIN, User::ROLE_INSTRUCTOR])) {
            abort(403, 'Unauthorized access to CBT Management.');
        }

        $this->loadExams();
    }

    public function loadExams()
    {
        $this->exams = CbtExam::with('questions')->get()->toArray();
    }

    public function loadQuestions($examId)
    {
        $this->selectedExamId = $examId;
        $this->questions = CbtQuestion::where('cbt_exam_id', $examId)->get()->toArray();
    }

    public function createExam()
    {
        $this->validateOnly('examTitle');
        $this->validateOnly('examDescription');
        $this->validateOnly('examDuration');

        $exam = CbtExam::create([
            'title' => $this->examTitle,
            'description' => $this->examDescription,
            'duration_minutes' => $this->examDuration,
            'total_marks' => 0,
            'created_by' => Auth::id(),
        ]);

        $this->loadExams();
        $this->showCreateExamModal = false;
        $this->reset(['examTitle', 'examDescription', 'examDuration']);
        $this->dispatch('notify', type: 'success', message: 'Exam created successfully!');
    }

    public function createQuestion()
    {
        $this->validateOnly('questionText');
        $this->validateOnly('questionOptions');
        $this->validateOnly('correctOptionIndex');
        $this->validateOnly('questionMarks');

        $question = CbtQuestion::create([
            'cbt_exam_id' => $this->selectedExamId,
            'question' => $this->questionText,
            'options' => $this->questionOptions,
            'correct_option_index' => $this->correctOptionIndex,
            'marks' => $this->questionMarks,
        ]);

        $exam = CbtExam::find($this->selectedExamId);
        $exam->update(['total_marks' => $exam->questions->sum('marks')]);

        $this->loadQuestions($this->selectedExamId);
        $this->showCreateQuestionModal = false;
        $this->reset(['questionText', 'questionOptions', 'correctOptionIndex', 'questionMarks']);
        $this->dispatch('notify', type: 'success', message: 'Question added successfully!');
    }

    public function toggleCreateExamModal()
    {
        $this->showCreateExamModal = !$this->showCreateExamModal;
    }

    public function toggleCreateQuestionModal()
    {
        $this->showCreateQuestionModal = !$this->showCreateQuestionModal;
    }

    public function render()
    {
        return view('livewire.component.cbt.cbt-management');
    }
}