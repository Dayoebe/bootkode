<?php

namespace App\Livewire\Cbt;

use App\Models\CbtExam;
use App\Models\Question;
use App\Models\Course;
use App\Models\Assessment;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Str;

#[Layout('layouts.dashboard')]
class CbtExamBuilder extends Component
{
    use WithFileUploads;

    public $currentStep = 1;
    public $maxSteps = 4;
    
    // Exam Basic Info
    #[Rule('required|string|min:3|max:255')]
    public $title = '';
    
    #[Rule('nullable|string|max:1000')]
    public $description = '';
    
    #[Rule('required|exists:courses,id')]
    public $course_id = '';
    
    #[Rule('required|in:practice,mock,final,certification')]
    public $exam_type = 'practice';
    
    #[Rule('required|in:beginner,intermediate,advanced,expert')]
    public $difficulty_level = 'intermediate';
    
    #[Rule('nullable|string|max:1000')]
    public $instructions = '';
    
    public $exam_thumbnail = null;
    
    // Exam Configuration
    #[Rule('required|integer|min:10|max:300')]
    public $duration_minutes = 60;
    
    #[Rule('required|numeric|min:50|max:100')]
    public $pass_percentage = 70;
    
    #[Rule('required|integer|min:1|max:10')]
    public $max_attempts = 3;
    
    #[Rule('required|integer|min:1|max:5')]
    public $questions_per_page = 1;
    
    public $max_participants = '';
    
    // Exam Settings
    public $randomize_questions = true;
    public $randomize_options = true;
    public $allow_navigation = true;
    public $allow_review = true;
    public $show_results_immediately = true;
    public $show_correct_answers = false;
    public $show_explanations = false;
    public $email_results = false;
    public $auto_submit = true;
    public $prevent_tab_switching = false;
    public $webcam_monitoring = false;
    public $restrict_copy_paste = false;
    
    // Scheduling
    #[Rule('required|in:instant,scheduled,manual')]
    public $result_delivery = 'instant';
    
    public $result_release_date = '';
    public $start_date = '';
    public $end_date = '';
    public $start_time = '';
    public $end_time = '';
    public $available_days = [];
    
    // Questions
    public $selectedQuestions = [];
    public $availableQuestions = [];
    public $searchQuestions = '';
    public $questionFilter = 'all';
    public $questionDifficulty = 'all';
    public $questionCourse = 'all';
    
    // Form state
    public $editingExam = null;
    public $showPreview = false;

    public function mount($examId = null)
    {
        $this->available_days = [1, 2, 3, 4, 5]; // Monday to Friday by default
        
        if ($examId) {
            $this->loadExistingExam($examId);
        }
        
        $this->loadAvailableQuestions();
    }

    public function loadExistingExam($examId)
    {
        $exam = CbtExam::with('questions')->findOrFail($examId);
        $this->editingExam = $exam;
        
        // Fill basic info
        $this->title = $exam->title;
        $this->description = $exam->description;
        $this->course_id = $exam->course_id;
        $this->exam_type = $exam->exam_type;
        $this->difficulty_level = $exam->difficulty_level;
        $this->instructions = $exam->instructions;
        
        // Fill configuration
        $this->duration_minutes = $exam->duration_minutes;
        $this->pass_percentage = $exam->pass_percentage;
        $this->max_attempts = $exam->max_attempts;
        $this->questions_per_page = $exam->questions_per_page;
        $this->max_participants = $exam->max_participants;
        
        // Fill settings
        $this->randomize_questions = $exam->randomize_questions;
        $this->randomize_options = $exam->randomize_options;
        $this->allow_navigation = $exam->allow_navigation;
        $this->allow_review = $exam->allow_review;
        $this->show_results_immediately = $exam->show_results_immediately;
        $this->show_correct_answers = $exam->show_correct_answers;
        $this->show_explanations = $exam->show_explanations;
        $this->email_results = $exam->email_results;
        
        // Fill advanced settings
        $settings = $exam->exam_settings ?? [];
        $this->auto_submit = $settings['auto_submit'] ?? true;
        $this->prevent_tab_switching = $settings['prevent_tab_switching'] ?? false;
        $this->webcam_monitoring = $settings['webcam_monitoring'] ?? false;
        $this->restrict_copy_paste = $settings['restrict_copy_paste'] ?? false;
        
        // Fill scheduling
        $this->result_delivery = $exam->result_delivery;
        $this->result_release_date = $exam->result_release_date?->format('Y-m-d\TH:i');
        $this->start_date = $exam->start_date?->format('Y-m-d\TH:i');
        $this->end_date = $exam->end_date?->format('Y-m-d\TH:i');
        $this->start_time = $exam->start_time?->format('H:i');
        $this->end_time = $exam->end_time?->format('H:i');
        $this->available_days = $exam->available_days ?? [];
        
        // Load selected questions
        $this->selectedQuestions = $exam->questions->pluck('id')->toArray();
    }

    public function nextStep()
    {
        $this->validateCurrentStep();
        
        if ($this->currentStep < $this->maxSteps) {
            $this->currentStep++;
        }
        
        if ($this->currentStep === 3) {
            $this->loadAvailableQuestions();
        }
    }

    public function previousStep()
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    public function goToStep($step)
    {
        if ($step <= $this->currentStep + 1 && $step >= 1 && $step <= $this->maxSteps) {
            $this->currentStep = $step;
            
            if ($step === 3) {
                $this->loadAvailableQuestions();
            }
        }
    }

    private function validateCurrentStep()
    {
        switch ($this->currentStep) {
            case 1:
                $this->validate([
                    'title' => 'required|string|min:3|max:255',
                    'course_id' => 'required|exists:courses,id',
                    'exam_type' => 'required|in:practice,mock,final,certification',
                    'difficulty_level' => 'required|in:beginner,intermediate,advanced,expert',
                ]);
                break;
            case 2:
                $this->validate([
                    'duration_minutes' => 'required|integer|min:10|max:300',
                    'pass_percentage' => 'required|numeric|min:50|max:100',
                    'max_attempts' => 'required|integer|min:1|max:10',
                    'questions_per_page' => 'required|integer|min:1|max:5',
                ]);
                break;
        }
    }

    public function loadAvailableQuestions()
    {
        $query = Question::with('assessment.course');

        if ($this->searchQuestions) {
            $query->where('question_text', 'like', '%' . $this->searchQuestions . '%');
        }

        if ($this->questionFilter !== 'all') {
            $query->where('question_type', $this->questionFilter);
        }

        if ($this->questionDifficulty !== 'all') {
            $query->where('difficulty_level', $this->questionDifficulty);
        }

        if ($this->questionCourse !== 'all') {
            $query->whereHas('assessment.course', function ($q) {
                $q->where('id', $this->questionCourse);
            });
        }

        $this->availableQuestions = $query->orderBy('created_at', 'desc')->take(50)->get();
    }

    public function toggleQuestionSelection($questionId)
    {
        if (in_array($questionId, $this->selectedQuestions)) {
            $this->selectedQuestions = array_diff($this->selectedQuestions, [$questionId]);
        } else {
            $this->selectedQuestions[] = $questionId;
        }
    }

    public function selectAllQuestions()
    {
        $this->selectedQuestions = $this->availableQuestions->pluck('id')->toArray();
    }

    public function clearQuestionSelection()
    {
        $this->selectedQuestions = [];
    }

    public function randomSelectQuestions($count)
    {
        if ($count > count($this->availableQuestions)) {
            session()->flash('error', 'Not enough questions available for random selection.');
            return;
        }

        $randomQuestions = $this->availableQuestions->random($count)->pluck('id')->toArray();
        $this->selectedQuestions = array_unique(array_merge($this->selectedQuestions, $randomQuestions));

        session()->flash('message', $count . ' questions randomly selected!');
    }

    public function addQuestionsFromAssessment($assessmentId)
    {
        $assessment = Assessment::with('questions')->findOrFail($assessmentId);
        $newQuestions = $assessment->questions->pluck('id')->toArray();

        $this->selectedQuestions = array_unique(array_merge($this->selectedQuestions, $newQuestions));
        $this->loadAvailableQuestions();

        session()->flash('message', count($newQuestions) . ' questions added from assessment!');
    }

    public function togglePreview()
    {
        $this->showPreview = !$this->showPreview;
    }

    public function saveDraft()
    {
        $this->validateCurrentStep();
        $this->saveExam(false);
    }

    public function publishExam()
    {
        $this->validate();
        
        if (empty($this->selectedQuestions)) {
            session()->flash('error', 'Please select at least one question before publishing.');
            return;
        }
        
        $this->saveExam(true);
    }

    private function saveExam($publish = false)
    {
        if ($this->exam_thumbnail) {
            $this->validate([
                'exam_thumbnail' => 'image|max:2048'
            ]);
        }

        DB::transaction(function () use ($publish) {
            $thumbnailPath = null;
            if ($this->exam_thumbnail) {
                $thumbnailPath = $this->exam_thumbnail->store('exam-thumbnails', 'public');
            }

            $data = [
                'title' => $this->title,
                'description' => $this->description,
                'course_id' => $this->course_id,
                'exam_type' => $this->exam_type,
                'difficulty_level' => $this->difficulty_level,
                'duration_minutes' => $this->duration_minutes,
                'pass_percentage' => $this->pass_percentage,
                'max_attempts' => $this->max_attempts,
                'questions_per_page' => $this->questions_per_page,
                'randomize_questions' => $this->randomize_questions,
                'randomize_options' => $this->randomize_options,
                'show_results_immediately' => $this->show_results_immediately,
                'allow_review' => $this->allow_review,
                'allow_navigation' => $this->allow_navigation,
                'email_results' => $this->email_results,
                'show_correct_answers' => $this->show_correct_answers,
                'show_explanations' => $this->show_explanations,
                'result_delivery' => $this->result_delivery,
                'result_release_date' => $this->result_release_date ? Carbon::parse($this->result_release_date) : null,
                'start_date' => $this->start_date ? Carbon::parse($this->start_date) : null,
                'end_date' => $this->end_date ? Carbon::parse($this->end_date) : null,
                'start_time' => $this->start_time ? Carbon::parse($this->start_time) : null,
                'end_time' => $this->end_time ? Carbon::parse($this->end_time) : null,
                'max_participants' => $this->max_participants ?: null,
                'instructions' => $this->instructions,
                'available_days' => $this->available_days,
                'exam_settings' => [
                    'auto_submit' => $this->auto_submit,
                    'prevent_tab_switching' => $this->prevent_tab_switching,
                    'webcam_monitoring' => $this->webcam_monitoring,
                    'restrict_copy_paste' => $this->restrict_copy_paste,
                ],
                'is_published' => $publish,
                'is_active' => true,
            ];

            if ($thumbnailPath) {
                $data['thumbnail'] = $thumbnailPath;
            }

            if ($this->editingExam) {
                $this->editingExam->update($data);
                $exam = $this->editingExam;
                $message = $publish ? 'Exam updated and published successfully!' : 'Exam updated and saved as draft!';
            } else {
                $data['created_by'] = Auth::id();
                $exam = CbtExam::create($data);
                $message = $publish ? 'Exam created and published successfully!' : 'Exam created and saved as draft!';
            }

            // Sync questions if selected
            if (!empty($this->selectedQuestions)) {
                $questionData = [];
                foreach ($this->selectedQuestions as $index => $questionId) {
                    $question = Question::find($questionId);
                    $questionData[$questionId] = [
                        'order' => $index + 1,
                        'points' => $question->points ?? 1,
                        'is_mandatory' => false,
                    ];
                }
                $exam->questions()->sync($questionData);
                $exam->update(['total_questions' => count($this->selectedQuestions)]);
            }

            session()->flash('message', $message);
            
            if ($publish) {
                return redirect()->route('cbt.management');
            }
        });
    }

    public function render()
    {
        return view('livewire.c-b-t.cbt-exam-builder', [
            'courses' => Course::all(), 
            'assessments' => Assessment::with('course')->get(),
            'questionTypes' => ['multiple_choice', 'true_false', 'essay', 'fill_blank'],
            'difficultyLevels' => ['beginner', 'intermediate', 'advanced', 'expert'],
        ]);
    }
}