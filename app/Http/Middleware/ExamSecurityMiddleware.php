<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\CbtResult;
use Illuminate\Support\Facades\Auth;

class ExamSecurityMiddleware
{
    /**
     * Handle an incoming request for exam routes with enhanced security
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Validate exam session if accessing exam viewer
        if ($request->route()->getName() === 'cbt.exam.take') {
            $examId = $request->route('exam');
            $this->validateExamSession($examId);
        }
        
        // Security headers for exam environment
        $response = $next($request);
        
        // Prevent caching
        $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate, private');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');
        
        // Security headers
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'no-referrer');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), clipboard-read=(), clipboard-write=()');
        
        // Content Security Policy for exam security
        $csp = [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com",
            "style-src 'self' 'unsafe-inline' https://fonts.bunny.net https://cdnjs.cloudflare.com",
            "font-src 'self' https://fonts.bunny.net https://cdnjs.cloudflare.com",
            "img-src 'self' data: blob:",
            "connect-src 'self'",
            "media-src 'self'",
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self'"
        ];
        
        $response->headers->set('Content-Security-Policy', implode('; ', $csp));
        
        return $response;
    }
    
    private function validateExamSession($examId)
    {
        $user = Auth::user();
        
        // Check for active session
        $activeSession = CbtResult::where('cbt_exam_id', $examId)
            ->where('user_id', $user->id)
            ->where('status', 'in_progress')
            ->first();
            
        if (!$activeSession) {
            // No active session - redirect to exam selection
            abort(redirect()->route('cbt.exams')->with('error', 'No active exam session found.'));
        }
        
        // Check session timeout
        $timeLimit = $activeSession->exam->duration_minutes * 60;
        $elapsed = now()->diffInSeconds($activeSession->started_at);
        
        if ($elapsed > $timeLimit) {
            // Session expired - auto submit
            $activeSession->update([
                'status' => 'completed',
                'auto_submitted' => true,
                'completed_at' => now(),
                'time_spent_seconds' => $timeLimit,
                'time_remaining_seconds' => 0,
            ]);
            
            abort(redirect()->route('cbt.exams')->with('error', 'Exam session has expired.'));
        }
    }
}

// Register this middleware in your app/Http/Kernel.php
// Add to the $routeMiddleware array:
// 'exam.security' => \App\Http\Middleware\ExamSecurityMiddleware::class,

// Then update your routes to use this middleware:
/*
Route::get('/cbt/exam/{exam}/take', CbtExamViewer::class)
    ->name('cbt.exam.take')
    ->middleware(['auth', 'verified', 'exam.security', 'throttle:1,1']);
*/