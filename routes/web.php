<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Livewire\Home;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Models\User;
use App\Livewire\Component\CourseManagement\AllCourses;
use App\Livewire\Component\CourseManagement\CreateCourse;
use App\Livewire\Component\CourseManagement\CourseCategories;
use App\Livewire\Component\CourseManagement\CourseBuilder;
use App\Livewire\Component\CourseManagement\CourseReviews; 
use App\Livewire\Component\CourseManagement\CourseApprovals;
use App\Livewire\Component\CourseManagement\EditCourse;;

// Public Home Page
Route::get('/', Home::class)->name('home');

// Route for post-login redirection based on role
// This route will be the target for successful logins
Route::get('/dashboard-redirect', DashboardController::class)
    ->middleware(['auth', 'verified']) // Ensure user is authenticated and email is verified
    ->name('dashboard.redirect');

// Email Verification Routes
// This route handles the actual email verification link click
Route::get('/email/verify/{id}/{hash}', [\App\Http\Controllers\Auth\VerifyEmailController::class, '__invoke']) // Use full namespace here
    ->middleware(['auth', 'signed']) // Ensure 'auth' and 'signed' middleware are present and correct
    ->name('verification.verify');

// This route handles resending the verification email
Route::post('/email/resend', [\App\Http\Controllers\Auth\EmailVerificationNotificationController::class, 'store']) // Use full namespace here
    ->middleware(['auth', 'throttle:6,1'])
    ->name('verification.send');

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


Route::middleware('auth')->group(function () {
    Route::get('/course-management/all-courses', AllCourses::class)->name('all-course');
    Route::get('/dashboard/courses/create', CreateCourse::class)->name('course_management.create_course'); 
    Route::get('/edit/{course}', EditCourse::class)->name('edit-course');
    Route::get('/course-categories', CourseCategories::class)       ->name('course-categories');
    Route::get('/dashboard/courses/{course}/builder', CourseBuilder::class)->name('course-builder'); 
    Route::get('/dashboard/courses/reviews', CourseReviews::class)->name('course-reviews');
    Route::get('/dashboard/courses/approvals', CourseApprovals::class)->name('course-approvals');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Super Admin Dashboard
    Route::get('/super-admin/dashboard', \App\Livewire\Dashboard\SuperAdminDashboard::class)
        ->name('super_admin.dashboard');

    // Academy Admin Dashboard
    Route::get('/academy-admin/dashboard', \App\Livewire\Dashboard\AcademyAdminDashboard::class)
        ->name('academy_admin.dashboard');

    // Instructor Dashboard
    Route::get('/instructor/dashboard', \App\Livewire\Dashboard\InstructorDashboard::class)
        ->name('instructor.dashboard');

    // Mentor Dashboard
    Route::get('/mentor/dashboard', \App\Livewire\Dashboard\MentorDashboard::class)
        ->name('mentor.dashboard');

    // Content Editor Dashboard
    Route::get('/content-editor/dashboard', \App\Livewire\Dashboard\ContentEditorDashboard::class)
        ->name('content_editor.dashboard');

    // Affiliate/Ambassador Dashboard
    Route::get('/affiliate-ambassador/dashboard', \App\Livewire\Dashboard\AffiliateAmbassadorDashboard::class)
        ->name('affiliate_ambassador.dashboard');

    // Student Dashboard (Default dashboard for general users)
    Route::get('/student/dashboard', \App\Livewire\Dashboard\StudentDashboard::class)
        ->name('student.dashboard');

    // Fallback dashboard
    Route::get('/dashboard', [DashboardController::class, '__invoke'])
        ->name('dashboard');
});

require __DIR__ . '/auth.php';