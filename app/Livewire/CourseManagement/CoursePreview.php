<?php

namespace App\Livewire\CourseManagement;

use Livewire\Component;
use App\Models\Learning\Course;
use App\Models\Learning\CourseEnrollment;
use App\Models\Core\User;
use App\Models\Learning\Section;
use App\Models\Learning\Lesson;
use App\Models\Marketplace\Wallet;
use App\Models\Marketplace\WalletTransaction;
use App\Notifications\CourseUpdateNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;

#[Layout('layouts.dashboard', ['title' => 'courses.preview', 'description' => 'Preview course details and content structure', 'icon' => 'fas fa-eye', 'active' => 'courses.preview'])]
class CoursePreview extends Component
{
    public Course $course;
    public $activeTab = 'overview';
    public $isEnrolled = false;
    public $enrollmentProgress = 0;
    public $userReview = null;
    public $showEnrollModal = false;
    public $showReviewModal = false;
    public array $enrollingCourseIds = [];
    public array $droppingCourseIds = [];

    public $reviewRating = 5;
    public $reviewComment = '';
    public $isFavorited = false;
    public $shareUrl = '';

    // Payment modal properties - FIXED TYPE DECLARATIONS
    public bool $showPaymentModal = false;
    public float $walletBalance = 0.0;
    public float $coursePrice = 0.0;
    public float $balanceAfterPayment = 0.0;
    public bool $hasSufficientFunds = false;

    // Course statistics
    public $totalLessons = 0;
    public $totalDuration = 0;
    public $completionRate = 0;
    public $averageRating = 0;
    public $totalReviews = 0;
    public $totalEnrollments = 0;

    // Processing states
    public bool $isEnrolling = false;

    public function mount(Course $course)
    {
        Log::info('CoursePreview: Component mounted', ['course_id' => $course->id]);

        $this->course = $course->load([
            'category',
            'instructor',
            'sections.lessons',
            'reviews.user',
            'enrollments' => function ($query) {
                $query->where('user_id', Auth::id());
            }
        ]);

        $this->checkEnrollmentStatus();
        $this->calculateStatistics();
        $this->loadUserReview();
        $this->loadWalletBalance();

        $this->shareUrl = route('courses.preview', $course);
        $this->isFavorited = Auth::check() ?
            Auth::user()->isCourseFavorited($course->id) :
            false;

        Log::info('CoursePreview: Initialization complete', [
            'is_enrolled' => $this->isEnrolled,
            'is_free' => $this->course->is_free,
            'price' => $this->course->price
        ]);
    }

    public function loadWalletBalance()
    {
        try {
            if (Auth::check()) {
                $user = Auth::user();
                $wallet = Wallet::getOrCreateWallet($user->id);
                $this->walletBalance = (float) $wallet->balance;
                Log::info('CoursePreview: Wallet balance loaded', ['balance' => $this->walletBalance]);
            }
        } catch (\Exception $e) {
            Log::error('CoursePreview: Failed to load wallet balance', ['error' => $e->getMessage()]);
            $this->walletBalance = 0.0;
        }
    }

    private function checkEnrollmentStatus()
    {
        if (Auth::check()) {
            $enrollment = CourseEnrollment::where('course_id', $this->course->id)
                ->where('user_id', Auth::id())
                ->first();

            $this->isEnrolled = !is_null($enrollment);
            $this->enrollmentProgress = $enrollment ? $enrollment->progress_percentage : 0;

            Log::info('CoursePreview: Enrollment status checked', [
                'is_enrolled' => $this->isEnrolled,
                'progress' => $this->enrollmentProgress
            ]);
        }
    }

    private function calculateStatistics()
    {
        // Calculate total lessons and duration
        $this->totalLessons = $this->course->sections->sum(function ($section) {
            return $section->lessons->count();
        });

        $this->totalDuration = $this->course->sections->sum(function ($section) {
            return $section->lessons->sum('duration_minutes');
        });

        // Get review statistics
        $reviews = $this->course->reviews;
        $this->totalReviews = $reviews->count();
        $this->averageRating = $reviews->avg('rating') ?: 0;
        $this->totalEnrollments = $this->course->enrollments()->count();

        // Calculate completion rate
        $completedEnrollments = $this->course->enrollments()->where('is_completed', true)->count();
        $this->completionRate = $this->totalEnrollments > 0
            ? ($completedEnrollments / $this->totalEnrollments) * 100
            : 0;

        Log::info('CoursePreview: Statistics calculated', [
            'total_lessons' => $this->totalLessons,
            'total_duration' => $this->totalDuration,
            'total_enrollments' => $this->totalEnrollments
        ]);
    }

    private function loadUserReview()
    {
        if (Auth::check()) {
            $this->userReview = $this->course->reviews()
                ->where('user_id', Auth::id())
                ->first();

            if ($this->userReview) {
                $this->reviewRating = $this->userReview->rating;
                $this->reviewComment = $this->userReview->review_text;
            }
        }
    }

   /**
 * Open enrollment/payment modal (main method)
 */
public function openEnrollmentModal()
{
    Log::info('CoursePreview: Opening enrollment modal', [
        'course_id' => $this->course->id,
        'is_free' => $this->course->is_free
    ]);

    if (!Auth::check()) {
        Log::info('CoursePreview: User not authenticated, redirecting to login');
        return redirect()->route('login');
    }

    // Check if already enrolled
    if ($this->isEnrolled) {
        Log::warning('CoursePreview: User already enrolled');
        $this->dispatch('notify', [
            'message' => 'You are already enrolled in this course!',
            'type' => 'info',
            'icon' => 'fas fa-info-circle'
        ]);
        return;
    }

    // For free courses, enroll directly
    if ($this->course->is_free) {
        Log::info('CoursePreview: Free course detected, enrolling directly');
        $this->enroll();
        return;
    }

    // For paid courses, show payment modal
    $this->loadWalletBalance();
    $this->coursePrice = (float) $this->course->price;
    $this->balanceAfterPayment = $this->walletBalance - $this->coursePrice;
    $this->hasSufficientFunds = $this->balanceAfterPayment >= 0;

    Log::info('CoursePreview: Payment modal prepared', [
        'wallet_balance' => $this->walletBalance,
        'course_price' => $this->coursePrice,
        'sufficient_funds' => $this->hasSufficientFunds
    ]);

    $this->showPaymentModal = true;
}
    /**
     * Alias method for backward compatibility
     */
    public function openPaymentModal()
    {
        return $this->openEnrollmentModal();
    }

    /**
     * Close payment modal
     */
    public function closePaymentModal()
    {
        Log::info('CoursePreview: Closing payment modal');
        $this->showPaymentModal = false;
    }

    /**
     * Confirm enrollment (called from payment modal)
     */
    public function confirmEnrollment()
    {
        Log::info('CoursePreview: Confirming enrollment', [
            'course_id' => $this->course->id,
            'is_free' => $this->course->is_free
        ]);

        if (!$this->course->is_free && !$this->hasSufficientFunds) {
            Log::warning('CoursePreview: Insufficient funds');
            $this->dispatch('notify', [
                'message' => 'Insufficient wallet balance. Please fund your wallet.',
                'type' => 'error',
                'icon' => 'fas fa-wallet'
            ]);
            return;
        }

        $this->enroll();
    }

    /**
     * Main enrollment method
     */
    public function enroll()
    {
        Log::info('CoursePreview: Starting enrollment process', [
            'course_id' => $this->course->id,
            'user_id' => Auth::id()
        ]);

        if (!Auth::check()) {
            Log::info('CoursePreview: User not authenticated');
            return redirect()->route('login');
        }

        if ($this->isEnrolling) {
            Log::warning('CoursePreview: Enrollment already in progress');
            return;
        }

        // Track enrollment state
        $this->isEnrolling = true;
        $this->enrollingCourseIds[] = $this->course->id;

        try {
            // Check if already enrolled
            if ($this->isEnrolled) {
                Log::warning('CoursePreview: User already enrolled');
                $this->dispatch('notify', [
                    'message' => 'You are already enrolled in this course!',
                    'type' => 'info'
                ]);
                $this->isEnrolling = false;
                $this->enrollingCourseIds = array_diff($this->enrollingCourseIds, [$this->course->id]);
                return;
            }

            $user = Auth::user();

            DB::beginTransaction();
            Log::info('CoursePreview: Transaction started');

            // Process payment for paid courses
            if (!$this->course->is_free) {
                Log::info('CoursePreview: Processing payment', ['price' => $this->course->price]);
                
                $wallet = Wallet::getOrCreateWallet($user->id);
                
                if (!$wallet->hasSufficientBalance($this->course->price)) {
                    DB::rollBack();
                    Log::error('CoursePreview: Insufficient balance');
                    
                    $this->closePaymentModal();
                    $this->isEnrolling = false;
                    $this->enrollingCourseIds = array_diff($this->enrollingCourseIds, [$this->course->id]);
                    
                    $this->dispatch('notify', [
                        'message' => 'Insufficient wallet balance. Please fund your wallet.',
                        'type' => 'error',
                        'icon' => 'fas fa-wallet'
                    ]);
                    return;
                }

                // Debit user wallet
                $wallet->debit(
                    $this->course->price,
                    WalletTransaction::CATEGORY_COURSE_PURCHASE,
                    "Enrolled in course: {$this->course->title}",
                    $this->course,
                    [
                        'course_id' => $this->course->id,
                        'course_title' => $this->course->title,
                        'enrollment_type' => 'paid'
                    ]
                );
                Log::info('CoursePreview: User wallet debited');

                // Credit instructor
                $this->creditInstructor($this->course);
                Log::info('CoursePreview: Instructor credited');
            }

            // Create enrollment
            $enrollment = CourseEnrollment::create([
                'course_id' => $this->course->id,
                'user_id' => $user->id,
                'enrolled_at' => now(),
                'progress_percentage' => 0,
                'is_completed' => false,
                'enrollment_type' => $this->course->is_free ? 'free' : 'paid',
                'amount_paid' => $this->course->is_free ? 0 : $this->course->price
            ]);
            Log::info('CoursePreview: Enrollment created', ['enrollment_id' => $enrollment->id]);

            // Log activity
            $user->logCustomActivity('Enrolled in course: ' . $this->course->title, [
                'course_id' => $this->course->id,
                'course_title' => $this->course->title,
                'amount_paid' => $this->course->price,
                'enrollment_type' => $this->course->is_free ? 'free' : 'paid'
            ]);

            DB::commit();
            Log::info('CoursePreview: Transaction committed');

            // Update component state
            $this->isEnrolled = true;
            $this->totalEnrollments++;
            $this->closePaymentModal();

            // Send notification
            try {
                $user->notify(new CourseUpdateNotification($this->course));
            } catch (\Exception $e) {
                Log::error('CoursePreview: Failed to send notification', ['error' => $e->getMessage()]);
            }

            $message = $this->course->is_free
                ? "Successfully enrolled in '{$this->course->title}'! Welcome aboard!"
                : "Successfully enrolled in '{$this->course->title}'! ₦" . number_format($this->course->price, 2) . " has been deducted.";

            Log::info('CoursePreview: Enrollment successful, redirecting to course view');

            // Dispatch success notification
            $this->dispatch('notify', [
                'message' => $message,
                'type' => 'success'
            ]);

            // Redirect to course view
            $redirectUrl = route('course.view', ['course' => $this->course->slug]);
            Log::info('CoursePreview: Redirecting', ['url' => $redirectUrl]);
            
            // Use JavaScript redirect via dispatch for better compatibility
            $this->dispatch('enrollment-success', ['redirectUrl' => $redirectUrl]);
            
            // Also try Livewire redirect as fallback
            return $this->redirect($redirectUrl, navigate: true);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('CoursePreview: Enrollment failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('notify', [
                'message' => 'Enrollment failed: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        } finally {
            $this->isEnrolling = false;
            $this->enrollingCourseIds = array_diff($this->enrollingCourseIds, [$this->course->id]);
        }
    }

    /**
     * Drop/unenroll from course
     */
    public function dropCourse($courseId)
    {
        Log::info('CoursePreview: Dropping course', ['course_id' => $courseId]);
        
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $this->droppingCourseIds[] = $courseId;

        try {
            $enrollment = CourseEnrollment::where('course_id', $courseId)
                ->where('user_id', $user->id)
                ->firstOrFail();

            $course = $enrollment->course;
            Log::info('CoursePreview: Enrollment found', ['enrollment_id' => $enrollment->id]);

            // Check if eligible for refund
            $isRefundEligible = $enrollment->enrolled_at->diffInDays(now()) <= 7
                && $enrollment->progress_percentage < 10
                && !$course->is_free;

            Log::info('CoursePreview: Refund eligibility checked', ['is_eligible' => $isRefundEligible]);

            DB::transaction(function () use ($enrollment, $user, $course, $isRefundEligible) {
                // Process refund if eligible
                if ($isRefundEligible && $enrollment->amount_paid > 0) {
                    Log::info('CoursePreview: Processing refund', ['amount' => $enrollment->amount_paid]);
                    
                    $wallet = Wallet::getOrCreateWallet($user->id);
                    $wallet->credit(
                        $enrollment->amount_paid,
                        WalletTransaction::CATEGORY_REFUND,
                        "Refund for course: {$course->title}",
                        $course,
                        [
                            'course_id' => $course->id,
                            'original_amount' => $enrollment->amount_paid
                        ]
                    );
                    Log::info('CoursePreview: Refund processed successfully');
                }

                // Store progress before deletion
                Cache::put("user_{$user->id}_course_{$course->id}_progress", [
                    'progress_percentage' => $enrollment->progress_percentage,
                    'dropped_at' => now(),
                    'was_refunded' => $isRefundEligible
                ], now()->addMonths(6));

                $enrollment->delete();
                Log::info('CoursePreview: Enrollment deleted');

                $user->logCustomActivity('Dropped course: ' . $course->title, [
                    'course_id' => $course->id,
                    'course_title' => $course->title,
                    'progress_lost' => $enrollment->progress_percentage,
                    'refunded' => $isRefundEligible
                ]);
            });

            // Update component state
            $this->isEnrolled = false;
            $this->enrollmentProgress = 0;
            $this->totalEnrollments = max(0, $this->totalEnrollments - 1);

            $message = $isRefundEligible
                ? "You have been unenrolled from '{$course->title}' and ₦" . number_format($enrollment->amount_paid, 2) . " has been refunded to your wallet."
                : "You have been unenrolled from '{$course->title}'. Your progress has been saved for 6 months.";

            Log::info('CoursePreview: Course dropped successfully', ['message' => $message]);

            $this->dispatch('notify', [
                'message' => $message,
                'type' => 'warning',
                'icon' => 'fas fa-sign-out-alt'
            ]);

        } catch (\Exception $e) {
            Log::error('CoursePreview: Failed to drop course', [
                'course_id' => $courseId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $this->dispatch('notify', [
                'message' => 'Failed to drop course: ' . $e->getMessage(),
                'type' => 'error',
                'icon' => 'fas fa-exclamation-triangle'
            ]);
        } finally {
            $this->droppingCourseIds = array_diff($this->droppingCourseIds, [$courseId]);
        }
    }
    
    /**
     * Credit instructor wallet
     */
    private function creditInstructor(Course $course): void
    {
        Log::info('CoursePreview: Crediting instructor', [
            'instructor_id' => $course->instructor_id,
            'course_price' => $course->price
        ]);

        try {
            $instructorWallet = Wallet::getOrCreateWallet($course->instructor_id, Wallet::TYPE_INSTRUCTOR);
            $instructorShare = $course->calculateInstructorShare($course->price);

            $instructorWallet->credit(
                $instructorShare,
                WalletTransaction::CATEGORY_INSTRUCTOR_EARNING,
                "Earnings from course enrollment: {$course->title}",
                $course,
                [
                    'course_id' => $course->id,
                    'course_title' => $course->title,
                    'total_amount' => $course->price,
                    'instructor_share' => $instructorShare
                ]
            );

            // Credit platform
            $platformShare = $course->price - $instructorShare;
            if ($platformShare > 0) {
                $platformWallet = Wallet::firstOrCreate(
                    ['wallet_type' => Wallet::TYPE_PLATFORM, 'user_id' => 1],
                    ['currency' => 'NGN', 'is_active' => true]
                );

                $platformWallet->credit(
                    $platformShare,
                    'platform_commission',
                    "Platform commission from course: {$course->title}",
                    $course,
                    [
                        'course_id' => $course->id,
                        'instructor_id' => $course->instructor_id,
                        'total_amount' => $course->price
                    ]
                );
            }

            Log::info('CoursePreview: Instructor and platform credited successfully');
        } catch (\Exception $e) {
            Log::error('CoursePreview: Failed to credit instructor', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function toggleFavorite()
    {
        Log::info('CoursePreview: Toggling favorite', ['course_id' => $this->course->id]);

        if (!Auth::check()) {
            return redirect()->route('login');
        }

        try {
            $user = Auth::user();

            if ($user->isCourseFavorited($this->course->id)) {
                $user->removeFavoriteCourse($this->course->id);
                $message = 'Course removed from favorites';
                $this->isFavorited = false;
            } else {
                $user->addFavoriteCourse($this->course->id);
                $message = 'Course added to favorites';
                $this->isFavorited = true;
            }

            Log::info('CoursePreview: Favorite toggled', ['is_favorited' => $this->isFavorited]);

            $this->dispatch('notify', [
                'message' => $message,
                'type' => 'success'
            ]);

        } catch (\Exception $e) {
            Log::error('CoursePreview: Failed to toggle favorite', ['error' => $e->getMessage()]);
            $this->dispatch('notify', [
                'message' => 'Failed to update favorites',
                'type' => 'error'
            ]);
        }
    }

    public function getNextLesson()
    {
        if (!$this->isEnrolled) {
            return null;
        }

        foreach ($this->course->sections as $section) {
            foreach ($section->lessons as $lesson) {
                return $lesson;
            }
        }

        return null;
    }

    public function submitReview()
    {
        Log::info('CoursePreview: Submitting review');

        $this->validate([
            'reviewRating' => 'required|integer|between:1,5',
            'reviewComment' => 'required|string|min:10|max:1000'
        ]);

        try {
            if ($this->userReview) {
                $this->userReview->update([
                    'rating' => $this->reviewRating,
                    'review_text' => $this->reviewComment
                ]);
            } else {
                $this->course->reviews()->create([
                    'user_id' => Auth::id(),
                    'rating' => $this->reviewRating,
                    'review_text' => $this->reviewComment
                ]);
            }

            $this->showReviewModal = false;
            $this->loadUserReview();
            $this->calculateStatistics();

            Log::info('CoursePreview: Review submitted successfully');

            $this->dispatch('notify', [
                'message' => 'Review submitted successfully!',
                'type' => 'success'
            ]);

        } catch (\Exception $e) {
            Log::error('CoursePreview: Failed to submit review', ['error' => $e->getMessage()]);
            $this->dispatch('notify', [
                'message' => 'Failed to submit review. Please try again.',
                'type' => 'error'
            ]);
        }
    }

    public function setActiveTab($tab)
    {
        Log::info('CoursePreview: Changing tab', ['tab' => $tab]);
        $this->activeTab = $tab;
    }

    #[On('courseEnrolled')]
    public function refreshEnrollmentStatus()
    {
        Log::info('CoursePreview: Refreshing enrollment status');
        $this->checkEnrollmentStatus();
        $this->calculateStatistics();
    }

    #[On('reviewSubmitted')]
    public function refreshReviews()
    {
        Log::info('CoursePreview: Refreshing reviews');
        $this->course->refresh();
        $this->calculateStatistics();
        $this->loadUserReview();
    }

    public function getFormattedDurationProperty()
    {
        if ($this->totalDuration < 60) {
            return $this->totalDuration . 'm';
        }

        $hours = floor($this->totalDuration / 60);
        $minutes = $this->totalDuration % 60;

        return $hours . 'h' . ($minutes > 0 ? ' ' . $minutes . 'm' : '');
    }

    public function canEnroll()
    {
        $canEnroll = Auth::check() &&
            !$this->isEnrolled &&
            $this->course->is_published &&
            $this->course->is_approved;

        Log::info('CoursePreview: Checking enrollment eligibility', ['can_enroll' => $canEnroll]);
        return $canEnroll;
    }

    public function canReview()
    {
        return Auth::check() &&
            $this->isEnrolled &&
            $this->enrollmentProgress >= 25;
    }

    public function shareCourse($platform)
    {
        Log::info('CoursePreview: Sharing course', ['platform' => $platform]);

        $courseUrl = route('courses.preview', $this->course);
        $message = "Check out this course: {$this->course->title}";

        $shareUrls = [
            'twitter' => "https://twitter.com/intent/tweet?text=" . urlencode($message . ' ' . $courseUrl),
            'facebook' => "https://www.facebook.com/sharer/sharer.php?u=" . urlencode($courseUrl),
            'linkedin' => "https://www.linkedin.com/sharing/share-offsite/?url=" . urlencode($courseUrl),
            'whatsapp' => "https://wa.me/?text=" . urlencode($message . ' ' . $courseUrl),
        ];

        if (isset($shareUrls[$platform])) {
            return redirect()->away($shareUrls[$platform]);
        }

        $this->dispatch('notify', [
            'message' => 'Course link copied to clipboard!',
            'type' => 'success'
        ]);
    }

    public function getIsFavoritedProperty()
    {
        if (!Auth::check()) {
            return false;
        }

        return Auth::user()->favoriteCourses()->where('course_id', $this->course->id)->exists();
    }

    public function render()
    {
        return view('livewire.course-management.course-preview');
    }
}