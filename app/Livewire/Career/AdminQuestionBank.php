<?php

namespace App\Livewire\Career;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\InterviewQuestion;
use App\Models\InterviewQuestionSet;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;

#[Layout('layouts.dashboard', [
    'title' => 'Question Bank',
    'description' => 'Manage interview questions',
    'icon' => 'fas fa-question-circle',
    'active' => 'admin.interview.questions'
])]
class AdminQuestionBank extends Component
{
    use WithPagination;

    // Search & Filters
    public $questionSearch = '';
    public $questionFilterType = '';
    public $questionFilterDifficulty = '';
    public $questionFilterStatus = '';

    // Question CRUD
    public $showCreateQuestionModal = false;
    public $editingQuestionId = null;
    public $question = '';
    public $type = 'technical';
    public $difficulty_level = 'intermediate';
    public $answer_type = 'text';
    public $options = [];
    public $correct_answer = '';
    public $sample_answer = '';
    public $keywords = '';
    public $max_points = 10;
    public $time_limit = 300;
    public $category = '';
    public $industry = '';
    public $job_role = '';

    // Question Set CRUD
    public $showCreateSetModal = false;
    public $setName = '';
    public $setDescription = '';
    public $setType = 'technical';
    public $setDifficulty = 'intermediate';
    public $setDuration = 60;

    protected $rules = [
        'question' => 'required|string|min:10',
        'type' => 'required|in:technical,behavioral,case_study,system_design,coding,hr,situational',
        'difficulty_level' => 'required|in:beginner,intermediate,advanced,expert',
        'answer_type' => 'required|in:text,multiple_choice,coding,file_upload',
        'max_points' => 'required|integer|min:1|max:100',
        'time_limit' => 'required|integer|min:30|max:3600',
    ];

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['questionSearch', 'questionFilterType', 'questionFilterDifficulty', 'questionFilterStatus'])) {
            $this->resetPage();
        }
    }

    #[Computed]
    public function questions()
    {
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
        return InterviewQuestionSet::with('creator')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function createQuestion()
    {
        $this->validate();

        try {
            $keywords = array_filter(explode(',', $this->keywords));

            InterviewQuestion::create([
                'created_by' => Auth::id(),
                'question' => $this->question,
                'type' => $this->type,
                'difficulty_level' => $this->difficulty_level,
                'answer_type' => $this->answer_type,
                'options' => $this->options,
                'correct_answer' => $this->correct_answer,
                'sample_answer' => $this->sample_answer,
                'keywords' => $keywords,
                'max_points' => $this->max_points,
                'time_limit' => $this->time_limit,
                'category' => $this->category,
                'industry' => $this->industry,
                'job_role' => $this->job_role,
                'is_active' => true,
                'is_approved' => Auth::user()->hasRole(['super_admin', 'academy_admin']),
            ]);

            session()->flash('message', 'Question created successfully!');
            $this->resetQuestionForm();
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to create question: ' . $e->getMessage());
        }
    }

    public function editQuestion($questionId)
    {
        $question = InterviewQuestion::findOrFail($questionId);

        $this->editingQuestionId = $question->id;
        $this->question = $question->question;
        $this->type = $question->type;
        $this->difficulty_level = $question->difficulty_level;
        $this->answer_type = $question->answer_type;
        $this->options = $question->options ?? [];
        $this->correct_answer = $question->correct_answer;
        $this->sample_answer = $question->sample_answer;
        $this->keywords = implode(', ', $question->keywords ?? []);
        $this->max_points = $question->max_points;
        $this->time_limit = $question->time_limit;
        $this->category = $question->category;
        $this->industry = $question->industry;
        $this->job_role = $question->job_role;

        $this->showCreateQuestionModal = true;
    }

    public function deleteQuestion($questionId)
    {
        $question = InterviewQuestion::findOrFail($questionId);
        $question->delete();

        session()->flash('message', 'Question deleted successfully.');
    }

    public function approveQuestion($questionId)
    {
        $question = InterviewQuestion::findOrFail($questionId);
        $question->approve(Auth::id());

        session()->flash('message', 'Question approved successfully.');
    }

    public function toggleQuestionStatus($questionId)
    {
        $question = InterviewQuestion::findOrFail($questionId);
        $question->update(['is_active' => !$question->is_active]);

        session()->flash('message', 'Question status updated.');
    }

    public function viewQuestion($questionId)
    {
        $this->editingQuestionId = $questionId;
        // This would open a view modal
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
            'options',
            'correct_answer',
            'sample_answer',
            'keywords',
            'max_points',
            'time_limit',
            'category',
            'industry',
            'job_role',
        ]);
    }

    public function render()
    {
        return view('livewire.career.admin-question-bank');
    }
}