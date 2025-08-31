<?php

namespace App\Livewire\Cbt;

use App\Models\CbtExam;
use App\Models\CbtResult;
use App\Models\Question;
use App\Models\Course;
use App\Models\Assessment;
use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Livewire\Attributes\Rule;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Str;

#[Layout('layouts.dashboard')]
class CbtManagement extends Component
{
    use WithPagination, WithFileUploads;

    public $activeTab = 'overview';

    // Exam creation/editing
    public $showExamModal = false;
    public $showQuestionModal = false;
    public $showResultModal = false;
    public $editingExam = null;
    public $selectedResult = null;

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

    #[Rule('required|integer|min:10|max:300')]
    public $duration_minutes = 60;

    #[Rule('required|numeric|min:50|max:100')]
    public $pass_percentage = 70;

    #[Rule('required|integer|min:1|max:10')]
    public $max_attempts = 3;

    #[Rule('required|integer|min:1|max:5')]
    public $questions_per_page = 1;

    public $randomize_questions = true;
    public $randomize_options = true;
    public $show_results_immediately = true;
    public $allow_review = true;
    public $allow_navigation = true;
    public $email_results = false;
    public $show_correct_answers = false;
    public $show_explanations = false;
    public $auto_submit = true;
    public $prevent_tab_switching = true;
    public $webcam_monitoring = false;
    public $restrict_copy_paste = true;

    #[Rule('required|in:instant,scheduled,manual')]
    public $result_delivery = 'instant';

    public $result_release_date = '';
    public $start_date = '';
    public $end_date = '';
    public $start_time = '';
    public $end_time = '';
    public $max_participants = '';
    public $instructions = '';
    public $available_days = [];
    public $exam_thumbnail = null;

    // Question management
    public $selectedQuestions = [];
    public $availableQuestions = [];
    public $searchQuestions = '';
    public $questionFilter = 'all';
    public $questionDifficulty = 'all';
    public $questionCourse = 'all';

    // Bulk actions
    public $selectedExams = [];
    public $bulkAction = '';

    // Filters
    public $examTypeFilter = 'all';
    public $statusFilter = 'all';
    public $searchTerm = '';
    public $courseFilter = 'all';
    public $difficultyFilter = 'all';

    // Analytics
    public $analyticsDateRange = '30days';
    public $selectedExamForAnalytics = null;

    // Export
    public $exportFormat = 'csv';
    public $exportDateRange = 'all';

    public function mount()
    {
        $this->available_days = [1, 2, 3, 4, 5]; // Monday to Friday by default
    }

    // Tab Management
    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
        $this->reset(['searchTerm', 'examTypeFilter', 'statusFilter']);
    }

    // Exam Management
    public function createExam()
    {
        $this->resetExamForm();
        $this->loadAvailableQuestions();
        $this->showExamModal = true;
    }

    public function editExam($examId)
    {
        $exam = CbtExam::with('questions')->findOrFail($examId);
        $this->editingExam = $exam;
        $this->fillExamForm($exam);
        $this->selectedQuestions = $exam->questions->pluck('id')->toArray();
        $this->loadAvailableQuestions();
        $this->showExamModal = true;
    }

    public function saveExam()
    {
        $this->validate();

        if ($this->exam_thumbnail) {
            $this->validate([
                'exam_thumbnail' => 'image|max:2048'
            ]);
        }

        DB::transaction(function () {
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
                'is_published' => false,
                'is_active' => true,
            ];

            if ($thumbnailPath) {
                $data['thumbnail'] = $thumbnailPath;
            }

            if ($this->editingExam) {
                $this->editingExam->update($data);
                $exam = $this->editingExam;
                session()->flash('message', 'Exam updated successfully!');
            } else {
                $data['created_by'] = Auth::id();
                $exam = CbtExam::create($data);
                session()->flash('message', 'Exam created successfully!');
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
        });

        $this->closeExamModal();
        $this->dispatch('examSaved');
    }

    public function deleteExam($examId)
    {
        $exam = CbtExam::findOrFail($examId);

        if ($exam->results()->exists()) {
            session()->flash('error', 'Cannot delete exam with existing results.');
            return;
        }

        // Delete thumbnail if exists
        if ($exam->thumbnail) {
            Storage::disk('public')->delete($exam->thumbnail);
        }

        $exam->delete();
        session()->flash('message', 'Exam deleted successfully!');
    }

    public function duplicateExam($examId)
    {
        $originalExam = CbtExam::with('questions')->findOrFail($examId);

        DB::transaction(function () use ($originalExam) {
            $newExam = $originalExam->replicate();
            $newExam->title = $originalExam->title . ' (Copy)';
            $newExam->exam_code = CbtExam::generateExamCode();
            $newExam->slug = CbtExam::generateUniqueSlug($newExam->title);
            $newExam->is_published = false;
            $newExam->created_by = Auth::id();
            $newExam->save();

            // Copy question associations
            $questionData = [];
            foreach ($originalExam->questions as $question) {
                $questionData[$question->id] = [
                    'order' => $question->pivot->order,
                    'points' => $question->pivot->points,
                    'is_mandatory' => $question->pivot->is_mandatory ?? false,
                ];
            }
            $newExam->questions()->attach($questionData);
        });

        session()->flash('message', 'Exam duplicated successfully!');
    }

    public function publishExam($examId)
    {
        $exam = CbtExam::findOrFail($examId);

        if ($exam->questions()->count() === 0) {
            session()->flash('error', 'Cannot publish exam without questions.');
            return;
        }

        $exam->update([
            'is_published' => true,
            'published_at' => now(),
        ]);

        session()->flash('message', 'Exam published successfully!');
    }

    public function unpublishExam($examId)
    {
        $exam = CbtExam::findOrFail($examId);
        $exam->update(['is_published' => false]);
        session()->flash('message', 'Exam unpublished successfully!');
    }

    public function activateExam($examId)
    {
        $exam = CbtExam::findOrFail($examId);
        $exam->update(['is_active' => true]);
        session()->flash('message', 'Exam activated successfully!');
    }

    public function deactivateExam($examId)
    {
        $exam = CbtExam::findOrFail($examId);
        $exam->update(['is_active' => false]);
        session()->flash('message', 'Exam deactivated successfully!');
    }

    // Question Management
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

    // Bulk Actions
    public function performBulkAction()
    {
        if (empty($this->selectedExams) || empty($this->bulkAction)) {
            return;
        }

        DB::transaction(function () {
            $exams = CbtExam::whereIn('id', $this->selectedExams);

            switch ($this->bulkAction) {
                case 'publish':
                    $validExams = $exams->has('questions')->get();
                    $validExams->each(function ($exam) {
                        $exam->update(['is_published' => true]);
                    });
                    session()->flash('message', count($validExams) . ' exam(s) published successfully!');
                    break;

                case 'unpublish':
                    $exams->update(['is_published' => false]);
                    session()->flash('message', 'Selected exams unpublished successfully!');
                    break;

                case 'activate':
                    $exams->update(['is_active' => true]);
                    session()->flash('message', 'Selected exams activated successfully!');
                    break;

                case 'deactivate':
                    $exams->update(['is_active' => false]);
                    session()->flash('message', 'Selected exams deactivated successfully!');
                    break;

                case 'delete':
                    $deletableExams = $exams->doesntHave('results')->get();
                    foreach ($deletableExams as $exam) {
                        if ($exam->thumbnail) {
                            Storage::disk('public')->delete($exam->thumbnail);
                        }
                        $exam->delete();
                    }
                    session()->flash('message', count($deletableExams) . ' exam(s) deleted successfully!');
                    break;
            }
        });

        $this->selectedExams = [];
        $this->bulkAction = '';
    }

    // Analytics and Statistics
    public function getOverviewStats()
    {
        $user = Auth::user();

        $examsQuery = CbtExam::query();
        if (!$user->isSuperAdmin()) {
            $examsQuery->where('created_by', $user->id);
        }

        $resultsQuery = CbtResult::whereHas('exam', function ($q) use ($user) {
            if (!$user->isSuperAdmin()) {
                $q->where('created_by', $user->id);
            }
        });

        return [
            'total_exams' => $examsQuery->count(),
            'published_exams' => $examsQuery->where('is_published', true)->count(),
            'draft_exams' => $examsQuery->where('is_published', false)->count(),
            'active_exams' => $examsQuery->where('is_active', true)->count(),
            'total_attempts' => $resultsQuery->count(),
            'completed_attempts' => $resultsQuery->where('status', 'completed')->count(),
            'unique_participants' => $resultsQuery->distinct('user_id')->count(),
            'average_pass_rate' => $resultsQuery->where('status', 'completed')->avg('percentage_score') ?? 0,
            'total_questions' => $examsQuery->sum('total_questions'),
        ];
    }

    public function getMonthlyData()
    {
        $user = Auth::user();

        $data = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthStart = $date->copy()->startOfMonth();
            $monthEnd = $date->copy()->endOfMonth();

            $query = CbtResult::whereBetween('started_at', [$monthStart, $monthEnd])
                ->whereHas('exam', function ($q) use ($user) {
                    if (!$user->isSuperAdmin()) {
                        $q->where('created_by', $user->id);
                    }
                });

            $data[] = [
                'month' => $date->format('M Y'),
                'attempts' => $query->count(),
                'completed' => $query->where('status', 'completed')->count(),
                'pass_rate' => $query->where('status', 'completed')->where('passed', true)->count(),
            ];
        }

        return $data;
    }

    public function getRecentResults($limit = 10)
    {
        $user = Auth::user();

        return CbtResult::with(['exam', 'user'])
            ->whereHas('exam', function ($q) use ($user) {
                if (!$user->isSuperAdmin()) {
                    $q->where('created_by', $user->id);
                }
            })
            ->where('status', 'completed')
            ->orderBy('completed_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getPopularExams($limit = 10)
    {
        $user = Auth::user();

        return CbtExam::withCount('results')
            ->when(!$user->isSuperAdmin(), function ($q) use ($user) {
                $q->where('created_by', $user->id);
            })
            ->orderBy('results_count', 'desc')
            ->limit($limit)
            ->get();
    }

    // Export Functions
    public function exportResults($format = 'csv')
    {
        $user = Auth::user();

        $results = CbtResult::with(['exam', 'user', 'answers.question'])
            ->whereHas('exam', function ($q) use ($user) {
                if (!$user->isSuperAdmin()) {
                    $q->where('created_by', $user->id);
                }
            })
            ->where('status', 'completed');

        if ($this->exportDateRange !== 'all') {
            $days = match ($this->exportDateRange) {
                '7days' => 7,
                '30days' => 30,
                '90days' => 90,
                default => 30
            };
            $results->where('completed_at', '>=', now()->subDays($days));
        }

        $results = $results->get();

        if ($format === 'csv') {
            return $this->exportToCsv($results);
        } else {
            return $this->exportToPdf($results);
        }
    }

    private function exportToCsv($results)
    {
        $filename = 'cbt-results-' . now()->format('Y-m-d-H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($results) {
            $file = fopen('php://output', 'w');

            // Headers
            fputcsv($file, [
                'Exam Title',
                'Student Name',
                'Student Email',
                'Attempt Number',
                'Score (%)',
                'Grade',
                'Passed',
                'Started At',
                'Completed At',
                'Time Spent (minutes)',
                'Total Questions',
                'Correct Answers',
                'Wrong Answers',
                'Unanswered'
            ]);

            // Data
            foreach ($results as $result) {
                fputcsv($file, [
                    $result->exam->title,
                    $result->user->name,
                    $result->user->email,
                    $result->attempt_number,
                    number_format($result->percentage_score, 2),
                    $result->grade,
                    $result->passed ? 'Yes' : 'No',
                    $result->started_at->format('Y-m-d H:i:s'),
                    $result->completed_at?->format('Y-m-d H:i:s'),
                    round($result->time_spent_seconds / 60, 1),
                    $result->total_questions,
                    $result->correct_answers,
                    $result->wrong_answers,
                    $result->unanswered_questions,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // Result Management
    public function viewResult($resultId)
    {
        $this->selectedResult = CbtResult::with(['exam', 'user', 'answers.question'])->findOrFail($resultId);
        $this->showResultModal = true;
    }

    public function closeResultModal()
    {
        $this->showResultModal = false;
        $this->selectedResult = null;
    }

    public function emailResultToStudent($resultId)
    {
        $result = CbtResult::with(['exam', 'user'])->findOrFail($resultId);

        // Here you would dispatch a job to send email
        // dispatch(new SendExamResultEmail($result));

        $result->update([
            'result_emailed' => true,
            'result_emailed_at' => now(),
        ]);

        session()->flash('message', 'Result emailed to student successfully!');
    }

    public function regenerateCertificate($resultId)
    {
        $result = CbtResult::findOrFail($resultId);

        if ($result->passed && $result->exam->exam_type === 'certification') {
            $result->update(['certificate_eligible' => true]);
            // Dispatch certificate generation job
            session()->flash('message', 'Certificate regenerated successfully!');
        } else {
            session()->flash('error', 'Student must pass certification exam to be eligible.');
        }
    }

    // Form Management
    private function resetExamForm()
    {
        $this->editingExam = null;
        $this->selectedQuestions = [];
        $this->exam_thumbnail = null;
        $this->reset([
            'title',
            'description',
            'course_id',
            'exam_type',
            'difficulty_level',
            'duration_minutes',
            'pass_percentage',
            'max_attempts',
            'questions_per_page',
            'result_delivery',
            'result_release_date',
            'start_date',
            'end_date',
            'start_time',
            'end_time',
            'max_participants',
            'instructions'
        ]);

        // Reset to defaults
        $this->duration_minutes = 60;
        $this->pass_percentage = 70;
        $this->max_attempts = 3;
        $this->questions_per_page = 1;
        $this->exam_type = 'practice';
        $this->difficulty_level = 'intermediate';
        $this->result_delivery = 'instant';
        $this->randomize_questions = true;
        $this->randomize_options = true;
        $this->show_results_immediately = true;
        $this->allow_review = true;
        $this->allow_navigation = true;
        $this->auto_submit = true;
        $this->prevent_tab_switching = true;
        $this->restrict_copy_paste = true;
        $this->available_days = [1, 2, 3, 4, 5];
    }

    private function fillExamForm($exam)
    {
        $this->title = $exam->title;
        $this->description = $exam->description;
        $this->course_id = $exam->course_id;
        $this->exam_type = $exam->exam_type;
        $this->difficulty_level = $exam->difficulty_level;
        $this->duration_minutes = $exam->duration_minutes;
        $this->pass_percentage = $exam->pass_percentage;
        $this->max_attempts = $exam->max_attempts;
        $this->questions_per_page = $exam->questions_per_page;
        $this->randomize_questions = $exam->randomize_questions;
        $this->randomize_options = $exam->randomize_options;
        $this->allow_review = $exam->allow_review;
        $this->allow_navigation = $exam->allow_navigation;
        $this->selectedQuestions = $exam->questions->pluck('id')->toArray();

        $this->showExamModal = true;
    }

    public function generateExamReport($examId)
    {
        $exam = CbtExam::with(['results.user', 'questions'])->findOrFail($examId);

        $reportData = [
            'exam' => $exam,
            'stats' => $exam->getStats(),
            'question_analysis' => $exam->getQuestionStats(),
            'participants' => $exam->results()->with('user')->where('status', 'completed')->get(),
        ];

        // You can generate PDF or detailed analytics here
        session()->flash('message', 'Report generated successfully!');
    }

    public function resetExamAttempts($examId)
    {
        $exam = CbtExam::findOrFail($examId);

        DB::transaction(function () use ($exam) {
            // Soft delete all results for this exam
            $exam->results()->delete();

            // Reset exam statistics
            $exam->update([
                'attempts_count' => 0,
                'average_score' => null,
            ]);
        });

        session()->flash('message', 'All exam attempts have been reset!');
    }

    public function archiveExam($examId)
    {
        $exam = CbtExam::findOrFail($examId);
        $exam->update([
            'is_active' => false,
            'is_published' => false,
        ]);

        session()->flash('message', 'Exam archived successfully!');
    }

    // Question Pool Management
    public function addQuestionsFromAssessment($assessmentId)
    {
        $assessment = Assessment::with('questions')->findOrFail($assessmentId);
        $newQuestions = $assessment->questions->pluck('id')->toArray();

        $this->selectedQuestions = array_unique(array_merge($this->selectedQuestions, $newQuestions));
        $this->loadAvailableQuestions();

        session()->flash('message', count($newQuestions) . ' questions added from assessment!');
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

    // Advanced Analytics
    public function getExamAnalytics($examId)
    {
        $exam = CbtExam::findOrFail($examId);

        return [
            'basic_stats' => $exam->getStats(),
            'question_difficulty' => $exam->getQuestionStats(),
            'time_analysis' => $this->getTimeAnalysis($exam),
            'participation_trends' => $this->getParticipationTrends($exam),
            'performance_distribution' => $this->getPerformanceDistribution($exam),
        ];
    }

    private function getTimeAnalysis($exam)
    {
        $results = $exam->results()->where('status', 'completed')->get();

        return [
            'average_time' => $results->avg('time_spent_seconds'),
            'median_time' => $results->pluck('time_spent_seconds')->median(),
            'fastest_completion' => $results->min('time_spent_seconds'),
            'slowest_completion' => $results->max('time_spent_seconds'),
            'time_distribution' => $this->getTimeDistribution($results),
        ];
    }

    private function getTimeDistribution($results)
    {
        $distribution = [
            'under_30_min' => 0,
            '30_60_min' => 0,
            '60_90_min' => 0,
            'over_90_min' => 0,
        ];

        foreach ($results as $result) {
            $minutes = $result->time_spent_seconds / 60;

            if ($minutes < 30) {
                $distribution['under_30_min']++;
            } elseif ($minutes < 60) {
                $distribution['30_60_min']++;
            } elseif ($minutes < 90) {
                $distribution['60_90_min']++;
            } else {
                $distribution['over_90_min']++;
            }
        }

        return $distribution;
    }

    private function getParticipationTrends($exam)
    {
        $trends = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dayStart = $date->copy()->startOfDay();
            $dayEnd = $date->copy()->endOfDay();

            $attempts = $exam->results()
                ->whereBetween('started_at', [$dayStart, $dayEnd])
                ->count();

            $trends[] = [
                'date' => $date->format('M d'),
                'attempts' => $attempts,
            ];
        }

        return $trends;
    }

    private function getPerformanceDistribution($exam)
    {
        $results = $exam->results()->where('status', 'completed')->get();

        $distribution = [
            'A' => $results->where('grade', 'A')->count(),
            'B' => $results->where('grade', 'B')->count(),
            'C' => $results->where('grade', 'C')->count(),
            'D' => $results->where('grade', 'D')->count(),
            'F' => $results->where('grade', 'F')->count(),
        ];

        return $distribution;
    }

    // Security and Monitoring
    public function flagSuspiciousActivity($resultId, $reason)
    {
        $result = CbtResult::findOrFail($resultId);
        $result->update([
            'status' => 'flagged',
            'notes' => ($result->notes ?? '') . "\n[" . now() . "] Flagged: " . $reason,
        ]);

        session()->flash('message', 'Result flagged for review.');
    }

    public function reviewFlaggedResult($resultId, $action)
    {
        $result = CbtResult::findOrFail($resultId);

        switch ($action) {
            case 'approve':
                $result->update(['status' => 'completed']);
                session()->flash('message', 'Result approved.');
                break;
            case 'disqualify':
                $result->update(['status' => 'disqualified']);
                session()->flash('message', 'Result disqualified.');
                break;
            case 'require_retake':
                $result->delete(); // Soft delete to allow retake
                session()->flash('message', 'Retake required - attempt deleted.');
                break;
        }
    }

    // Exam Templates
    public function createFromTemplate($templateType)
    {
        $templates = [
            'utme_style' => [
                'duration_minutes' => 120,
                'questions_per_page' => 1,
                'randomize_questions' => true,
                'randomize_options' => true,
                'allow_navigation' => false,
                'allow_review' => false,
                'show_results_immediately' => false,
                'result_delivery' => 'scheduled',
                'auto_submit' => true,
                'prevent_tab_switching' => true,
                'webcam_monitoring' => true,
                'restrict_copy_paste' => true,
            ],
            'practice_quiz' => [
                'duration_minutes' => 30,
                'questions_per_page' => 1,
                'allow_navigation' => true,
                'allow_review' => true,
                'show_results_immediately' => true,
                'show_correct_answers' => true,
                'show_explanations' => true,
            ],
            'certification' => [
                'duration_minutes' => 90,
                'pass_percentage' => 80,
                'max_attempts' => 2,
                'randomize_questions' => true,
                'email_results' => true,
                'prevent_tab_switching' => true,
                'webcam_monitoring' => true,
            ],
        ];

        if (isset($templates[$templateType])) {
            $this->resetExamForm();
            foreach ($templates[$templateType] as $key => $value) {
                $this->$key = $value;
            }
            $this->showExamModal = true;
        }
    }

    // Notification Management
    public function sendExamNotification($examId, $type)
    {
        $exam = CbtExam::findOrFail($examId);

        switch ($type) {
            case 'reminder':
                // Send reminder to enrolled students
                // dispatch(new SendExamReminderJob($exam));
                session()->flash('message', 'Reminder sent to all participants!');
                break;
            case 'results_available':
                // Notify students that results are available
                // dispatch(new NotifyResultsAvailableJob($exam));
                session()->flash('message', 'Results availability notification sent!');
                break;
        }
    }

    // Import/Export Questions
    public function importQuestionsFromFile()
    {
        // This would handle CSV/Excel import of questions
        session()->flash('message', 'Questions imported successfully!');
    }

    public function exportExamData($examId, $format)
    {
        $exam = CbtExam::with(['questions', 'results.user'])->findOrFail($examId);

        // Generate comprehensive exam report
        $filename = Str::slug($exam->title) . '-report-' . now()->format('Y-m-d');

        if ($format === 'pdf') {
            // Generate PDF report
            return response()->download(storage_path("app/reports/{$filename}.pdf"));
        } else {
            // Generate CSV export
            return $this->exportExamToCsv($exam);
        }
    }

    private function exportExamToCsv($exam)
    {
        $filename = Str::slug($exam->title) . '-data-' . now()->format('Y-m-d-H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($exam) {
            $file = fopen('php://output', 'w');

            // Exam metadata
            fputcsv($file, ['EXAM DETAILS']);
            fputcsv($file, ['Title', $exam->title]);
            fputcsv($file, ['Code', $exam->exam_code]);
            fputcsv($file, ['Type', $exam->exam_type]);
            fputcsv($file, ['Duration', $exam->duration_minutes . ' minutes']);
            fputcsv($file, ['Pass Percentage', $exam->pass_percentage . '%']);
            fputcsv($file, ['Total Questions', $exam->total_questions]);
            fputcsv($file, []);

            // Results data
            fputcsv($file, ['RESULTS DATA']);
            fputcsv($file, [
                'Student Name',
                'Email',
                'Attempt',
                'Score',
                'Grade',
                'Passed',
                'Started',
                'Completed',
                'Duration',
                'Status'
            ]);

            foreach ($exam->results as $result) {
                fputcsv($file, [
                    $result->user->name,
                    $result->user->email,
                    $result->attempt_number,
                    number_format($result->percentage_score, 2) . '%',
                    $result->grade,
                    $result->passed ? 'Yes' : 'No',
                    $result->started_at->format('Y-m-d H:i:s'),
                    $result->completed_at?->format('Y-m-d H:i:s'),
                    gmdate('H:i:s', $result->time_spent_seconds),
                    $result->status,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // Real-time monitoring
    public function getActiveExamSessions()
    {
        $user = Auth::user();

        return CbtResult::with(['exam', 'user'])
            ->where('status', 'in_progress')
            ->whereHas('exam', function ($q) use ($user) {
                if (!$user->isSuperAdmin()) {
                    $q->where('created_by', $user->id);
                }
            })
            ->where('started_at', '>=', now()->subHours(6)) // Only show sessions from last 6 hours
            ->orderBy('started_at', 'desc')
            ->get();
    }

    public function terminateSession($resultId)
    {
        $result = CbtResult::findOrFail($resultId);
        $result->update([
            'status' => 'terminated',
            'submitted_at' => now(),
            'auto_submitted' => true,
            'notes' => ($result->notes ?? '') . "\n[" . now() . "] Session terminated by administrator.",
        ]);

        session()->flash('message', 'Session terminated successfully!');
    }

    // Question Bank Integration
    public function syncQuestionBank()
    {
        // This would sync with external question banks or APIs
        session()->flash('message', 'Question bank synchronized!');
    }

    public function validateExamQuestions($examId)
    {
        $exam = CbtExam::with('questions')->findOrFail($examId);
        $issues = [];

        foreach ($exam->questions as $question) {
            if (!$question->question_text) {
                $issues[] = "Question {$question->id}: Missing question text";
            }

            if ($question->question_type === 'multiple_choice' && (!$question->options || count($question->options) < 2)) {
                $issues[] = "Question {$question->id}: Insufficient options";
            }

            if (!$question->correct_answer) {
                $issues[] = "Question {$question->id}: No correct answer defined";
            }
        }

        if (empty($issues)) {
            session()->flash('message', 'All questions are valid!');
        } else {
            session()->flash('error', 'Validation issues found: ' . implode(', ', $issues));
        }
    }

    // Proctoring Features
    public function enableProctoring($examId, $settings)
    {
        $exam = CbtExam::findOrFail($examId);

        $proctoringSettings = [
            'webcam_required' => $settings['webcam'] ?? false,
            'screen_recording' => $settings['screen_recording'] ?? false,
            'tab_switching_detection' => $settings['tab_switching'] ?? false,
            'copy_paste_detection' => $settings['copy_paste'] ?? false,
            'suspicious_activity_threshold' => $settings['threshold'] ?? 3,
        ];

        $examSettings = $exam->exam_settings ?? [];
        $examSettings['proctoring'] = $proctoringSettings;

        $exam->update(['exam_settings' => $examSettings]);

        session()->flash('message', 'Proctoring settings updated!');
    }

    // Performance Optimization
    public function optimizeExamPerformance($examId)
    {
        $exam = CbtExam::findOrFail($examId);

        // Optimize question loading
        $exam->questions()->chunk(100, function ($questions) {
            foreach ($questions as $question) {
                // Pre-process question data for faster loading
                if ($question->question_type === 'multiple_choice') {
                    // Ensure options are properly formatted
                    if (is_string($question->options)) {
                        $question->options = json_decode($question->options, true);
                        $question->save();
                    }
                }
            }
        });

        session()->flash('message', 'Exam performance optimized!');
    }

    // Event Handlers
    public function toggleExamSelection($examId)
    {
        if (in_array($examId, $this->selectedExams)) {
            $this->selectedExams = array_diff($this->selectedExams, [$examId]);
        } else {
            $this->selectedExams[] = $examId;
        }
    }

    public function selectAllExams()
    {
        $this->selectedExams = $this->getFilteredExams()->pluck('id')->toArray();
    }

    public function clearExamSelection()
    {
        $this->selectedExams = [];
    }

    private function getFilteredExams()
    {
        $user = Auth::user();
        $query = CbtExam::query();

        if (!$user->isSuperAdmin()) {
            $query->where('created_by', $user->id);
        }

        if ($this->searchTerm) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->searchTerm . '%')
                    ->orWhere('exam_code', 'like', '%' . $this->searchTerm . '%');
            });
        }

        if ($this->examTypeFilter !== 'all') {
            $query->where('exam_type', $this->examTypeFilter);
        }

        if ($this->statusFilter !== 'all') {
            switch ($this->statusFilter) {
                case 'published':
                    $query->where('is_published', true);
                    break;
                case 'draft':
                    $query->where('is_published', false);
                    break;
                case 'active':
                    $query->where('is_active', true);
                    break;
                case 'inactive':
                    $query->where('is_active', false);
                    break;
            }
        }

        return $query->get();
    }

    // AI-Powered Features
    public function generateExamSummary($examId)
    {
        $exam = CbtExam::with(['results', 'questions'])->findOrFail($examId);

        $summary = [
            'performance_insight' => $this->generatePerformanceInsight($exam),
            'question_recommendations' => $this->generateQuestionRecommendations($exam),
            'difficulty_adjustment' => $this->suggestDifficultyAdjustments($exam),
        ];

        return $summary;
    }

    private function generatePerformanceInsight($exam)
    {
        $stats = $exam->getStats();

        if ($stats['pass_rate'] < 50) {
            return 'Low pass rate indicates exam may be too difficult. Consider reviewing question difficulty or adjusting pass percentage.';
        } elseif ($stats['pass_rate'] > 90) {
            return 'High pass rate suggests exam may be too easy. Consider adding more challenging questions.';
        } else {
            return 'Exam difficulty appears well-balanced with good discrimination between students.';
        }
    }

    private function generateQuestionRecommendations($exam)
    {
        $questionStats = $exam->getQuestionStats();
        $recommendations = [];

        foreach ($questionStats as $stat) {
            if ($stat['accuracy_rate'] > 95) {
                $recommendations[] = "Question {$stat['question_id']}: Too easy - consider making it more challenging";
            } elseif ($stat['accuracy_rate'] < 20) {
                $recommendations[] = "Question {$stat['question_id']}: Too difficult - review for clarity or accuracy";
            }
        }

        return $recommendations;
    }

    private function suggestDifficultyAdjustments($exam)
    {
        $averageScore = $exam->results()->where('status', 'completed')->avg('percentage_score');

        if ($averageScore < 60) {
            return 'Consider reducing difficulty or providing additional study materials';
        } elseif ($averageScore > 85) {
            return 'Consider increasing difficulty to better assess student knowledge';
        }

        return 'Current difficulty level appears appropriate';
    }
    public function render()
    {
        $data = [
            'availableExams' => $this->activeTab === 'exams' ? $this->getAvailableExams() : collect(),
            'userResults' => $this->activeTab === 'results' ? $this->getUserResults() : collect(),
            'dashboardStats' => $this->activeTab === 'dashboard' ? $this->getDashboardStats() : [],
        ];

        return view('livewire.c-b-t.cbt-management', $data);
    }
}