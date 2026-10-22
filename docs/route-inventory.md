# Route inventory

Generated from `php artisan route:list --json` on 22 October 2026 after the private project/assignment submission continuation. Middleware and request-time component/service checks determine access; route existence is not proof of functional completion.

| Method | URI | Name | Handler | Middleware |
|---|---|---|---|---|
| GET / HEAD | / | home | App\Livewire\Pages\Home | web |
| GET / HEAD | About | about | App\Livewire\ManualPages\AboutUs | web |
| GET / HEAD | Contact | contact | App\Livewire\ManualPages\ContactUs | web |
| GET / HEAD | Guideline | guideline | App\Livewire\ManualPages\Guideline | web |
| GET / HEAD | Statistics | statistics | App\Livewire\ManualPages\Statistics | web |
| GET / HEAD | academy-admin/dashboard | academy_admin.dashboard | App\Livewire\Dashboard\AcademyAdminDashboard | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | admin/blog/categories | admin.blog.categories.index | App\Livewire\Blog\AdminBlogCategories | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | admin/blog/comments | admin.blog.comments.index | App\Livewire\Blog\AdminBlogComments | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | admin/blog/posts | admin.blog.posts.index | App\Livewire\Blog\AdminBlogPosts | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | admin/blog/posts/create | admin.blog.posts.create | App\Livewire\Blog\AdminBlogPostForm | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | admin/blog/posts/{post}/edit | admin.blog.posts.edit | App\Livewire\Blog\AdminBlogPostForm | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | admin/blog/seo | admin.blog.seo | App\Livewire\Blog\AdminBlogSettings | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | admin/blog/settings | admin.blog.settings | App\Livewire\Blog\AdminBlogSettings | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | admin/certificates | admin.certificates.manage | App\Livewire\CertificateManagement\CertificateManagement | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified, App\Http\Middleware\CheckRole:super_admin,academy_admin,instructor |
| GET / HEAD | admin/certificates/analytics | admin.certificates.analytics | App\Livewire\CertificateManagement\CertificateAnalytics | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified, App\Http\Middleware\CheckRole:super_admin,academy_admin,instructor |
| POST | admin/certificates/bulk-approve | admin.certificates.bulk.approve | Closure | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified, App\Http\Middleware\CheckRole:super_admin,academy_admin,instructor |
| GET / HEAD | admin/certificates/templates | admin.certificates.templates | App\Livewire\CertificateManagement\CertificateTemplates | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified, App\Http\Middleware\CheckRole:super_admin,academy_admin,instructor |
| POST | admin/certificates/{certificate}/approve | admin.certificates.approve | Closure | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified, App\Http\Middleware\CheckRole:super_admin,academy_admin,instructor |
| POST | admin/certificates/{certificate}/reject | admin.certificates.reject | Closure | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified, App\Http\Middleware\CheckRole:super_admin,academy_admin,instructor |
| POST | admin/certificates/{certificate}/revoke | admin.certificates.revoke | Closure | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified, App\Http\Middleware\CheckRole:super_admin,academy_admin,instructor |
| GET / HEAD | admin/commercial-readiness | admin.commercial.readiness | App\Livewire\Financial\Admin\CommercialReadiness | web, Illuminate\Auth\Middleware\Authenticate, App\Http\Middleware\CheckRole:super_admin,academy_admin |
| GET / HEAD | admin/financial | admin.financial.dashboard | App\Livewire\Financial\Admin\FinancialDashboard | web, Illuminate\Auth\Middleware\Authenticate, App\Http\Middleware\CheckRole:super_admin,academy_admin |
| GET / HEAD | admin/financial/settings | admin.financial.settings | App\Livewire\Financial\Admin\FinancialSettings | web, Illuminate\Auth\Middleware\Authenticate, App\Http\Middleware\CheckRole:super_admin,academy_admin |
| GET / HEAD | admin/interview | admin.interview | App\Livewire\Career\AdminMockInterview | web, Illuminate\Auth\Middleware\Authenticate |
| GET / HEAD | admin/job | admin.job | App\Livewire\Career\JobManagement | web, Illuminate\Auth\Middleware\Authenticate, App\Http\Middleware\CheckRole:super_admin,academy_admin |
| GET / HEAD | admin/pages | pages.index | App\Livewire\Pages\PageManager | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | admin/pages/analytics | pages.analytics | App\Livewire\Pages\PageManager | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | admin/pages/create | pages.create | App\Livewire\Pages\PageManager | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | admin/pages/media | pages.media | App\Livewire\Pages\PageManager | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | admin/pages/seo | pages.seo | App\Livewire\Pages\PageManager | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | admin/pages/settings | pages.settings | App\Livewire\Pages\PageManager | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | admin/pages/templates | pages.templates | App\Livewire\Pages\PageManager | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| POST | admin/pages/track-view/{slug} | pages.track-view | App\Http\Controllers\PageController@trackView | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | admin/payments | admin.payments | App\Livewire\Financial\Admin\PaymentProcessing | web, Illuminate\Auth\Middleware\Authenticate, App\Http\Middleware\CheckRole:super_admin,academy_admin |
| GET / HEAD | admin/payments/processing | admin.payments.processing | App\Livewire\Financial\Admin\PaymentProcessing | web, Illuminate\Auth\Middleware\Authenticate, App\Http\Middleware\CheckRole:super_admin,academy_admin |
| GET / HEAD | admin/paystack/settings | admin.paystack.settings | App\Livewire\Financial\Admin\PaystackSettings | web, Illuminate\Auth\Middleware\Authenticate, App\Http\Middleware\CheckRole:super_admin,academy_admin |
| GET / HEAD | admin/revenue/reports | admin.revenue.reports | App\Livewire\Financial\Admin\RevenueReports | web, Illuminate\Auth\Middleware\Authenticate, App\Http\Middleware\CheckRole:super_admin,academy_admin |
| GET / HEAD | affiliate-ambassador/dashboard | affiliate_ambassador.dashboard | App\Livewire\Dashboard\AffiliateAmbassadorDashboard | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | affiliate-banners/{size}/{code} | affiliate.banner | App\Http\Controllers\AffiliateBannerController | web |
| GET / HEAD | affiliate/analytics | affiliate.analytics | App\Livewire\Affiliate\Analytics | web, Illuminate\Auth\Middleware\Authenticate |
| GET / HEAD | affiliate/api/validate-referral/{code} | affiliate.api.validate-referral | Closure | web, Illuminate\Auth\Middleware\Authenticate |
| GET / HEAD | affiliate/apply | affiliate.apply | App\Livewire\Affiliate\Apply | web, Illuminate\Auth\Middleware\Authenticate |
| GET / HEAD | affiliate/commissions | affiliate.commissions | App\Livewire\Affiliate\CommissionHistory | web, Illuminate\Auth\Middleware\Authenticate |
| GET / HEAD | affiliate/dashboard | affiliate.dashboard | App\Livewire\Affiliate\Dashboard | web, Illuminate\Auth\Middleware\Authenticate |
| GET / HEAD | affiliate/not-eligible | affiliate.not-eligible | App\Livewire\Affiliate\NotEligible | web, Illuminate\Auth\Middleware\Authenticate |
| GET / HEAD | affiliate/reports | affiliate.reports | App\Livewire\Affiliate\CommissionReports | web, Illuminate\Auth\Middleware\Authenticate |
| GET / HEAD | affiliate/settings | affiliate.settings | App\Livewire\Affiliate\Settings | web, Illuminate\Auth\Middleware\Authenticate |
| GET / HEAD | affiliate/tools | affiliate.tools | App\Livewire\Affiliate\Tools | web, Illuminate\Auth\Middleware\Authenticate |
| GET / HEAD | ai-learning-coach | ai.learning.coach | App\Livewire\StudentManagement\AiLearningCoach | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | api/banks | api.banks | App\Http\Controllers\FinancialController@getBanks | web, Illuminate\Auth\Middleware\Authenticate |
| POST | api/blog/comments/{comment}/react | api.blog.comment.react | App\Http\Controllers\BlogController@toggleCommentReaction | web, api |
| POST | api/blog/posts/{post}/react | api.blog.react | App\Http\Controllers\BlogController@toggleReaction | web, api |
| POST | api/blog/posts/{post}/view | api.blog.view | App\Http\Controllers\BlogController@incrementView | web, api |
| POST | api/blog/upload-image | api.blog.upload.image | App\Http\Controllers\BlogController@uploadImage | web, api |
| POST | api/certificate/batch-verify | api.certificate.batch.verify | App\Http\Controllers\CertificateVerificationController@batchVerify | web |
| GET / HEAD | api/certificate/verify/{verificationCode} | api.certificate.verify | App\Http\Controllers\CertificateVerificationController@api | web |
| POST | api/resolve-account | api.resolve-account | App\Http\Controllers\FinancialController@resolveAccount | web, Illuminate\Auth\Middleware\Authenticate |
| GET / HEAD | auth/facebook | login.facebook | App\Http\Controllers\Auth\SocialAuthController@redirectToFacebook | web, Illuminate\Auth\Middleware\RedirectIfAuthenticated |
| GET / HEAD | auth/facebook/callback | — | App\Http\Controllers\Auth\SocialAuthController@handleFacebookCallback | web, Illuminate\Auth\Middleware\RedirectIfAuthenticated |
| GET / HEAD | auth/google | login.google | App\Http\Controllers\Auth\SocialAuthController@redirectToGoogle | web, Illuminate\Auth\Middleware\RedirectIfAuthenticated |
| GET / HEAD | auth/google/callback | — | App\Http\Controllers\Auth\SocialAuthController@handleGoogleCallback | web, Illuminate\Auth\Middleware\RedirectIfAuthenticated |
| GET / HEAD | auth/twitter | login.twitter | App\Http\Controllers\Auth\SocialAuthController@redirectToTwitter | web, Illuminate\Auth\Middleware\RedirectIfAuthenticated |
| GET / HEAD | auth/twitter/callback | — | App\Http\Controllers\Auth\SocialAuthController@handleTwitterCallback | web, Illuminate\Auth\Middleware\RedirectIfAuthenticated |
| GET / HEAD | billing/documents | billing.documents | App\Livewire\Financial\BillingDocuments | web, Illuminate\Auth\Middleware\Authenticate |
| GET / HEAD | billing/documents/{commercialDocument} | commercial.documents.show | App\Http\Controllers\CommercialDocumentController@show | web, Illuminate\Auth\Middleware\Authenticate |
| GET / HEAD | blog | blog.index | App\Livewire\Blog\PublicBlogIndex | web |
| GET / HEAD | blog/category/{category} | blog.category | App\Livewire\Blog\PublicBlogIndex | web |
| GET / HEAD | blog/search | blog.search | App\Livewire\Blog\PublicBlogIndex | web |
| GET / HEAD | blog/tag/{tag} | blog.tag | App\Livewire\Blog\PublicBlogIndex | web |
| GET / HEAD | blog/{post} | blog.show | App\Livewire\Blog\PublicBlogPost | web |
| GET / HEAD | bootkode-ai/mentor | ai.coding-mentor | App\Livewire\Ai\CodingMentor | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | cbt/exam/{assessmentId?} | cbt.exam | App\Livewire\Cbt\CbtExam | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | cbt/exam/{assessmentId}/summary | cbt.exam.summary | App\Livewire\Cbt\CbtViewer | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | cbt/exam/{assessment}/take | cbt.exam.take | App\Livewire\Cbt\CbtExamInterface | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | cbt/exams | cbt.exams | App\Livewire\Cbt\CbtExamSelection | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | cbt/manage | cbt.manage | App\Livewire\Cbt\CbtManagement | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified, App\Http\Middleware\CheckRole:super_admin,academy_admin,instructor |
| GET / HEAD | cbt/management | cbt.management | App\Livewire\Cbt\CbtManagement | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified, App\Http\Middleware\CheckRole:super_admin,academy_admin,instructor |
| GET / HEAD | cbt/results | cbt.viewer | App\Livewire\Cbt\CbtViewer | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | certificate/download/{verificationCode} | certificate.download | App\Http\Controllers\CertificateVerificationController@download | web |
| GET / HEAD | certificate/qr/{verificationCode} | certificate.qr | App\Http\Controllers\CertificateVerificationController@qrCode | web |
| GET / HEAD | certificate/verify | certificate.verify | App\Http\Controllers\CertificateVerificationController@index | web |
| POST | certificate/verify | certificate.verify.submit | App\Http\Controllers\CertificateVerificationController@verify | web |
| GET / HEAD | certificate/verify/{verificationCode} | certificate.verify.code | App\Http\Controllers\CertificateVerificationController@verify | web |
| GET / HEAD | certificate/view/{verificationCode} | certificate.view | App\Http\Controllers\CertificateVerificationController@show | web |
| GET / HEAD | community | community.center | App\Livewire\Community\CommunityCenter | web, Illuminate\Auth\Middleware\Authenticate |
| GET / HEAD | community/code-challenges | community.code-challenges | App\Livewire\Community\CommunityCenter | web, Illuminate\Auth\Middleware\Authenticate |
| GET / HEAD | community/feedback | community.feedback | App\Livewire\Community\CommunityCenter | web, Illuminate\Auth\Middleware\Authenticate |
| GET / HEAD | community/forums | community.forums | App\Livewire\Community\CommunityCenter | web, Illuminate\Auth\Middleware\Authenticate |
| GET / HEAD | community/live-events | community.live-events | App\Livewire\Community\CommunityCenter | web, Illuminate\Auth\Middleware\Authenticate |
| GET / HEAD | community/moderation | community.moderation | App\Livewire\Community\CommunityCenter | web, Illuminate\Auth\Middleware\Authenticate, App\Http\Middleware\CheckRole:super_admin,academy_admin |
| GET / HEAD | community/study-groups | community.study-groups | App\Livewire\Community\CommunityCenter | web, Illuminate\Auth\Middleware\Authenticate |
| GET / HEAD | confirm-password | password.confirm | App\Http\Controllers\Auth\AuthController@showConfirmPasswordForm | web, Illuminate\Auth\Middleware\Authenticate |
| POST | confirm-password | — | App\Http\Controllers\Auth\AuthController@confirmPassword | web, Illuminate\Auth\Middleware\Authenticate |
| GET / HEAD | content | content.index | App\Livewire\Content\ContentDocumentationCenter | web, Illuminate\Auth\Middleware\Authenticate |
| GET / HEAD | content-editor/dashboard | content_editor.dashboard | App\Livewire\Dashboard\ContentEditorDashboard | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | content/all-documents | content.all-documents | App\Livewire\Content\ContentDocumentationCenter | web, Illuminate\Auth\Middleware\Authenticate |
| GET / HEAD | content/analytics | content.analytics | App\Livewire\Content\ContentDocumentationCenter | web, Illuminate\Auth\Middleware\Authenticate |
| GET / HEAD | content/archives | content.archives | App\Livewire\Content\ContentDocumentationCenter | web, Illuminate\Auth\Middleware\Authenticate |
| GET / HEAD | content/categories | content.categories | App\Livewire\Content\ContentDocumentationCenter | web, Illuminate\Auth\Middleware\Authenticate |
| GET / HEAD | content/create-document | content.create-document | App\Livewire\Content\ContentDocumentationCenter | web, Illuminate\Auth\Middleware\Authenticate |
| GET / HEAD | content/documentation | content.documentation | App\Livewire\Content\ContentDocumentationCenter | web, Illuminate\Auth\Middleware\Authenticate |
| GET / HEAD | content/faq | content.faq | App\Livewire\Content\ContentDocumentationCenter | web, Illuminate\Auth\Middleware\Authenticate |
| GET / HEAD | content/feedback | content.feedback | App\Livewire\Content\ContentDocumentationCenter | web, Illuminate\Auth\Middleware\Authenticate |
| GET / HEAD | content/learning-materials | content.learning-materials | App\Livewire\Content\ContentDocumentationCenter | web, Illuminate\Auth\Middleware\Authenticate |
| GET / HEAD | content/localization | content.localization | App\Livewire\Content\ContentDocumentationCenter | web, Illuminate\Auth\Middleware\Authenticate |
| GET / HEAD | content/moderation | content.moderation | App\Livewire\Content\ContentDocumentationCenter | web, Illuminate\Auth\Middleware\Authenticate |
| GET / HEAD | content/notifications | content.notifications | App\Livewire\Content\ContentDocumentationCenter | web, Illuminate\Auth\Middleware\Authenticate |
| GET / HEAD | content/overview | content.overview | App\Livewire\Content\ContentDocumentationCenter | web, Illuminate\Auth\Middleware\Authenticate |
| GET / HEAD | content/reports | content.reports | App\Livewire\Content\ContentDocumentationCenter | web, Illuminate\Auth\Middleware\Authenticate |
| GET / HEAD | content/reviews | content.reviews | App\Livewire\Content\ContentDocumentationCenter | web, Illuminate\Auth\Middleware\Authenticate |
| GET / HEAD | content/settings | content.settings | App\Livewire\Content\ContentDocumentationCenter | web, Illuminate\Auth\Middleware\Authenticate |
| GET / HEAD | content/updates | content.updates | App\Livewire\Content\ContentDocumentationCenter | web, Illuminate\Auth\Middleware\Authenticate |
| GET / HEAD | content/video-library | content.video-library | App\Livewire\Content\ContentDocumentationCenter | web, Illuminate\Auth\Middleware\Authenticate |
| GET / HEAD | course-catalog | student.course-catalog | App\Livewire\StudentManagement\CourseCatalog | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | course-categories | course-categories | App\Livewire\CourseManagement\CourseCategories | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | course-management/all-courses | all-course | App\Livewire\CourseManagement\AllCourses | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | course-management/assessment-reviews | assessment.reviews | App\Livewire\CourseManagement\AssessmentReviewQueue | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified, App\Http\Middleware\CheckRole:super_admin,academy_admin,instructor |
| GET / HEAD | course-management/lessons/{lessonId}/assessments | lesson.assessments.manage | App\Livewire\CourseManagement\CourseBuilder\AssessmentManager | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified, App\Http\Middleware\CheckRole:super_admin,academy_admin,instructor |
| GET / HEAD | course-management/my-courses | my-course | App\Livewire\CourseManagement\UserCourses | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | course-reviews/analytics/{courseId?} | review-analytics | App\Livewire\CourseManagement\ReviewAnalytics | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | course/{course} | course.view | App\Livewire\StudentManagement\CourseView | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | courses | courses.index | App\Livewire\Pages\Courses | web |
| GET / HEAD | courses/{course} | courses.show | App\Livewire\Pages\CourseDetails | web |
| GET / HEAD | courses/{course}/checkout | course.checkout | App\Livewire\Financial\CourseCheckout | web, Illuminate\Auth\Middleware\Authenticate |
| GET / HEAD | dashboard | dashboard | App\Http\Controllers\DashboardController@__invoke | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | dashboard-redirect | dashboard.redirect | App\Http\Controllers\DashboardController | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | dashboard/all-users | all-users | App\Livewire\UserManagement\AllUser | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | dashboard/announcement-management | announcement.management | App\Livewire\SystemManagement\AnnouncementManagement | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | dashboard/announcements | announcements | App\Livewire\SystemManagement\Announcements | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | dashboard/courses/approvals | course-approvals | App\Livewire\CourseManagement\CourseApprovals | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | dashboard/courses/available | courses.available | App\Livewire\CourseManagement\AvailableCourses | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | dashboard/courses/create | create.course | App\Livewire\CourseManagement\CourseForm | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | dashboard/courses/quality-control | course-quality.control | App\Livewire\CourseManagement\CourseQualityControl | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | dashboard/courses/reviews | course-reviews | App\Livewire\CourseManagement\CourseReviews | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | dashboard/courses/{course}/builder | course-builder | App\Livewire\CourseManagement\CourseBuilder | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | dashboard/courses/{course}/edit | edit_course | App\Livewire\CourseManagement\EditCourse | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | dashboard/courses/{course}/preview | courses.preview | App\Livewire\CourseManagement\CoursePreview | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | dashboard/faq-management | faq.management | App\Livewire\SystemManagement\FaqManagement | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified, App\Http\Middleware\CheckRole:super_admin,academy_admin |
| GET / HEAD | dashboard/feedback | feedback | App\Livewire\SystemManagement\Feedback | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | dashboard/feedback-management | feedback.management | App\Livewire\SystemManagement\FeedbackManagement | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified, App\Http\Middleware\CheckRole:super_admin,academy_admin |
| GET / HEAD | dashboard/feedback/{feedback}/attachment | feedback.attachment | App\Http\Controllers\FeedbackAttachmentController | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | dashboard/help-support | help.support | App\Livewire\SystemManagement\HelpSupport | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | dashboard/language-localization | language.localization | App\Livewire\SystemManagement\LanguageLocalization | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | dashboard/learning-analytics | learning.analytics | App\Livewire\Dashboard\LearningAnalyticsDashboard | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | dashboard/newsletter | newsletter.index | App\Livewire\Newsletter\NewsletterCenter | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | dashboard/newsletter/analytics | newsletter.analytics | App\Livewire\Newsletter\NewsletterCenter | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | dashboard/newsletter/campaigns | newsletter.campaigns | App\Livewire\Newsletter\NewsletterCenter | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | dashboard/newsletter/performance | newsletter.performance | App\Livewire\Newsletter\NewsletterCenter | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | dashboard/newsletter/reports | newsletter.reports | App\Livewire\Newsletter\NewsletterCenter | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | dashboard/newsletter/settings | newsletter.settings | App\Livewire\Newsletter\NewsletterCenter | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | dashboard/newsletter/subscribers | newsletter.subscribers | App\Livewire\Newsletter\NewsletterCenter | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | dashboard/newsletter/templates | newsletter.templates | App\Livewire\Newsletter\NewsletterCenter | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | dashboard/notification-preferences | notification.preferences | App\Livewire\SystemManagement\NotificationPreferences | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | dashboard/notifications | notifications | App\Livewire\SystemManagement\Notifications | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| POST | dashboard/notifications/{notification}/open | notifications.open | App\Http\Controllers\NotificationOpenController | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | dashboard/pending-verifications | pending-verifications | App\Livewire\UserManagement\PendingVerifications | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | dashboard/privacy-settings | privacy.settings | App\Livewire\SystemManagement\PrivacySettings | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | dashboard/production-observability | production.observability | App\Livewire\SystemManagement\ProductionObservability | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | dashboard/profile | profile.view | App\Livewire\UserManagement\Profile | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | dashboard/profile/edit | profile.edit | Closure | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | dashboard/roles-permissions | roles-permissions | App\Livewire\UserManagement\RolesPermissions | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | dashboard/search | dashboard.search | App\Livewire\Dashboard\GlobalSearch | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | dashboard/search/suggest | dashboard.search.suggest | App\Http\Controllers\DashboardSearchController@suggest | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | dashboard/settings | settings | App\Livewire\SystemManagement\Settings | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | dashboard/support-tickets | support.tickets | App\Livewire\SystemManagement\SupportTicketManagement | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified, App\Http\Middleware\CheckRole:super_admin,academy_admin |
| GET / HEAD | dashboard/support-tickets/{ticket}/attachment | support.attachment | App\Http\Controllers\SupportAttachmentController | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | dashboard/system-status | system-status | App\Livewire\SystemManagement\SystemStatus | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | dashboard/system-status-management | system-status.management | App\Livewire\SystemManagement\SystemStatusManagement | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | dashboard/user | user-management | App\Livewire\UserManagement\UserManagement | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | dashboard/user-activity | user.activity | App\Livewire\UserManagement\UserActivity | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| POST | email/verification-notification | verification.send | App\Http\Controllers\Auth\EmailVerificationController@sendVerificationEmail | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Routing\Middleware\ThrottleRequests:6,1 |
| GET / HEAD | enrolled-courses | student.enrolled-courses | App\Livewire\StudentManagement\EnrolledCourses | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | forgot-password | password.request | App\Http\Controllers\Auth\PasswordController@showForgotPasswordForm | web, Illuminate\Auth\Middleware\RedirectIfAuthenticated |
| POST | forgot-password | password.email | App\Http\Controllers\Auth\PasswordController@sendResetLink | web, Illuminate\Auth\Middleware\RedirectIfAuthenticated |
| GET / HEAD | gamification/badges | gamification.badges | App\Livewire\Gamification\GamificationDashboard | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | gamification/dashboard | gamification.dashboard | App\Livewire\Gamification\GamificationDashboard | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | gamification/games | gamification.games | App\Livewire\Gamification\GamificationDashboard | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | gamification/games/{gameId} | gamification.games.play | App\Http\Controllers\GamificationController@playGame | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| POST | gamification/items/{purchaseId}/toggle-equip | gamification.items.toggle-equip | App\Http\Controllers\GamificationController@toggleEquip | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | gamification/leaderboard | gamification.leaderboard | App\Livewire\Gamification\GamificationDashboard | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | gamification/store | gamification.store | App\Livewire\Gamification\GamificationDashboard | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| POST | gamification/store/purchase/{itemId} | gamification.store.purchase | App\Http\Controllers\GamificationController@purchaseItem | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | institution/analytics | institution.analytics | App\Livewire\Institution\InstitutionPortal | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified, App\Http\Middleware\CheckRole:super_admin,academy_admin |
| GET / HEAD | institution/bulk-enrollment | institution.bulk-enrollment | App\Livewire\Institution\InstitutionPortal | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified, App\Http\Middleware\CheckRole:super_admin,academy_admin |
| GET / HEAD | institution/cohorts | institution.cohorts | App\Livewire\Institution\InstitutionPortal | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified, App\Http\Middleware\CheckRole:super_admin,academy_admin |
| GET / HEAD | institution/invitations/{token}/accept | institution.invitations.accept | App\Http\Controllers\InstitutionInvitationController@accept | web |
| POST | institution/invitations/{token}/accept | institution.invitations.confirm | App\Http\Controllers\InstitutionInvitationController@confirm | web, Illuminate\Auth\Middleware\Authenticate |
| GET / HEAD | institution/licenses | institution.licenses | App\Livewire\Institution\InstitutionPortal | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified, App\Http\Middleware\CheckRole:super_admin,academy_admin |
| GET / HEAD | institution/overview | institution.overview | App\Livewire\Institution\InstitutionPortal | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified, App\Http\Middleware\CheckRole:super_admin,academy_admin |
| GET / HEAD | institution/partners | institution.partners | App\Livewire\Institution\InstitutionPortal | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified, App\Http\Middleware\CheckRole:super_admin,academy_admin |
| GET / HEAD | institution/portal | institution.portal | App\Livewire\Institution\InstitutionPortal | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | institution/whitelabel | institution.whitelabel | App\Livewire\Institution\InstitutionPortal | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified, App\Http\Middleware\CheckRole:super_admin,academy_admin |
| GET / HEAD | instructor/dashboard | instructor.dashboard | App\Livewire\Dashboard\InstructorDashboard | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | instructor/earnings | instructor.earnings | App\Livewire\Financial\InstructorEarnings | web, Illuminate\Auth\Middleware\Authenticate |
| GET / HEAD | interview/user | user.interview | App\Livewire\Career\UserMockInterview | web, Illuminate\Auth\Middleware\Authenticate |
| GET / HEAD | interview/{interview}/results | interview.results | App\Livewire\Career\InterviewResults | web, Illuminate\Auth\Middleware\Authenticate |
| GET / HEAD | interview/{interview}/take | interview.take | App\Livewire\Career\StudentInterviewTaker | web, Illuminate\Auth\Middleware\Authenticate |
| GET / HEAD | job | user.job | App\Livewire\Career\JobPortal | web, Illuminate\Auth\Middleware\Authenticate |
| GET / HEAD | job-applications/{jobApplication}/resume | job-applications.resume | App\Http\Controllers\JobApplicationResumeController | web, Illuminate\Auth\Middleware\Authenticate |
| GET / HEAD | learner-journey | learner.journey | App\Livewire\StudentManagement\LearnerJourney | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | learning-analytics | student.learning-analytics | App\Livewire\StudentManagement\LearningAnalytics | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | learning/attempts/{session}/files/{file} | assessment.submission-file | App\Http\Controllers\AssessmentSubmissionFileController | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | learning/lessons/{lesson}/assessments | lesson.assessments | App\Livewire\StudentManagement\CourseView\StudentAssessmentTaker | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | livewire/livewire.js | — | Livewire\Mechanisms\FrontendAssets\FrontendAssets@returnJavaScriptAsFile |  |
| GET / HEAD | livewire/livewire.min.js.map | — | Livewire\Mechanisms\FrontendAssets\FrontendAssets@maps |  |
| GET / HEAD | livewire/preview-file/{filename} | livewire.preview-file | Livewire\Features\SupportFileUploads\FilePreviewController@handle | web |
| POST | livewire/update | livewire.update | Livewire\Mechanisms\HandleRequests\HandleRequests@handleUpdate | web |
| POST | livewire/upload-file | livewire.upload-file | Livewire\Features\SupportFileUploads\FileUploadController@handle | web, Illuminate\Routing\Middleware\ThrottleRequests:60,1 |
| GET / HEAD | login | login | App\Http\Controllers\Auth\AuthController@showLoginForm | web, Illuminate\Auth\Middleware\RedirectIfAuthenticated |
| POST | login | — | App\Http\Controllers\Auth\AuthController@login | web, Illuminate\Auth\Middleware\RedirectIfAuthenticated |
| POST | logout | logout | App\Http\Controllers\Auth\AuthController@logout | web, Illuminate\Auth\Middleware\Authenticate |
| GET / HEAD | marketplace | marketplace.browse | App\Livewire\Marketplace\PublicMarketplace | web |
| GET / HEAD | marketplace/admin/analytics | marketplace.analytics | App\Livewire\Marketplace\MarketplaceCenter | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified, App\Http\Middleware\CheckRole:super_admin,academy_admin |
| GET / HEAD | marketplace/admin/orders | marketplace.orders | App\Livewire\Marketplace\MarketplaceCenter | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified, App\Http\Middleware\CheckRole:super_admin,academy_admin |
| GET / HEAD | marketplace/admin/payments | marketplace.payments | App\Livewire\Marketplace\MarketplaceCenter | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified, App\Http\Middleware\CheckRole:super_admin,academy_admin |
| GET / HEAD | marketplace/admin/settings | marketplace.settings | App\Livewire\Marketplace\MarketplaceCenter | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified, App\Http\Middleware\CheckRole:super_admin,academy_admin |
| GET / HEAD | marketplace/admin/vendors | marketplace.vendor.applications | App\Livewire\Marketplace\MarketplaceCenter | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified, App\Http\Middleware\CheckRole:super_admin,academy_admin |
| GET / HEAD | marketplace/cart | marketplace.cart | App\Livewire\Marketplace\MarketplaceCenter | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | marketplace/categories | marketplace.categories | App\Livewire\Marketplace\PublicMarketplace | web |
| GET / HEAD | marketplace/category/{slug} | marketplace.category.show | App\Livewire\Marketplace\PublicMarketplace | web |
| GET / HEAD | marketplace/checkout | marketplace.checkout | App\Livewire\Marketplace\MarketplaceCenter | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | marketplace/drafts | marketplace.seller.drafts | App\Livewire\Marketplace\MarketplaceCenter | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | marketplace/instructor/{id} | marketplace.instructor.show | App\Livewire\Marketplace\PublicMarketplace | web |
| GET / HEAD | marketplace/item/{slug} | marketplace.item.public | App\Livewire\Marketplace\MarketplaceCenter | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | marketplace/my-listings | marketplace.seller.listings | App\Livewire\Marketplace\MarketplaceCenter | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | marketplace/my-purchases | marketplace.purchases | App\Livewire\Marketplace\MarketplaceCenter | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | marketplace/orders/{order}/download/{file?} | marketplace.order.download | App\Http\Controllers\MarketplaceDownloadController | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | marketplace/payment/failed | marketplace.payment.failed | Closure | web |
| GET / HEAD | marketplace/payment/success | marketplace.payment.success | Closure | web |
| GET / HEAD | marketplace/product/{slug} | marketplace.product.show | App\Livewire\Marketplace\PublicMarketplace | web |
| GET / HEAD | marketplace/promotions | marketplace.promotions | App\Livewire\Marketplace\MarketplaceCenter | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified, App\Http\Middleware\CheckRole:super_admin,academy_admin,content_editor |
| GET / HEAD | marketplace/reviews | marketplace.reviews | App\Livewire\Marketplace\MarketplaceCenter | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified, App\Http\Middleware\CheckRole:super_admin,academy_admin,content_editor |
| GET / HEAD | marketplace/sell | marketplace.sell | App\Livewire\Marketplace\MarketplaceCenter | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | marketplace/seller/create | marketplace.seller.create | App\Livewire\Marketplace\MarketplaceCenter | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | marketplace/vendor/dashboard | marketplace.vendor.dashboard | App\Livewire\Marketplace\MarketplaceCenter | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | marketplace/vendor/orders | marketplace.vendor.orders | App\Livewire\Marketplace\MarketplaceCenter | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | marketplace/vendor/withdrawals | marketplace.vendor.withdrawals | App\Livewire\Marketplace\MarketplaceCenter | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | mentor/dashboard | mentor.dashboard | App\Livewire\Dashboard\MentorDashboard | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | mentorship | mentorship.hub | App\Livewire\Mentorship\MentorshipHub | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | mentorship/code-reviews | mentorship.code-reviews | App\Livewire\Mentorship\CodeReviews | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | mentorship/dashboard | mentorship.dashboard | App\Livewire\Mentorship\MentorDashboard | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | mentorship/find | mentorship.find | App\Livewire\Mentorship\FindMentor | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | mentorship/management | mentorship.management | App\Livewire\Mentorship\MentorManagement | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified, App\Http\Middleware\CheckRole:super_admin,academy_admin |
| GET / HEAD | mentorship/my-mentorships | mentorship.my-mentorships | App\Livewire\Mentorship\MyMentorships | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | mentorship/operations | mentorship.operations | App\Livewire\Mentorship\Sessions | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | mentorship/profile | mentorship.profile | App\Livewire\Mentorship\MentorProfile | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | mentorship/resources | mentorship.resources | App\Livewire\Mentorship\MentorResources | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | mentorship/sessions | mentorship.sessions | App\Livewire\Mentorship\Sessions | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | messages | messages.index | App\Livewire\Messaging\DirectMessages | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | messages/{conversation} | messages.show | App\Livewire\Messaging\DirectMessages | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| POST | newsletter/resubscribe/{token} | newsletter.resubscribe | App\Http\Controllers\NewsletterController@resubscribe | web |
| POST | newsletter/subscribe | newsletter.subscribe | App\Http\Controllers\NewsletterController@subscribe | web |
| GET / HEAD | newsletter/track/click/{token} | newsletter.track-click | App\Http\Controllers\NewsletterController@trackClick | web |
| GET / HEAD | newsletter/track/open/{token} | newsletter.track-open | App\Http\Controllers\NewsletterController@trackOpen | web |
| GET / HEAD | newsletter/unsubscribe/{token} | newsletter.unsubscribe | App\Http\Controllers\NewsletterController@unsubscribe | web |
| GET / HEAD | offline-learning | student.offline-learning | App\Livewire\StudentManagement\OfflineLearningLibrary | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | offline-learning/packs/{course}/manifest | offline-learning.manifest | App\Http\Controllers\OfflineLearningController@manifest | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| POST | offline-learning/packs/{course}/sync | offline-learning.sync | App\Http\Controllers\OfflineLearningController@sync | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| PUT | password | password.update | App\Http\Controllers\Auth\PasswordController@updatePassword | web, Illuminate\Auth\Middleware\Authenticate |
| GET / HEAD | paystack/callback | paystack.callback | App\Http\Controllers\FinancialController@paystackCallback | web, Illuminate\Auth\Middleware\Authenticate |
| GET / HEAD | portfolio | portfolio.show | App\Livewire\Career\PortfolioBuilder | web, Illuminate\Auth\Middleware\Authenticate |
| GET / HEAD | pricing | pricing | App\Livewire\Pages\Pricing | web |
| GET / HEAD | projects/{slug} | project.show | App\Http\Controllers\ProjectController@show | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | register | register | App\Http\Controllers\Auth\AuthController@showRegistrationForm | web, Illuminate\Auth\Middleware\RedirectIfAuthenticated |
| POST | register | — | App\Http\Controllers\Auth\AuthController@register | web, Illuminate\Auth\Middleware\RedirectIfAuthenticated |
| POST | reset-password | password.store | App\Http\Controllers\Auth\PasswordController@resetPassword | web, Illuminate\Auth\Middleware\RedirectIfAuthenticated |
| GET / HEAD | reset-password/{token} | password.reset | App\Http\Controllers\Auth\PasswordController@showResetPasswordForm | web, Illuminate\Auth\Middleware\RedirectIfAuthenticated |
| GET / HEAD | resume/builder | resume.builder | App\Livewire\Career\ResumeBuilder | web, Illuminate\Auth\Middleware\Authenticate |
| GET / HEAD | review-reminder/unsubscribe/{reminder} | review-reminder.unsubscribe | Closure | web |
| GET / HEAD | review-reminders/unsubscribe-all | review-reminders.unsubscribe-all | Closure | web, Illuminate\Auth\Middleware\Authenticate |
| GET / HEAD | sanctum/csrf-cookie | sanctum.csrf-cookie | Laravel\Sanctum\Http\Controllers\CsrfCookieController@show | web |
| GET / HEAD | saved-resources | student.saved-resources | App\Livewire\StudentManagement\SavedResources | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | search/job | search.job | App\Livewire\Career\JobSearch | web, Illuminate\Auth\Middleware\Authenticate |
| GET / HEAD | student/certificate/request/{courseId?} | student.certificate.request | App\Livewire\CertificateManagement\CertificateRequest | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | student/certificates | student.certificates.index | App\Livewire\CertificateManagement\StudentCertificates | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | student/dashboard | student.dashboard | App\Livewire\Dashboard\StudentDashboard | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | super-admin/dashboard | super_admin.dashboard | App\Livewire\Dashboard\SuperAdminDashboard | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Auth\Middleware\EnsureEmailIsVerified |
| GET / HEAD | transactions/history | transactions.history | App\Livewire\Financial\TransactionHistory | web, Illuminate\Auth\Middleware\Authenticate |
| GET / HEAD | up | — | Closure |  |
| GET / HEAD | verify-email | verification.notice | App\Http\Controllers\Auth\EmailVerificationController@showVerificationPrompt | web, Illuminate\Auth\Middleware\Authenticate |
| GET / HEAD | verify-email/{id}/{hash} | verification.verify | App\Http\Controllers\Auth\EmailVerificationController@verifyEmail | web, Illuminate\Auth\Middleware\Authenticate, Illuminate\Routing\Middleware\ValidateSignature, Illuminate\Routing\Middleware\ThrottleRequests:6,1 |
| GET / HEAD | wallet | wallet.index | App\Livewire\Financial\WalletDashboard | web, Illuminate\Auth\Middleware\Authenticate |
| POST | webhook/paystack | paystack.webhook | App\Http\Controllers\FinancialController@paystackWebhook | web |
| GET / HEAD | withdrawals | withdrawals.index | App\Livewire\Financial\WithdrawalManager | web, Illuminate\Auth\Middleware\Authenticate |
| GET / HEAD | {slug} | page.show | App\Http\Controllers\PageController@show | web |

The 22 October recorded-mark continuation reuses the existing lesson workspace, personal result and assessment-review routes. The verified route count remains 284; no new public download or review endpoint is introduced in this phase.
