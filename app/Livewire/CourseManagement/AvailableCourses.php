<?php

namespace App\Livewire\CourseManagement;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Learning\Course;
use App\Models\Learning\CourseCategory;
use App\Models\Learning\CourseEnrollment;
use App\Models\Marketplace\Wallet;
use App\Models\Marketplace\WalletTransaction;
use App\Notifications\CourseUpdateNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;

#[Layout('layouts.dashboard', ['title' => 'Available Courses', 'description' => 'Browse and enroll in available courses', 'icon' => 'fas fa-book-open', 'active' => 'courses.available'])]
class AvailableCourses extends Component
{
    use WithPagination;

    public string $search = '';
    public string $categoryFilter = '';
    public string $difficultyFilter = '';
    public string $sortBy = 'latest';
    public bool $showOnlyFree = false;
    public bool $showOnlyWithCertificate = false;
    protected int $perPage = 9;

    // Enrollment states
    public array $enrollingCourseIds = [];
    public array $droppingCourseIds = [];
    public array $wishlistingCourseIds = [];

    // Payment Modal
    public bool $showPaymentModal = false;
    public ?Course $selectedCourse = null;
    public float $walletBalance = 0;
    public float $coursePrice = 0;
    public float $balanceAfterPayment = 0;
    public bool $hasSufficientFunds = false;

    // Statistics
    public int $totalAvailable = 0;
    public int $totalEnrolled = 0;
    public int $totalCompleted = 0;

    public function mount()
    {
        Log::info('AvailableCourses: Component mounted');
        $this->updateStatistics();
        $this->loadWalletBalance();
    }

    public function loadWalletBalance()
    {
        try {
            $user = Auth::user();
            $wallet = Wallet::getOrCreateWallet($user->id);
            $this->walletBalance = $wallet->balance;
            Log::info('Wallet balance loaded', ['balance' => $this->walletBalance]);
        } catch (\Exception $e) {
            Log::error('Failed to load wallet balance', ['error' => $e->getMessage()]);
            $this->walletBalance = 0;
        }
    }

    public function updateStatistics()
    {
        try {
            $user = Auth::user();

            $this->totalAvailable = Course::where('is_published', true)
                ->where('is_approved', true)
                ->count();

            $this->totalEnrolled = $user->enrollments()->count();

            $this->totalCompleted = $user->enrollments()
                ->where('is_completed', true)
                ->count();

            Log::info('Statistics updated', [
                'available' => $this->totalAvailable,
                'enrolled' => $this->totalEnrolled,
                'completed' => $this->totalCompleted
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update statistics', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Open payment confirmation modal
     */
    public function openPaymentModal(int $courseId): void
    {
        Log::info('Opening payment modal', ['course_id' => $courseId]);
        
        try {
            $this->selectedCourse = Course::where('id', $courseId)
                ->where('is_published', true)
                ->where('is_approved', true)
                ->firstOrFail();

            Log::info('Course loaded', [
                'course_id' => $this->selectedCourse->id,
                'title' => $this->selectedCourse->title,
                'is_free' => $this->selectedCourse->is_free,
                'price' => $this->selectedCourse->price
            ]);

            // Check if already enrolled
            if ($this->isEnrolled($courseId)) {
                Log::warning('User already enrolled', ['course_id' => $courseId]);
                $this->dispatch('notify', [
                    'message' => 'You are already enrolled in this course!',
                    'type' => 'info',
                    'icon' => 'fas fa-info-circle'
                ]);
                return;
            }

            // Check if course is free
            if ($this->selectedCourse->is_free) {
                Log::info('Free course detected, processing enrollment directly');
                // Directly enroll for free courses
                $this->confirmEnrollment();
                return;
            }

            // Load wallet balance for paid courses
            $this->loadWalletBalance();
            $this->coursePrice = $this->selectedCourse->price;
            $this->balanceAfterPayment = $this->walletBalance - $this->coursePrice;
            $this->hasSufficientFunds = $this->balanceAfterPayment >= 0;

            Log::info('Payment modal prepared', [
                'wallet_balance' => $this->walletBalance,
                'course_price' => $this->coursePrice,
                'balance_after' => $this->balanceAfterPayment,
                'sufficient_funds' => $this->hasSufficientFunds
            ]);

            $this->showPaymentModal = true;

        } catch (\Exception $e) {
            Log::error('Failed to open payment modal', [
                'course_id' => $courseId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            $this->dispatch('notify', [
                'message' => 'Course not found or unavailable',
                'type' => 'error',
                'icon' => 'fas fa-exclamation-triangle'
            ]);
        }
    }

    /**
     * Close payment modal
     */
    public function closePaymentModal(): void
    {
        Log::info('Closing payment modal');
        $this->showPaymentModal = false;
        $this->selectedCourse = null;
        $this->coursePrice = 0;
        $this->balanceAfterPayment = 0;
        $this->hasSufficientFunds = false;
    }

    /**
     * Confirm enrollment with payment
     */
    public function confirmEnrollment()
    {
        Log::info('Confirming enrollment', [
            'course_id' => $this->selectedCourse?->id,
            'course_title' => $this->selectedCourse?->title
        ]);

        if (!$this->selectedCourse) {
            Log::error('No course selected for enrollment');
            $this->dispatch('notify', [
                'message' => 'Invalid course selection',
                'type' => 'error'
            ]);
            return;
        }

        $user = Auth::user();
        $courseId = $this->selectedCourse->id;
        $this->enrollingCourseIds[] = $courseId;

        try {
            DB::beginTransaction();
            Log::info('Starting enrollment transaction');

            // Get or create wallet
            $wallet = Wallet::getOrCreateWallet($user->id);
            Log::info('Wallet retrieved', ['wallet_id' => $wallet->id, 'balance' => $wallet->balance]);

            // For paid courses, process payment
            if (!$this->selectedCourse->is_free) {
                Log::info('Processing payment for paid course', [
                    'course_price' => $this->selectedCourse->price,
                    'wallet_balance' => $wallet->balance
                ]);

                // Verify sufficient funds
                if (!$wallet->hasSufficientBalance($this->selectedCourse->price)) {
                    Log::warning('Insufficient wallet balance', [
                        'required' => $this->selectedCourse->price,
                        'available' => $wallet->balance
                    ]);
                    
                    DB::rollBack();
                    $this->closePaymentModal();

                    $this->dispatch('notify', [
                        'message' => 'Insufficient wallet balance. Please fund your wallet.',
                        'type' => 'error',
                        'icon' => 'fas fa-wallet'
                    ]);

                    $this->enrollingCourseIds = array_diff($this->enrollingCourseIds, [$courseId]);
                    return;
                }

                // Debit user wallet
                Log::info('Debiting user wallet');
                $wallet->debit(
                    $this->selectedCourse->price,
                    WalletTransaction::CATEGORY_COURSE_PURCHASE,
                    "Enrolled in course: {$this->selectedCourse->title}",
                    $this->selectedCourse,
                    [
                        'course_id' => $this->selectedCourse->id,
                        'course_title' => $this->selectedCourse->title,
                        'enrollment_type' => 'paid'
                    ]
                );
                Log::info('User wallet debited successfully');

                // Credit instructor wallet
                Log::info('Crediting instructor wallet');
                $this->creditInstructor($this->selectedCourse);
                Log::info('Instructor wallet credited successfully');
            }

            // Create enrollment
            Log::info('Creating enrollment record');
            $enrollment = CourseEnrollment::create([
                'course_id' => $this->selectedCourse->id,
                'user_id' => $user->id,
                'enrolled_at' => now(),
                'progress_percentage' => 0,
                'is_completed' => false,
                'enrollment_type' => $this->selectedCourse->is_free ? 'free' : 'paid',
                'amount_paid' => $this->selectedCourse->is_free ? 0 : $this->selectedCourse->price
            ]);
            Log::info('Enrollment created', ['enrollment_id' => $enrollment->id]);

            // Log activity
            $user->logCustomActivity('Enrolled in course: ' . $this->selectedCourse->title, [
                'course_id' => $this->selectedCourse->id,
                'course_title' => $this->selectedCourse->title,
                'amount_paid' => $this->selectedCourse->price,
                'enrollment_type' => $this->selectedCourse->is_free ? 'free' : 'paid'
            ]);

            DB::commit();
            Log::info('Enrollment transaction committed successfully');

            // Send notification
            try {
                $user->notify(new CourseUpdateNotification($this->selectedCourse));
                Log::info('Enrollment notification sent');
            } catch (\Exception $e) {
                Log::error('Failed to send notification', ['error' => $e->getMessage()]);
            }

            // Prepare success message
            $message = $this->selectedCourse->is_free
                ? "Successfully enrolled in '{$this->selectedCourse->title}'! Welcome aboard!"
                : "Successfully enrolled in '{$this->selectedCourse->title}'! ₦" . number_format($this->selectedCourse->price, 2) . " has been deducted from your wallet.";

            Log::info('Enrollment successful', ['message' => $message]);

            // Update statistics
            $this->updateStatistics();
            $this->loadWalletBalance();

            // Close modal
            $this->closePaymentModal();

            // Store redirect URL in session for JavaScript to handle
            session()->flash('enrollment_redirect', route('course.view', ['course' => $this->selectedCourse->slug]));
            
            // Dispatch success notification with redirect
            $this->dispatch('notify', [
                'message' => $message,
                'type' => 'success'
            ]);

            $redirectUrl = route('course.view', ['course' => $this->selectedCourse->slug]);
            Log::info('Redirecting to course view', ['url' => $redirectUrl]);
            
            // Use JavaScript redirect via dispatch for better compatibility
            $this->dispatch('enrollment-success', ['redirectUrl' => $redirectUrl]);
            
            // Also try Livewire redirect as fallback
            return $this->redirect($redirectUrl, navigate: true);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Enrollment failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->dispatch('notify', [
                'message' => 'Enrollment failed: ' . $e->getMessage(),
                'type' => 'error',
                'icon' => 'fas fa-exclamation-triangle'
            ]);
        } finally {
            $this->enrollingCourseIds = array_diff($this->enrollingCourseIds, [$courseId]);
        }
    }

    /**
     * Credit instructor wallet with revenue split
     */
    private function creditInstructor(Course $course): void
    {
        Log::info('Starting instructor credit', [
            'course_id' => $course->id,
            'instructor_id' => $course->instructor_id,
            'course_price' => $course->price
        ]);

        try {
            // Get instructor wallet
            $instructorWallet = Wallet::getOrCreateWallet($course->instructor_id, Wallet::TYPE_INSTRUCTOR);
            Log::info('Instructor wallet retrieved', ['wallet_id' => $instructorWallet->id]);

            // Calculate instructor share (70% default)
            $instructorShare = $course->calculateInstructorShare($course->price);
            Log::info('Instructor share calculated', ['share' => $instructorShare]);

            // Credit instructor
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
            Log::info('Instructor credited successfully');

            // Credit platform wallet with remaining amount
            $platformShare = $course->price - $instructorShare;
            Log::info('Platform share calculated', ['share' => $platformShare]);

            if ($platformShare > 0) {
                $platformWallet = Wallet::firstOrCreate(
                    ['wallet_type' => Wallet::TYPE_PLATFORM, 'user_id' => 1],
                    ['currency' => 'NGN', 'is_active' => true]
                );
                Log::info('Platform wallet retrieved', ['wallet_id' => $platformWallet->id]);

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
                Log::info('Platform credited successfully');
            }
        } catch (\Exception $e) {
            Log::error('Failed to credit instructor/platform', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Legacy enroll method - redirects to payment modal
     */
    public function enroll(int $courseId): void
    {
        Log::info('Enroll method called (legacy)', ['course_id' => $courseId]);
        $this->openPaymentModal($courseId);
    }

    /**
     * Drop/unenroll from a course with refund (if within refund period)
     */
    public function dropCourse(int $courseId): void
    {
        Log::info('Dropping course', ['course_id' => $courseId]);
        
        $user = Auth::user();
        $this->droppingCourseIds[] = $courseId;

        try {
            $enrollment = CourseEnrollment::where('course_id', $courseId)
                ->where('user_id', $user->id)
                ->firstOrFail();

            $course = $enrollment->course;
            Log::info('Enrollment found', ['enrollment_id' => $enrollment->id]);

            // Check if eligible for refund
            $isRefundEligible = $enrollment->enrolled_at->diffInDays(now()) <= 7
                && $enrollment->progress_percentage < 10
                && !$course->is_free;

            Log::info('Refund eligibility checked', ['is_eligible' => $isRefundEligible]);

            DB::transaction(function () use ($enrollment, $user, $course, $isRefundEligible) {
                // Process refund if eligible
                if ($isRefundEligible && $enrollment->amount_paid > 0) {
                    Log::info('Processing refund', ['amount' => $enrollment->amount_paid]);
                    
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
                    Log::info('Refund processed successfully');
                }

                // Store progress before deletion
                Cache::put("user_{$user->id}_course_{$course->id}_progress", [
                    'progress_percentage' => $enrollment->progress_percentage,
                    'dropped_at' => now(),
                    'was_refunded' => $isRefundEligible
                ], now()->addMonths(6));

                $enrollment->delete();
                Log::info('Enrollment deleted');

                $user->logCustomActivity('Dropped course: ' . $course->title, [
                    'course_id' => $course->id,
                    'course_title' => $course->title,
                    'progress_lost' => $enrollment->progress_percentage,
                    'refunded' => $isRefundEligible
                ]);
            });

            $this->updateStatistics();
            $this->loadWalletBalance();
            $this->dispatch('enrollment-updated');

            $message = $isRefundEligible
                ? "You have been unenrolled from '{$course->title}' and ₦" . number_format($enrollment->amount_paid, 2) . " has been refunded to your wallet."
                : "You have been unenrolled from '{$course->title}'. Your progress has been saved for 6 months.";

            Log::info('Course dropped successfully', ['message' => $message]);

            $this->dispatch('notify', [
                'message' => $message,
                'type' => 'warning',
                'icon' => 'fas fa-sign-out-alt'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to drop course', [
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
     * Toggle wishlist
     */
    public function toggleWishlist(int $courseId): void
    {
        Log::info('Toggling wishlist', ['course_id' => $courseId]);
        
        $user = Auth::user();
        $this->wishlistingCourseIds[] = $courseId;

        try {
            $wishlist = $user->wishlists()->where('course_id', $courseId)->first();

            if ($wishlist) {
                $wishlist->delete();
                $message = 'Removed from wishlist';
                $icon = 'fas fa-heart-broken';
                Log::info('Removed from wishlist');
            } else {
                $user->wishlists()->create(['course_id' => $courseId]);
                $message = 'Added to wishlist';
                $icon = 'fas fa-heart';
                Log::info('Added to wishlist');
            }

            $this->dispatch('notify', [
                'message' => $message,
                'type' => 'info',
                'icon' => $icon
            ]);

        } catch (\Exception $e) {
            Log::error('Wishlist action failed', [
                'course_id' => $courseId,
                'error' => $e->getMessage()
            ]);
            
            $this->dispatch('notify', [
                'message' => 'Wishlist action failed',
                'type' => 'error',
                'icon' => 'fas fa-exclamation-triangle'
            ]);
        } finally {
            $this->wishlistingCourseIds = array_diff($this->wishlistingCourseIds, [$courseId]);
        }
    }

    /**
     * Check if user is enrolled in course
     */
    public function isEnrolled(int $courseId): bool
    {
        $isEnrolled = Auth::user()->enrollments()->where('course_id', $courseId)->exists();
        Log::info('Checking enrollment status', ['course_id' => $courseId, 'is_enrolled' => $isEnrolled]);
        return $isEnrolled;
    }

    /**
     * Check if course is in wishlist
     */
    public function isWishlisted(int $courseId): bool
    {
        return Auth::user()->wishlists()->where('course_id', $courseId)->exists();
    }

    /**
     * Get user's progress for a course
     */
    public function getCourseProgress(int $courseId): int
    {
        $enrollment = Auth::user()->enrollments()->where('course_id', $courseId)->first();
        return $enrollment ? $enrollment->progress_percentage : 0;
    }

    /**
     * Reset all filters
     */
    public function resetFilters(): void
    {
        Log::info('Resetting filters');
        $this->search = '';
        $this->categoryFilter = '';
        $this->difficultyFilter = '';
        $this->sortBy = 'latest';
        $this->showOnlyFree = false;
        $this->showOnlyWithCertificate = false;
        $this->resetPage();
    }

    /**
     * Update pagination when filters change
     */
    public function updating($key): void
    {
        if (in_array($key, ['search', 'categoryFilter', 'difficultyFilter', 'sortBy', 'showOnlyFree', 'showOnlyWithCertificate'])) {
            $this->resetPage();
        }
    }

    /**
     * Listen for enrollment updates
     */
    #[On('enrollment-updated')]
    public function refreshEnrollments(): void
    {
        Log::info('Refreshing enrollments');
        $this->updateStatistics();
        $this->loadWalletBalance();
    }

    /**
     * Get cached categories
     */
    #[Computed]
    public function categories()
    {
        return Cache::remember('course_categories_active', 3600, function () {
            return CourseCategory::whereHas('courses', function ($query) {
                $query->where('is_published', true)->where('is_approved', true);
            })->orderBy('name')->get();
        });
    }

    public function previewCourse(int $courseId): void
    {
        Log::info('Previewing course', ['course_id' => $courseId]);
        $course = Course::findOrFail($courseId);
        $this->redirect(route('courses.preview', $course));
    }

    public function render()
    {
        $courses = Course::where('is_published', true)
            ->where('is_approved', true)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', '%' . $this->search . '%')
                        ->orWhere('description', 'like', '%' . $this->search . '%')
                        ->orWhereHas('instructor', function ($subq) {
                            $subq->where('name', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->when($this->categoryFilter, fn($query) => $query->where('category_id', $this->categoryFilter))
            ->when($this->difficultyFilter, fn($query) => $query->where('difficulty_level', $this->difficultyFilter))
            ->when($this->showOnlyFree, fn($query) => $query->where('is_free', true))
            ->when($this->showOnlyWithCertificate, fn($query) => $query->whereNotNull('certificate_template'))
            ->with([
                'category',
                'instructor',
                'enrollments' => function ($query) {
                    $query->where('user_id', Auth::id());
                }
            ])
            ->withAvg('reviews as average_rating', 'rating')
            ->withCount(['reviews as rating_count', 'enrollments as total_enrollments'])
            ->when($this->sortBy === 'latest', fn($query) => $query->latest())
            ->when($this->sortBy === 'popular', fn($query) => $query->orderBy('total_enrollments', 'desc'))
            ->when($this->sortBy === 'rating', fn($query) => $query->orderBy('average_rating', 'desc'))
            ->when($this->sortBy === 'title', fn($query) => $query->orderBy('title'))
            ->select([
                'id',
                'title',
                'description',
                'thumbnail',
                'difficulty_level',
                'category_id',
                'instructor_id',
                'slug',
                'is_free',
                'price',
                'certificate_template',
                'estimated_duration_minutes'
            ])
            ->paginate($this->perPage);

        return view('livewire.course-management.available-courses', [
            'courses' => $courses,
            'categories' => $this->categories,
            'totalAvailable' => $this->totalAvailable,
            'totalEnrolled' => $this->totalEnrolled,
            'totalCompleted' => $this->totalCompleted
        ]);
    }
}