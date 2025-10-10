<?php

namespace App\Services;

use App\Models\Certificate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Color\Color;

class CertificateService
{
    /**
     * Generate all certificate assets (QR code and PDF)
     */
    public function generateCertificateAssets(Certificate $certificate): bool
    {
        try {
            Log::info('Starting certificate asset generation', [
                'certificate_id' => $certificate->id,
                'verification_code' => $certificate->verification_code
            ]);

            // Generate QR Code FIRST
            $qrCodePath = $this->generateQRCode($certificate);
            
            // Update certificate with QR path
            $certificate->update(['qr_code_path' => $qrCodePath]);
            
            // Refresh to get updated data
            $certificate->refresh();
            
            // Generate PDF (now with QR code available)
            $pdfPath = $this->generatePDF($certificate);
            
            // Update certificate with PDF path
            $certificate->update(['pdf_path' => $pdfPath]);

            Log::info('Certificate assets generated successfully', [
                'certificate_id' => $certificate->id,
                'qr_path' => $qrCodePath,
                'pdf_path' => $pdfPath
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Certificate asset generation failed', [
                'certificate_id' => $certificate->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Generate QR Code for certificate with enhanced styling
     */
    protected function generateQRCode(Certificate $certificate): string
    {
        try {
            // Ensure verification URL exists
            if (empty($certificate->verification_url)) {
                throw new \Exception('Verification URL is missing');
            }

            Log::info('Generating QR code', [
                'certificate_id' => $certificate->id,
                'verification_url' => $certificate->verification_url
            ]);

            // Create QR code with high error correction
            $qrCode = QrCode::create($certificate->verification_url)
                ->setSize(config('certificate.qr_code.size', 300))
                ->setMargin(config('certificate.qr_code.margin', 15))
                ->setRoundBlockSizeMode(RoundBlockSizeMode::Margin)
                ->setErrorCorrectionLevel(ErrorCorrectionLevel::High)
                ->setForegroundColor(new Color(12, 35, 64))
                ->setBackgroundColor(new Color(255, 255, 255));

            $writer = new PngWriter();
            $result = $writer->write($qrCode);
            
            // Generate filename and path
            $filename = config('certificate.storage.qr_path', 'certificates/qr') . '/' 
                . $certificate->verification_code . '.png';
            
            // Store QR code
            $disk = Storage::disk(config('certificate.storage.disk', 'public'));
            $disk->put($filename, $result->getString());
            
            // Verify file was created
            if (!$disk->exists($filename)) {
                throw new \Exception('QR code file was not created');
            }

            Log::info('QR code generated successfully', [
                'certificate_id' => $certificate->id,
                'filename' => $filename,
                'file_size' => $disk->size($filename)
            ]);
            
            return $filename;
        } catch (\Exception $e) {
            Log::error('QR Code generation failed', [
                'certificate_id' => $certificate->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Generate PDF certificate using public-view template
     */
    protected function generatePDF(Certificate $certificate): string
    {
        try {
            Log::info('Generating PDF', [
                'certificate_id' => $certificate->id,
                'qr_code_path' => $certificate->qr_code_path
            ]);

            // Verify QR code exists before generating PDF
            if ($certificate->qr_code_path) {
                $qrPath = storage_path('app/public/' . $certificate->qr_code_path);
                if (!file_exists($qrPath)) {
                    Log::warning('QR code file not found during PDF generation', [
                        'expected_path' => $qrPath
                    ]);
                }
            }

            // Load the public-view template with isPdf flag set to true
            $pdf = Pdf::loadView('certificates.public-view', [
                    'certificate' => $certificate,
                    'isPdf' => true // This flag disables buttons and screen-only elements
                ])
                ->setPaper('A4', 'landscape')
                ->setOptions([
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled' => true,
                    'enable_php' => true,
                    'dpi' => 150, // Higher DPI for better quality
                    'defaultFont' => 'DejaVu Sans',
                    'isFontSubsettingEnabled' => true,
                    'isPhpEnabled' => true,
                    'chroot' => storage_path('app/public'),
                    'debugPng' => false,
                    'debugKeepTemp' => false,
                    'debugCss' => false,
                    'fontHeightRatio' => 1.1,
                    'isJavascriptEnabled' => false,
                ]);
            
            $filename = config('certificate.storage.pdf_path', 'certificates/pdf') . '/' 
                . $certificate->verification_code . '.pdf';
            
            // Get PDF output
            $pdfOutput = $pdf->output();
            
            // Store PDF
            $disk = Storage::disk(config('certificate.storage.disk', 'public'));
            $disk->put($filename, $pdfOutput);
            
            // Verify file was created
            if (!$disk->exists($filename)) {
                throw new \Exception('PDF file was not created');
            }

            Log::info('PDF generated successfully', [
                'certificate_id' => $certificate->id,
                'filename' => $filename,
                'file_size' => $disk->size($filename)
            ]);
            
            return $filename;
        } catch (\Exception $e) {
            Log::error('PDF generation failed', [
                'certificate_id' => $certificate->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Regenerate certificate assets (useful after template changes)
     */
    public function regenerateCertificateAssets(Certificate $certificate): bool
    {
        // Clean up old assets first
        $this->cleanupAssets($certificate);
        
        // Generate new assets
        return $this->generateCertificateAssets($certificate);
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
                'completion_percentage' => 0,
                'completed_lessons' => 0,
                'total_lessons' => 0
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
     * Calculate course grade based on performance
     */
    public function calculateGrade($userId, $courseId): string
    {
        $course = \App\Models\Course::findOrFail($courseId);
        
        // Get assessments for this course
        $assessments = $course->assessments ?? collect();
        
        if ($assessments->count() == 0) {
            return config('certificate.grading.default_grade', 'Pass');
        }

        $totalScore = 0;
        $assessmentCount = 0;

        foreach ($assessments as $assessment) {
            $result = $this->getAssessmentResult($userId, $assessment->id);
            
            if ($result && $result['passed']) {
                $totalScore += $result['percentage'];
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
        $gradeScale = config('certificate.grading.scale', [
            'A+' => 97,
            'A' => 93,
            'A-' => 90,
            'B+' => 87,
            'B' => 83,
            'B-' => 80,
            'C+' => 77,
            'C' => 73,
            'C-' => 70,
            'D' => 60,
        ]);
        
        foreach ($gradeScale as $grade => $threshold) {
            if ($score >= $threshold) {
                return $grade;
            }
        }
        
        return 'F';
    }

    /**
     * Get assessment result for user
     */
    protected function getAssessmentResult($userId, $assessmentId): ?array
    {
        return null;
    }

    /**
     * Clean up certificate assets (QR code and PDF)
     */
    public function cleanupAssets(Certificate $certificate): bool
    {
        try {
            $disk = Storage::disk(config('certificate.storage.disk', 'public'));
            
            if ($certificate->qr_code_path && $disk->exists($certificate->qr_code_path)) {
                $disk->delete($certificate->qr_code_path);
                Log::info('Deleted QR code', ['path' => $certificate->qr_code_path]);
            }
            
            if ($certificate->pdf_path && $disk->exists($certificate->pdf_path)) {
                $disk->delete($certificate->pdf_path);
                Log::info('Deleted PDF', ['path' => $certificate->pdf_path]);
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

    /**
     * Get certificate statistics for analytics
     */
    public function getCertificateStatistics(): array
    {
        return [
            'total_issued' => Certificate::approved()->count(),
            'pending_approval' => Certificate::requested()->count(),
            'rejected' => Certificate::rejected()->count(),
            'revoked' => Certificate::revoked()->count(),
            'issued_this_month' => Certificate::approved()
                ->whereYear('approved_at', now()->year)
                ->whereMonth('approved_at', now()->month)
                ->count(),
            'issued_this_year' => Certificate::approved()
                ->whereYear('approved_at', now()->year)
                ->count(),
        ];
    }

    /**
     * Bulk approve certificates
     */
    public function bulkApprove(array $certificateIds, $approverId): array
    {
        $approved = 0;
        $failed = 0;
        $errors = [];

        foreach ($certificateIds as $id) {
            try {
                $certificate = Certificate::find($id);
                
                if (!$certificate) {
                    $failed++;
                    $errors[] = "Certificate #{$id} not found";
                    continue;
                }

                if (!$certificate->canBeApproved()) {
                    $failed++;
                    $errors[] = "Certificate #{$id} cannot be approved (status: {$certificate->status})";
                    continue;
                }

                $certificate->approve($approverId);
                $approved++;
            } catch (\Exception $e) {
                $failed++;
                $errors[] = "Certificate #{$id}: " . $e->getMessage();
                Log::error('Bulk approve error', [
                    'certificate_id' => $id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return [
            'success' => $approved,
            'failed' => $failed,
            'errors' => $errors,
            'message' => "Approved {$approved} certificate(s). {$failed} failed."
        ];
    }
}