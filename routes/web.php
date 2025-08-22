<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CertificateController;

Route::get('/dashboard', \App\Livewire\Component\DashboardOverview::class)->name('dashboard');


//Certificate
Route::middleware(['auth', 'verified'])->prefix('certificates')->group(function () {
    Route::get('/my-certificates', \App\Livewire\Certification\MyCertificates::class)->name('certificates.index')->middleware('can:view_own_certificates');
    Route::get('/request', \App\Livewire\Certification\CertificateRequest::class)->name('certificates.request')->middleware('can:request_certificates');
    Route::get('/templates', \App\Livewire\Certification\CertificateTemplates::class)->name('certificates.templates')->middleware('can:manage_certificate_templates');
    Route::get('/certificates/verify/{uuid?}', \App\Livewire\Certification\PublicCertificateVerification::class)->name('certificates.public-verify');
    Route::get('/certificates/download/{certificate}', [CertificateController::class, 'download'])->name('certificates.download');
    Route::get('/approvals', \App\Livewire\Certification\CertificateApprovals::class)->name('certificates.approvals');
});

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
    Route::get('/dashboard/courses/create', \App\Livewire\CourseManagement\CourseForm::class)->name('create_course');
    Route::get('/dashboard/courses/{courseId}/edit', \App\Livewire\CourseManagement\CourseForm::class)->name('edit_course');
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

//CBT
Route::middleware('auth')->group(function () {
    Route::get('/cbt/exam/{examId}', \App\Livewire\Component\CBT\TakeCbtExam::class)->name('cbt.exam');
    Route::get('/cbt/results', \App\Livewire\Component\CBT\ViewCbtResults::class)->name('cbt.results');
    Route::get('/cbt/management', \App\Livewire\Component\CBT\CbtManagement::class)->name('cbt.management');
});

require __DIR__ . '/auth.php';


