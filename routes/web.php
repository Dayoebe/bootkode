<?php

use App\Livewire\CertificateManagement\CertificateAnalytics;
use App\Livewire\CertificateManagement\CertificateTemplates;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CertificateVerificationController;
use App\Livewire\CertificateManagement\CertificateRequest;
use App\Livewire\CertificateManagement\CertificateManagement;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// =============================================================================
// PUBLIC PAGES (No Authentication Required)
// =============================================================================

// Public Certificate Verification Routes

    Route::get('/About', \App\Livewire\Pages\AboutUs::class)->name('about');
    Route::get('/Contact', \App\Livewire\Pages\ContactUs::class)->name('contact');
    Route::get('/Statistics', \App\Livewire\Pages\Statistics::class)->name('statistics');
    Route::get('/Guideline', \App\Livewire\Pages\Guideline::class)->name('guideline');

// =============================================================================
// PUBLIC ROUTES (No Authentication Required)
// =============================================================================

// Public Certificate Verification Routes
Route::prefix('certificate')->name('certificate.')->group(function () {
    Route::get('/verify', [CertificateVerificationController::class, 'index'])->name('verify');
    Route::get('/verify/{verificationCode}', [CertificateVerificationController::class, 'verify'])->name('verify.code');
    Route::post('/verify', [CertificateVerificationController::class, 'verify'])->name('verify.submit');
    Route::get('/view/{verificationCode}', [CertificateVerificationController::class, 'show'])->name('view');
    Route::get('/download/{verificationCode}', [CertificateVerificationController::class, 'download'])->name('download');
    Route::get('/qr/{verificationCode}', [CertificateVerificationController::class, 'qrCode'])->name('qr');
    Route::get('/widget/{verificationCode}', [CertificateVerificationController::class, 'widget'])->name('widget');
});

// API Routes for Certificate Verification
Route::prefix('api/certificate')->name('api.certificate.')->group(function () {
    Route::get('/verify/{verificationCode}', [CertificateVerificationController::class, 'api'])->name('verify');
    Route::post('/batch-verify', [CertificateVerificationController::class, 'batchVerify'])->name('batch.verify');
});

// Webhook Routes
Route::prefix('webhooks/certificates')->name('webhooks.certificates.')->group(function () {
    Route::post('/verify', function () {
        $verificationCode = request()->input('verification_code');
        if (!$verificationCode) {
            return response()->json(['error' => 'Verification code required'], 400);
        }
        $certificate = \App\Models\Certificate::findByVerificationCode($verificationCode);
        if (!$certificate) {
            return response()->json(['valid' => false, 'error' => 'Certificate not found'], 404);
        }
        return response()->json($certificate->getVerificationData());
    })->name('webhook.verify');
});

// =============================================================================
// AUTHENTICATED ROUTES - DASHBOARD
// =============================================================================

Route::get('/dashboard', \App\Livewire\Component\DashboardOverview::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// =============================================================================
// USER PROFILE & MANAGEMENT ROUTES
// =============================================================================

Route::middleware(['auth', 'verified'])->prefix('dashboard')->name('')->group(function () {

    Route::get('/profile', \App\Livewire\UserManagement\Profile::class)->name('profile.view');
    Route::get('/profile/edit', function () {
        return redirect()->route('profile.view', ['mode' => 'edit']);
    })->name('profile.edit');
    
    // User Management - Admin Only
    Route::get('/all-users', \App\Livewire\UserManagement\AllUser::class)->name('all-users');
    Route::get('/roles-permissions', \App\Livewire\UserManagement\RolesPermissions::class)->name('roles-permissions');
    Route::get('/pending-verifications', \App\Livewire\UserManagement\PendingVerifications::class)->name('pending-verifications');
    Route::get('/user', \App\Livewire\UserManagement\UserManagement::class)->name('user-management');
    Route::get('/user-activity', \App\Livewire\UserManagement\UserActivity::class)->name('user.activity');
});

// =============================================================================
// STUDENT ROUTES
// =============================================================================

Route::middleware(['auth', 'verified'])->group(function () {
    // Student Dashboard Features
    Route::get('/enrolled-courses', \App\Livewire\StudentManagement\EnrolledCourses::class)->name('student.enrolled-courses');
    Route::get('/course-catalog', \App\Livewire\StudentManagement\CourseCatalog::class)->name('student.course-catalog');
    Route::get('/learning-analytics', \App\Livewire\StudentManagement\LearningAnalytics::class)->name('student.learning-analytics');
    Route::get('/saved-resources', \App\Livewire\StudentManagement\SavedResources::class)->name('student.saved-resources');
    Route::get('/offline-learning', \App\Livewire\StudentManagement\OfflineLearning::class)->name('student.offline-learning');
    Route::get('/course/{course:slug}', \App\Livewire\StudentManagement\CourseView::class)->name('course.view');
});

// Student Certificate Routes
Route::middleware(['auth', 'verified'])->prefix('student')->name('student.')->group(function () {
    Route::get('/certificates', \App\Livewire\CertificateManagement\StudentCertificates::class)->name('certificates.index');
    Route::get('/certificate/request/{courseId?}', CertificateRequest::class)->name('certificate.request');
    Route::get('/certificate/report/{verificationCode}', [CertificateVerificationController::class, 'report'])->name('certificate.report');
});

// =============================================================================
// COURSE MANAGEMENT ROUTES
// =============================================================================

Route::middleware(['auth', 'verified'])->group(function () {
    // Course Management
    Route::get('/course-management/all-courses', \App\Livewire\CourseManagement\AllCourses::class)->name('all-course');
    Route::get('/course-management/my-courses', \App\Livewire\CourseManagement\UserCourses::class)->name('my-course');
    Route::get('/course-categories', \App\Livewire\CourseManagement\CourseCategories::class)->name('course-categories');
});

// Course CRUD Routes
Route::middleware(['auth', 'verified'])->prefix('dashboard/courses')->name('')->group(function () {
    Route::get('/create', \App\Livewire\CourseManagement\CourseForm::class)->name('create_course');
    Route::get('/{courseId}/edit', \App\Livewire\CourseManagement\CourseForm::class)->name('edit_course');
    Route::get('/{course}/builder', \App\Livewire\CourseManagement\CourseBuilder::class)->name('course-builder');
    Route::get('/reviews', \App\Livewire\CourseManagement\CourseReviews::class)->name('course-reviews');
    Route::get('/approvals', \App\Livewire\CourseManagement\CourseApprovals::class)->name('course-approvals');
    Route::get('/available', \App\Livewire\CourseManagement\AvailableCourses::class)->name('courses.available');
});

// Project Routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/projects/{slug}', [App\Http\Controllers\ProjectController::class, 'show'])->name('project.show');
});

// =============================================================================
// CERTIFICATE MANAGEMENT ROUTES (ADMIN/INSTRUCTOR)
// =============================================================================

// Certificate Management Dashboard
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/admin/certificates', CertificateManagement::class)->name('admin.certificates.manage');
    Route::get('/admin/analytics', CertificateAnalytics::class)->name('admin.certificates.analytics');
    Route::get('/admin/templates', CertificateTemplates::class)->name('admin.certificates.templates');
});

// Certificate Actions
Route::middleware(['auth', 'verified'])->prefix('admin/certificates')->name('admin.certificates.')->group(function () {
    Route::post('/{certificate}/approve', function ($certificateId) {
        $certificate = \App\Models\Certificate::findOrFail($certificateId);

        // Check permissions using User model methods
        if (!auth()->user()->canManageCertificates()) {
            return response()->json(['error' => 'Unauthorized access'], 403);
        }

        // Additional check for instructors
        if (auth()->user()->isInstructor() && $certificate->course->instructor_id !== auth()->id()) {
            return response()->json(['error' => 'You can only manage certificates for your own courses'], 403);
        }

        $certificate->approve(auth()->id());
        return response()->json(['success' => true, 'message' => 'Certificate approved successfully']);
    })->name('approve');

    Route::post('/{certificate}/reject', function ($certificateId) {
        $certificate = \App\Models\Certificate::findOrFail($certificateId);
        $reason = request()->input('reason');

        // Check permissions using User model methods
        if (!auth()->user()->canManageCertificates()) {
            return response()->json(['error' => 'Unauthorized access'], 403);
        }

        // Additional check for instructors
        if (auth()->user()->isInstructor() && $certificate->course->instructor_id !== auth()->id()) {
            return response()->json(['error' => 'You can only manage certificates for your own courses'], 403);
        }

        $certificate->reject($reason, auth()->id());
        return response()->json(['success' => true, 'message' => 'Certificate rejected']);
    })->name('reject');

    Route::post('/{certificate}/revoke', function ($certificateId) {
        $certificate = \App\Models\Certificate::findOrFail($certificateId);
        $reason = request()->input('reason');

        // Check permissions (only super admin and academy admin can revoke)
        if (!auth()->user()->canApproveAllCertificates()) {
            return response()->json(['error' => 'You do not have permission to revoke certificates'], 403);
        }

        $certificate->revoke($reason, auth()->id());
        return response()->json(['success' => true, 'message' => 'Certificate revoked']);
    })->name('revoke');
});

// Certificate Analytics & Reports (Super Admin Only)
Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    Route::post('/certificates/bulk-approve', function () {
        $certificateIds = request()->input('certificate_ids', []);
        $approved = 0;

        foreach ($certificateIds as $id) {
            try {
                $certificate = \App\Models\Certificate::find($id);
                if ($certificate && $certificate->isRequested()) {
                    $certificate->approve(auth()->id());
                    $approved++;
                }
            } catch (\Exception $e) {
                \Log::error('Bulk approve error: ' . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Approved {$approved} certificates"
        ]);
    })->name('certificates.bulk.approve');

    // Route::get('/certificates/templates', function () {
    //     return view('certificates.templates');
    // })->name('certificates.templates');
});

// =============================================================================
// SYSTEM MANAGEMENT ROUTES
// =============================================================================

Route::middleware(['auth', 'verified'])->prefix('dashboard')->name('')->group(function () {
    // Settings & Configuration
    Route::get('/settings', \App\Livewire\SystemManagement\Settings::class)->name('settings');
    Route::get('/notifications', \App\Livewire\SystemManagement\Notifications::class)->name('notifications');
    Route::get('/system-status', \App\Livewire\SystemManagement\SystemStatus::class)->name('system-status');
    Route::get('/system-status-management', \App\Livewire\SystemManagement\SystemStatusManagement::class)->name('system-status.management');
    
    // Support System
    Route::get('/help-support', \App\Livewire\SystemManagement\HelpSupport::class)->name('help.support');
    Route::get('/support-tickets', \App\Livewire\SystemManagement\SupportTicketManagement::class)->name('support.tickets');
    Route::get('/faq-management', \App\Livewire\SystemManagement\FaqManagement::class)->name('faq.management');
    
    // Feedback System
    Route::get('/feedback', \App\Livewire\SystemManagement\Feedback::class)->name('feedback');
    Route::get('/feedback-management', \App\Livewire\SystemManagement\FeedbackManagement::class)->name('feedback.management');
    
    // Announcements
    Route::get('/announcements', \App\Livewire\SystemManagement\Announcements::class)->name('announcements');
    Route::get('/announcement-management', \App\Livewire\SystemManagement\AnnouncementManagement::class)->name('announcement.management');
});

// =============================================================================
// UTILITY & BACKWARD COMPATIBILITY ROUTES
// =============================================================================

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard certificate redirect
    Route::get('/dashboard/certificates', function() {
        // Redirect based on user role
        if (auth()->user()->canManageCertificates()) {
            return redirect()->route('admin.certificates.manage');
        }
        return redirect()->route('student.certificates.index');
    })->name('certificates.dashboard');

    // Alternative certificate routes for backward compatibility
    Route::get('/certificates', \App\Livewire\CertificateManagement\StudentCertificates::class)->name('certificates.index');
    Route::get('/my-certificates', \App\Livewire\CertificateManagement\StudentCertificates::class)->name('my.certificates');
    Route::get('/dashboard/my-certificates', \App\Livewire\CertificateManagement\StudentCertificates::class)->name('dashboard.certificates');
});

// =============================================================================
// DEVELOPMENT/TESTING ROUTES (NON-PRODUCTION ONLY)
// =============================================================================
if (app()->environment(['local', 'staging'])) {
    Route::prefix('dev/certificates')->name('dev.certificates.')->group(function () {
        Route::get('/test/{userId}/{courseId}', function ($userId, $courseId) {
            $certificate = \App\Models\Certificate::create([
                'user_id' => $userId,
                'course_id' => $courseId,
                'status' => \App\Models\Certificate::STATUS_APPROVED,
                'completion_date' => now()->subDays(rand(1, 30)),
                'grade' => collect(['A+', 'A', 'A-', 'B+', 'B', 'Pass'])->random(),
                'approved_at' => now(),
                'approved_by' => 1,
                'issued_date' => now(),
            ]);

            return response()->json([
                'message' => 'Test certificate created',
                'certificate' => $certificate,
                'verification_url' => route('certificate.verify.code', $certificate->verification_code)
            ]);
        })->name('test');
    });
}

// =============================================================================
// AUTHENTICATION ROUTES
// =============================================================================
require __DIR__ . '/auth.php';