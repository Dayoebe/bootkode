<?php

namespace App\Livewire\StudentManagement;

use App\Models\Assessment\StudentAnswer;
use App\Models\Career\JobApplication;
use App\Models\Content\Portfolio;
use App\Models\Learning\Course;
use App\Models\Mentorship\CodeReview;
use App\Models\Mentorship\CodeSubmission;
use App\Models\Mentorship\Mentorship;
use App\Models\Mentorship\MockInterview;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.dashboard', ['title' => 'Learner Journey', 'description' => 'Your guided path from goal to career support', 'icon' => 'fas fa-route', 'active' => 'learner.journey'])]
class LearnerJourney extends Component
{
    public string $selectedGoal = '';

    public function mount(): void
    {
        $this->selectedGoal = (string) data_get(Auth::user()->metadata, 'learner_journey.goal', '');
    }

    public function chooseGoal(string $goal): void
    {
        if (! array_key_exists($goal, $this->goals())) {
            return;
        }

        $user = Auth::user();
        $metadata = $user->metadata ?? [];

        data_set($metadata, 'learner_journey.goal', $goal);
        data_set($metadata, 'learner_journey.goal_selected_at', now()->toDateTimeString());

        $user->forceFill(['metadata' => $metadata])->save();

        $this->selectedGoal = $goal;
        $this->dispatch('notify', 'Learning goal saved.', 'success');
    }

    public function render()
    {
        $user = Auth::user();
        $enrolledCourseIds = $this->enrolledCourseIds($user->id);
        $progressByCourse = $this->progressByCourse($user->id);
        $currentCourse = $this->currentCourse($user->id, $progressByCourse);
        $stats = $this->journeyStats($user->id, $enrolledCourseIds, $progressByCourse);
        $steps = $this->steps($stats, $currentCourse);
        $nextStep = collect($steps)->firstWhere('status', 'next') ?? collect($steps)->firstWhere('status', 'open');
        $completedSteps = collect($steps)->where('done', true)->count();
        $journeyProgress = count($steps) > 0 ? (int) round(($completedSteps / count($steps)) * 100) : 0;

        return view('livewire.student-management.learner-journey', [
            'goals' => $this->goals(),
            'selectedGoalData' => $this->selectedGoal ? ($this->goals()[$this->selectedGoal] ?? null) : null,
            'steps' => $steps,
            'nextStep' => $nextStep,
            'completedSteps' => $completedSteps,
            'journeyProgress' => $journeyProgress,
            'stats' => $stats,
            'currentCourse' => $currentCourse,
            'recommendedCourses' => $this->recommendedCourses($enrolledCourseIds),
        ]);
    }

    private function goals(): array
    {
        return [
            'frontend' => [
                'label' => 'Frontend Developer',
                'short' => 'Build polished websites and interfaces.',
                'icon' => 'fas fa-window-maximize',
                'keywords' => ['frontend', 'html', 'css', 'javascript', 'react', 'vue', 'ui', 'web'],
            ],
            'backend' => [
                'label' => 'Backend Developer',
                'short' => 'Build APIs, databases, and server-side systems.',
                'icon' => 'fas fa-server',
                'keywords' => ['backend', 'php', 'laravel', 'api', 'database', 'server'],
            ],
            'fullstack' => [
                'label' => 'Full-Stack Developer',
                'short' => 'Move from interface to database with confidence.',
                'icon' => 'fas fa-layer-group',
                'keywords' => ['full stack', 'fullstack', 'web', 'laravel', 'javascript', 'application'],
            ],
            'data' => [
                'label' => 'Data and AI Builder',
                'short' => 'Use data, Python, automation, and AI tools.',
                'icon' => 'fas fa-brain',
                'keywords' => ['data', 'python', 'ai', 'machine learning', 'analytics', 'automation'],
            ],
            'security' => [
                'label' => 'Cybersecurity Starter',
                'short' => 'Learn secure systems, networks, and defense basics.',
                'icon' => 'fas fa-shield-alt',
                'keywords' => ['security', 'cyber', 'network', 'ethical hacking', 'defense'],
            ],
            'product' => [
                'label' => 'Digital Product Builder',
                'short' => 'Shape ideas into useful products and launchable work.',
                'icon' => 'fas fa-rocket',
                'keywords' => ['product', 'design', 'business', 'no-code', 'startup', 'project'],
            ],
        ];
    }

    private function enrolledCourseIds(int $userId): Collection
    {
        $ids = collect();

        if (Schema::hasTable('course_user')) {
            $ids = $ids->merge(DB::table('course_user')->where('user_id', $userId)->pluck('course_id'));
        }

        if (Schema::hasTable('course_enrollments')) {
            $ids = $ids->merge(DB::table('course_enrollments')->where('user_id', $userId)->pluck('course_id'));
        }

        return $ids->filter()->unique()->values();
    }

    private function progressByCourse(int $userId): Collection
    {
        $progress = collect();

        if (Schema::hasTable('course_user')) {
            DB::table('course_user')
                ->where('user_id', $userId)
                ->select('course_id', DB::raw('COALESCE(progress, 0) as progress_value'), 'updated_at', 'completed_at')
                ->get()
                ->each(function ($row) use ($progress) {
                    $progress->put((int) $row->course_id, [
                        'progress' => (float) $row->progress_value,
                        'updated_at' => $row->updated_at,
                        'completed_at' => $row->completed_at,
                        'source' => 'course_user',
                    ]);
                });
        }

        if (Schema::hasTable('course_enrollments')) {
            DB::table('course_enrollments')
                ->where('user_id', $userId)
                ->select('course_id', DB::raw('COALESCE(progress_percentage, 0) as progress_value'), 'is_completed', 'updated_at', 'completed_at')
                ->get()
                ->each(function ($row) use ($progress) {
                    $courseId = (int) $row->course_id;
                    $existing = $progress->get($courseId, ['progress' => 0]);
                    $progressValue = max((float) $existing['progress'], (float) $row->progress_value);

                    if ($row->is_completed) {
                        $progressValue = max($progressValue, 100);
                    }

                    $progress->put($courseId, [
                        'progress' => $progressValue,
                        'updated_at' => $row->updated_at ?? ($existing['updated_at'] ?? null),
                        'completed_at' => $row->completed_at ?? ($existing['completed_at'] ?? null),
                        'source' => 'course_enrollments',
                    ]);
                });
        }

        return $progress;
    }

    private function currentCourse(int $userId, Collection $progressByCourse): ?object
    {
        if ($progressByCourse->isEmpty() || ! Schema::hasTable('courses')) {
            return null;
        }

        $courseIds = $progressByCourse->keys()->values()->all();

        $course = Course::query()
            ->with(['category', 'instructor'])
            ->whereIn('id', $courseIds)
            ->get()
            ->map(function (Course $course) use ($progressByCourse) {
                $progress = $progressByCourse->get($course->id, ['progress' => 0, 'updated_at' => null]);

                return (object) [
                    'id' => $course->id,
                    'title' => $course->title,
                    'slug' => $course->slug,
                    'description' => $course->description,
                    'category' => $course->category?->name,
                    'instructor' => $course->instructor?->name,
                    'progress' => (int) round((float) $progress['progress']),
                    'updated_at' => $progress['updated_at'],
                    'completed_at' => $progress['completed_at'],
                    'is_complete' => (float) $progress['progress'] >= 100 || filled($progress['completed_at']),
                ];
            })
            ->sortBy([
                ['is_complete', 'asc'],
                ['progress', 'desc'],
                ['updated_at', 'desc'],
            ])
            ->first();

        return $course ?: null;
    }

    private function journeyStats(int $userId, Collection $enrolledCourseIds, Collection $progressByCourse): array
    {
        $lessonCompletionCount = $this->tableCount('lesson_user', fn ($query) => $query
            ->where('user_id', $userId)
            ->whereNotNull('completed_at'));

        $assessmentSubmissions = $this->modelCount(StudentAnswer::class, 'student_answers', fn ($query) => $query
            ->where('user_id', $userId)
            ->whereNotNull('submitted_at'));

        $gradedAssessments = $this->modelCount(StudentAnswer::class, 'student_answers', fn ($query) => $query
            ->where('user_id', $userId)
            ->where(function ($nested) {
                $nested->whereNotNull('graded_at')->orWhereNotNull('feedback');
            }));

        $codeSubmissions = $this->modelCount(CodeSubmission::class, 'code_submissions', fn ($query) => $query
            ->where('user_id', $userId));

        $codeReviews = $this->modelCount(CodeReview::class, 'code_reviews', fn ($query) => $query
            ->where('requested_by', $userId));

        $completedReviews = $this->modelCount(CodeReview::class, 'code_reviews', fn ($query) => $query
            ->where('requested_by', $userId)
            ->whereIn('status', ['in_review', 'completed']));

        $activeMentorships = $this->modelCount(Mentorship::class, 'mentorships', fn ($query) => $query
            ->where('mentee_id', $userId)
            ->whereIn('status', ['active', 'completed']));

        $issuedCertificates = $this->tableCount('certificates', fn ($query) => $query
            ->where('user_id', $userId)
            ->whereIn('status', ['approved', 'issued'])
            ->whereNull('deleted_at'));

        $pendingCertificates = $this->tableCount('certificates', fn ($query) => $query
            ->where('user_id', $userId)
            ->whereIn('status', ['pending', 'requested'])
            ->whereNull('deleted_at'));

        $portfolioCount = $this->modelCount(Portfolio::class, 'portfolios', fn ($query) => $query
            ->where('user_id', $userId));

        $jobApplicationCount = $this->modelCount(JobApplication::class, 'job_applications', fn ($query) => $query
            ->where('user_id', $userId));

        $mockInterviewCount = $this->modelCount(MockInterview::class, 'mock_interviews', fn ($query) => $query
            ->where('user_id', $userId));

        $resumeProfileCount = $this->tableCount('resume_profiles', fn ($query) => $query
            ->where('user_id', $userId));

        $averageProgress = $progressByCourse->isEmpty()
            ? 0
            : (int) round($progressByCourse->avg(fn ($item) => (float) $item['progress']));

        $completedCourses = $progressByCourse->filter(fn ($item) => (float) $item['progress'] >= 100 || filled($item['completed_at']));

        return [
            'hasGoal' => filled($this->selectedGoal),
            'enrollmentCount' => $enrolledCourseIds->count(),
            'averageProgress' => $averageProgress,
            'completedCourseCount' => $completedCourses->count(),
            'certificateCourseId' => $completedCourses->keys()->first(),
            'lessonCompletionCount' => $lessonCompletionCount,
            'assessmentSubmissions' => $assessmentSubmissions,
            'codeSubmissions' => $codeSubmissions,
            'submittedWorkCount' => $assessmentSubmissions + $codeSubmissions,
            'codeReviews' => $codeReviews,
            'completedReviews' => $completedReviews + $gradedAssessments + $activeMentorships,
            'issuedCertificates' => $issuedCertificates,
            'pendingCertificates' => $pendingCertificates,
            'portfolioCount' => $portfolioCount,
            'resumeProfileCount' => $resumeProfileCount,
            'jobApplicationCount' => $jobApplicationCount,
            'mockInterviewCount' => $mockInterviewCount,
            'careerActions' => $portfolioCount + $resumeProfileCount + $jobApplicationCount + $mockInterviewCount,
        ];
    }

    private function steps(array $stats, ?object $currentCourse): array
    {
        $steps = [
            [
                'key' => 'goal',
                'label' => 'Choose your goal',
                'copy' => 'Pick the outcome BootKode should guide you toward.',
                'done' => $stats['hasGoal'],
                'metric' => $stats['hasGoal'] ? ($this->goals()[$this->selectedGoal]['label'] ?? 'Goal selected') : 'Not selected',
                'action' => 'Choose goal',
                'route' => null,
                'icon' => 'fas fa-bullseye',
            ],
            [
                'key' => 'enroll',
                'label' => 'Enroll in a track',
                'copy' => 'Start with a course that matches the goal you selected.',
                'done' => $stats['enrollmentCount'] > 0,
                'metric' => $stats['enrollmentCount'] . ' course' . ($stats['enrollmentCount'] === 1 ? '' : 's'),
                'action' => 'Browse catalog',
                'route' => route('student.course-catalog'),
                'icon' => 'fas fa-book-open',
            ],
            [
                'key' => 'learn',
                'label' => 'Learn consistently',
                'copy' => 'Continue lessons and keep your course progress moving.',
                'done' => $stats['averageProgress'] > 0 || $stats['lessonCompletionCount'] > 0,
                'metric' => $stats['averageProgress'] . '% average progress',
                'action' => $currentCourse ? 'Continue course' : 'View my courses',
                'route' => $currentCourse ? route('course.view', ['course' => $currentCourse->slug, 'continue' => 1]) : route('student.enrolled-courses'),
                'icon' => 'fas fa-play-circle',
            ],
            [
                'key' => 'submit',
                'label' => 'Submit work',
                'copy' => 'Turn learning into evidence through assessments, projects, or code.',
                'done' => $stats['submittedWorkCount'] > 0,
                'metric' => $stats['submittedWorkCount'] . ' submission' . ($stats['submittedWorkCount'] === 1 ? '' : 's'),
                'action' => $currentCourse ? 'Open coursework' : 'Request code review',
                'route' => $currentCourse ? route('course.view', ['course' => $currentCourse->slug]) : route('mentorship.code-reviews'),
                'icon' => 'fas fa-code',
            ],
            [
                'key' => 'review',
                'label' => 'Get reviewed',
                'copy' => 'Use mentor feedback, grading, and code reviews to improve.',
                'done' => $stats['completedReviews'] > 0,
                'metric' => ($stats['completedReviews'] > 0 ? $stats['completedReviews'] : $stats['codeReviews']) . ' review signal' . (($stats['completedReviews'] + $stats['codeReviews']) === 1 ? '' : 's'),
                'action' => 'Open reviews',
                'route' => route('mentorship.code-reviews'),
                'icon' => 'fas fa-user-check',
            ],
            [
                'key' => 'certificate',
                'label' => 'Earn certificate',
                'copy' => 'Complete enough work to request and receive your certificate.',
                'done' => $stats['issuedCertificates'] > 0,
                'metric' => $stats['issuedCertificates'] > 0 ? $stats['issuedCertificates'] . ' issued' : ($stats['pendingCertificates'] . ' pending'),
                'action' => $stats['completedCourseCount'] > 0 ? 'Request certificate' : 'View certificates',
                'route' => $stats['certificateCourseId'] ? route('student.certificate.request', ['courseId' => $stats['certificateCourseId']]) : route('student.certificates.index'),
                'icon' => 'fas fa-certificate',
            ],
            [
                'key' => 'career',
                'label' => 'Get career help',
                'copy' => 'Build a portfolio, resume, interview practice, and job-search trail.',
                'done' => $stats['careerActions'] > 0,
                'metric' => $stats['careerActions'] . ' career action' . ($stats['careerActions'] === 1 ? '' : 's'),
                'action' => 'Open career tools',
                'route' => route('portfolio.show'),
                'icon' => 'fas fa-briefcase',
            ],
        ];

        $firstIncomplete = collect($steps)->search(fn ($step) => ! $step['done']);

        return collect($steps)
            ->map(function ($step, $index) use ($firstIncomplete) {
                $step['status'] = $step['done'] ? 'complete' : ($index === $firstIncomplete ? 'next' : 'open');

                return $step;
            })
            ->all();
    }

    private function recommendedCourses(Collection $enrolledCourseIds): Collection
    {
        if (! Schema::hasTable('courses')) {
            return collect();
        }

        $query = Course::query()
            ->with(['category', 'instructor'])
            ->withCount('enrollments')
            ->where('is_published', true)
            ->where('is_approved', true)
            ->when($enrolledCourseIds->isNotEmpty(), fn ($builder) => $builder->whereNotIn('id', $enrolledCourseIds->all()));

        $keywords = data_get($this->goals(), "{$this->selectedGoal}.keywords", []);

        if ($keywords) {
            $query->where(function ($builder) use ($keywords) {
                foreach ($keywords as $keyword) {
                    $builder->orWhere('title', 'like', "%{$keyword}%")
                        ->orWhere('subtitle', 'like', "%{$keyword}%")
                        ->orWhere('description', 'like', "%{$keyword}%")
                        ->orWhereHas('category', fn ($category) => $category->where('name', 'like', "%{$keyword}%"));
                }
            });
        }

        $courses = $query
            ->orderByDesc('average_rating')
            ->orderByDesc('created_at')
            ->limit(4)
            ->get();

        if ($courses->count() >= 4 || ! $keywords) {
            return $courses;
        }

        $fill = Course::query()
            ->with(['category', 'instructor'])
            ->withCount('enrollments')
            ->where('is_published', true)
            ->where('is_approved', true)
            ->when($enrolledCourseIds->isNotEmpty(), fn ($builder) => $builder->whereNotIn('id', $enrolledCourseIds->all()))
            ->whereNotIn('id', $courses->pluck('id')->all())
            ->latest()
            ->limit(4 - $courses->count())
            ->get();

        return $courses->merge($fill);
    }

    private function tableCount(string $table, callable $callback): int
    {
        if (! Schema::hasTable($table)) {
            return 0;
        }

        $query = DB::table($table);
        $callback($query);

        return (int) $query->count();
    }

    private function modelCount(string $model, string $table, callable $callback): int
    {
        if (! Schema::hasTable($table)) {
            return 0;
        }

        $query = $model::query();
        $callback($query);

        return (int) $query->count();
    }
}
