<?php

namespace App\Livewire\Component\CourseManagement\CourseBuilder;

use App\Models\Quiz;
use App\Models\Question;
use App\Models\Lesson;
use Livewire\Attributes\On;
use Livewire\Component;

class QuizEditor extends Component
{
    public $quizId;
    public $quiz;
    public $questions = [];
    
    // Quiz creation
    public $showQuizModal = false;
    public $newQuizTitle = '';
    public $newQuizDescription = '';
    public $newQuizPassPercentage = 70;
    
    // Question management
    public $newQuestionText = '';
    public $newQuestionType = 'multiple_choice';
    public $newQuestionOptions = ['', '', '', ''];
    public $correctOptionIndex = 0;
    public $newQuestionCorrectAnswer = '';

    protected $rules = [
        'newQuizTitle' => 'required|string|max:255',
        'newQuizDescription' => 'nullable|string|max:1000',
        'newQuizPassPercentage' => 'required|integer|min:1|max:100',
        'newQuestionText' => 'required|string|max:1000',
        'newQuestionType' => 'required|in:multiple_choice,true_false,short_answer,essay',
        'newQuestionCorrectAnswer' => 'required_if:newQuestionType,short_answer,essay',
    ];

    public function mount($quizId = null)
    {
        if ($quizId) {
            $this->quizId = $quizId;
            $this->quiz = Quiz::with('questions.options')->findOrFail($quizId);
            $this->questions = $this->quiz->questions;
        }
        $this->resetQuestionForm();
    }

    #[On('quiz-selected')]
    public function loadQuiz($quizId)
    {
        $this->quizId = $quizId;
        $this->quiz = Quiz::with('questions.options')->findOrFail($quizId);
        $this->questions = $this->quiz->questions;
    }

    public function showQuizModal()
    {
        $this->showQuizModal = true;
        $this->reset(['newQuizTitle', 'newQuizDescription', 'newQuizPassPercentage']);
        $this->newQuizPassPercentage = 70;
    }

    public function closeModals()
    {
        $this->showQuizModal = false;
    }

    public function createQuiz($lessonId)
    {
        $this->validate([
            'newQuizTitle' => 'required|string|max:255',
            'newQuizDescription' => 'nullable|string|max:1000',
            'newQuizPassPercentage' => 'required|integer|min:1|max:100'
        ]);

        // Find or create corresponding lesson in the modules system
        $moduleLesson = Lesson::where('slug', $lessonId)->first();
        
        if (!$moduleLesson) {
            $this->dispatch('notify', 'Unable to create quiz: lesson not found.', 'error');
            return;
        }

        $quiz = $moduleLesson->quizzes()->create([
            'title' => $this->newQuizTitle,
            'description' => $this->newQuizDescription,
            'pass_percentage' => $this->newQuizPassPercentage,
        ]);

        $this->quiz = $quiz;
        $this->quizId = $quiz->id;
        $this->questions = collect();
        $this->closeModals();
        $this->dispatch('notify', 'Quiz created successfully!', 'success');
        $this->dispatch('quiz-selected', $quiz->id);
    }

    public function addQuestion()
    {
        $this->validate([
            'newQuestionText' => 'required|string|max:1000',
            'newQuestionType' => 'required|in:multiple_choice,true_false,short_answer,essay'
        ]);

        if (!$this->quiz) {
            $this->dispatch('notify', 'Please select a quiz first.', 'error');
            return;
        }

        $questionData = [
            'question_text' => $this->newQuestionText,
            'type' => $this->newQuestionType,
        ];

        if (in_array($this->newQuestionType, ['short_answer', 'essay'])) {
            $this->validate(['newQuestionCorrectAnswer' => 'required']);
            $questionData['correct_answer'] = $this->newQuestionCorrectAnswer;
        }

        $question = $this->quiz->questions()->create($questionData);

        // Add options for multiple choice questions
        if ($this->newQuestionType === 'multiple_choice') {
            $validOptions = array_filter($this->newQuestionOptions, function($option) {
                return !empty(trim($option));
            });

            if (count($validOptions) < 2) {
                $question->delete();
                $this->dispatch('notify', 'Multiple choice questions need at least 2 options.', 'error');
                return;
            }

            foreach ($this->newQuestionOptions as $index => $optionText) {
                if (!empty(trim($optionText))) {
                    $question->options()->create([
                        'option_text' => $optionText,
                        'is_correct' => $index === $this->correctOptionIndex
                    ]);
                }
            }
        } elseif ($this->newQuestionType === 'true_false') {
            $question->options()->create([
                'option_text' => 'True',
                'is_correct' => $this->correctOptionIndex === 0
            ]);
            $question->options()->create([
                'option_text' => 'False',
                'is_correct' => $this->correctOptionIndex === 1
            ]);
        }

        $this->quiz->load('questions.options');
        $this->questions = $this->quiz->questions;
        $this->resetQuestionForm();
        $this->dispatch('notify', 'Question added successfully!', 'success');
    }

    public function deleteQuestion($questionId)
    {
        $question = Question::findOrFail($questionId);
        $question->options()->delete();
        $question->delete();
        
        $this->quiz->load('questions.options');
        $this->questions = $this->quiz->questions;
        $this->dispatch('notify', 'Question deleted successfully!', 'success');
    }

    public function updateQuestion($questionId, $field, $value)
    {
        $question = Question::findOrFail($questionId);
        $question->update([$field => $value]);
        
        $this->quiz->load('questions.options');
        $this->questions = $this->quiz->questions;
        $this->dispatch('notify', 'Question updated successfully!', 'success');
    }

    public function duplicateQuestion($questionId)
    {
        $question = Question::with('options')->findOrFail($questionId);
        
        $duplicatedQuestion = $question->replicate();
        $duplicatedQuestion->question_text = $question->question_text . ' (Copy)';
        $duplicatedQuestion->save();

        // Duplicate options
        foreach ($question->options as $option) {
            $duplicatedOption = $option->replicate();
            $duplicatedOption->question_id = $duplicatedQuestion->id;
            $duplicatedOption->save();
        }

        $this->quiz->load('questions.options');
        $this->questions = $this->quiz->questions;
        $this->dispatch('notify', 'Question duplicated successfully!', 'success');
    }

    public function reorderQuestions($orderedIds)
    {
        foreach ($orderedIds as $index => $id) {
            Question::find($id)->update(['order' => $index]);
        }
        
        $this->quiz->load('questions.options');
        $this->questions = $this->quiz->questions;
        $this->dispatch('notify', 'Questions reordered successfully!', 'success');
    }

    public function setCorrectOption($questionId, $optionIndex)
    {
        $question = Question::with('options')->findOrFail($questionId);
        
        foreach ($question->options as $index => $option) {
            $option->update(['is_correct' => $index === $optionIndex]);
        }
        
        $this->quiz->load('questions.options');
        $this->questions = $this->quiz->questions;
        $this->dispatch('notify', 'Correct answer updated!', 'success');
    }

    public function updateQuizSettings()
    {
        $this->validate([
            'quiz.title' => 'required|string|max:255',
            'quiz.description' => 'nullable|string|max:1000',
            'quiz.pass_percentage' => 'required|integer|min:1|max:100'
        ]);

        $this->quiz->save();
        $this->dispatch('notify', 'Quiz settings updated successfully!', 'success');
    }

    private function resetQuestionForm()
    {
        $this->reset(['newQuestionText', 'newQuestionType', 'newQuestionOptions', 'correctOptionIndex', 'newQuestionCorrectAnswer']);
        $this->newQuestionOptions = ['', '', '', ''];
        $this->newQuestionType = 'multiple_choice';
    }

    public function previewQuiz()
    {
        if (!$this->quiz) {
            $this->dispatch('notify', 'No quiz selected for preview.', 'error');
            return;
        }

        $this->dispatch('open-quiz-preview', [
            'quiz' => $this->quiz->toArray(),
            'questions' => $this->questions->toArray()
        ]);
    }

    public function exportQuiz()
    {
        if (!$this->quiz) {
            $this->dispatch('notify', 'No quiz selected for export.', 'error');
            return;
        }

        $quizData = [
            'title' => $this->quiz->title,
            'description' => $this->quiz->description,
            'pass_percentage' => $this->quiz->pass_percentage,
            'questions' => $this->questions->map(function($question) {
                return [
                    'question_text' => $question->question_text,
                    'type' => $question->type,
                    'correct_answer' => $question->correct_answer,
                    'options' => $question->options->map(function($option) {
                        return [
                            'option_text' => $option->option_text,
                            'is_correct' => $option->is_correct
                        ];
                    })
                ];
            })
        ];

        $filename = str_slug($this->quiz->title) . '-quiz-' . now()->format('Y-m-d') . '.json';
        
        return response()->json($quizData)
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    public function render()
    {
        return view('livewire.component.course-management.course-builder.quiz-editor');
    }
}