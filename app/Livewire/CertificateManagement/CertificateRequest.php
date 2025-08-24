<?php

namespace App\Livewire\CertificateManagement;

use Livewire\Component;
use App\Models\Course;
use App\Models\Certificate;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.dashboard')]
#[Title('Request Certificate')]
class CertificateRequest extends Component
{
    public $course;
    public $existingCertificate;
    public $canRequestCertificate = false;
    public $completionPercentage = 0;
    public $completedLessons = 0;
    public $totalLessons = 0;
    public $courseProgress;

    protected $rules = [
        'course' => 'required|exists:courses,id',
    ];

    public function mount($courseId = null)
    {
        if ($courseId) {
            $this->course = Course::findOrFail($courseId);
            $this->checkCertificateEligibility();
            $this->checkExistingCertificate();
        }
    }

    public function checkCertificateEligibility()
    {
        $user = Auth::user();
        
        // Check if user is enrolled
        $enrollment = $user->courses()->where('course_id', $this->course->id)->first();
        if (!$enrollment) {
            $this->canRequestCertificate = false;
            return;
        }

        // Calculate completion percentage
        $this->totalLessons = $this->course->allLessons()->count();
        $this->completedLessons = $user->completedLessons()
            ->whereIn('lesson_id', $this->course->allLessons()->pluck('id'))
            ->count();
        
        $this->completionPercentage = $this->totalLessons > 0 ? 
            round(($this->completedLessons / $this->totalLessons) * 100) : 0;

        // Check course completion requirement (configurable, default 100%)
        $requiredCompletion = config('certificate.required_completion_percentage', 100);
        $this->canRequestCertificate = $this->completionPercentage >= $requiredCompletion;

        // Get course progress from pivot table if available
        $this->courseProgress = $enrollment->pivot->progress ?? $this->completionPercentage;
    }

    public function checkExistingCertificate()
    {
        $this->existingCertificate = Certificate::where('user_id', Auth::id())
            ->where('course_id', $this->course->id)
            ->first();
    }

    public function requestCertificate()
    {
        if (!$this->canRequestCertificate) {
            $this->dispatch('notify', [
                'message' => 'You must complete the course before requesting a certificate.',
                'type' => 'error'
            ]);
            return;
        }

        if ($this->existingCertificate) {
            $this->dispatch('notify', [
                'message' => 'You have already requested a certificate for this course.',
                'type' => 'warning'
            ]);
            return;
        }

        try {
            // Calculate completion date (when the last lesson was completed)
            $lastCompletedLesson = Auth::user()->completedLessons()
                ->whereIn('lesson_id', $this->course->allLessons()->pluck('id'))
                ->orderBy('lesson_user.completed_at', 'desc')
                ->first();

            $completionDate = $lastCompletedLesson 
                ? $lastCompletedLesson->pivot->completed_at 
                : now();

            // Calculate grade based on assessments if available
            $grade = $this->calculateCourseGrade();

            $certificate = Certificate::create([
                'user_id' => Auth::id(),
                'course_id' => $this->course->id,
                'status' => Certificate::STATUS_REQUESTED,
                'requested_at' => now(),
                'completion_date' => $completionDate,
                'grade' => $grade,
                'credits' => $this->course->credits ?? null,
                'metadata' => [
                    'completion_percentage' => $this->completionPercentage,
                    'total_lessons' => $this->totalLessons,
                    'completed_lessons' => $this->completedLessons,
                ]
            ]);

            $this->existingCertificate = $certificate;

            // Notify instructor and super admin
            $this->notifyRelevantUsers($certificate);

            $this->dispatch('notify', [
                'message' => 'Certificate request submitted successfully! You will be notified once it is reviewed.',
                'type' => 'success'
            ]);

        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'message' => 'Error requesting certificate. Please try again.',
                'type' => 'error'
            ]);
        }
    }

    private function calculateCourseGrade()
    {
        // Get all assessments for this course
        $assessments = $this->course->assessments();
        
        if ($assessments->count() == 0) {
            return 'Pass'; // Default grade if no assessments
        }

        $totalScore = 0;
        $totalPossible = 0;
        $assessmentCount = 0;

        foreach ($assessments->get() as $assessment) {
            $studentResults = $assessment->getStudentResults(Auth::id());
            
            if ($studentResults && $studentResults['passed']) {
                $totalScore += $studentResults['percentage'];
                $totalPossible += 100;
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

    private function notifyRelevantUsers($certificate)
    {
        // Notify course instructor
        if ($this->course->instructor) {
            $this->course->instructor->notify(
                new \App\Notifications\CertificateRequestReceived($certificate)
            );
        }

        // Notify super admins
        $superAdmins = \App\Models\User::role('super_admin')->get();
        foreach ($superAdmins as $admin) {
            $admin->notify(
                new \App\Notifications\CertificateRequestReceived($certificate)
            );
        }
    }

    public function cancelRequest()
    {
        if ($this->existingCertificate && $this->existingCertificate->isRequested()) {
            $this->existingCertificate->delete();
            $this->existingCertificate = null;

            $this->dispatch('notify', [
                'message' => 'Certificate request cancelled.',
                'type' => 'info'
            ]);
        }
    }

    public function downloadCertificate()
    {
        if ($this->existingCertificate && $this->existingCertificate->isApproved() && $this->existingCertificate->pdf_path) {
            return response()->download(
                storage_path('app/public/' . $this->existingCertificate->pdf_path),
                "Certificate_{$this->course->title}_{$this->existingCertificate->certificate_number}.pdf"
            );
        }
    }

    public function render()
    {
        return view('livewire.certificate-management.certificate-request');
    }
}