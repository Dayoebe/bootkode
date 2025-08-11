<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\SocialAuthController; 
use App\Livewire\Home;

// Public Home Page
Route::get('/', Home::class)->name('home');
Route::get('/dashboard-redirect', DashboardController::class)
    ->middleware(['auth', 'verified']) 
    ->name('dashboard.redirect');

Route::middleware('guest')->group(function () {


    // Add Social Login Routes inside the 'guest' middleware group
    Route::prefix('auth')->group(function () {
        // Google
        Route::get('/google', [SocialAuthController::class, 'redirectToGoogle'])->name('login.google');
        Route::get('/google/callback', [SocialAuthController::class, 'handleGoogleCallback']);
        
        // Facebook
        Route::get('/facebook', [SocialAuthController::class, 'redirectToFacebook'])->name('login.facebook');
        Route::get('/facebook/callback', [SocialAuthController::class, 'handleFacebookCallback']);
        
        // Twitter
        Route::get('/twitter', [SocialAuthController::class, 'redirectToTwitter'])->name('login.twitter');
        Route::get('/twitter/callback', [SocialAuthController::class, 'handleTwitterCallback']);
    });


    // Registration routes
    Route::get('register', [AuthController::class, 'showRegistrationForm'])
        ->name('register');

    Route::post('register', [AuthController::class, 'register']);

    // Login routes
    Route::get('login', [AuthController::class, 'showLoginForm'])
        ->name('login');

    Route::post('login', [AuthController::class, 'login']);

    // Password reset routes
    Route::get('forgot-password', [PasswordController::class, 'showForgotPasswordForm'])
        ->name('password.request');

    Route::post('forgot-password', [PasswordController::class, 'sendResetLink'])
        ->name('password.email');

    Route::get('reset-password/{token}', [PasswordController::class, 'showResetPasswordForm'])
        ->name('password.reset');

    Route::post('reset-password', [PasswordController::class, 'resetPassword'])
        ->name('password.store');
});

Route::middleware('auth')->group(function () {
    // Email verification routes
    Route::get('verify-email', [EmailVerificationController::class, 'showVerificationPrompt'])
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', [EmailVerificationController::class, 'verifyEmail'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationController::class, 'sendVerificationEmail'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    // Password confirmation routes
    Route::get('confirm-password', [AuthController::class, 'showConfirmPasswordForm'])
        ->name('password.confirm');

    Route::post('confirm-password', [AuthController::class, 'confirmPassword']);

    // Password update route
    Route::put('password', [PasswordController::class, 'updatePassword'])
        ->name('password.update');

    // Logout route
    Route::post('logout', [AuthController::class, 'logout'])
        ->name('logout');
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