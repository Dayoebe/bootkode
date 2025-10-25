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
use App\Models\Learning\LessonProgress; // Add this if you're using the new model
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache; // ADD THIS LINE
use Illuminate\Http\Request;
use App\Models\Credentials\Certificate;
use App\Notifications\CourseCompletionCertificateReady;
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

    public function mount($course)
    {
        // If $course is a string (slug), find the course by slug
        if (is_string($course)) {
            $this->course = Course::with(['sections.lessons', 'instructor', 'category'])
                ->where('slug', $course)
                ->where('is_published', true)
                ->where('is_approved', true)
                ->firstOrFail();
        } else {
            // If it's already a Course model (shouldn't happen with current setup)
            $this->course = $course;
        }

        // Check if user is enrolled using CourseEnrollment model
        if (
            !CourseEnrollment::where('course_id', $this->course->id)
                ->where('user_id', Auth::id())
                ->exists()
        ) {
            abort(403, 'You are not enrolled in this course.');
        }

        // Check if user wants to continue from last lesson
        if (request()->has('continue') && request()->get('continue') === 'true') {
            $lastLesson = $this->getLastViewedLesson();
            if ($lastLesson) {
                $this->currentLesson = $lastLesson;
                $this->currentSection = $lastLesson->section;
                return; // Skip setInitialLesson
            }
        }
        // Set completion threshold from config
        $this->sectionCompletionThreshold = config('course.section_completion_threshold', 80);

        $this->loadUserProgress();
        $this->determineUnlockedSections();
        $this->setInitialLesson();
    }

    protected function loadUserProgress()
    {
        // Get completed lessons
        $completedLessonsQuery = Auth::user()->completedLessons()
            ->whereIn('lesson_id', $this->getAllLessonIds())
            ->pluck('lesson_id');

        $this->completedLessons = $completedLessonsQuery->toArray();
    }

    protected function getAllLessonIds()
    {
        $lessonIds = [];
        foreach ($this->course->sections as $section) {
            foreach ($section->lessons as $lesson) {
                $lessonIds[] = $lesson->id;
            }
        }
        return $lessonIds;
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

                if ($previousProgress >= $this->sectionCompletionThreshold) {
                    $this->unlockedSections[] = $section->id;
                }
            }
        }
    }

    protected function setInitialLesson()
    {
        // Find the first incomplete lesson in unlocked sections
        foreach ($this->course->sections as $section) {
            if (in_array($section->id, $this->unlockedSections)) {
                foreach ($section->lessons as $lesson) {
                    if (!in_array($lesson->id, $this->completedLessons)) {
                        $this->currentLesson = $lesson;
                        $this->currentSection = $section;
                        return;
                    }
                }
            }
        }

        // If all unlocked lessons are completed, set to first lesson of first unlocked section
        foreach ($this->course->sections as $section) {
            if (in_array($section->id, $this->unlockedSections) && $section->lessons->count() > 0) {
                $this->currentLesson = $section->lessons->first();
                $this->currentSection = $section;
                return;
            }
        }
    }

    #[On('lesson-selected')]
    public function setCurrentLesson($lessonId)
    {
        try {
            $lesson = Lesson::with('section')->findOrFail($lessonId);

            // Check if the section is unlocked
            if (!in_array($lesson->section_id, $this->unlockedSections)) {
                $sectionIndex = $this->getSectionIndex($lesson->section_id);
                $requiredProgress = $this->sectionCompletionThreshold;

                $this->dispatch('notify', [
                    'message' => "Complete at least {$requiredProgress}% of the previous section to unlock this lesson.",
                    'type' => 'warning'
                ]);
                return;
            }

            $this->currentLesson = $lesson;
            $this->currentSection = $lesson->section;
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'message' => 'Lesson not found.',
                'type' => 'error'
            ]);
        }
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
        if (in_array($lessonId, $this->completedLessons)) {
            try {
                Auth::user()->completedLessons()->detach($lessonId);
                $this->completedLessons = array_values(array_diff($this->completedLessons, [$lessonId]));

                $this->updateCourseProgress();
                $this->determineUnlockedSections();
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

        if ($nextSection && !in_array($nextSection->id, $this->unlockedSections)) {
            $this->dispatch('section-unlocked', [
                'sectionId' => $nextSection->id,
                'sectionTitle' => $nextSection->title
            ]);
        }
    }

    public function isSectionUnlocked($sectionId)
    {
        return in_array($sectionId, $this->unlockedSections);
    }

    public function calculateSectionProgress($section)
    {
        $totalLessons = $section->lessons->count();
        if ($totalLessons === 0)
            return 0;

        $completed = 0;
        foreach ($section->lessons as $lesson) {
            if (in_array($lesson->id, $this->completedLessons)) {
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

        if (in_array($section->id, $this->unlockedSections)) {
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
        // Refresh the current lesson data when assessments are cleared
        $this->loadUserProgress();
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
            $lesson = Lesson::find($lastViewed['lesson_id']);
            if ($lesson && in_array($lesson->section_id, $this->unlockedSections)) {
                return $lesson;
            }
        }

        // Fallback: Use database progress
        $progress = LessonProgress::getLastAccessedLesson(Auth::id(), $this->course->id);
        if ($progress && $progress->lesson) {
            return $progress->lesson;
        }

        return null;
    }

    /**
     * Resume from last viewed lesson
     */
    public function continueFromLastLesson()
    {
        $lastLesson = $this->getLastViewedLesson();

        if ($lastLesson) {
            $this->currentLesson = $lastLesson;
            $this->currentSection = $lastLesson->section;

            $this->dispatch('notify', [
                'message' => 'Resumed from where you left off!',
                'type' => 'success'
            ]);
        } else {
            // Start from first incomplete lesson
            $this->setInitialLesson();
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

            // Show success message
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
        if ($averageScore >= 97) return 'A+';
        if ($averageScore >= 93) return 'A';
        if ($averageScore >= 90) return 'A-';
        if ($averageScore >= 87) return 'B+';
        if ($averageScore >= 83) return 'B';
        if ($averageScore >= 80) return 'B-';
        if ($averageScore >= 77) return 'C+';
        if ($averageScore >= 73) return 'C';
        if ($averageScore >= 70) return 'C-';
        if ($averageScore >= 60) return 'D';
        
        return 'F';
    }

    /**
     * Update existing handleLessonCompleted method
     */
    #[On('lesson-completed')]
    public function handleLessonCompleted($lessonId)
    {
        if (!in_array($lessonId, $this->completedLessons)) {
            if (!$this->canCompleteLessonWithoutAssessments($lessonId)) {
                $this->dispatch('notify', [
                    'message' => 'You must pass all required assessments before marking this lesson as complete.',
                    'type' => 'warning'
                ]);
                return;
            }

            try {
                Auth::user()->completedLessons()->attach($lessonId, ['completed_at' => now()]);
                $this->completedLessons[] = $lessonId;
                
                $this->updateCourseProgress();
                $this->determineUnlockedSections();
                
                $lesson = Lesson::find($lessonId);
                if ($lesson) {
                    $sectionProgress = $this->calculateSectionProgress($lesson->section);
                    if ($sectionProgress >= $this->sectionCompletionThreshold) {
                        $this->checkAndUnlockNextSection($lesson->section);
                    }
                }

                // CHECK IF COURSE IS FULLY COMPLETED AND AWARD CERTIFICATE
                $this->checkAndAwardCertificate();

                $this->dispatch('progress-updated');
            } catch (\Exception $e) {
                $this->dispatch('notify', [
                    'message' => 'Error marking lesson as completed.',
                    'type' => 'error'
                ]);
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