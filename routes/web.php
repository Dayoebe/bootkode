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
use App\Livewire\Component\CourseManagement\EditCourse;
use App\Livewire\Component\CourseManagement\CourseBuilder\CoursePreview;
use App\Livewire\Component\UserManagement;


// Public Home Page
Route::get('/', Home::class)->name('home');

// Route for post-login redirection based on role
// This route will be the target for successful logins
Route::get('/dashboard-redirect', DashboardController::class)
    ->middleware(['auth', 'verified']) // Ensure user is authenticated and email is verified
    ->name('dashboard.redirect');


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