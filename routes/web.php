<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CertificateController;
use App\Livewire\Component\CourseManagement\AllCourses;
use App\Livewire\Component\CourseManagement\CreateCourse;
use App\Livewire\Component\CourseManagement\CourseCategories;
use App\Livewire\Component\CourseManagement\CourseBuilder;
use App\Livewire\Component\CourseManagement\CourseReviews;
use App\Livewire\Component\CourseManagement\CourseApprovals;
use App\Livewire\Component\CourseManagement\EditCourse;
use App\Livewire\Component\CourseManagement\CourseBuilder\CoursePreview;

Route::get('/dashboard', \App\Livewire\Component\DashboardOverview::class)->name('dashboard');

Route::middleware(['auth', 'verified'])->prefix('certificates')->group(function () {
    Route::get('/my-certificates', \App\Livewire\Certification\MyCertificates::class)->name('certificates.index')->middleware('can:view_own_certificates');
    Route::get('/request', \App\Livewire\Certification\CertificateRequest::class)->name('certificates.request')->middleware('can:request_certificates');
    Route::get('/templates', \App\Livewire\Certification\CertificateTemplates::class)->name('certificates.templates')->middleware('can:manage_certificate_templates');
    Route::middleware(['can:manage_certificates'])->group(function () {
        Route::get('/approvals', \App\Livewire\Certification\CertificateApprovals::class)->name('certificates.approvals');
        // Route::get('/bulk-issue', \App\Livewire\Certification\BulkCertificateIssuance::class)->name('certificates.bulk'); // Uncomment when implemented
    });
});

Route::get('/projects/{slug}', [App\Http\Controllers\ProjectController::class, 'show'])->name('project.show');



// In the auth middleware group
Route::middleware('auth')->group(function () {
    Route::get('/dashboard/profile/view', \App\Livewire\Component\Profile\ViewProfile::class)->name('profile.view');
    Route::get('/dashboard/profile/edit', \App\Livewire\Component\Profile\EditProfile::class)->name('profile.edit');
    Route::get('/dashboard/all-users', \App\Livewire\Component\User\AllUser::class)->name('all-users');
    Route::get('/dashboard/roles-permissions', \App\Livewire\Component\User\RolesPermissions::class)->name('roles-permissions');
    Route::get('/dashboard/pending-verifications', \App\Livewire\Component\User\PendingVerifications::class)->name('pending-verifications');
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
    Route::get('/dashboard/courses/available', \App\Livewire\Component\CourseManagement\AvailableCourses::class)->name('courses.available');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard/user', \App\Livewire\Component\UserManagement::class)->name('user-management');
    Route::get('/dashboard/user-activity', \App\Livewire\Component\UserActivity::class)->name('user.activity');
    Route::get('/dashboard/settings', \App\Livewire\Component\Settings::class)->name('settings');
    Route::get('/dashboard/notifications', \App\Livewire\Component\Notifications::class)->name('notifications');
    Route::get('/dashboard/help-support', \App\Livewire\Component\HelpSupport::class)->name('help.support');
    Route::get('/dashboard/support-tickets', \App\Livewire\Component\SupportTicketManagement::class)->name('support.tickets');
    Route::get('/dashboard/faq-management', \App\Livewire\Component\FaqManagement::class)->name('faq.management');
    Route::get('/dashboard/feedback', \App\Livewire\Component\Feedback::class)->name('feedback');
    Route::get('/dashboard/feedback-management', \App\Livewire\Component\FeedbackManagement::class)->name('feedback.management');
    Route::get('/dashboard/announcements', \App\Livewire\Component\Announcements::class)->name('announcements');
    Route::get('/dashboard/announcement-management', \App\Livewire\Component\AnnouncementManagement::class)->name('announcement.management');
    Route::get('/dashboard/system-status', \App\Livewire\Component\SystemStatus::class)->name('system-status');
    Route::get('/dashboard/system-status-management', \App\Livewire\Component\SystemStatusManagement::class)->name('system-status.management');

});


Route::middleware('auth')->group(function () {
    Route::get('/cbt/exam/{examId}', \App\Livewire\Component\CBT\TakeCbtExam::class)->name('cbt.exam');
    Route::get('/cbt/results', \App\Livewire\Component\CBT\ViewCbtResults::class)->name('cbt.results');
    Route::get('/cbt/management', \App\Livewire\Component\CBT\CbtManagement::class)->name('cbt.management');
});

require __DIR__ . '/auth.php';


