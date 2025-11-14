<?php

namespace App\Livewire\Cbt;

use Livewire\Component;
use App\Models\Assessment\Assessment;
use App\Models\Assessment\Question;
use App\Models\Learning\Course;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.dashboard', ['title' => 'CBT Exam Management', 'description' => 'Manage CBT exams', 'icon' => 'fas fa-microphone-alt'])]
class CbtManagement extends Component
{
    use WithPagination;

    public $showCreateModal = false;
    public $showEditModal = false;
    public $showQuestionModal = false;

    public $title = '';
    public $description = '';
    public $pass_percentage = 70;
    public $estimated_duration_minutes = 60;
    public $max_score = 100;
    public $max_attempts = null; // NEW: null = unlimited
    public $type = 'quiz';
    public $course_id = null;

    public $selectedAssessment = null;
    public $editingAssessment = null;

    // Question properties
    public $question_text = '';
    public $question_type = 'multiple_choice';
    public $points = 1;
    public $options = ['', '', '', ''];
    public $correct_answers = [];
    public $explanation = '';

    protected $rules = [
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'pass_percentage' => 'required|integer|min:1|max:100',
        'estimated_duration_minutes' => 'required|integer|min:1',
        'max_score' => 'required|integer|min:1',
        'max_attempts' => 'nullable|integer|min:1|max:100',
        'course_id' => 'nullable|exists:courses,id',
        'question_text' => 'required|string',
        'question_type' => 'required|string',
        'points' => 'required|numeric|min:0.1',
        'options' => 'required_if:question_type,multiple_choice,true_false|array|min:2',
        'correct_answers' => 'required|array|min:1',
    ];

    public function mount()
    {
        $this->resetForm();
        $this->resetQuestionForm();
    }

    // Dispatch events when fields change
    public function updatedQuestionText($value)
    {
        $this->dispatch('question-text-updated', $value);
    }

    public function updatedExplanation($value)
    {
        $this->dispatch('explanation-updated', $value);
    }

    public function render()
    {
        // Get ONLY standalone CBT assessments (not tied to courses through sections)
        $assessments = Assessment::where('type', 'quiz')
            ->whereNull('section_id')
            ->whereNull('lesson_id')
            ->with(['questions', 'course'])
            ->latest()
            ->paginate(10);

        $courses = Course::published()->approved()->get();

        return view('livewire.cbt.cbt-management', compact('assessments', 'courses'));
    }

    public function createAssessment()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'pass_percentage' => 'required|integer|min:1|max:100',
            'estimated_duration_minutes' => 'required|integer|min:1',
            'max_score' => 'required|integer|min:1',
            'max_attempts' => 'nullable|integer|min:1|max:100',
            'course_id' => 'nullable|exists:courses,id',
        ]);

        // Handle course_id - can be null for standalone CBT
        $courseId = $this->course_id;

        Assessment::create([
            'course_id' => $courseId,
            'title' => $this->title,
            'description' => $this->description,
            'type' => 'quiz',
            'pass_percentage' => $this->pass_percentage,
            'estimated_duration_minutes' => $this->estimated_duration_minutes,
            'max_score' => $this->max_score,
            'max_attempts' => $this->max_attempts, // NEW
            'is_mandatory' => true,
            'section_id' => null, // Ensure CBT is not tied to sections
            'lesson_id' => null,  // Ensure CBT is not tied to lessons
        ]);

        $this->resetForm();
        $this->showCreateModal = false;
        session()->flash('message', 'CBT Assessment created successfully!');
    }

    public function editAssessment($assessmentId)
    {
        $this->editingAssessment = Assessment::findOrFail($assessmentId);
        $this->title = $this->editingAssessment->title;
        $this->description = $this->editingAssessment->description;
        $this->pass_percentage = $this->editingAssessment->pass_percentage;
        $this->estimated_duration_minutes = $this->editingAssessment->estimated_duration_minutes;
        $this->max_score = $this->editingAssessment->max_score;
        $this->max_attempts = $this->editingAssessment->max_attempts; // NEW
        $this->course_id = $this->editingAssessment->course_id;
        
        $this->showEditModal = true;
    }

    public function updateAssessment()
    {
        $this->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'pass_percentage' => 'required|integer|min:1|max:100',
            'estimated_duration_minutes' => 'required|integer|min:1',
            'max_score' => 'required|integer|min:1',
            'max_attempts' => 'nullable|integer|min:1|max:100',
            'course_id' => 'nullable|exists:courses,id',
        ]);

        if (!$this->editingAssessment) {
            session()->flash('error', 'Assessment not found.');
            return;
        }

        $this->editingAssessment->update([
            'title' => $this->title,
            'description' => $this->description,
            'pass_percentage' => $this->pass_percentage,
            'estimated_duration_minutes' => $this->estimated_duration_minutes,
            'max_score' => $this->max_score,
            'max_attempts' => $this->max_attempts, // NEW
            'course_id' => $this->course_id,
        ]);

        $this->resetForm();
        $this->showEditModal = false;
        session()->flash('message', 'CBT Assessment updated successfully!');
    }

    public function deleteAssessment($assessmentId)
    {
        $assessment = Assessment::find($assessmentId);
        if ($assessment) {
            $assessment->delete();
            session()->flash('message', 'CBT Assessment deleted successfully!');
        } else {
            session()->flash('error', 'Assessment not found.');
        }
    }

    public function manageQuestions($assessmentId)
    {
        $this->selectedAssessment = Assessment::with('questions')->find($assessmentId);

        if (!$this->selectedAssessment) {
            session()->flash('error', 'Assessment not found.');
            return;
        }

        $this->showQuestionModal = true;
        $this->resetQuestionForm();
    }

    public function addQuestion()
    {
        if (!$this->selectedAssessment) {
            session()->flash('error', 'No assessment selected.');
            return;
        }

        $this->validate([
            'question_text' => 'required|string',
            'question_type' => 'required|string',
            'points' => 'required|numeric|min:0.1',
        ]);

        if ($this->question_type === 'multiple_choice') {
            $this->validate([
                'options' => 'required|array|min:2',
                'correct_answers' => 'required|array|min:1',
            ]);

            // Filter out empty options
            $this->options = array_values(array_filter($this->options, function ($option) {
                return !empty(trim($option));
            }));

            if (count($this->options) < 2) {
                $this->addError('options', 'Please provide at least 2 non-empty options.');
                return;
            }
        }

        Question::create([
            'assessment_id' => $this->selectedAssessment->id,
            'question_text' => $this->question_text,
            'question_type' => $this->question_type,
            'points' => $this->points,
            'options' => $this->question_type === 'multiple_choice' ? $this->options : null,
            'correct_answers' => $this->correct_answers,
            'explanation' => $this->explanation,
        ]);

        $this->resetQuestionForm();
        $this->selectedAssessment->refresh();
        session()->flash('message', 'Question added successfully!');
    }

    public function deleteQuestion($questionId)
    {
        $question = Question::find($questionId);
        if ($question) {
            $question->delete();
            if ($this->selectedAssessment) {
                $this->selectedAssessment->refresh();
            }
            session()->flash('message', 'Question deleted successfully!');
        } else {
            session()->flash('error', 'Question not found.');
        }
    }

    public function resetForm()
    {
        $this->title = '';
        $this->description = '';
        $this->pass_percentage = 70;
        $this->estimated_duration_minutes = 60;
        $this->max_score = 100;
        $this->max_attempts = null; // NEW
        $this->course_id = null;
        $this->editingAssessment = null;
    }

    public function resetQuestionForm()
    {
        $this->question_text = '';
        $this->question_type = 'multiple_choice';
        $this->points = 1;
        $this->options = ['', '', '', ''];
        $this->correct_answers = [];
        $this->explanation = '';
    }

    public function closeModals()
    {
        $this->showCreateModal = false;
        $this->showEditModal = false;
        $this->showQuestionModal = false;
        $this->selectedAssessment = null;
        $this->resetForm();
        $this->resetQuestionForm();
    }
}