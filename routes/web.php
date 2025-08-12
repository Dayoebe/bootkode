<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\ProfileController;
use App\Livewire\Component\CourseManagement\AllCourses;
use App\Livewire\Component\CourseManagement\CreateCourse;
use App\Livewire\Component\CourseManagement\CourseCategories;
use App\Livewire\Component\CourseManagement\CourseBuilder;
use App\Livewire\Component\CourseManagement\CourseReviews;
use App\Livewire\Component\CourseManagement\CourseApprovals;
use App\Livewire\Component\CourseManagement\EditCourse;
use App\Livewire\Component\CourseManagement\CourseBuilder\CoursePreview;
use App\Livewire\Component\UserManagement;

Route::middleware(['auth', 'verified'])->prefix('certificates')->group(function () {
    Route::get('/my-certificates', \App\Livewire\Certification\MyCertificates::class)->name('certificates.index')->middleware('can:view_own_certificates');
    Route::get('/request', \App\Livewire\Certification\CertificateRequest::class)->name('certificates.request')->middleware('can:request_certificates');
    Route::get('/templates', \App\Livewire\Certification\CertificateTemplates::class)->name('certificates.templates')->middleware('can:manage_certificate_templates');
    Route::middleware(['can:manage_certificates'])->group(function () {
        Route::get('/approvals', \App\Livewire\Certification\CertificateApprovals::class)->name('certificates.approvals');
        // Route::get('/bulk-issue', \App\Livewire\Certification\BulkCertificateIssuance::class)->name('certificates.bulk'); // Uncomment when implemented
    });
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

Route::get('/certificates/verify/{uuid?}', \App\Livewire\Certification\PublicCertificateVerification::class)
    ->name('certificates.public-verify');

Route::get('/certificates/download/{certificate}', [CertificateController::class, 'download'])
    ->name('certificates.download')
    ->middleware('auth');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/enrolled-courses', \App\Livewire\Component\StudentManagement\EnrolledCourses::class)
        ->name('student.enrolled-courses');
    Route::get('/course-catalog', \App\Livewire\Component\StudentManagement\CourseCatalog::class)
        ->name('student.course-catalog');
    Route::get('/learning-analytics', \App\Livewire\Component\StudentManagement\LearningAnalytics::class)
        ->name('student.learning-analytics');
    Route::get('/saved-resources', \App\Livewire\Component\StudentManagement\SavedResources::class)
        ->name('student.saved-resources');
    Route::get('/offline-learning', \App\Livewire\Component\StudentManagement\OfflineLearning::class)
        ->name('student.offline-learning');
});

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

Route::middleware('auth')->group(function () {
    Route::get('/dashboard/user', UserManagement::class)->name('user-management');
});

require __DIR__ . '/auth.php';


