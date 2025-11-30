<?php

use Illuminate\Support\Facades\Route;
use Ifsnop\Mysqldump\Mysqldump;
use App\Http\Controllers\PageController;
use App\Http\Controllers\CertificateVerificationController;
use App\Livewire\Affiliate;
use App\Livewire\Content\ContentDocumentationCenter;
use App\Livewire\Cbt\CbtExamInterface;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::middleware(['auth', 'verified'])->prefix('dashboard')->name('newsletter.')->group(function () {
    // Newsletter management routes (requires proper role)
    Route::get('/newsletter', App\Livewire\Newsletter\NewsletterCenter::class)->name('index');
    Route::get('/newsletter/subscribers', App\Livewire\Newsletter\NewsletterCenter::class)->name('subscribers');
    Route::get('/newsletter/campaigns', App\Livewire\Newsletter\NewsletterCenter::class)->name('campaigns');
    Route::get('/newsletter/reports', App\Livewire\Newsletter\NewsletterCenter::class)->name('reports');
    Route::get('/newsletter/performance', App\Livewire\Newsletter\NewsletterCenter::class)->name('performance'); // NEW
    Route::get('/newsletter/templates', App\Livewire\Newsletter\NewsletterCenter::class)->name('templates');
    Route::get('/newsletter/analytics', App\Livewire\Newsletter\NewsletterCenter::class)->name('analytics');
    Route::get('/newsletter/settings', App\Livewire\Newsletter\NewsletterCenter::class)->name('settings');
});

// Public newsletter routes (no auth required)
Route::prefix('newsletter')->name('newsletter.')->group(function () {
    // Subscription endpoints
    Route::post('/subscribe', [App\Http\Controllers\NewsletterController::class, 'subscribe'])->name('subscribe');
    // Tracking endpoints
    Route::get('/track/open/{token}', [App\Http\Controllers\NewsletterController::class, 'trackOpen'])->name('track-open');
    Route::get('/track/click/{token}', [App\Http\Controllers\NewsletterController::class, 'trackClick'])->name('track-click');
    // Unsubscribe endpoints
    Route::get('/unsubscribe/{token}', [App\Http\Controllers\NewsletterController::class, 'unsubscribe'])->name('unsubscribe');
    Route::post('/resubscribe/{token}', [App\Http\Controllers\NewsletterController::class, 'resubscribe'])->name('resubscribe');
});


// Affiliate Routes
Route::middleware(['auth'])->prefix('affiliate')->name('affiliate.')->group(function () {
    Route::get('/dashboard', \App\Livewire\Affiliate\Dashboard::class)->name('dashboard');
    Route::get('/tools', \App\Livewire\Affiliate\Tools::class)->name('tools');
    Route::get('/commissions', \App\Livewire\Affiliate\CommissionHistory::class)->name('commissions');
    Route::get('/reports', \App\Livewire\Affiliate\CommissionReports::class)->name('reports');
    Route::get('/analytics', \App\Livewire\Affiliate\Analytics::class)->name('analytics');
    Route::get('/settings', \App\Livewire\Affiliate\Settings::class)->name('settings');
    Route::get('/apply', \App\Livewire\Affiliate\Apply::class)->name('apply');
    Route::get('/not-eligible', \App\Livewire\Affiliate\NotEligible::class)->name('not-eligible');

    // API endpoint for referral code validation
    Route::get('/api/validate-referral/{code}', function ($code) {
        $service = app(\App\Services\AffiliateService::class);
        return response()->json($service->validateReferralCode($code));
    })->name('api.validate-referral');
});


// User Wallet Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/wallet', App\Livewire\Financial\WalletDashboard::class)->name('wallet.index');
    Route::get('/withdrawals', App\Livewire\Financial\WithdrawalManager::class)->name('withdrawals.index');
    Route::get('/instructor/earnings', App\Livewire\Financial\InstructorEarnings::class)->name('instructor.earnings');
    Route::get('/transactions/history', App\Livewire\Financial\TransactionHistory::class)->name('transactions.history');
    Route::get('/courses/{course}/checkout', App\Livewire\Financial\CourseCheckout::class)->name('course.checkout');
    Route::get('/paystack/callback', [App\Http\Controllers\FinancialController::class, 'paystackCallback'])->name('paystack.callback');
    Route::get('/api/banks', [App\Http\Controllers\FinancialController::class, 'getBanks'])->name('api.banks');
    Route::post('/api/resolve-account', [App\Http\Controllers\FinancialController::class, 'resolveAccount'])->name('api.resolve-account');
    Route::get('/admin/financial', App\Livewire\Financial\Admin\FinancialDashboard::class)->name('admin.financial.dashboard');
    Route::get('/admin/payments/processing', App\Livewire\Financial\Admin\PaymentProcessing::class)->name('admin.payments.processing');
    Route::get('/admin/revenue/reports', App\Livewire\Financial\Admin\RevenueReports::class)->name('admin.revenue.reports');
    Route::get('/admin/financial/settings', App\Livewire\Financial\Admin\FinancialSettings::class)->name('admin.financial.settings');
    Route::get('/admin/paystack/settings', App\Livewire\Financial\Admin\PaystackSettings::class)->name('admin.paystack.settings');
    Route::get('/admin/payments', \App\Livewire\Financial\Admin\PaymentProcessing::class)->name('admin.payments');
    Route::post('/webhook/paystack', [App\Http\Controllers\FinancialController::class, 'paystackWebhook'])->name('paystack.webhook');
});


// Institution Portal Routes
Route::prefix('institution')->name('institution.')->group(function () {

    // Main portal dashboard
    Route::get('/portal', App\Livewire\Institution\InstitutionPortal::class)->name('portal');
    Route::get('/overview', App\Livewire\Institution\InstitutionPortal::class)->name('overview');
    Route::get('/partners', App\Livewire\Institution\InstitutionPortal::class)->name('partners');
    Route::get('/licenses', App\Livewire\Institution\InstitutionPortal::class)->name('licenses');
    Route::get('/bulk-enrollment', App\Livewire\Institution\InstitutionPortal::class)->name('bulk-enrollment');
    Route::get('/analytics', App\Livewire\Institution\InstitutionPortal::class)->name('analytics');
    Route::get('/whitelabel', App\Livewire\Institution\InstitutionPortal::class)->name('whitelabel');
});


// Blog Routes
Route::prefix('blog')->name('blog.')->group(function () {
    Route::get('/', \App\Livewire\Blog\PublicBlogIndex::class)->name('index');
    Route::get('/category/{category:slug}', \App\Livewire\Blog\PublicBlogIndex::class)->name('category');
    Route::get('/tag/{tag}', \App\Livewire\Blog\PublicBlogIndex::class)->name('tag');
    Route::get('/search', \App\Livewire\Blog\PublicBlogIndex::class)->name('search');
    Route::get('/{post:slug}', \App\Livewire\Blog\PublicBlogPost::class)->name('show');
});

// Admin Blog Routes (requires authentication and proper roles)
Route::middleware(['auth', 'verified'])->prefix('admin/blog')->name('admin.blog.')->group(function () {
    Route::get('/posts', \App\Livewire\Blog\AdminBlogPosts::class)->name('posts.index');
    Route::get('/posts/create', \App\Livewire\Blog\AdminBlogPostForm::class)->name('posts.create');
    Route::get('/posts/{post:slug}/edit', \App\Livewire\Blog\AdminBlogPostForm::class)->name('posts.edit');
    Route::get('/categories', \App\Livewire\Blog\AdminBlogCategories::class)->name('categories.index');
    Route::get('/comments', \App\Livewire\Blog\AdminBlogComments::class)->name('comments.index');
    Route::get('/settings', \App\Livewire\Blog\AdminBlogSettings::class)->name('settings');
    Route::get('/seo', \App\Livewire\Blog\AdminBlogSettings::class)->name('seo');
});

// API Routes for AJAX operations
Route::middleware('api')->prefix('api/blog')->name('api.blog.')->group(function () {
    Route::post('/posts/{post}/react', [\App\Http\Controllers\BlogController::class, 'toggleReaction'])->name('react');
    Route::post('/posts/{post}/view', [\App\Http\Controllers\BlogController::class, 'incrementView'])->name('view');
    Route::post('/comments/{comment}/react', [\App\Http\Controllers\BlogController::class, 'toggleCommentReaction'])->name('comment.react');
    Route::post('/upload-image', [\App\Http\Controllers\BlogController::class, 'uploadImage'])->name('upload.image');
});


// =============================================================================
// PUBLIC PAGES (No Authentication Required) - MANUAL PAGES
// =============================================================================
Route::get('/About', \App\Livewire\ManualPages\AboutUs::class)->name('about');
Route::get('/Contact', \App\Livewire\ManualPages\ContactUs::class)->name('contact');
Route::get('/Statistics', \App\Livewire\ManualPages\Statistics::class)->name('statistics');
Route::get('/Guideline', \App\Livewire\ManualPages\Guideline::class)->name('guideline');


Route::middleware(['auth'])->group(function () {
    Route::get('/content', ContentDocumentationCenter::class)->name('content.index');
    Route::get('/content/learning-materials', ContentDocumentationCenter::class)->name('content.learning-materials');
    Route::get('/content/video-library', ContentDocumentationCenter::class)->name('content.video-library');
    Route::get('/content/documentation', ContentDocumentationCenter::class)->name('content.documentation');
    Route::get('/content/all-documents', ContentDocumentationCenter::class)->name('content.all-documents');
    Route::get('/content/moderation', ContentDocumentationCenter::class)->name('content.moderation');
    Route::get('/content/create-document', ContentDocumentationCenter::class)->name('content.create-document');
    Route::get('/content/reviews', ContentDocumentationCenter::class)->name('content.reviews');
    Route::get('/content/localization', ContentDocumentationCenter::class)->name('content.localization');
    Route::get('/content/categories', ContentDocumentationCenter::class)->name('content.categories');
    Route::get('/content/settings', ContentDocumentationCenter::class)->name('content.settings');
    Route::get('/content/reports', ContentDocumentationCenter::class)->name('content.reports');
    Route::get('/content/analytics', ContentDocumentationCenter::class)->name('content.analytics');
    Route::get('/content/overview', ContentDocumentationCenter::class)->name('content.overview');
    Route::get('/content/feedback', ContentDocumentationCenter::class)->name('content.feedback');
    Route::get('/content/archives', ContentDocumentationCenter::class)->name('content.archives');
    Route::get('/content/faq', ContentDocumentationCenter::class)->name('content.faq');
    Route::get('/content/notifications', ContentDocumentationCenter::class)->name('content.notifications');
    Route::get('/content/updates', ContentDocumentationCenter::class)->name('content.updates');
});

// Community Routes
Route::middleware(['auth'])->prefix('community')->name('community.')->group(function () {
    Route::get('/', App\Livewire\Community\CommunityCenter::class)->name('center');
    Route::get('/forums', App\Livewire\Community\CommunityCenter::class)->name('forums');
    Route::get('/study-groups', App\Livewire\Community\CommunityCenter::class)->name('study-groups');
    Route::get('/code-challenges', App\Livewire\Community\CommunityCenter::class)->name('code-challenges');
    Route::get('/live-events', App\Livewire\Community\CommunityCenter::class)->name('live-events');
    Route::get('/feedback', App\Livewire\Community\CommunityCenter::class)->name('feedback');
    Route::get('/moderation', App\Livewire\Community\CommunityCenter::class)->name('moderation');
});

// Gamification routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::prefix('gamification')->name('gamification.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\GamificationController::class, 'dashboard'])->name('dashboard');
        Route::get('/badges', [\App\Http\Controllers\GamificationController::class, 'badges'])->name('badges');
        Route::get('/leaderboard', [\App\Http\Controllers\GamificationController::class, 'leaderboard'])->name('leaderboard');
        Route::get('/store', [\App\Http\Controllers\GamificationController::class, 'store'])->name('store');
        Route::get('/games', [\App\Http\Controllers\GamificationController::class, 'games'])->name('games');
        Route::get('/games/{gameId}', [\App\Http\Controllers\GamificationController::class, 'playGame'])->name('games.play');
        Route::post('/store/purchase/{itemId}', [\App\Http\Controllers\GamificationController::class, 'purchaseItem'])->name('store.purchase');
        Route::post('/items/{purchaseId}/toggle-equip', [\App\Http\Controllers\GamificationController::class, 'toggleEquip'])->name('items.toggle-equip');
    });
});

// CBT Routes - Removed role restrictions
Route::get('/cbt/management', \App\Livewire\Cbt\CbtManagement::class)->name('cbt.management');
Route::get('/cbt/results', \App\Livewire\Cbt\CbtViewer::class)->name('cbt.viewer');
Route::middleware(['auth', 'verified'])->group(function () {
    // CBT Exam Routes
    Route::get('/cbt/exams', App\Livewire\Cbt\CbtExamSelection::class)->name('cbt.exams');
    Route::get('/cbt/exam/{assessment}/take', App\Livewire\Cbt\CbtExamInterface::class)->name('cbt.exam.take');
    Route::get('/cbt/exam/{assessmentId}/summary', [CbtExamInterface::class, 'summary'])
    ->name('cbt.exam.summary');

    // /exam/{assessmentId}
    // Legacy route for backward compatibility
    Route::get('/cbt/exam/{assessmentId?}', App\Livewire\Cbt\CbtExam::class)->name('cbt.exam');

    // CBT Management (now available to all authenticated users)
    Route::get('/cbt/manage', App\Livewire\Cbt\CbtManagement::class)->name('cbt.manage');
});


// // Replace your existing mentorship routes with:
Route::middleware(['auth', 'verified'])->prefix('mentorship')->name('mentorship.')->group(function () {
    Route::get('/', \App\Livewire\Mentorship\MentorshipHub::class)->name('hub');
    Route::get('/find', \App\Livewire\Mentorship\FindMentor::class)->name('find');
    Route::get('/my-mentorships', \App\Livewire\Mentorship\MyMentorships::class)->name('my-mentorships');
    Route::get('/sessions', \App\Livewire\Mentorship\Sessions::class)->name('sessions');
    Route::get('/code-reviews', \App\Livewire\Mentorship\CodeReviews::class)->name('code-reviews');

    Route::middleware(['auth', 'verified'])->group(function () {
        Route::get('/dashboard', \App\Livewire\Mentorship\MentorDashboard::class)->name('dashboard');
        Route::get('/profile', \App\Livewire\Mentorship\MentorProfile::class)->name('profile');
        Route::get('/resources', \App\Livewire\Mentorship\MentorResources::class)->name('resources');
    });

    Route::middleware(['auth', 'verified'])->group(function () {
        Route::get('/management', \App\Livewire\Mentorship\MentorManagement::class)->name('management');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/search/job', \App\Livewire\Career\JobSearch::class)->name('search.job');
    Route::get('/portfolio', \App\Livewire\Career\PortfolioBuilder::class)->name('portfolio.show');
    Route::get('/resume/builder', \App\Livewire\Career\ResumeBuilder::class)->name('resume.builder');
    Route::get('/job', \App\Livewire\Career\JobPortal::class)->name('user.job');
    Route::get('/admin/job', \App\Livewire\Career\JobManagement::class)->name('admin.job');
});
Route::middleware('auth')->group(function () {
    Route::get('/interview/user', \App\Livewire\Career\UserMockInterview::class)->name('user.interview');
    Route::get('/admin/interview', \App\Livewire\Career\AdminMockInterview::class)->name('admin.interview');
    // Route::get('/admin/interview/questions', \App\Livewire\Career\AdminQuestionBank::class)->name('admin.interview.questions');
    // Route::get('/admin/interview/question-sets', \App\Livewire\Career\AdminQuestionSets::class)->name('admin.interview.question-sets');
    Route::get('/interview/{interview}/take', \App\Livewire\Career\StudentInterviewTaker::class)->name('interview.take');
    Route::get('/interview/{interview}/results', \App\Livewire\Career\InterviewResults::class)->name('interview.results');
});
// =============================================================================
// AUTHENTICATED ROUTES - DASHBOARD
// =============================================================================

Route::get('/dashboard', \App\Livewire\Component\DashboardOverview::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// =============================================================================
// USER PROFILE & MANAGEMENT ROUTES
// =============================================================================

Route::middleware(['auth', 'verified'])->prefix('dashboard')->name('')->group(function () {
    Route::get('/profile', \App\Livewire\UserManagement\Profile::class)->name('profile.view');
    Route::get('/profile/edit', function () {
        return redirect()->route('profile.view', ['mode' => 'edit']);
    })->name('profile.edit');
    Route::get('/learning-analytics', \App\Livewire\Dashboard\LearningAnalyticsDashboard::class)->name('learning.analytics');
    // User Management - Now available to all authenticated users
    Route::get('/all-users', \App\Livewire\UserManagement\AllUser::class)->name('all-users');
    Route::get('/roles-permissions', \App\Livewire\UserManagement\RolesPermissions::class)->name('roles-permissions');
    Route::get('/pending-verifications', \App\Livewire\UserManagement\PendingVerifications::class)->name('pending-verifications');
    Route::get('/user', \App\Livewire\UserManagement\UserManagement::class)->name('user-management');
    Route::get('/user-activity', \App\Livewire\UserManagement\UserActivity::class)->name('user.activity');
});

// =============================================================================
// STUDENT ROUTES
// =============================================================================

Route::middleware(['auth', 'verified'])->group(function () {
    // Student Dashboard Features
    Route::get('/enrolled-courses', \App\Livewire\StudentManagement\EnrolledCourses::class)->name('student.enrolled-courses');
    Route::get('/course-catalog', \App\Livewire\StudentManagement\CourseCatalog::class)->name('student.course-catalog');
    Route::get('/learning-analytics', \App\Livewire\StudentManagement\LearningAnalytics::class)->name('student.learning-analytics');
    Route::get('/saved-resources', \App\Livewire\StudentManagement\SavedResources::class)->name('student.saved-resources');
    Route::get('/course/{course:slug}', \App\Livewire\StudentManagement\CourseView::class)->name('course.view');
    Route::get('/course-reviews/analytics/{courseId?}', \App\Livewire\CourseManagement\ReviewAnalytics::class)->name('review-analytics');
    // Route::post('/student/course/{course}/review', [\App\Livewire\StudentManagement\CourseView::class, 'submitReview'])->name('student.course.review');
});
// Review reminder unsubscribe routes
Route::get('/review-reminder/unsubscribe/{reminder}', function (App\Models\ReviewReminder $reminder) {
    if (!request()->hasValidSignature()) {
        abort(403, 'Invalid or expired link');
    }

    app(\App\Services\ReviewReminderService::class)->unsubscribeFromCourse($reminder);

    return view('review-reminders.unsubscribed', ['course' => $reminder->course]);
})->name('review-reminder.unsubscribe');

Route::get('/review-reminders/unsubscribe-all', function () {
    if (!auth()->check()) {
        return redirect()->route('login');
    }

    app(\App\Services\ReviewReminderService::class)->unsubscribeFromAll(auth()->user());

    return view('review-reminders.unsubscribed-all');
})->middleware('auth')->name('review-reminders.unsubscribe-all');


// =============================================================================
// COURSE MANAGEMENT ROUTES
// =============================================================================

Route::middleware(['auth', 'verified'])->group(function () {
    // Course Management
    Route::get('/course-management/all-courses', \App\Livewire\CourseManagement\AllCourses::class)->name('all-course');
    Route::get('/course-management/my-courses', \App\Livewire\CourseManagement\UserCourses::class)->name('my-course');
    Route::get('/course-categories', \App\Livewire\CourseManagement\CourseCategories::class)->name('course-categories');
});

// Course CRUD Routes
Route::middleware(['auth', 'verified'])->prefix('dashboard/courses')->name('')->group(function () {
    Route::get('/create', \App\Livewire\CourseManagement\CourseForm::class)->name('create.course');
    Route::get('/{course}/edit', \App\Livewire\CourseManagement\EditCourse::class)->name('edit_course');
    Route::get('/{course}/builder', \App\Livewire\CourseManagement\CourseBuilder::class)->name('course-builder');
    Route::get('/reviews', \App\Livewire\CourseManagement\CourseReviews::class)->name('course-reviews');
    Route::get('/{course}/preview', \App\Livewire\CourseManagement\CoursePreview::class)->name('courses.preview');
    Route::get('/approvals', \App\Livewire\CourseManagement\CourseApprovals::class)->name('course-approvals');
    Route::get('/available', \App\Livewire\CourseManagement\AvailableCourses::class)->name('courses.available');
});

// Project Routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/projects/{slug}', [App\Http\Controllers\ProjectController::class, 'show'])->name('project.show');
});


// =============================================================================
// SYSTEM MANAGEMENT ROUTES
// =============================================================================

Route::middleware(['auth', 'verified'])->prefix('dashboard')->name('')->group(function () {
    // Settings & Configuration
    Route::get('/settings', \App\Livewire\SystemManagement\Settings::class)->name('settings');
    Route::get('/notifications', \App\Livewire\SystemManagement\Notifications::class)->name('notifications');
    Route::get('/system-status', \App\Livewire\SystemManagement\SystemStatus::class)->name('system-status');
    Route::get('/system-status-management', \App\Livewire\SystemManagement\SystemStatusManagement::class)->name('system-status.management');

    // Support System
    Route::get('/help-support', \App\Livewire\SystemManagement\HelpSupport::class)->name('help.support');
    Route::get('/support-tickets', \App\Livewire\SystemManagement\SupportTicketManagement::class)->name('support.tickets');
    Route::get('/faq-management', \App\Livewire\SystemManagement\FaqManagement::class)->name('faq.management');

    // Feedback System
    Route::get('/feedback', \App\Livewire\SystemManagement\Feedback::class)->name('feedback');
    Route::get('/feedback-management', \App\Livewire\SystemManagement\FeedbackManagement::class)->name('feedback.management');

    // Announcements
    Route::get('/announcements', \App\Livewire\SystemManagement\Announcements::class)->name('announcements');
    Route::get('/announcement-management', \App\Livewire\SystemManagement\AnnouncementManagement::class)->name('announcement.management');

    Route::get('/notification-preferences', \App\Livewire\SystemManagement\NotificationPreferences::class)->name('notification.preferences');
    Route::get('/privacy-settings', \App\Livewire\SystemManagement\PrivacySettings::class)->name('privacy.settings');
    Route::get('/language-localization', \App\Livewire\SystemManagement\LanguageLocalization::class)->name('language.localization');
});

// =============================================================================
// MARKETPLACE ROUTES
// =============================================================================
Route::get('/marketplace', App\Livewire\Marketplace\PublicMarketplace::class)->name('marketplace.browse');
Route::get('/marketplace/categories', App\Livewire\Marketplace\PublicMarketplace::class)->name('marketplace.categories');
Route::get('/marketplace/product/{slug}', function ($slug) {
    return app(App\Livewire\Marketplace\PublicMarketplace::class, ['slug' => $slug, 'type' => 'product']);
})->name('marketplace.product.show');
Route::get('/marketplace/category/{slug}', function ($slug) {
    return app(App\Livewire\Marketplace\PublicMarketplace::class, ['slug' => $slug, 'type' => 'category']);
})->name('marketplace.category.show');
Route::get('/marketplace/instructor/{id}', function ($id) {
    return app(App\Livewire\Marketplace\PublicMarketplace::class, ['slug' => $id, 'type' => 'vendor']);
})->name('marketplace.instructor.show');

// AUTHENTICATED ROUTES - Dashboard Access
Route::middleware(['auth', 'verified'])->group(function () {
    // GENERAL USER ROUTES (Students, Everyone)
    Route::get('/marketplace/cart', App\Livewire\Marketplace\MarketplaceCenter::class)->name('marketplace.cart');
    Route::get('/marketplace/checkout', App\Livewire\Marketplace\MarketplaceCenter::class)->name('marketplace.checkout');
    Route::get('/marketplace/my-purchases', App\Livewire\Marketplace\MarketplaceCenter::class)->name('marketplace.purchases');
    Route::get('/marketplace/reviews', App\Livewire\Marketplace\MarketplaceCenter::class)->name('marketplace.reviews');
    Route::get('/marketplace/item/{slug}', App\Livewire\Marketplace\MarketplaceCenter::class)->name('marketplace.item.public');

    // VENDOR ROUTES (Instructors, Academy Admin, Super Admin)
    Route::get('/marketplace/sell', App\Livewire\Marketplace\MarketplaceCenter::class)->name('marketplace.sell');
    Route::get('/marketplace/seller/create', App\Livewire\Marketplace\MarketplaceCenter::class)->name('marketplace.seller.create');
    Route::get('/marketplace/my-listings', App\Livewire\Marketplace\MarketplaceCenter::class)->name('marketplace.seller.listings');
    Route::get('/marketplace/drafts', App\Livewire\Marketplace\MarketplaceCenter::class)->name('marketplace.seller.drafts');
    Route::get('/marketplace/vendor/dashboard', App\Livewire\Marketplace\MarketplaceCenter::class)->name('marketplace.vendor.dashboard');
    Route::get('/marketplace/vendor/orders', App\Livewire\Marketplace\MarketplaceCenter::class)->name('marketplace.vendor.orders');
    Route::get('/marketplace/vendor/withdrawals', App\Livewire\Marketplace\MarketplaceCenter::class)->name('marketplace.vendor.withdrawals');

    // ADMIN ROUTES (Academy Admin, Super Admin)
    Route::get('/marketplace/admin/vendors', App\Livewire\Marketplace\MarketplaceCenter::class)->name('marketplace.vendor.applications');
    Route::get('/marketplace/admin/orders', App\Livewire\Marketplace\MarketplaceCenter::class)->name('marketplace.orders');
    Route::get('/marketplace/admin/payments', App\Livewire\Marketplace\MarketplaceCenter::class)->name('marketplace.payments');
    Route::get('/marketplace/admin/analytics', App\Livewire\Marketplace\MarketplaceCenter::class)->name('marketplace.analytics');
    Route::get('/marketplace/admin/settings', App\Livewire\Marketplace\MarketplaceCenter::class)->name('marketplace.settings');

    // CONTENT EDITOR + ADMIN ROUTES
    Route::get('/marketplace/promotions', App\Livewire\Marketplace\MarketplaceCenter::class)->name('marketplace.promotions');
});

// PAYMENT CALLBACKS (Public, no auth)
// Route::post('/marketplace/payment/callback', [App\Http\Controllers\PaymentController::class, 'marketplaceCallback'])->name('marketplace.payment.callback');
Route::get('/marketplace/payment/success', function () {
    return redirect()->route('marketplace.purchases')->with('success', 'Payment completed successfully!');
})->name('marketplace.payment.success');
Route::get('/marketplace/payment/failed', function () {
    return redirect()->route('marketplace.checkout')->with('error', 'Payment failed. Please try again.');
})->name('marketplace.payment.failed');


// =============================================================================
// PUBLIC CERTIFICATE VERIFICATION ROUTES (No Authentication Required)
// =============================================================================

Route::prefix('certificate')->name('certificate.')->group(function () {
    // Verification routes
    Route::get('/verify', [CertificateVerificationController::class, 'index'])->name('verify');
    Route::get('/verify/{verificationCode}', [CertificateVerificationController::class, 'verify'])->name('verify.code');
    Route::post('/verify', [CertificateVerificationController::class, 'verify'])->name('verify.submit');

    // Public view route (uses unified template)
    Route::get('/view/{verificationCode}', [CertificateVerificationController::class, 'show'])->name('view');

    // Download and QR code routes
    Route::get('/download/{verificationCode}', [CertificateVerificationController::class, 'download'])->name('download');
    Route::get('/qr/{verificationCode}', [CertificateVerificationController::class, 'qrCode'])->name('qr');
});

// API Routes for Certificate Verification
Route::prefix('api/certificate')->name('api.certificate.')->group(function () {
    Route::get('/verify/{verificationCode}', [CertificateVerificationController::class, 'api'])->name('verify');
    Route::post('/batch-verify', [CertificateVerificationController::class, 'batchVerify'])->name('batch.verify');
});

// =============================================================================
// AUTHENTICATED CERTIFICATE ROUTES (Students)
// =============================================================================

Route::middleware(['auth', 'verified'])->prefix('student')->name('student.')->group(function () {
    Route::get('/certificates', \App\Livewire\CertificateManagement\StudentCertificates::class)->name('certificates.index');
    Route::get('/certificate/request/{courseId?}', \App\Livewire\CertificateManagement\CertificateRequest::class)->name('certificate.request');
});

// =============================================================================
// CERTIFICATE MANAGEMENT ROUTES (Admin/Instructor)
// =============================================================================

\Illuminate\Support\Facades\Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    // Certificate Management Dashboard
    Route::get('/certificates', \App\Livewire\CertificateManagement\CertificateManagement::class)->name('certificates.manage');
    Route::get('/certificates/analytics', \App\Livewire\CertificateManagement\CertificateAnalytics::class)->name('certificates.analytics');
    Route::get('/certificates/templates', \App\Livewire\CertificateManagement\CertificateTemplates::class)->name('certificates.templates');

    // Certificate Actions
    Route::post('/certificates/{certificate}/approve', function ($certificateId) {
        $certificate = \App\Models\Credentials\Certificate::findOrFail($certificateId);

        // Permission check
        if (!auth()->user()->hasAnyRole(['super_admin', 'academy_admin', 'instructor'])) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        if (auth()->user()->hasRole('instructor') && $certificate->course->instructor_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $certificate->approve(auth()->id());
        return response()->json(['success' => true, 'message' => 'Certificate approved successfully']);
    })->name('certificates.approve');

    Route::post('/certificates/{certificate}/reject', function ($certificateId) {
        $certificate = \App\Models\Credentials\Certificate::findOrFail($certificateId);

        // Permission check
        if (!auth()->user()->hasAnyRole(['super_admin', 'academy_admin', 'instructor'])) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        if (auth()->user()->hasRole('instructor') && $certificate->course->instructor_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $reason = request()->input('reason');
        $certificate->reject($reason, auth()->id());
        return response()->json(['success' => true, 'message' => 'Certificate rejected']);
    })->name('certificates.reject');

    Route::post('/certificates/{certificate}/revoke', function ($certificateId) {
        $certificate = \App\Models\Credentials\Certificate::findOrFail($certificateId);

        // Permission check - Only admins can revoke
        if (!auth()->user()->hasAnyRole(['super_admin', 'academy_admin'])) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $reason = request()->input('reason');
        $certificate->revoke($reason, auth()->id());
        return response()->json(['success' => true, 'message' => 'Certificate revoked']);
    })->name('certificates.revoke');

    // Bulk Approve
    Route::post('/certificates/bulk-approve', function () {
        $certificateIds = request()->input('certificate_ids', []);

        if (!auth()->user()->hasAnyRole(['super_admin', 'academy_admin'])) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $approved = 0;

        foreach ($certificateIds as $id) {
            try {
                $certificate = \App\Models\Credentials\Certificate::find($id);
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
});

// =============================================================================
// AUTHENTICATION ROUTES
// =============================================================================
require __DIR__ . '/auth.php';



// Page management routes (protected)
Route::prefix('admin/pages')->middleware(['auth', 'verified'])->group(function () {
    // Main page manager with tab support
    Route::get('/', \App\Livewire\Pages\PageManager::class)->name('pages.index');
    Route::get('/create', \App\Livewire\Pages\PageManager::class)->defaults('activeTab', 'create-page')->name('pages.create');
    Route::get('/analytics', \App\Livewire\Pages\PageManager::class)->defaults('activeTab', 'analytics')->name('pages.analytics');
    Route::get('/templates', \App\Livewire\Pages\PageManager::class)->defaults('activeTab', 'templates')->name('pages.templates');
    Route::get('/media', \App\Livewire\Pages\PageManager::class)->defaults('activeTab', 'media')->name('pages.media');
    Route::get('/seo', \App\Livewire\Pages\PageManager::class)->defaults('activeTab', 'seo')->name('pages.seo');
    Route::get('/settings', \App\Livewire\Pages\PageManager::class)->defaults('activeTab', 'settings')->name('pages.settings');

    // // API endpoints
    // Route::post('/upload-media', [\App\Http\Controllers\MediaController::class, 'upload'])->name('pages.upload-media');
    // Route::delete('/media/{media}', [\App\Http\Controllers\MediaController::class, 'delete'])->name('pages.delete-media');
    Route::post('/track-view/{slug}', [PageController::class, 'trackView'])->name('pages.track-view');
});











// use Ifsnop\Mysqldump\Mysqldump;

Route::get('/export-db', function () {
    $file = storage_path('app/backup.sql');

    $dump = new Mysqldump(
        'mysql:host=' . env('DB_HOST') . ';dbname=' . env('DB_DATABASE'),
        env('DB_USERNAME'),
        env('DB_PASSWORD')
    );

    $dump->start($file);

    return response()->download($file);
});


Route::get('/test-cloudinary', function () {
    try {
        $c = new \Cloudinary\Cloudinary();
        return $c->uploadApi()->upload('https://upload.wikimedia.org/wikipedia/commons/a/ae/Olympic_flag.jpg', [
            'public_id' => 'olympic_flag_test_' . time(),
            'folder' => 'tests'
        ]);
        return "Cloudinary working!";
    } catch (\Exception $e) {
        return "Error: " . $e->getMessage();
    }
});







// =============================================================================
// CATCH-ALL ROUTE FOR PAGES (Must be absolutely last)
// =============================================================================
Route::get('/{slug}', [PageController::class, 'show'])
    ->name('page.show')
    ->where('slug', '[A-Za-z0-9\-_]+');