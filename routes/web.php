<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CertificateVerificationController;
use App\Livewire\StudentManagement\CertificateRequest;
use App\Livewire\AdminManagement\CertificateManagement;
use App\Http\Controllers\CertificateController;

Route::get('/dashboard', \App\Livewire\Component\DashboardOverview::class)->name('dashboard');



// Public Certificate Verification Routes (no auth required)
Route::prefix('certificate')->name('certificate.')->group(function () {
    // Main verification page
    Route::get('/verify', [CertificateVerificationController::class, 'index'])
        ->name('verify');
    
    // Verification by code (both GET and POST)
    Route::get('/verify/{verificationCode}', [CertificateVerificationController::class, 'verify'])
        ->name('verify.code');
    Route::post('/verify', [CertificateVerificationController::class, 'verify'])
        ->name('verify.submit');
    
    // Public certificate view
    Route::get('/view/{verificationCode}', [CertificateVerificationController::class, 'show'])
        ->name('view');
    
    // Certificate downloads
    Route::get('/download/{verificationCode}', [CertificateVerificationController::class, 'download'])
        ->name('download');
    
    // QR code endpoint
    Route::get('/qr/{verificationCode}', [CertificateVerificationController::class, 'qrCode'])
        ->name('qr');
    
    // Verification widget (embeddable)
    Route::get('/widget/{verificationCode}', [CertificateVerificationController::class, 'widget'])
        ->name('widget');
});

// API Routes for Certificate Verification
Route::prefix('api/certificate')->name('api.certificate.')->group(function () {
    // Single certificate verification
    Route::get('/verify/{verificationCode}', [CertificateVerificationController::class, 'api'])
        ->name('verify');
    
    // Batch certificate verification
    Route::post('/batch-verify', [CertificateVerificationController::class, 'batchVerify'])
        ->name('batch.verify');
});

// Authenticated Student Routes
Route::middleware(['auth', 'verified'])->prefix('student')->name('student.')->group(function () {
    // Certificate request page
    Route::get('/certificate/request/{courseId?}', CertificateRequest::class)
        ->name('certificate.request');
    
    // Student's certificates list
    Route::get('/certificates', function () {
        return view('student.certificates.index');
    })->name('certificates.index');
    
    // Verification report (only for certificate owner)
    Route::get('/certificate/report/{verificationCode}', [CertificateVerificationController::class, 'report'])
        ->name('certificate.report');
});

// Admin/Instructor Routes
Route::middleware(['auth', 'verified'])->group(function () {
    // Certificate Management (Super Admin, Academy Admin, Instructor)
    Route::middleware(['role:super_admin|academy_admin|instructor'])->group(function () {
        Route::get('/admin/certificates', CertificateManagement::class)
            ->name('admin.certificates.manage');
        
        // Certificate approval/rejection endpoints
        Route::prefix('admin/certificates')->name('admin.certificates.')->group(function () {
            Route::post('/{certificate}/approve', function ($certificateId) {
                $certificate = \App\Models\Certificate::findOrFail($certificateId);
                
                // Check permissions
                if (!auth()->user()->isSuperAdmin() && 
                    !auth()->user()->isAcademyAdmin() && 
                    !(auth()->user()->isInstructor() && $certificate->course->instructor_id === auth()->id())) {
                    abort(403);
                }
                
                $certificate->approve(auth()->id());
                
                return response()->json(['success' => true, 'message' => 'Certificate approved successfully']);
            })->name('approve');
            
            Route::post('/{certificate}/reject', function ($certificateId) {
                $certificate = \App\Models\Certificate::findOrFail($certificateId);
                $reason = request()->input('reason');
                
                // Check permissions
                if (!auth()->user()->isSuperAdmin() && 
                    !auth()->user()->isAcademyAdmin() && 
                    !(auth()->user()->isInstructor() && $certificate->course->instructor_id === auth()->id())) {
                    abort(403);
                }
                
                $certificate->reject($reason, auth()->id());
                
                return response()->json(['success' => true, 'message' => 'Certificate rejected']);
            })->name('reject');
            
            Route::post('/{certificate}/revoke', function ($certificateId) {
                $certificate = \App\Models\Certificate::findOrFail($certificateId);
                $reason = request()->input('reason');
                
                // Check permissions (only super admin and academy admin can revoke)
                if (!auth()->user()->isSuperAdmin() && !auth()->user()->isAcademyAdmin()) {
                    abort(403);
                }
                
                $certificate->revoke($reason, auth()->id());
                
                return response()->json(['success' => true, 'message' => 'Certificate revoked']);
            })->name('revoke');
        });
    });
});

// Super Admin Only Routes
Route::middleware(['auth', 'verified', 'role:super_admin'])->prefix('admin')->name('admin.')->group(function () {
    // Certificate analytics and reports
    Route::get('/certificates/analytics', function () {
        return view('admin.certificates.analytics');
    })->name('certificates.analytics');
    
    // Bulk certificate operations
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
    
    // Certificate templates management
    Route::get('/certificates/templates', function () {
        return view('admin.certificates.templates');
    })->name('certificates.templates');
});

// Webhook Routes (for external integrations)
Route::prefix('webhooks/certificates')->name('webhooks.certificates.')->group(function () {
    // Verification webhook for external systems
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

// Development/Testing Routes (only in non-production environments)
if (app()->environment(['local', 'staging'])) {
    Route::prefix('dev/certificates')->name('dev.certificates.')->group(function () {
        // Generate test certificate
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


// //Certificate
// Route::middleware(['auth', 'verified'])->prefix('certificates')->group(function () {
//     Route::get('/my-certificates', \App\Livewire\Certification\MyCertificates::class)->name('certificates.index')->middleware('can:view_own_certificates');
//     Route::get('/request', \App\Livewire\Certification\CertificateRequest::class)->name('certificates.request')->middleware('can:request_certificates');
//     Route::get('/templates', \App\Livewire\Certification\CertificateTemplates::class)->name('certificates.templates')->middleware('can:manage_certificate_templates');
//     Route::get('/certificates/verify/{uuid?}', \App\Livewire\Certification\PublicCertificateVerification::class)->name('certificates.public-verify');
//     Route::get('/certificates/download/{certificate}', [CertificateController::class, 'download'])->name('certificates.download');
//     Route::get('/approvals', \App\Livewire\Certification\CertificateApprovals::class)->name('certificates.approvals');
// });

// User Management
Route::middleware('auth')->group(function () {
    Route::get('/dashboard/profile/view', \App\Livewire\Component\Profile\ViewProfile::class)->name('profile.view');
    Route::get('/dashboard/profile/edit', \App\Livewire\Component\Profile\EditProfile::class)->name('profile.edit');
    Route::get('/dashboard/all-users', \App\Livewire\UserManagement\AllUser::class)->name('all-users');
    Route::get('/dashboard/roles-permissions', \App\Livewire\UserManagement\RolesPermissions::class)->name('roles-permissions');
    Route::get('/dashboard/pending-verifications', \App\Livewire\UserManagement\PendingVerifications::class)->name('pending-verifications');
    Route::get('/dashboard/user', \App\Livewire\UserManagement\UserManagement::class)->name('user-management');
    Route::get('/dashboard/user-activity', \App\Livewire\UserManagement\UserActivity::class)->name('user.activity');
});

//Student Management
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/enrolled-courses', \App\Livewire\StudentManagement\EnrolledCourses::class)->name('student.enrolled-courses');
    Route::get('/course-catalog', \App\Livewire\StudentManagement\CourseCatalog::class)->name('student.course-catalog');
    Route::get('/learning-analytics', \App\Livewire\StudentManagement\LearningAnalytics::class)->name('student.learning-analytics');
    Route::get('/saved-resources', \App\Livewire\StudentManagement\SavedResources::class)->name('student.saved-resources');
    Route::get('/offline-learning', \App\Livewire\StudentManagement\OfflineLearning::class)->name('student.offline-learning');
});

//Course Management
Route::middleware('auth')->group(function () {
    Route::get('/course-management/all-courses', \App\Livewire\CourseManagement\AllCourses::class)->name('all-course');
    Route::get('/course-management/my-courses', \App\Livewire\CourseManagement\UserCourses::class)->name('my-course');
    Route::get('/dashboard/courses/create', \App\Livewire\CourseManagement\CourseForm::class)->name('create_course');
    Route::get('/dashboard/courses/{courseId}/edit', \App\Livewire\CourseManagement\CourseForm::class)->name('edit_course');
    Route::get('/course/{course:slug}', \App\Livewire\StudentManagement\CourseView::class)->name('course.view');
    Route::get('/course-categories', \App\Livewire\CourseManagement\CourseCategories::class)->name('course-categories');
    Route::get('/dashboard/courses/{course}/builder', \App\Livewire\CourseManagement\CourseBuilder::class)->name('course-builder');
    Route::get('/dashboard/courses/reviews', \App\Livewire\CourseManagement\CourseReviews::class)->name('course-reviews');
    Route::get('/dashboard/courses/approvals', \App\Livewire\CourseManagement\CourseApprovals::class)->name('course-approvals');
    Route::get('/dashboard/courses/available', \App\Livewire\CourseManagement\AvailableCourses::class)->name('courses.available');
    Route::get('/projects/{slug}', [App\Http\Controllers\ProjectController::class, 'show'])->name('project.show');
});

//System Setting
Route::middleware('auth')->group(function () {
    Route::get('/dashboard/settings', \App\Livewire\SystemManagement\Settings::class)->name('settings');
    Route::get('/dashboard/notifications', \App\Livewire\SystemManagement\Notifications::class)->name('notifications');
    Route::get('/dashboard/help-support', \App\Livewire\SystemManagement\HelpSupport::class)->name('help.support');
    Route::get('/dashboard/support-tickets', \App\Livewire\SystemManagement\SupportTicketManagement::class)->name('support.tickets');
    Route::get('/dashboard/faq-management', \App\Livewire\SystemManagement\FaqManagement::class)->name('faq.management');
    Route::get('/dashboard/feedback', \App\Livewire\SystemManagement\Feedback::class)->name('feedback');
    Route::get('/dashboard/feedback-management', \App\Livewire\SystemManagement\FeedbackManagement::class)->name('feedback.management');
    Route::get('/dashboard/announcements', \App\Livewire\SystemManagement\Announcements::class)->name('announcements');
    Route::get('/dashboard/announcement-management', \App\Livewire\SystemManagement\AnnouncementManagement::class)->name('announcement.management');
    Route::get('/dashboard/system-status', \App\Livewire\SystemManagement\SystemStatus::class)->name('system-status');
    Route::get('/dashboard/system-status-management', \App\Livewire\SystemManagement\SystemStatusManagement::class)->name('system-status.management');

});

// //CBT
// Route::middleware('auth')->group(function () {
//     Route::get('/cbt/exam/{examId}', \App\Livewire\Component\CBT\TakeCbtExam::class)->name('cbt.exam');
//     Route::get('/cbt/results', \App\Livewire\Component\CBT\ViewCbtResults::class)->name('cbt.results');
//     Route::get('/cbt/management', \App\Livewire\Component\CBT\CbtManagement::class)->name('cbt.management');
// });

require __DIR__ . '/auth.php';


