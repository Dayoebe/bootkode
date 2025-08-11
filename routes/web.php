<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ProfileController;
use App\Models\User;
use App\Livewire\Component\CourseManagement\AllCourses;
use App\Livewire\Component\CourseManagement\CreateCourse;
use App\Livewire\Component\CourseManagement\CourseCategories;
use App\Livewire\Component\CourseManagement\CourseBuilder;
use App\Livewire\Component\CourseManagement\CourseReviews;
use App\Livewire\Component\CourseManagement\CourseApprovals;
use App\Livewire\Component\CourseManagement\EditCourse;
use App\Livewire\Component\CourseManagement\CourseBuilder\CoursePreview;
use App\Livewire\Component\UserManagement;


// Certification Routes
Route::middleware(['auth', 'verified'])->prefix('certificates')->group(function () {
    // Student routes
    Route::get('/my-certificates', \App\Livewire\Certification\MyCertificates::class)->name('certificates.index');
    Route::get('/request', \App\Livewire\Certification\CertificateRequest::class)->name('certificates.request');
    Route::get('/templates', \App\Livewire\Certification\CertificateTemplates::class)->name('certificates.templates');
    
    // Admin routes
    Route::middleware(['can:manage_certificates'])->group(function () {
        // Route::get('/verify', \App\Livewire\Certification\VerifyCertificate::class)->name('certificates.verify');
        // Route::get('/bulk-issue', \App\Livewire\Certification\BulkCertificateIssuance::class)->name('certificates.bulk');
        Route::get('/approvals', \App\Livewire\Certification\CertificateApprovals::class)->name('certificates.approvals');
    });
    
    // Public verification route (no auth needed)
    // Route::get('/verify/{uuid}', \App\Livewire\Certification\PublicCertificateVerification::class)->name('certificates.public-verify');
});

// Public verification route (no auth needed)
// Public verification route (no auth needed)
Route::get('/certificates/verify/{uuid?}', \App\Livewire\Certification\PublicCertificateVerification::class)
    ->name('certificates.public-verify');




    Route::get('/certificates/download/{certificate}', [\App\Http\Controllers\CertificateController::class, 'download'])
    ->name('certificates.download')
    ->middleware('auth');

Route::middleware(['auth', 'verified'])->prefix('certificates')->group(function () {
    // Route::get('/list', \App\Livewire\Component\Certification\CertificateList::class)->name('certificates.index');
    Route::get('/request', \App\Livewire\Component\Certification\CertificateRequest::class)->name('certificates.request');
    Route::get('/templates', \App\Livewire\Component\Certification\CertificateTemplates::class)->name('certificates.templates')->middleware('can:manage_templates');
    Route::get('/{certificate:uuid}', \App\Livewire\Component\Certification\CertificateShow::class)->name('certificates.show');
});




// Group for authenticated user profiles
Route::middleware('auth')->group(function () {
    // Existing profile routes (keep if needed for API)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // New Livewire Profile routes
    Route::get('/dashboard/profile/view', \App\Livewire\Component\Profile\ViewProfile::class)->name('profile.view');
    Route::get('/dashboard/profile/edit', \App\Livewire\Component\Profile\EditProfile::class)->name('profile.edit');

});


// Student Management Routes
Route::middleware(['auth', 'verified'])->prefix('student')->group(function () {
    // Dashboard Home
    Route::get('/dashboard', \App\Livewire\Dashboard\StudentDashboard::class)->name('student.dashboard');
    // Enrolled Courses
    Route::get('/enrolled-courses', \App\Livewire\Component\StudentManagement\EnrolledCourses::class)
        ->name('student.enrolled-courses');
    // Course Catalog
    Route::get('/course-catalog', \App\Livewire\Component\StudentManagement\CourseCatalog::class)
        ->name('student.course-catalog');
    // // Learning Analytics
    Route::get('/learning-analytics', \App\Livewire\Component\StudentManagement\LearningAnalytics::class)
        ->name('student.learning-analytics');

    Route::get('/saved-resources', \App\Livewire\Component\StudentManagement\SavedResources::class)
        ->name('student.saved-resources');
    Route::get('/offline-content/{path}', function (Request $request, $path) {
        // Verify the signed URL
        if (!$request->hasValidSignature()) {
            abort(401);
        }

        $user = $request->user();
        $fullPath = config('app.offline_content_path') . "/user_{$user->id}/{$path}";

        if (!Storage::exists($fullPath)) {
            abort(404);
        }

        return Storage::response($fullPath);
    })->name('offline.content');
    Route::get('/offline-learning', \App\Livewire\Component\StudentManagement\OfflineLearning::class)
        ->name('student.offline-learning');
    // Assignments
    // Route::get('/assignments', \App\Livewire\Component\StudentManagement\Assignments::class)
    //     ->name('student.assignments');
});


//Courses  
Route::middleware('auth')->group(function () {
    Route::get('/course-management/all-courses', AllCourses::class)->name('all-course');
    Route::get('/dashboard/courses/create', CreateCourse::class)->name('course_management.create_course');
    Route::get('/edit/{course}', EditCourse::class)->name('edit-course');
    Route::get('/course-categories', CourseCategories::class)->name('course-categories');
    Route::get('/dashboard/courses/{course}/builder', CourseBuilder::class)->name('course-builder');
    Route::get('/dashboard/courses/reviews', CourseReviews::class)->name('course-reviews');
    Route::get('/dashboard/courses/approvals', CourseApprovals::class)->name('course-approvals');
    Route::get('/courses/{course}/preview/{highlight?}', CoursePreview::class)->name('course.preview');
});

//User management
Route::middleware('auth')->group(function () {
    Route::get('/dashboard/user', UserManagement::class)->name('user-management');

});


require __DIR__ . '/auth.php';