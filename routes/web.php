<?php

use App\Livewire\CertificateManagement\CertificateAnalytics;
use App\Livewire\CertificateManagement\CertificateTemplates;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CertificateVerificationController;
use App\Livewire\CertificateManagement\CertificateRequest;
use App\Livewire\CertificateManagement\CertificateManagement;
use App\Http\Controllers\PageController;
use App\Livewire\Affiliate;
use App\Livewire\Content\ContentDocumentationCenter;


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



use App\Livewire\Marketplace\MarketplaceCenter;
use App\Http\Controllers\MarketplaceController;

Route::middleware(['auth', 'verified'])->group(function () {
    // Main marketplace routes (accessible to all authenticated users)
    Route::get('/marketplace', MarketplaceCenter::class)->name('marketplace.browse');
    Route::get('/marketplace/categories', MarketplaceCenter::class)->name('marketplace.categories');
    Route::get('/marketplace/product/{slug}', MarketplaceCenter::class)->name('marketplace.product.show');
    Route::get('/marketplace/cart', MarketplaceCenter::class)->name('marketplace.checkout');
    Route::get('/marketplace/reviews', MarketplaceCenter::class)->name('marketplace.reviews');
    Route::get('/marketplace/my-purchases', MarketplaceCenter::class)->name('marketplace.purchases');
    Route::post('/marketplace/cart/add/{item}', [MarketplaceController::class, 'addToCart'])->name('marketplace.cart.add');
    Route::delete('/marketplace/cart/remove/{item}', [MarketplaceController::class, 'removeFromCart'])->name('marketplace.cart.remove');
    Route::post('/marketplace/checkout', [MarketplaceController::class, 'checkout'])->name('marketplace.checkout.process');
    // Vendor routes (Instructors, Academy Admin, Super Admin)

    Route::get('/marketplace/sell', MarketplaceCenter::class)->name('marketplace.seller.create');
    Route::get('/marketplace/my-listings', MarketplaceCenter::class)->name('marketplace.seller.listings');
    Route::get('/marketplace/drafts', MarketplaceCenter::class)->name('marketplace.seller.drafts');
    Route::get('/marketplace/vendor/dashboard', MarketplaceCenter::class)->name('marketplace.vendor.dashboard');
    Route::get('/marketplace/vendor/orders', MarketplaceCenter::class)->name('marketplace.vendor.orders');
    Route::get('/marketplace/vendor/withdrawals', MarketplaceCenter::class)->name('marketplace.vendor.withdrawals');

    // Vendor API routes
    Route::post('/marketplace/items', [MarketplaceController::class, 'store'])->name('marketplace.items.store');
    Route::patch('/marketplace/items/{item}', [MarketplaceController::class, 'update'])->name('marketplace.items.update');
    Route::delete('/marketplace/items/{item}', [MarketplaceController::class, 'destroy'])->name('marketplace.items.destroy');
    Route::post('/marketplace/items/{item}/submit', [MarketplaceController::class, 'submitForReview'])->name('marketplace.items.submit');
    Route::patch('/marketplace/orders/{order}/fulfill', [MarketplaceController::class, 'fulfillOrder'])->name('marketplace.orders.fulfill');

    // Admin routes (Academy Admin, Super Admin)
    Route::get('/marketplace/admin/vendors', MarketplaceCenter::class)->name('marketplace.vendor.applications');
    Route::get('/marketplace/admin/orders', MarketplaceCenter::class)->name('marketplace.orders');
    Route::get('/marketplace/admin/payments', MarketplaceCenter::class)->name('marketplace.payments');
    Route::get('/marketplace/admin/analytics', MarketplaceCenter::class)->name('marketplace.analytics');
    Route::get('/marketplace/admin/settings', MarketplaceCenter::class)->name('marketplace.settings');

    // Admin API routes
    Route::post('/marketplace/items/{item}/approve', [MarketplaceController::class, 'approve'])->name('marketplace.items.approve');
    Route::post('/marketplace/items/{item}/reject', [MarketplaceController::class, 'reject'])->name('marketplace.items.reject');
    Route::post('/marketplace/items/{item}/suspend', [MarketplaceController::class, 'suspend'])->name('marketplace.items.suspend');
    Route::post('/marketplace/orders/{order}/refund', [MarketplaceController::class, 'refund'])->name('marketplace.orders.refund');
    Route::post('/marketplace/vendors/{user}/approve', [MarketplaceController::class, 'approveVendor'])->name('marketplace.vendors.approve');

    // Content Editor + Admin routes

    Route::get('/marketplace/promotions', MarketplaceCenter::class)->name('marketplace.promotions');

    // Common API routes (for authenticated users)
    Route::post('/marketplace/items/{item}/view', [MarketplaceController::class, 'incrementViews'])->name('marketplace.items.view');
});
// Public routes (no auth required)
Route::prefix('marketplace')->group(function () {
    Route::get('/item/{slug}', [MarketplaceController::class, 'show'])->name('marketplace.item.public');
    Route::get('/category/{category}', [MarketplaceController::class, 'category'])->name('marketplace.category.public');
    Route::get('/vendor/{vendor}', [MarketplaceController::class, 'vendor'])->name('marketplace.vendor.public');
});

// Payment callback routes (public, no auth)
Route::post('/marketplace/payment/callback', [MarketplaceController::class, 'paymentCallback'])->name('marketplace.payment.callback');
Route::get('/marketplace/payment/success', [MarketplaceController::class, 'paymentSuccess'])->name('marketplace.payment.success');
Route::get('/marketplace/payment/failed', [MarketplaceController::class, 'paymentFailed'])->name('marketplace.payment.failed');













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

    // API endpoints
    Route::post('/upload-media', [\App\Http\Controllers\MediaController::class, 'upload'])->name('pages.upload-media');
    Route::delete('/media/{media}', [\App\Http\Controllers\MediaController::class, 'delete'])->name('pages.delete-media');
    Route::post('/track-view/{slug}', [PageController::class, 'trackView'])->name('pages.track-view');
});





















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























//Affiliate Routes
Route::middleware(['auth'])->group(function () {
    // Affiliate routes
    Route::get('/affiliate/dashboard', Affiliate\Dashboard::class)->name('affiliate.dashboard');
    Route::get('/affiliate/tools', Affiliate\Tools::class)->name('affiliate.tools');
    Route::get('/affiliate/commissions', Affiliate\CommissionHistory::class)->name('affiliate.commissions');
    Route::get('/affiliate/reports', Affiliate\CommissionReports::class)->name('affiliate.reports');
    Route::get('/affiliate/analytics', Affiliate\Analytics::class)->name('affiliate.analytics');
    Route::get('/affiliate/settings', Affiliate\Settings::class)->name('affiliate.settings');

    // API endpoint for referral code validation
    Route::get('/api/validate-referral/{code}', function ($code) {
        $service = app(\App\Services\AffiliateService::class);
        return response()->json($service->validateReferralCode($code));
    })->name('api.validate-referral');
});

// Public registration route with referral support
Route::get('/register', function () {
    $referralCode = request('ref');
    $validation = null;

    if ($referralCode) {
        $service = app(\App\Services\AffiliateService::class);
        $validation = $service->validateReferralCode($referralCode);
    }

    return view('auth.register', compact('referralCode', 'validation'));
})->name('register');



























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

    // Legacy route for backward compatibility
    Route::get('/cbt/exam/{assessmentId?}', App\Livewire\Cbt\CbtExam::class)->name('cbt.exam');

    // CBT Management (now available to all authenticated users)
    Route::get('/cbt/manage', App\Livewire\Cbt\CbtManagement::class)->name('cbt.manage');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/mentorship', \App\Livewire\Mentorship\MentorshipHub::class)->name('mentorship.hub');
    Route::get('/mentorship.dashboard', \App\Livewire\Mentorship\MentorDashboard::class)->name('mentorship.dashboard');
    Route::get('/mentorship/actions', \App\Livewire\Mentorship\MentorshipActions::class)->name('mentorship.actions');
});


Route::middleware('auth')->group(function () {
    Route::get('/search/job', \App\Livewire\Career\JobSearch::class)->name('search.job');
    Route::get('/portfolio', \App\Livewire\Career\PortfolioBuilder::class)->name('portfolio.show');
    Route::get('/resume/builder', \App\Livewire\Career\ResumeBuilder::class)->name('resume.builder');
    Route::get('/interview/user', \App\Livewire\Career\UserMockInterview::class)->name('user.interview');
    Route::get('/admin/interview', \App\Livewire\Career\AdminMockInterview::class)->name('admin.interview');
    Route::get('/job', \App\Livewire\Career\JobPortal::class)->name('user.job');
    Route::get('/admin/job', \App\Livewire\Career\JobManagement::class)->name('admin.job');
});

// =============================================================================
// PUBLIC ROUTES (No Authentication Required)
// =============================================================================

// Public Certificate Verification Routes
Route::prefix('certificate')->name('certificate.')->group(function () {
    Route::get('/verify', [CertificateVerificationController::class, 'index'])->name('verify');
    Route::get('/verify/{verificationCode}', [CertificateVerificationController::class, 'verify'])->name('verify.code');
    Route::post('/verify', [CertificateVerificationController::class, 'verify'])->name('verify.submit');
    Route::get('/view/{verificationCode}', [CertificateVerificationController::class, 'show'])->name('view');
    Route::get('/download/{verificationCode}', [CertificateVerificationController::class, 'download'])->name('download');
    Route::get('/qr/{verificationCode}', [CertificateVerificationController::class, 'qrCode'])->name('qr');
    Route::get('/widget/{verificationCode}', [CertificateVerificationController::class, 'widget'])->name('widget');
});

// API Routes for Certificate Verification
Route::prefix('api/certificate')->name('api.certificate.')->group(function () {
    Route::get('/verify/{verificationCode}', [CertificateVerificationController::class, 'api'])->name('verify');
    Route::post('/batch-verify', [CertificateVerificationController::class, 'batchVerify'])->name('batch.verify');
});

// Webhook Routes
Route::prefix('webhooks/certificates')->name('webhooks.certificates.')->group(function () {
    Route::post('/verify', function () {
        $verificationCode = request()->input('verification_code');
        if (!$verificationCode) {
            return response()->json(['error' => 'Verification code required'], 400);
        }
        $certificate = \App\Models\Certificate::findByVerificationCode($verificationCode);
        if (!$certificate) {
            return response()->json(['valid' => false, 'error' => 'Certificate not found'], 404);
        }
        return response()->json($certificate->getVerificationData());
    })->name('webhook.verify');
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
    Route::get('/offline-learning', \App\Livewire\StudentManagement\OfflineLearning::class)->name('student.offline-learning');
    Route::get('/course/{course:slug}', \App\Livewire\StudentManagement\CourseView::class)->name('course.view');
});

// Student Certificate Routes
Route::middleware(['auth', 'verified'])->prefix('student')->name('student.')->group(function () {
    Route::get('/certificates', \App\Livewire\CertificateManagement\StudentCertificates::class)->name('certificates.index');
    Route::get('/certificate/request/{courseId?}', CertificateRequest::class)->name('certificate.request');
    Route::get('/certificate/report/{verificationCode}', [CertificateVerificationController::class, 'report'])->name('certificate.report');
});

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
    Route::get('/create', \App\Livewire\CourseManagement\CourseForm::class)->name('create_course');
    Route::get('/{courseId}/edit', \App\Livewire\CourseManagement\CourseForm::class)->name('edit_course');
    Route::get('/{course}/builder', \App\Livewire\CourseManagement\CourseBuilder::class)->name('course-builder');
    Route::get('/reviews', \App\Livewire\CourseManagement\CourseReviews::class)->name('course-reviews');
    Route::get('/approvals', \App\Livewire\CourseManagement\CourseApprovals::class)->name('course-approvals');
    Route::get('/available', \App\Livewire\CourseManagement\AvailableCourses::class)->name('courses.available');
});

// Project Routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/projects/{slug}', [App\Http\Controllers\ProjectController::class, 'show'])->name('project.show');
});

// =============================================================================
// CERTIFICATE MANAGEMENT ROUTES (ADMIN/INSTRUCTOR)
// =============================================================================

// Certificate Management Dashboard
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/admin/certificates', CertificateManagement::class)->name('admin.certificates.manage');
    Route::get('/admin/analytics', CertificateAnalytics::class)->name('admin.certificates.analytics');
    Route::get('/admin/templates', CertificateTemplates::class)->name('admin.certificates.templates');
});

// Certificate Actions - Removed role checks
Route::middleware(['auth', 'verified'])->prefix('admin/certificates')->name('admin.certificates.')->group(function () {
    Route::post('/{certificate}/approve', function ($certificateId) {
        $certificate = \App\Models\Certificate::findOrFail($certificateId);
        $certificate->approve(auth()->id());
        return response()->json(['success' => true, 'message' => 'Certificate approved successfully']);
    })->name('approve');

    Route::post('/{certificate}/reject', function ($certificateId) {
        $certificate = \App\Models\Certificate::findOrFail($certificateId);
        $reason = request()->input('reason');
        $certificate->reject($reason, auth()->id());
        return response()->json(['success' => true, 'message' => 'Certificate rejected']);
    })->name('reject');

    Route::post('/{certificate}/revoke', function ($certificateId) {
        $certificate = \App\Models\Certificate::findOrFail($certificateId);
        $reason = request()->input('reason');
        $certificate->revoke($reason, auth()->id());
        return response()->json(['success' => true, 'message' => 'Certificate revoked']);
    })->name('revoke');
});

// Certificate Analytics & Reports
Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    Route::post('/certificates/bulk-approve', function () {
        $certificateIds = request()->input('certificate_ids', []);
        $approved = 0;

        foreach ($certificateIds as $id) {
            try {
                $certificate = \App\Models\Certificate::find($id);
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
});

// =============================================================================
// UTILITY & BACKWARD COMPATIBILITY ROUTES
// =============================================================================

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard certificate redirect - Removed role checks
    Route::get('/dashboard/certificates', function () {
        return redirect()->route('admin.certificates.manage');
    })->name('certificates.dashboard');

    // Alternative certificate routes for backward compatibility
    Route::get('/certificates', \App\Livewire\CertificateManagement\StudentCertificates::class)->name('certificates.index');
    Route::get('/my-certificates', \App\Livewire\CertificateManagement\StudentCertificates::class)->name('my.certificates');
    Route::get('/dashboard/my-certificates', \App\Livewire\CertificateManagement\StudentCertificates::class)->name('dashboard.certificates');
});

// =============================================================================
// DEVELOPMENT/TESTING ROUTES (NON-PRODUCTION ONLY)
// =============================================================================
if (app()->environment(['local', 'staging'])) {
    Route::prefix('dev/certificates')->name('dev.certificates.')->group(function () {
        Route::get('/test/{userId}/{courseId}', function ($userId, $courseId) {
            $certificate = \App\Models\Certificate::create([
                'user_id' => $userId,
                'course_id' => $courseId,
                'status' => \App\Models\Certificate::STATUS_APPROVED,
                'completion_date' => now()->subDays(rand(1, 30)),
                'grade' => collect(['A+', 'A', 'A-', 'B+', 'B', 'Pass'])->random(),
                'approved_at' => now(),
                'approved_by' => 1,
                'issued_date' => now(),
            ]);

            return response()->json([
                'message' => 'Test certificate created',
                'certificate' => $certificate,
                'verification_url' => route('certificate.verify.code', $certificate->verification_code)
            ]);
        })->name('test');
    });
}
// =============================================================================
// AUTHENTICATION ROUTES
// =============================================================================
require __DIR__ . '/auth.php';




// =============================================================================
// CATCH-ALL ROUTE FOR PAGES (Must be absolutely last)
// =============================================================================
Route::get('/{slug}', [PageController::class, 'show'])
    ->name('page.show')
    ->where('slug', '[A-Za-z0-9\-_]+');