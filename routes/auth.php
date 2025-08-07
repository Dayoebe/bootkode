<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\EmailVerificationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\SocialAuthController; 

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