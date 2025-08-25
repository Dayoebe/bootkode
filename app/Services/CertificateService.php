<?php
// App\Services\CertificateService.php

namespace App\Services;

use App\Models\Certificate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class CertificateService
{
    protected $qrCodeService;
    protected $pdfService;

    public function __construct(
        QRCodeService $qrCodeService,
        CertificatePDFService $pdfService
    ) {
        $this->qrCodeService = $qrCodeService;
        $this->pdfService = $pdfService;
    }

    /**
     * Generate all certificate assets (QR code and PDF)
     */
    public function generateCertificateAssets(Certificate $certificate): bool
    {
        try {
            // Generate QR Code
            $qrCodePath = $this->qrCodeService->generate($certificate);
            
            // Generate PDF
            $pdfPath = $this->pdfService->generate($certificate);
            
            // Update certificate with asset paths
            $certificate->update([
                'qr_code_path' => $qrCodePath,
                'pdf_path' => $pdfPath,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Certificate asset generation failed', [
                'certificate_id' => $certificate->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Verify certificate by code
     */
    public function verify(string $verificationCode): array
    {
        $certificate = Certificate::findByVerificationCode($verificationCode);
        
        if (!$certificate) {
            return [
                'valid' => false,
                'message' => 'Certificate not found. Please check the verification code.',
                'certificate' => null
            ];
        }

        return $certificate->getVerificationData();
    }

    /**
     * Batch verify certificates
     */
    public function batchVerify(array $codes): array
    {
        $results = [];
        
        foreach ($codes as $code) {
            $results[$code] = $this->verify($code);
        }

        return $results;
    }

    /**
     * Check if user can request certificate for course
     */
    public function canRequestCertificate($userId, $courseId): array
    {
        // Check if user is enrolled
        $enrollment = \DB::table('course_user')
            ->where('user_id', $userId)
            ->where('course_id', $courseId)
            ->first();

        if (!$enrollment) {
            return [
                'can_request' => false,
                'reason' => 'User is not enrolled in this course.',
                'completion_percentage' => 0
            ];
        }

        // Check completion
        $course = \App\Models\Course::findOrFail($courseId);
        $totalLessons = $course->allLessons()->count();
        $completedLessons = \DB::table('lesson_user')
            ->whereIn('lesson_id', $course->allLessons()->pluck('id'))
            ->where('user_id', $userId)
            ->whereNotNull('completed_at')
            ->count();

        $completionPercentage = $totalLessons > 0 ? 
            round(($completedLessons / $totalLessons) * 100) : 0;

        $requiredCompletion = config('certificate.required_completion_percentage', 100);
        $canRequest = $completionPercentage >= $requiredCompletion;

        return [
            'can_request' => $canRequest,
            'reason' => $canRequest ? '' : "Course completion is {$completionPercentage}%, but {$requiredCompletion}% is required.",
            'completion_percentage' => $completionPercentage,
            'completed_lessons' => $completedLessons,
            'total_lessons' => $totalLessons
        ];
    }

    /**
     * Calculate course grade
     */
    public function calculateGrade($userId, $courseId): string
    {
        $course = \App\Models\Course::findOrFail($courseId);
        
        // Get assessments for this course (if you have assessment system)
        // This is a placeholder - adjust based on your assessment system
        $assessments = $course->assessments ?? collect();
        
        if ($assessments->count() == 0) {
            return config('certificate.grading.default_grade', 'Pass');
        }

        $totalScore = 0;
        $totalPossible = 0;
        $assessmentCount = 0;

        foreach ($assessments as $assessment) {
            // Get student results - adjust this based on your assessment system
            $result = $this->getAssessmentResult($userId, $assessment->id);
            
            if ($result && $result['passed']) {
                $totalScore += $result['percentage'];
                $totalPossible += 100;
                $assessmentCount++;
            }
        }

        if ($assessmentCount == 0) {
            return config('certificate.grading.default_grade', 'Pass');
        }

        $averageScore = $totalScore / $assessmentCount;
        return $this->getGradeFromScore($averageScore);
    }

    /**
     * Get grade from score based on configuration
     */
    protected function getGradeFromScore(float $score): string
    {
        $gradeScale = config('certificate.grading.scale', []);
        
        foreach ($gradeScale as $grade => $threshold) {
            if ($score >= $threshold) {
                return $grade;
            }
        }
        
        return 'F';
    }

    /**
     * Placeholder for assessment result - implement based on your system
     */
    protected function getAssessmentResult($userId, $assessmentId): ?array
    {
        // Implement this based on your assessment/quiz system
        // Return format: ['passed' => bool, 'percentage' => float]
        return null;
    }

    /**
     * Clean up certificate assets
     */
    public function cleanupAssets(Certificate $certificate): bool
    {
        try {
            $disk = Storage::disk(config('certificate.storage.disk', 'public'));
            
            if ($certificate->qr_code_path && $disk->exists($certificate->qr_code_path)) {
                $disk->delete($certificate->qr_code_path);
            }
            
            if ($certificate->pdf_path && $disk->exists($certificate->pdf_path)) {
                $disk->delete($certificate->pdf_path);
            }
            
            return true;
        } catch (\Exception $e) {
            Log::error('Certificate asset cleanup failed', [
                'certificate_id' => $certificate->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}
