<?php

namespace App\Livewire\StudentManagement;

use Livewire\Component;
use App\Models\Learning\Course;
use App\Models\Learning\CourseReview;
use App\Models\Learning\Section;
use App\Models\Learning\Lesson;
use App\Models\Assessment\Assessment;
use App\Models\Assessment\StudentAnswer;
use App\Models\Learning\CourseEnrollment;
use App\Models\Learning\LessonProgress;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Models\Credentials\Certificate;
use App\Notifications\CourseCompletionCertificateReady;
use App\Services\DirectMessagingService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\On;

#[Layout('layouts.dashboard')]
#[Title('Course View')]
class CourseView extends Component
{
    public $course;
    public $currentLesson;
    public $currentSection;
    public $completedLessons = [];
    public $unlockedSections = [];
    public $sectionCompletionThreshold;
    public $reviewRating = 5;
    public $reviewComment = '';
    public $overallProgress = 0;
    public $lastViewedLesson = null;
    public bool $certificateEarned = false;
    public bool $shouldCelebrate = false;
    public int $progressVersion = 0;

    public function mount($course)
    {
        $this->sectionCompletionThreshold = (int) config('course.section_completion_threshold', 80);
        $this->course = $this->resolveCourse($course);

        if (
            !CourseEnrollment::where('course_id', $this->course->id)
                ->where('user_id', Auth::id())
                ->exists()
        ) {
            abort(403, 'You are not enrolled in this course.');
        }

        $this->refreshProgressState();
        $preferredLesson = request()->boolean('continue') ? $this->lastViewedLesson : null;
        $this->setInitialLesson($preferredLesson);

        if (session()->has('success')) {
            $this->dispatch('notify', [
                'message' => session('success'),
                'type' => 'success',
                'icon' => 'fas fa-graduation-cap'
            ]);
        }

        if (session()->has('show_confetti')) {
            $this->shouldCelebrate = true;
            session()->forget('show_confetti');
        }
    }

    protected function resolveCourse($course): Course
    {
        $courseId = $course instanceof Course ? $course->getKey() : null;

        $query = Course::query()
            ->with([
                'sections' => fn($sectionQuery) => $sectionQuery
                    ->orderBy('order')
                    ->with([
                        'lessons' => fn($lessonQuery) => $lessonQuery
                            ->published()
                            ->orderBy('order')
                            ->with([
                                'assessments' => fn($assessmentQuery) => $assessmentQuery
                                    ->orderBy('order')
                                    ->with([
                                        'questions' => fn($questionQuery) => $questionQuery->orderBy('order'),
                                    ]),
                            ]),
                    ]),
                'instructor',
                'category',
                'enrollments' => fn($enrollmentQuery) => $enrollmentQuery->where('user_id', Auth::id()),
            ])
            ->where('is_published', true)
            ->where('is_approved', true);

        if ($courseId) {
            return $query->whereKey($courseId)->firstOrFail();
        }

        return $query->where('slug', $course)->firstOrFail();
    }

    protected function refreshProgressState(): void
    {
        $this->loadUserProgress();
        $this->determineUnlockedSections();
        $this->overallProgress = $this->calculateOverallProgress();
        $this->lastViewedLesson = $this->getLastViewedLesson();
        $this->certificateEarned = Certificate::where('user_id', Auth::id())
            ->where('course_id', $this->course->id)
            ->exists();

        if ($this->currentLesson instanceof Lesson) {
            $this->syncCurrentLessonToAccessibleState();
        }

        $this->progressVersion++;
    }

    protected function loadUserProgress()
    {
        $completedLessonsQuery = Auth::user()->completedLessons()
            ->whereIn('lesson_id', $this->getAllLessonIds())
            ->pluck('lesson_id');

        $this->completedLessons = $completedLessonsQuery->toArray();
    }

    protected function getAllLessonIds()
    {
        return $this->course->sections
            ->flatMap->lessons
            ->pluck('id')
            ->values()
            ->all();
    }

    protected function determineUnlockedSections()
    {
        $this->unlockedSections = [];

        foreach ($this->course->sections as $index => $section) {
            if ($index === 0) {
                // First section is always unlocked
                $this->unlockedSections[] = $section->id;
            } else {
                // Check if previous section meets completion threshold
                $previousSection = $this->course->sections[$index - 1];
                $previousProgress = $this->calculateSectionProgress($previousSection);

                if ($previousSection->lessons->isEmpty() || $previousProgress >= $this->sectionCompletionThreshold) {
                    $this->unlockedSections[] = $section->id;
                }
            }
        }
    }

    protected function setInitialLesson($preferredLesson = null)
    {
        if ($preferredLesson instanceof Lesson && $this->isLessonAccessible($preferredLesson)) {
            $this->focusLesson($preferredLesson, $preferredLesson->section);
            return;
        }

        $firstIncompleteLesson = $this->findFirstIncompleteAccessibleLesson();
        if ($firstIncompleteLesson) {
            $this->focusLesson($firstIncompleteLesson['lesson'], $firstIncompleteLesson['section']);
            return;
        }

        $firstAccessibleLesson = $this->findFirstAccessibleLesson();
        if ($firstAccessibleLesson) {
            $this->focusLesson($firstAccessibleLesson['lesson'], $firstAccessibleLesson['section']);
            return;
        }

        $this->clearCurrentLesson();
    }

    protected function isLessonAccessible(Lesson $lesson): bool
    {
        $lesson->loadMissing('section');

        return $lesson->section !== null
            && (int) $lesson->section->course_id === (int) $this->course->id
            && in_array($lesson->section_id, $this->unlockedSections, true);
    }

    protected function findLessonInCourse($lessonId): ?array
    {
        foreach ($this->course->sections as $section) {
            $lesson = $section->lessons->firstWhere('id', (int) $lessonId);

            if ($lesson) {
                return [
                    'lesson' => $lesson,
                    'section' => $section,
                ];
            }
        }

        return null;
    }

    protected function findFirstIncompleteAccessibleLesson(): ?array
    {
        foreach ($this->course->sections as $section) {
            if (!in_array($section->id, $this->unlockedSections, true)) {
                continue;
            }

            foreach ($section->lessons as $lesson) {
                if (!in_array($lesson->id, $this->completedLessons, true)) {
                    return [
                        'lesson' => $lesson,
                        'section' => $section,
                    ];
                }
            }
        }

        return null;
    }

    protected function findFirstAccessibleLesson(): ?array
    {
        foreach ($this->course->sections as $section) {
            if (!in_array($section->id, $this->unlockedSections, true)) {
                continue;
            }

            $lesson = $section->lessons->first();

            if ($lesson) {
                return [
                    'lesson' => $lesson,
                    'section' => $section,
                ];
            }
        }

        return null;
    }

    protected function getOrderedLessonMap()
    {
        return $this->course->sections
            ->flatMap(fn($section) => $section->lessons->map(fn($lesson) => [
                'lesson' => $lesson,
                'section' => $section,
            ]))
            ->values();
    }

    protected function findAdjacentLesson(int $lessonId, string $direction = 'next'): ?array
    {
        $orderedLessons = $this->getOrderedLessonMap();
        $currentIndex = $orderedLessons->search(
            fn($item) => (int) $item['lesson']->id === $lessonId
        );

        if ($currentIndex === false) {
            return null;
        }

        $targetIndex = $direction === 'previous' ? $currentIndex - 1 : $currentIndex + 1;

        return $orderedLessons->get($targetIndex);
    }

    protected function focusLesson(?Lesson $lesson, ?Section $section = null): void
    {
        $this->currentLesson = $lesson;
        $this->currentSection = $section ?? $lesson?->section;
    }

    protected function clearCurrentLesson(): void
    {
        $this->currentLesson = null;
        $this->currentSection = null;
    }

    protected function syncCurrentLessonToAccessibleState(): void
    {
        $currentLessonData = $this->currentLesson
            ? $this->findLessonInCourse($this->currentLesson->id)
            : null;

        if ($currentLessonData && in_array($currentLessonData['section']->id, $this->unlockedSections, true)) {
            $this->focusLesson($currentLessonData['lesson'], $currentLessonData['section']);
            return;
        }

        $this->setInitialLesson($this->lastViewedLesson);
    }

    protected function rememberLastViewedLesson(Lesson $lesson): void
    {
        Cache::put(
            'user_' . Auth::id() . '_last_lesson_' . $this->course->id,
            [
                'lesson_id' => $lesson->id,
                'section_id' => $lesson->section_id,
                'timestamp' => now(),
            ],
            now()->addDays(30)
        );

        $this->lastViewedLesson = $lesson;
    }

    protected function completeLessonProgress(int $lessonId): bool
    {
        try {
            if (in_array($lessonId, $this->completedLessons, true)) {
                return true;
            }

            if (!$this->canCompleteLessonWithoutAssessments($lessonId)) {
                return false;
            }

            Auth::user()->completedLessons()->syncWithoutDetaching([
                $lessonId => ['completed_at' => now()],
            ]);

            $this->refreshProgressState();
            $this->updateCourseProgress();

            $lessonData = $this->findLessonInCourse($lessonId);
            if ($lessonData) {
                $sectionProgress = $this->calculateSectionProgress($lessonData['section']);

                if ($sectionProgress >= $this->sectionCompletionThreshold) {
                    $this->checkAndUnlockNextSection($lessonData['section']);
                }
            }

            $this->checkAndAwardCertificate();
            $this->refreshProgressState();
            $this->dispatch('progress-updated');

            return true;
        } catch (\Exception $e) {
            \Log::error('Error marking lesson progress', [
                'lesson_id' => $lessonId,
                'course_id' => $this->course->id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    protected function getNextSectionUnlockMessage(array $currentLessonData, array $nextLessonData): string
    {
        $currentSection = $currentLessonData['section'];
        $nextSection = $nextLessonData['section'];
        $totalLessons = $currentSection->lessons->count();
        $requiredLessons = max(1, (int) ceil($totalLessons * ($this->sectionCompletionThreshold / 100)));
        $completedLessons = $currentSection->lessons
            ->filter(fn($lesson) => in_array($lesson->id, $this->completedLessons, true))
            ->count();
        $remainingLessons = max($requiredLessons - $completedLessons, 0);

        if ($remainingLessons <= 0) {
            return "The next section ({$nextSection->title}) is refreshing. Try again now.";
        }

        return "Complete {$remainingLessons} more lesson" . ($remainingLessons === 1 ? '' : 's')
            . " in {$currentSection->title} to unlock {$nextSection->title}.";
    }

    #[On('lesson-selected')]
    public function setCurrentLesson($lessonId)
    {
        try {
            $lessonData = $this->findLessonInCourse($lessonId);

            if (!$lessonData) {
                throw new \RuntimeException('Lesson not found.');
            }

            if (!in_array($lessonData['section']->id, $this->unlockedSections, true)) {
                $this->dispatch('notify', [
                    'message' => "Complete at least {$this->sectionCompletionThreshold}% of the previous section to unlock this lesson.",
                    'type' => 'warning'
                ]);
                return;
            }

            $this->focusLesson($lessonData['lesson'], $lessonData['section']);
            $this->rememberLastViewedLesson($lessonData['lesson']);
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'message' => 'Lesson not found.',
                'type' => 'error'
            ]);
        }
    }

    #[On('advance-to-next-lesson')]
    public function advanceToNextLesson($lessonId)
    {
        $lessonId = (int) $lessonId;
        $currentLessonData = $this->findLessonInCourse($lessonId);

        if (!$currentLessonData) {
            $this->dispatch('notify', [
                'message' => 'Current lesson could not be resolved.',
                'type' => 'error'
            ]);
            return;
        }

        if (!$this->completeLessonProgress($lessonId)) {
            $this->dispatch('notify', [
                'message' => 'Pass every required assessment before continuing.',
                'type' => 'warning'
            ]);
            return;
        }

        $nextLessonData = $this->findAdjacentLesson($lessonId, 'next');

        if (!$nextLessonData) {
            $this->focusLesson($currentLessonData['lesson'], $currentLessonData['section']);
            return;
        }

        if (!in_array($nextLessonData['section']->id, $this->unlockedSections, true)) {
            $this->dispatch('notify', [
                'message' => $this->getNextSectionUnlockMessage($currentLessonData, $nextLessonData),
                'type' => 'warning'
            ]);
            return;
        }

        $this->focusLesson($nextLessonData['lesson'], $nextLessonData['section']);
        $this->rememberLastViewedLesson($nextLessonData['lesson']);
        $this->dispatch('progress-updated');
    }

    #[On('complete-course-from-lesson')]
    public function completeCourseFromLesson($lessonId)
    {
        $lessonId = (int) $lessonId;

        if (!$this->completeLessonProgress($lessonId)) {
            $this->dispatch('notify', [
                'message' => 'Pass every required assessment before finishing the course.',
                'type' => 'warning'
            ]);
            return;
        }

        if ($this->overallProgress < 100) {
            $this->dispatch('notify', [
                'message' => 'Complete all remaining lessons before finishing the course.',
                'type' => 'warning'
            ]);
            return;
        }

        session()->flash('success', 'Course completed. Opening your certificates.');

        return $this->redirectRoute('student.certificates.index', navigate: true);
    }

    protected function getSectionIndex($sectionId)
    {
        foreach ($this->course->sections as $index => $section) {
            if ($section->id == $sectionId) {
                return $index;
            }
        }
        return 0;
    }

    /**
     * New method to handle lesson uncompletion
     */
    #[On('lesson-uncompleted')]
    public function handleLessonUncompleted($lessonId)
    {
        if (in_array($lessonId, $this->completedLessons, true)) {
            try {
                Auth::user()->completedLessons()->detach($lessonId);
                $this->refreshProgressState();
                $this->updateCourseProgress();
                $this->dispatch('progress-updated');
            } catch (\Exception $e) {
                $this->dispatch('notify', [
                    'message' => 'Error marking lesson as incomplete.',
                    'type' => 'error'
                ]);
            }
        }
    }

    protected function checkAndUnlockNextSection($currentSection)
    {
        $currentIndex = $this->getSectionIndex($currentSection->id);
        $nextSection = $this->course->sections[$currentIndex + 1] ?? null;

        if ($nextSection && !in_array($nextSection->id, $this->unlockedSections, true)) {
            $this->dispatch('section-unlocked', [
                'sectionId' => $nextSection->id,
                'sectionTitle' => $nextSection->title
            ]);
        }
    }

    public function isSectionUnlocked($sectionId)
    {
        return in_array($sectionId, $this->unlockedSections, true);
    }

    public function calculateSectionProgress($section)
    {
        $totalLessons = $section->lessons->count();
        if ($totalLessons === 0)
            return 0;

        $completed = 0;
        foreach ($section->lessons as $lesson) {
            if (in_array($lesson->id, $this->completedLessons, true)) {
                $completed++;
            }
        }

        return round(($completed / $totalLessons) * 100);
    }

    public function calculateOverallProgress()
    {
        $totalLessons = $this->getAllLessonIds();
        $totalCount = count($totalLessons);
        $completedCount = count($this->completedLessons);

        return $totalCount > 0 ? round(($completedCount / $totalCount) * 100) : 0;
    }

    private function updateCourseProgress()
    {
        $progress = $this->calculateOverallProgress();

        try {
            // Update user's course progress in CourseEnrollment
            CourseEnrollment::where('course_id', $this->course->id)
                ->where('user_id', Auth::id())
                ->update([
                    'progress_percentage' => $progress,
                    'updated_at' => now()
                ]);
        } catch (\Exception $e) {
            \Log::error('Error updating course progress: ' . $e->getMessage());
        }
    }

    public function getSectionRequirementText($section)
    {
        $sectionIndex = $this->getSectionIndex($section->id);
        if ($sectionIndex === 0) {
            return 'Available to start';
        }

        if (in_array($section->id, $this->unlockedSections, true)) {
            return 'Unlocked';
        }

        return "Complete {$this->sectionCompletionThreshold}% of previous section to unlock";
    }

    #[On('assessment-status-changed')]
    public function handleAssessmentStatusChanged($data)
    {
        $lessonId = $data['lessonId'];
        $allPassed = $data['allPassed'];

        // If all assessments are now passed, we might need to update progress
        if ($allPassed && $this->currentLesson && $this->currentLesson->id == $lessonId) {
            $this->dispatch('notify', [
                'message' => 'All assessments completed! You can now proceed to the next lesson.',
                'type' => 'success'
            ]);
        }
    }

    #[On('assessment-cleared')]
    public function handleAssessmentCleared()
    {
        $this->refreshProgressState();
        $this->dispatch('progress-updated');
    }

    protected function canCompleteLessonWithoutAssessments($lessonId)
    {
        $assessments = Assessment::where('lesson_id', $lessonId)->get();

        if ($assessments->isEmpty()) {
            return true; // No assessments, can complete
        }

        // Check if all assessments are passed
        foreach ($assessments as $assessment) {
            $latestAttempt = StudentAnswer::where('user_id', Auth::id())
                ->where('assessment_id', $assessment->id)
                ->orderBy('attempt_number', 'desc')
                ->first();

            if (!$latestAttempt) {
                return false; // No attempt made
            }

            // Calculate score for the latest attempt
            $totalPoints = StudentAnswer::where('user_id', Auth::id())
                ->where('assessment_id', $assessment->id)
                ->where('attempt_number', $latestAttempt->attempt_number)
                ->sum('points_earned');

            $maxPoints = $assessment->questions->sum('points');
            $percentage = $maxPoints > 0 ? round(($totalPoints / $maxPoints) * 100, 1) : 0;

            if ($percentage < $assessment->pass_percentage) {
                return false; // Assessment not passed
            }
        }

        return true; // All assessments passed
    }
    /**
     * Get the last viewed lesson for "Continue Learning" feature
     */
    protected function getLastViewedLesson()
    {
        $cacheKey = 'user_' . Auth::id() . '_last_lesson_' . $this->course->id;
        $lastViewed = Cache::get($cacheKey);

        if ($lastViewed) {
            $lesson = Lesson::query()
                ->with('section')
                ->published()
                ->find($lastViewed['lesson_id']);

            if ($lesson && $this->isLessonAccessible($lesson)) {
                return $lesson;
            }
        }

        $progress = LessonProgress::getLastAccessedLesson(Auth::id(), $this->course->id);
        if ($progress && $progress->lesson) {
            $lesson = $progress->lesson->loadMissing('section');

            if ($this->isLessonAccessible($lesson)) {
                return $lesson;
            }
        }

        return null;
    }

    /**
     * Resume from last viewed lesson
     */
    public function continueFromLastLesson()
    {
        $this->refreshProgressState();
        $lastLesson = $this->lastViewedLesson;

        if ($lastLesson) {
            $this->focusLesson($lastLesson, $lastLesson->section);
            $this->rememberLastViewedLesson($lastLesson);

            $this->dispatch('notify', [
                'message' => 'Resumed from where you left off!',
                'type' => 'success'
            ]);
        } else {
            // Start from first incomplete lesson
            $this->setInitialLesson();
        }
    }

    public function messageInstructor()
    {
        try {
            $conversation = app(DirectMessagingService::class)
                ->getOrCreateCourseConversation($this->course, Auth::user());

            return $this->redirectRoute('messages.index', [
                'conversation' => $conversation->id,
            ], navigate: true);
        } catch (AuthorizationException $exception) {
            $this->dispatch('notify', [
                'message' => $exception->getMessage(),
                'type' => 'error',
            ]);
        } catch (ValidationException $exception) {
            $this->dispatch('notify', [
                'message' => collect($exception->errors())->flatten()->first(),
                'type' => 'error',
            ]);
        }
    }
    /**
     * Check if course is fully completed and award certificate
     */
    protected function checkAndAwardCertificate()
    {
        // Check if user has completed all lessons
        $totalLessons = $this->getAllLessonIds();
        $completedLessons = count($this->completedLessons);

        if ($completedLessons < count($totalLessons)) {
            return false; // Not all lessons completed
        }

        // Calculate completion percentage
        $completionPercentage = 100;

        // Check if certificate already exists
        $existingCertificate = Certificate::where('user_id', Auth::id())
            ->where('course_id', $this->course->id)
            ->first();

        if ($existingCertificate) {
            return false; // Certificate already exists
        }

        try {
            // Calculate grade based on assessments
            $grade = $this->calculateCourseGrade();

            // Get completion date (last completed lesson)
            $lastCompletedLesson = Auth::user()->completedLessons()
                ->whereIn('lesson_id', $totalLessons)
                ->orderBy('lesson_user.completed_at', 'desc')
                ->first();

            $completionDate = $lastCompletedLesson
                ? $lastCompletedLesson->pivot->completed_at
                : now();

            // Create and auto-approve certificate
            $certificate = Certificate::create([
                'user_id' => Auth::id(),
                'course_id' => $this->course->id,
                'status' => Certificate::STATUS_APPROVED, // Auto-approve
                'requested_at' => now(),
                'approved_at' => now(),
                'approved_by' => $this->course->instructor_id ?? 1, // Auto-approved by instructor or system
                'completion_date' => $completionDate,
                'issued_date' => now(),
                'grade' => $grade,
                'credits' => $this->course->credits ?? null,
                'metadata' => [
                    'completion_percentage' => $completionPercentage,
                    'total_lessons' => count($totalLessons),
                    'completed_lessons' => $completedLessons,
                    'auto_generated' => true,
                ]
            ]);

            // Generate certificate assets (PDF, QR code)
            if (class_exists(\App\Services\CertificateService::class)) {
                app(\App\Services\CertificateService::class)->generateCertificateAssets($certificate);
            }

            // Send notification to user
            Auth::user()->notify(new CourseCompletionCertificateReady(
                $this->course,
                $certificate,
                $completionPercentage
            ));

            // Notify instructor
            if ($this->course->instructor) {
                $this->course->instructor->notify(
                    new \App\Notifications\StudentCompletedCourse(Auth::user(), $this->course, $certificate)
                );
            }

            // Update enrollment status
            CourseEnrollment::where('course_id', $this->course->id)
                ->where('user_id', Auth::id())
                ->update([
                    'is_completed' => true,
                    'completed_at' => now(),
                    'progress_percentage' => 100
                ]);

            $this->certificateEarned = true;

            $this->dispatch('notify', [
                'message' => '🎉 Congratulations! You completed the course and your certificate is ready!',
                'type' => 'success'
            ]);

            $this->dispatch('certificate-awarded', [
                'certificateId' => $certificate->id,
                'verificationCode' => $certificate->verification_code
            ]);

            return true;

        } catch (\Exception $e) {
            \Log::error('Error awarding certificate: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Calculate course grade based on assessments
     */
    protected function calculateCourseGrade()
    {
        $assessments = $this->course->assessments()->get();

        if ($assessments->isEmpty()) {
            return 'Pass'; // Default grade if no assessments
        }

        $totalScore = 0;
        $assessmentCount = 0;

        foreach ($assessments as $assessment) {
            $results = $assessment->getStudentResults(Auth::id());

            if ($results && $results['passed']) {
                $totalScore += $results['percentage'];
                $assessmentCount++;
            }
        }

        if ($assessmentCount == 0) {
            return 'Pass';
        }

        $averageScore = $totalScore / $assessmentCount;

        // Grade scale
        if ($averageScore >= 97)
            return 'A+';
        if ($averageScore >= 93)
            return 'A';
        if ($averageScore >= 90)
            return 'A-';
        if ($averageScore >= 87)
            return 'B+';
        if ($averageScore >= 83)
            return 'B';
        if ($averageScore >= 80)
            return 'B-';
        if ($averageScore >= 77)
            return 'C+';
        if ($averageScore >= 73)
            return 'C';
        if ($averageScore >= 70)
            return 'C-';
        if ($averageScore >= 60)
            return 'D';

        return 'F';
    }

    /**
     * Update existing handleLessonCompleted method
     */
    #[On('lesson-completed')]
    public function handleLessonCompleted($lessonId)
    {
        $lessonId = (int) $lessonId;

        if (!in_array($lessonId, $this->completedLessons, true)) {
            if (!$this->completeLessonProgress($lessonId)) {
                $this->dispatch('notify', [
                    'message' => 'You must pass all required assessments before marking this lesson as complete.',
                    'type' => 'warning'
                ]);
                return;
            }
        }
    }
    public function submitReview()
    {
        // Validate
        $this->validate([
            'reviewRating' => 'required|integer|min:1|max:5',
            'reviewComment' => 'required|string|min:10|max:1000'
        ]);

        // Check enrollment
        if (!Auth::user()->enrollments()->where('course_id', $this->course->id)->exists()) {
            $this->dispatch('notify', [
                'message' => 'You must be enrolled to review this course.',
                'type' => 'error',
                'icon' => 'fas fa-exclamation-circle'
            ]);
            return false;
        }

        try {
            // Create or update review
            CourseReview::updateOrCreate(
                [
                    'user_id' => Auth::id(),
                    'course_id' => $this->course->id
                ],
                [
                    'rating' => $this->reviewRating,
                    'comment' => $this->reviewComment,
                    'is_approved' => true
                ]
            );

            // Reset form
            $this->reset(['reviewRating', 'reviewComment']);
            $this->reviewRating = 5;

            // Success notification
            $this->dispatch('notify', [
                'message' => 'Thank you for your review! 🎉',
                'type' => 'success',
                'icon' => 'fas fa-check-circle'
            ]);

            return true;

        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'message' => 'Failed to submit review. Please try again.',
                'type' => 'error',
                'icon' => 'fas fa-exclamation-triangle'
            ]);
            return false;
        }
    }
    public function render()
    {
        return view('livewire.student-management.course-view');
    }
}
