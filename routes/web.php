<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Livewire\Home;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Models\User;
use App\Livewire\Component\ProfileManagement; 

// Use specific Auth controllers directly in route definitions to avoid conflicts
// use App\Http\Controllers\Auth\VerifyEmailController; // REMOVE THIS LINE
// use App\Http\Controllers\Auth\EmailVerificationNotificationController; // REMOVE THIS LINE



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

require __DIR__.'/auth.php';