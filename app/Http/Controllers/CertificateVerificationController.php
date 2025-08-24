<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class CertificateVerificationController extends Controller
{
    /**
     * Show certificate verification page
     */
    public function index()
    {
        return view('certificates.verify')->with([
            'title' => 'Verify Certificate'
        ]);
    }

    /**
     * Verify certificate by code
     */
    public function verify(Request $request, $verificationCode = null)
    {
        $code = $verificationCode ?? $request->input('code');
        
        if (!$code) {
            return response()->json([
                'valid' => false,
                'message' => 'Verification code is required.',
                'certificate' => null
            ]);
        }

        $certificate = Certificate::findByVerificationCode($code);
        
        if (!$certificate) {
            return response()->json([
                'valid' => false,
                'message' => 'Certificate not found. Please check the verification code.',
                'certificate' => null
            ]);
        }

        $verificationData = $certificate->getVerificationData();
        
        // Log verification attempt
        $this->logVerificationAttempt($certificate, $request);
        
        if ($request->expectsJson()) {
            return response()->json($verificationData);
        }

        return view('certificates.verification-result')->with([
            'verificationData' => $verificationData,
            'certificate' => $certificate,
            'title' => 'Certificate Verification Result'
        ]);
    }

    /**
     * Show certificate publicly (for verified certificates)
     */
    public function show($verificationCode)
    {
        $certificate = Certificate::findByVerificationCode($verificationCode);
        
        if (!$certificate) {
            abort(404, 'Certificate not found');
        }

        if (!$certificate->isActive()) {
            return view('certificates.invalid')->with([
                'message' => $certificate->isRevoked() 
                    ? 'This certificate has been revoked and is no longer valid.' 
                    : 'This certificate is not valid.',
                'certificate' => $certificate,
                'title' => 'Invalid Certificate'
            ]);
        }

        // Log view
        $this->logVerificationAttempt($certificate, request(), 'view');

        return view('certificates.public-view')->with([
            'certificate' => $certificate,
            'title' => 'Certificate - ' . $certificate->certificate_number
        ]);
    }

    /**
     * Download certificate PDF
     */
    public function download($verificationCode)
    {
        $certificate = Certificate::findByVerificationCode($verificationCode);
        
        if (!$certificate || !$certificate->isActive() || !$certificate->pdf_path) {
            abort(404, 'Certificate not available for download');
        }

        $filePath = storage_path('app/public/' . $certificate->pdf_path);
        
        if (!file_exists($filePath)) {
            abort(404, 'Certificate file not found');
        }

        // Log download
        $this->logVerificationAttempt($certificate, request(), 'download');

        return Response::download(
            $filePath,
            "Certificate_{$certificate->certificate_number}.pdf",
            [
                'Content-Type' => 'application/pdf',
            ]
        );
    }

    /**
     * Get QR code for certificate
     */
    public function qrCode($verificationCode)
    {
        $certificate = Certificate::findByVerificationCode($verificationCode);
        
        if (!$certificate || !$certificate->isActive() || !$certificate->qr_code_path) {
            abort(404, 'QR code not available');
        }

        $filePath = storage_path('app/public/' . $certificate->qr_code_path);
        
        if (!file_exists($filePath)) {
            abort(404, 'QR code file not found');
        }

        return response()->file($filePath, [
            'Content-Type' => 'image/png',
        ]);
    }

    /**
     * Batch verify multiple certificates
     */
    public function batchVerify(Request $request)
    {
        $request->validate([
            'codes' => 'required|array|max:10',
            'codes.*' => 'required|string|max:50'
        ]);

        $results = [];
        
        foreach ($request->codes as $code) {
            $certificate = Certificate::findByVerificationCode($code);
            
            if ($certificate) {
                $results[$code] = $certificate->getVerificationData();
                $this->logVerificationAttempt($certificate, $request, 'batch');
            } else {
                $results[$code] = [
                    'valid' => false,
                    'message' => 'Certificate not found.',
                    'certificate' => null
                ];
            }
        }

        return response()->json([
            'success' => true,
            'results' => $results
        ]);
    }

    /**
     * API endpoint for certificate details
     */
    public function api($verificationCode)
    {
        $certificate = Certificate::findByVerificationCode($verificationCode);
        
        if (!$certificate) {
            return response()->json([
                'error' => 'Certificate not found'
            ], 404);
        }

        $verificationData = $certificate->getVerificationData();
        
        // Log API access
        $this->logVerificationAttempt($certificate, request(), 'api');

        return response()->json($verificationData);
    }

    /**
     * Embed widget for certificate verification
     */
    public function widget($verificationCode)
    {
        $certificate = Certificate::findByVerificationCode($verificationCode);
        
        if (!$certificate) {
            return view('certificates.widget-invalid');
        }

        $verificationData = $certificate->getVerificationData();

        return view('certificates.verification-widget')->with([
            'verificationData' => $verificationData,
            'certificate' => $certificate
        ]);
    }

    /**
     * Generate verification report
     */
    public function report($verificationCode)
    {
        $certificate = Certificate::findByVerificationCode($verificationCode);
        
        if (!$certificate || !$certificate->isActive()) {
            abort(404, 'Certificate not found');
        }

        // Only allow certificate owner or authorized users to access report
        if (!auth()->check() || 
            (auth()->id() !== $certificate->user_id && 
             !auth()->user()->isSuperAdmin() && 
             !auth()->user()->isAcademyAdmin() &&
             !(auth()->user()->isInstructor() && $certificate->course->instructor_id === auth()->id()))) {
            abort(403, 'Unauthorized');
        }

        $verificationLogs = $this->getVerificationLogs($certificate);

        return view('certificates.verification-report')->with([
            'certificate' => $certificate,
            'verificationLogs' => $verificationLogs,
            'title' => 'Certificate Verification Report'
        ]);
    }

    /**
     * Log verification attempt
     */
    private function logVerificationAttempt($certificate, $request, $type = 'verify')
    {
        try {
            \Log::info('Certificate verification', [
                'certificate_id' => $certificate->id,
                'certificate_number' => $certificate->certificate_number,
                'verification_code' => $certificate->verification_code,
                'type' => $type,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'user_id' => auth()->id(),
                'timestamp' => now()
            ]);

            // You could also store this in a separate verification_logs table
            // if you want to track verification attempts more permanently
        } catch (\Exception $e) {
            // Don't let logging errors break verification
        }
    }

    /**
     * Get verification logs for certificate
     */
    private function getVerificationLogs($certificate)
    {
        // This would typically come from a separate verification_logs table
        // For now, we'll return empty array or implement with log file parsing
        return [];
    }
}