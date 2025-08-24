<?php

namespace App\Livewire\CourseManagement\CourseBuilder;

use App\Models\Question;
use App\Models\Assessment;
use Livewire\Component;

class QuestionManager extends Component
{
    public $assessmentId;
    public $assessment;
    public $questions = [];
    public $showCreateForm = false;
    public $editingQuestion = null;
    
    // Question form fields
    public $questionText = '';
    public $questionType = 'multiple_choice';
    public $points = 1;
    public $explanation = '';
    public $isRequired = true;
    public $timeLimit = null;
    
    // Options for multiple choice, true/false, etc.
    public $options = [];
    public $correctAnswers = [];
    
    // For different question types
    public $trueAnswerText = 'True';
    public $falseAnswerText = 'False';
    public $correctAnswer = '';
    
    protected $listeners = [
        'questionCreated' => 'loadQuestions',
        'questionUpdated' => 'loadQuestions',
        'questionDeleted' => 'loadQuestions',
    ];

    protected function rules()
    {
        $rules = [
            'questionText' => 'required|string|max:1000',
            'questionType' => 'required|in:multiple_choice,true_false,short_answer,essay,fill_blank,matching',
            'points' => 'required|numeric|min:0.1|max:100',
            'explanation' => 'nullable|string|max:500',
            'isRequired' => 'boolean',
            'timeLimit' => 'nullable|integer|min:1|max:300',
        ];

        if ($this->questionType === 'multiple_choice') {
            $rules['options.*'] = 'required|string|max:255';
            $rules['correctAnswers'] = 'required|array|min:1';
        } elseif ($this->questionType === 'true_false') {
            $rules['correctAnswer'] = 'required|in:true,false';
        } elseif (in_array($this->questionType, ['short_answer', 'fill_blank'])) {
            $rules['correctAnswer'] = 'required|string|max:255';
        }

        return $rules;
    }

    public function mount($assessmentId)
    {
        $this->assessmentId = $assessmentId;
        $this->assessment = Assessment::findOrFail($assessmentId);
        $this->loadQuestions();
        
        // Initialize default options for multiple choice
        $this->options = ['', '', '', ''];
    }

    protected function loadQuestions()
    {
        $this->questions = Question::where('assessment_id', $this->assessmentId)
            ->orderBy('order')
            ->get()
            ->toArray();
    }

    public function toggleCreateForm()
    {
        $this->showCreateForm = !$this->showCreateForm;
        if (!$this->showCreateForm) {
            $this->resetForm();
        }
    }

    public function selectQuestionType($type)
    {
        $this->questionType = $type;
        $this->resetTypeSpecificFields();
    }

    protected function resetTypeSpecificFields()
    {
        $this->correctAnswers = [];
        $this->correctAnswer = '';
        
        if ($this->questionType === 'multiple_choice') {
            $this->options = ['', '', '', ''];
        } elseif ($this->questionType === 'true_false') {
            $this->correctAnswer = 'true';
        }
    }

    public function addOption()
    {
        if (count($this->options) < 6) {
            $this->options[] = '';
        }
    }

    public function removeOption($index)
    {
        if (count($this->options) > 2) {
            array_splice($this->options, $index, 1);
            
            // Remove from correct answers if it was selected
            if (in_array($index, $this->correctAnswers)) {
                $this->correctAnswers = array_diff($this->correctAnswers, [$index]);
            }
            
            // Adjust correct answer indices
            $this->correctAnswers = array_map(function($answerIndex) use ($index) {
                return $answerIndex > $index ? $answerIndex - 1 : $answerIndex;
            }, $this->correctAnswers);
        }
    }

    public function createQuestion()
    {
        $this->validate();

        $questionData = [
            'assessment_id' => $this->assessmentId,
            'question_text' => $this->questionText,
            'question_type' => $this->questionType,
            'points' => $this->points,
            'explanation' => $this->explanation,
            'is_required' => $this->isRequired,
            'time_limit' => $this->timeLimit,
            'order' => count($this->questions) + 1,
        ];

        // Handle type-specific data
        if ($this->questionType === 'multiple_choice') {
            $questionData['options'] = json_encode(array_values(array_filter($this->options)));
            $questionData['correct_answers'] = json_encode($this->correctAnswers);
        } elseif ($this->questionType === 'true_false') {
            $questionData['options'] = json_encode([$this->trueAnswerText, $this->falseAnswerText]);
            $questionData['correct_answers'] = json_encode([$this->correctAnswer === 'true' ? 0 : 1]);
        } elseif (in_array($this->questionType, ['short_answer', 'fill_blank'])) {
            $questionData['correct_answers'] = json_encode([$this->correctAnswer]);
        }

        Question::create($questionData);

        $this->loadQuestions();
        $this->resetForm();
        $this->showCreateForm = false;
        
        session()->flash('success', 'Question created successfully!');
        $this->dispatch('questionCreated');
    }

    public function editQuestion($questionId)
    {
        $question = collect($this->questions)->firstWhere('id', $questionId);
        if ($question) {
            $this->editingQuestion = $question;
            $this->fillFormFromQuestion($question);
            $this->showCreateForm = true;
        }
    }

    protected function fillFormFromQuestion($question)
    {
        $this->questionText = $question['question_text'];
        $this->questionType = $question['question_type'];
        $this->points = $question['points'];
        $this->explanation = $question['explanation'] ?? '';
        $this->isRequired = $question['is_required'] ?? true;
        $this->timeLimit = $question['time_limit'];

        if ($question['question_type'] === 'multiple_choice') {
            $this->options = json_decode($question['options'], true) ?? [];
            $this->correctAnswers = json_decode($question['correct_answers'], true) ?? [];
        } elseif ($question['question_type'] === 'true_false') {
            $options = json_decode($question['options'], true) ?? ['True', 'False'];
            $this->trueAnswerText = $options[0] ?? 'True';
            $this->falseAnswerText = $options[1] ?? 'False';
            $correctAnswers = json_decode($question['correct_answers'], true) ?? [0];
            $this->correctAnswer = $correctAnswers[0] === 0 ? 'true' : 'false';
        } elseif (in_array($question['question_type'], ['short_answer', 'fill_blank'])) {
            $correctAnswers = json_decode($question['correct_answers'], true) ?? [''];
            $this->correctAnswer = $correctAnswers[0] ?? '';
        }
    }

    public function updateQuestion()
    {
        $this->validate();

        if ($this->editingQuestion) {
            $question = Question::findOrFail($this->editingQuestion['id']);
            
            $questionData = [
                'question_text' => $this->questionText,
                'question_type' => $this->questionType,
                'points' => $this->points,
                'explanation' => $this->explanation,
                'is_required' => $this->isRequired,
                'time_limit' => $this->timeLimit,
            ];

            // Handle type-specific data
            if ($this->questionType === 'multiple_choice') {
                $questionData['options'] = json_encode(array_values(array_filter($this->options)));
                $questionData['correct_answers'] = json_encode($this->correctAnswers);
            } elseif ($this->questionType === 'true_false') {
                $questionData['options'] = json_encode([$this->trueAnswerText, $this->falseAnswerText]);
                $questionData['correct_answers'] = json_encode([$this->correctAnswer === 'true' ? 0 : 1]);
            } elseif (in_array($this->questionType, ['short_answer', 'fill_blank'])) {
                $questionData['correct_answers'] = json_encode([$this->correctAnswer]);
            }

            $question->update($questionData);

            $this->loadQuestions();
            $this->resetForm();
            $this->showCreateForm = false;
            
            session()->flash('success', 'Question updated successfully!');
            $this->dispatch('questionUpdated');
        }
    }

    public function deleteQuestion($questionId)
    {
        Question::findOrFail($questionId)->delete();
        $this->loadQuestions();
        
        session()->flash('success', 'Question deleted successfully!');
        $this->dispatch('questionDeleted');
    }

    public function duplicateQuestion($questionId)
    {
        $originalQuestion = Question::findOrFail($questionId);
        $duplicatedQuestion = $originalQuestion->replicate();
        $duplicatedQuestion->question_text = $originalQuestion->question_text . ' (Copy)';
        $duplicatedQuestion->order = count($this->questions) + 1;
        $duplicatedQuestion->save();

        $this->loadQuestions();
        session()->flash('success', 'Question duplicated successfully!');
    }

    public function reorderQuestions($orderedIds)
    {
        foreach ($orderedIds as $index => $id) {
            Question::where('id', $id)->update(['order' => $index + 1]);
        }
        
        $this->loadQuestions();
    }

    protected function resetForm()
    {
        $this->questionText = '';
        $this->questionType = 'multiple_choice';
        $this->points = 1;
        $this->explanation = '';
        $this->isRequired = true;
        $this->timeLimit = null;
        $this->options = ['', '', '', ''];
        $this->correctAnswers = [];
        $this->correctAnswer = '';
        $this->trueAnswerText = 'True';
        $this->falseAnswerText = 'False';
        $this->editingQuestion = null;
    }

    public function render()
    {
        return view('livewire.course-management.course-builder.question-manager');
    }
}