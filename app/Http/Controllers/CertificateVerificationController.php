<?php

namespace App\Http\Controllers;

use App\Models\Credentials\Certificate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Log;

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
            if ($request->expectsJson()) {
                return response()->json([
                    'valid' => false,
                    'message' => 'Verification code is required.',
                    'certificate' => null
                ]);
            }
            return back()->with('error', 'Verification code is required.');
        }

        $certificate = Certificate::findByVerificationCode($code);
        
        if (!$certificate) {
            if ($request->expectsJson()) {
                return response()->json([
                    'valid' => false,
                    'message' => 'Certificate not found. Please check the verification code.',
                    'certificate' => null
                ]);
            }
            return back()->with('error', 'Certificate not found.');
        }

        $verificationData = $certificate->getVerificationData();
        
        // Log verification attempt
        $this->logVerificationAttempt($certificate, $request);
        
        if ($request->expectsJson()) {
            return response()->json($verificationData);
        }

        return view('certificates.verify')->with([
            'verificationData' => $verificationData,
            'certificate' => $certificate,
            'title' => 'Certificate Verification Result'
        ]);
    }

    /**
     * Show certificate publicly (unified view for both screen and PDF)
     */
    public function show($verificationCode)
    {
        $certificate = Certificate::findByVerificationCode($verificationCode);
        
        if (!$certificate) {
            abort(404, 'Certificate not found');
        }

        if (!$certificate->isActive()) {
            return view('certificates.verify')->with([
                'message' => $certificate->isRevoked() 
                    ? 'This certificate has been revoked and is no longer valid.' 
                    : 'This certificate is not valid.',
                'certificate' => $certificate,
                'title' => 'Invalid Certificate',
                'showInvalid' => true
            ]);
        }

        // Log view
        $this->logVerificationAttempt($certificate, request(), 'view');

        // Use the unified template for display
        return view('certificates.public-view')->with([
            'certificate' => $certificate,
            'isPdf' => false, // Screen view
            'title' => 'Certificate - ' . $certificate->certificate_number
        ]);
    }

    /**
     * Download certificate PDF
     */
    public function download($verificationCode)
    {
        $certificate = Certificate::findByVerificationCode($verificationCode);
        
        if (!$certificate || !$certificate->isActive()) {
            abort(404, 'Certificate not available for download');
        }

        // Auto-generate PDF if it doesn't exist
        if (!$certificate->pdf_path || !Storage::disk('public')->exists($certificate->pdf_path)) {
            try {
                Log::info('PDF not found, generating on-demand', [
                    'certificate_id' => $certificate->id,
                    'verification_code' => $certificate->verification_code
                ]);
                
                app(\App\Services\CertificateService::class)->generateCertificateAssets($certificate);
                $certificate->refresh();
            } catch (\Exception $e) {
                Log::error('Failed to generate PDF on download: ' . $e->getMessage(), [
                    'certificate_id' => $certificate->id,
                    'trace' => $e->getTraceAsString()
                ]);
                abort(500, 'Failed to generate certificate PDF');
            }
        }

        $filePath = storage_path('app/public/' . $certificate->pdf_path);
        
        if (!file_exists($filePath)) {
            Log::error('PDF file not found after generation', [
                'certificate_id' => $certificate->id,
                'expected_path' => $filePath
            ]);
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
        
        if (!$certificate || !$certificate->isActive()) {
            abort(404, 'QR code not available');
        }

        // Auto-generate QR if it doesn't exist
        if (!$certificate->qr_code_path || !Storage::disk('public')->exists($certificate->qr_code_path)) {
            try {
                Log::info('QR code not found, generating on-demand', [
                    'certificate_id' => $certificate->id
                ]);
                
                app(\App\Services\CertificateService::class)->generateCertificateAssets($certificate);
                $certificate->refresh();
            } catch (\Exception $e) {
                Log::error('Failed to generate QR code: ' . $e->getMessage());
                abort(500, 'Failed to generate QR code');
            }
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
            'codes' => 'required|array|min:1|max:10',
            'codes.*' => 'required|string|min:10|max:50'
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
     * Log verification attempt with detailed information
     */
    private function logVerificationAttempt($certificate, $request, $type = 'verify')
    {
        try {
            Log::info('Certificate verification', [
                'certificate_id' => $certificate->id,
                'certificate_number' => $certificate->certificate_number,
                'verification_code' => $certificate->verification_code,
                'type' => $type,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'user_id' => auth()->id(),
                'timestamp' => now(),
                'referer' => $request->header('referer')
            ]);
        } catch (\Exception $e) {
            // Don't let logging errors break verification
            Log::error('Verification logging failed: ' . $e->getMessage());
        }
    }
}