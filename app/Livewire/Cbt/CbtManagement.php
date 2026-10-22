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
    public $showParticipantsModal = false;
    public $showEditQuestionModal = false;

    public $title = '';
    public $description = '';
    public $pass_percentage = 70;
    public $estimated_duration_minutes = 60;
    public $max_score = 100;
    public $max_attempts = null;
    public $shuffle_questions = false; // NEW
    public $shuffle_options = false;   // NEW
    public $type = 'quiz';
    public $course_id = null;

    #[\Livewire\Attributes\Locked]
    public $selectedAssessment = null;
    #[\Livewire\Attributes\Locked]
    public $editingAssessment = null;
    #[\Livewire\Attributes\Locked]
    public $editingQuestion = null;

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
        'shuffle_questions' => 'boolean',
        'shuffle_options' => 'boolean',
        'course_id' => 'nullable|exists:courses,id',
        'question_text' => 'required|string',
        'question_type' => 'required|string',
        'points' => 'required|numeric|min:0.1',
        'options' => 'required_if:question_type,multiple_choice,true_false|array|min:2',
        'correct_answers' => 'required|array|min:1',
    ];

    public function boot(): void
    {
        abort_unless(auth()->user()?->hasAnyRole(['super_admin', 'academy_admin', 'instructor']), 403);
        foreach ([$this->selectedAssessment, $this->editingAssessment] as $assessment) if ($assessment) $this->authorizeAssessment($assessment);
        if ($this->editingQuestion) $this->authorizeAssessment($this->editingQuestion->assessment);

    }

    private function authorizeAssessment(Assessment $assessment): void
    {
        $assessment->refresh();
        abort_unless(!$assessment->lesson_id && !$assessment->section_id && $assessment->type === 'quiz', 403);
        app(\App\Services\AssessmentReviewService::class)->authorize(auth()->user(), $assessment);
    }

    private function authorizeCourse(): void
    {
        if (!$this->course_id) {
            abort_unless(auth()->user()->hasAnyRole(['super_admin', 'academy_admin']), 403, 'Choose one of your courses.');
            return;
        }
        $course = Course::findOrFail($this->course_id);
        abort_unless(auth()->user()->hasAnyRole(['super_admin', 'academy_admin']) || (int) $course->instructor_id === (int) auth()->id(), 403);
    }

    private function ownedAssessment($id): Assessment
    {
        $assessment = Assessment::findOrFail($id);
        $this->authorizeAssessment($assessment);
        return $assessment;
    }

    private function validateQuestion(): void
    {
        $this->validate([
            'question_text' => 'required|string|max:10000',
            'question_type' => 'required|in:multiple_choice,true_false',
            'points' => 'required|numeric|min:0.1|max:10000',
            'correct_answers' => 'required|array|size:1',
            'correct_answers.*' => 'required|integer|min:0',
            'explanation' => 'nullable|string|max:10000',
        ]);
        if ($this->question_type === 'true_false') $this->options = ['True', 'False'];
        $this->validate(['options' => 'required|array|min:2|max:20', 'options.*' => 'required|string|max:2000']);
        foreach ($this->correct_answers as $index) abort_unless(array_key_exists((int) $index, $this->options), 422, 'Choose a valid answer key.');
    }

    public function mount()
    {
        $this->resetForm();
        $this->resetQuestionForm();
    }

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
        $assessments = Assessment::query()->when(!auth()->user()->hasAnyRole(['super_admin', 'academy_admin']), fn ($query) => $query->whereHas('course', fn ($course) => $course->where('instructor_id', auth()->id())))->where('type', 'quiz')
            ->whereNull('section_id')
            ->whereNull('lesson_id')
            ->with(['questions', 'course'])
            ->latest()
            ->paginate(10);

        $courses = Course::published()->approved()->when(!auth()->user()->hasAnyRole(['super_admin', 'academy_admin']), fn ($query) => $query->where('instructor_id', auth()->id()))->get();

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
            'shuffle_questions' => 'boolean',
            'shuffle_options' => 'boolean',
            'course_id' => 'nullable|exists:courses,id',
        ]);

        $this->authorizeCourse();
        Assessment::create([
            'course_id' => $this->course_id,
            'title' => $this->title,
            'description' => $this->description,
            'type' => 'quiz',
            'pass_percentage' => $this->pass_percentage,
            'estimated_duration_minutes' => $this->estimated_duration_minutes,
            'max_score' => $this->max_score,
            'max_attempts' => $this->max_attempts,
            'shuffle_questions' => $this->shuffle_questions,
            'shuffle_options' => $this->shuffle_options,
            'is_mandatory' => true,
            'section_id' => null,
            'lesson_id' => null,
        ]);

        $this->resetForm();
        $this->showCreateModal = false;
        session()->flash('message', 'CBT Assessment created successfully!');
    }

    public function editAssessment($assessmentId)
    {
        $this->editingAssessment = $this->ownedAssessment($assessmentId);
        $this->title = $this->editingAssessment->title;
        $this->description = $this->editingAssessment->description;
        $this->pass_percentage = $this->editingAssessment->pass_percentage;
        $this->estimated_duration_minutes = $this->editingAssessment->estimated_duration_minutes;
        $this->max_score = $this->editingAssessment->max_score;
        $this->max_attempts = $this->editingAssessment->max_attempts;
        $this->shuffle_questions = $this->editingAssessment->shuffle_questions ?? false;
        $this->shuffle_options = $this->editingAssessment->shuffle_options ?? false;
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
            'shuffle_questions' => 'boolean',
            'shuffle_options' => 'boolean',
            'course_id' => 'nullable|exists:courses,id',
        ]);

        if (!$this->editingAssessment) {
            session()->flash('error', 'Assessment not found.');
            return;
        }

        $this->authorizeAssessment($this->editingAssessment);
        $this->authorizeCourse();
        abort_if((int) $this->editingAssessment->course_id !== (int) $this->course_id && ($this->editingAssessment->studentAnswers()->exists() || \App\Models\Assessment\LearningSession::where('metadata->assessment_id', $this->editingAssessment->id)->exists()), 422, 'Keep the original course for an assessment with recorded attempts.');
        $this->editingAssessment->update([
            'title' => $this->title,
            'description' => $this->description,
            'pass_percentage' => $this->pass_percentage,
            'estimated_duration_minutes' => $this->estimated_duration_minutes,
            'max_score' => $this->max_score,
            'max_attempts' => $this->max_attempts,
            'shuffle_questions' => $this->shuffle_questions,
            'shuffle_options' => $this->shuffle_options,
            'course_id' => $this->course_id,
        ]);

        $this->resetForm();
        $this->showEditModal = false;
        session()->flash('message', 'CBT Assessment updated successfully!');
    }

    public function deleteAssessment($assessmentId)
    {
        \Illuminate\Support\Facades\DB::transaction(function () use ($assessmentId) {
            $assessment = Assessment::lockForUpdate()->findOrFail($assessmentId);
            $this->authorizeAssessment($assessment);
            abort_if($assessment->studentAnswers()->exists() || \App\Models\Assessment\LearningSession::where('activity_type', 'assessment')->where('metadata->assessment_id', $assessment->id)->exists(), 422, 'Attempt history exists; keep this assessment.');
            $assessment->delete();
        });
        session()->flash('message', 'CBT Assessment deleted successfully!');
    }

    public function manageQuestions($assessmentId)
    {
        $this->selectedAssessment = $this->ownedAssessment($assessmentId)->load('questions');
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

        $this->authorizeAssessment($this->selectedAssessment);
        $this->validateQuestion();

        Question::create([
            'assessment_id' => $this->selectedAssessment->id,
            'question_text' => $this->question_text,
            'question_type' => $this->question_type,
            'points' => $this->points,
            'options' => $this->options,
            'correct_answers' => $this->correct_answers,
            'explanation' => $this->explanation,
        ]);

        $this->resetQuestionForm();
        $this->selectedAssessment->refresh();
        session()->flash('message', 'Question added successfully!');
    }

    public function editQuestion($questionId)
    {
        $this->editingQuestion = Question::findOrFail($questionId);
        $this->authorizeAssessment($this->editingQuestion->assessment);
        $this->selectedAssessment = $this->editingQuestion->assessment;
        $this->question_text = $this->editingQuestion->question_text;
        $this->question_type = $this->editingQuestion->question_type;
        $this->points = $this->editingQuestion->points;
        $this->options = $this->editingQuestion->options ?? ['', '', '', ''];
        $this->correct_answers = $this->editingQuestion->correct_answers ?? [];
        $this->explanation = $this->editingQuestion->explanation ?? '';

        $this->showEditQuestionModal = true;
    }

    public function updateQuestion()
    {
        if (!$this->editingQuestion) {
            session()->flash('error', 'Question not found.');
            return;
        }

        $this->authorizeAssessment($this->editingQuestion->assessment);
        $this->validateQuestion();

        $this->editingQuestion->update([
            'question_text' => $this->question_text,
            'question_type' => $this->question_type,
            'points' => $this->points,
            'options' => $this->options,
            'correct_answers' => $this->correct_answers,
            'explanation' => $this->explanation,
        ]);

        $this->resetQuestionForm();
        $this->showEditQuestionModal = false;
        $this->selectedAssessment->refresh();
        session()->flash('message', 'Question updated successfully!');
    }

    public function reorderQuestions($orderedIds)
    {
        abort_unless($this->selectedAssessment, 422);
        $this->authorizeAssessment($this->selectedAssessment);
        validator(['ids' => $orderedIds], ['ids' => 'required|array', 'ids.*' => 'integer|distinct'])->validate();
        $ids = $this->selectedAssessment->questions()->pluck('id')->all();
        abort_unless(count($ids) === count($orderedIds) && !array_diff($ids, $orderedIds), 422);
        \Illuminate\Support\Facades\DB::transaction(function () use ($orderedIds) {
            foreach ($orderedIds as $index => $id) $this->selectedAssessment->questions()->whereKey($id)->update(['order' => $index + 1]);
        });
        $this->selectedAssessment->refresh();
        session()->flash('message', 'Questions reordered successfully!');
    }

    public function deleteQuestion($questionId)
    {
        $question = Question::findOrFail($questionId);
        \Illuminate\Support\Facades\DB::transaction(function () use ($question) {
            Assessment::whereKey($question->assessment_id)->lockForUpdate()->firstOrFail();
            $this->authorizeAssessment($question->assessment);
            abort_if($question->studentAnswers()->exists() || \App\Models\Assessment\LearningSession::where('activity_type', 'assessment')->where('metadata->assessment_id', $question->assessment_id)->exists(), 422, 'Attempt history exists; keep this question.');
            $question->delete();
        });
        if ($this->selectedAssessment) $this->selectedAssessment->refresh();
        session()->flash('message', 'Question deleted successfully!');
    }

    public function viewParticipants($assessmentId)
    {
        $this->selectedAssessment = $this->ownedAssessment($assessmentId)->load([
            'studentAnswers' => function ($query) {
                $query->whereNotNull('submitted_at')
                    ->with('user')
                    ->orderBy('user_id')
                    ->orderBy('attempt_number', 'desc');
            }
        ]);

        $this->showParticipantsModal = true;
    }

    public function getParticipantsData()
    {
        if (!$this->selectedAssessment) {
            return collect();
        }

        $participants = $this->selectedAssessment->studentAnswers
            ->groupBy('user_id')
            ->map(function ($answers, $userId) {
                $user = $answers->first()->user;
                $attempts = $answers->groupBy('attempt_number')->map(function ($attemptAnswers, $attemptNumber) {
                    return \Illuminate\Support\Arr::except($this->selectedAssessment->getStudentResults($attemptAnswers->first()->user_id, $attemptNumber), ['answers']);
                })->sortByDesc('attempt_number')->values();

                $bestAttempt = $attempts->sortByDesc('percentage')->first();

                return [
                    'user' => $user,
                    'user_id' => $userId,
                    'attempts' => $attempts,
                    'best_attempt' => $bestAttempt,
                    'total_attempts' => $attempts->count(),
                ];
            })
            ->sortByDesc('best_attempt.percentage')
            ->values();

        return $participants;
    }

    public function clearAttempt($userId, $attemptNumber)
    {
        abort_unless($this->selectedAssessment, 422);
        $this->authorizeAssessment($this->selectedAssessment);
        session()->flash('error', 'Assessment history is retained. Change the attempt limit to permit another attempt; recorded answers are never cleared here.');
    }

    public function clearAllUserAttempts($userId)
    {
        $this->clearAttempt($userId, null);
    }

    public function resetForm()
    {
        $this->title = '';
        $this->description = '';
        $this->pass_percentage = 70;
        $this->estimated_duration_minutes = 60;
        $this->max_score = 100;
        $this->max_attempts = null;
        $this->shuffle_questions = false;
        $this->shuffle_options = false;
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
        $this->editingQuestion = null;
    }

    public function closeModals()
    {
        $this->showCreateModal = false;
        $this->showEditModal = false;
        $this->showQuestionModal = false;
        $this->showParticipantsModal = false;
        $this->showEditQuestionModal = false;
        $this->selectedAssessment = null;
        $this->resetForm();
        $this->resetQuestionForm();
    }
}