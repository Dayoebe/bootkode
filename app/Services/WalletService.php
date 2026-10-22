<?php

// WalletService.php - Business Logic Layer

namespace App\Services;

use App\Models\Core\User;
use App\Models\Learning\Course;
use App\Models\Marketplace\Wallet;
use App\Models\Marketplace\WalletTransaction;
use App\Models\Marketplace\Withdrawal;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WalletService
{
    private PaystackService $paystackService;

    public function __construct(PaystackService $paystackService)
    {
        $this->paystackService = $paystackService;
    }

    /**
     * Initialize wallet funding
     */
    public function initiateFunding(User $user, float $amount): array
    {
        try {
            $wallet = $user->getOrCreateWallet();

            $data = [
                'user_id' => $user->id,
                'wallet_id' => $wallet->id,
                'email' => $user->email,
                'amount' => $amount,
                'customer_name' => $user->name,
            ];

            return $this->paystackService->initializeWalletFunding($data);

        } catch (\Exception $e) {
            Log::error('Wallet funding initiation error: '.$e->getMessage());

            return [
                'success' => false,
                'message' => 'Failed to initiate wallet funding',
            ];
        }
    }

    /**
     * Process course purchase from wallet
     */
    public function purchaseCourse(User $student, Course $course): array
    {
        try {
            return DB::transaction(function () use ($student, $course) {
                // Serialize enrollments for this learner, including the first enrollment.
                User::whereKey($student->id)->lockForUpdate()->firstOrFail();
                $course = Course::whereKey($course->id)->publiclyAvailable()->lockForUpdate()->firstOrFail();
                $existing = $student->enrollments()->where('course_id', $course->id)->exists()
                    || $student->courses()->where('courses.id', $course->id)->exists();
                if ($existing) {
                    return ['success' => true, 'message' => 'You are already enrolled in this course'];
                }
                if ((float) $course->price < 0) {
                    throw new \DomainException('Invalid course price');
                }
                $wallet = $student->getOrCreateWallet();
                $wallet = Wallet::whereKey($wallet->id)->lockForUpdate()->firstOrFail();
                if (! $wallet->is_active || $wallet->currency !== 'NGN') {
                    throw new \DomainException('Wallet is unavailable');
                }
                if (! $wallet->hasSufficientBalance((float) $course->price)) {
                    return ['success' => false, 'message' => 'Insufficient wallet balance'];
                }
                $splitAmounts = (float) $course->price > 0 ? $course->processPurchase($student, $wallet) : [];
                $student->courses()->syncWithoutDetaching([
                    $course->id => ['progress' => 0, 'last_accessed_at' => now()],
                ]);
                $student->enrollments()->create([
                    'course_id' => $course->id, 'enrolled_at' => now(),
                    'progress_percentage' => 0, 'is_completed' => false,
                    'enrollment_type' => (float) $course->price > 0 ? 'paid' : 'free',
                    'amount_paid' => $course->price,
                ]);

                return ['success' => true, 'message' => 'Course enrollment completed',
                    'split_amounts' => $splitAmounts, 'remaining_balance' => (float) $wallet->fresh()->balance];
            });
        } catch (\Throwable $e) {
            Log::error('Course purchase failed', ['exception' => $e::class]);

            return ['success' => false, 'message' => 'Unable to enroll. Please try again or contact support.'];
        }
    }

    /**
     * Request withdrawal for instructor
     */
    public function requestWithdrawal(User $instructor, float $amount, array $bankDetails): array
    {
        try {
            return DB::transaction(function () use ($instructor, $amount, $bankDetails) {
                if (! is_finite($amount) || $amount < config('financial.minimum_withdrawal') || $amount > config('financial.maximum_withdrawal')) {
                    throw new \InvalidArgumentException('Withdrawal amount is outside the permitted limits.');
                }
                $instructorWallet = $instructor->getOrCreateInstructorWallet();
                $instructorWallet = Wallet::whereKey($instructorWallet->id)->lockForUpdate()->firstOrFail();

                if (! $instructorWallet->hasSufficientBalance($amount)) {
                    return [
                        'success' => false,
                        'message' => 'Insufficient balance for withdrawal',
                    ];
                }

                // Resolve account details with Paystack
                $accountResolution = $this->paystackService->resolveAccountNumber(
                    $bankDetails['account_number'],
                    $bankDetails['bank_code']
                );

                if (! $accountResolution['success']) {
                    return [
                        'success' => false,
                        'message' => 'Invalid bank account details',
                    ];
                }

                // Debit wallet (put amount in pending)
                $instructorWallet->debit(
                    $amount,
                    WalletTransaction::CATEGORY_WITHDRAWAL,
                    'Withdrawal request',
                    null,
                    ['status' => 'pending']
                );

                // Create withdrawal record
                $withdrawal = Withdrawal::create([
                    'user_id' => $instructor->id,
                    'wallet_id' => $instructorWallet->id,
                    'amount' => $amount,
                    'bank_code' => $bankDetails['bank_code'],
                    'account_number' => $bankDetails['account_number'],
                    'account_name' => $accountResolution['account_name'],
                    'status' => Withdrawal::STATUS_PENDING,
                ]);

                app(CommercialReadinessService::class)->recordPayoutAudit(
                    $withdrawal,
                    'withdrawal_requested',
                    null,
                    Withdrawal::STATUS_PENDING,
                    $instructor,
                    ['bank_code' => $bankDetails['bank_code']],
                    'Instructor requested a payout.'
                );

                return [
                    'success' => true,
                    'message' => 'Withdrawal request submitted successfully',
                    'withdrawal' => $withdrawal,
                ];

            });
        } catch (\Throwable $e) {
            Log::error('Withdrawal request error: '.$e->getMessage());

            return [
                'success' => false,
                'message' => 'Failed to process withdrawal request',
            ];
        }
    }

    /**
     * Process withdrawal (Admin action)
     */
    public function processWithdrawal(Withdrawal $withdrawal, User $admin): array
    {
        try {
            return DB::transaction(function () use ($withdrawal, $admin) {
                abort_unless($admin->hasAnyRole([User::ROLE_SUPER_ADMIN, User::ROLE_ACADEMY_ADMIN]), 403);
                $withdrawal = Withdrawal::whereKey($withdrawal->id)->lockForUpdate()->firstOrFail();
                if (! $withdrawal->canBeApproved()) {
                    return [
                        'success' => false,
                        'message' => 'Withdrawal cannot be processed',
                    ];
                }

                // Approve withdrawal
                $previousStatus = $withdrawal->status;
                $withdrawal->approve($admin->id);

                app(CommercialReadinessService::class)->recordPayoutAudit(
                    $withdrawal->fresh(),
                    'withdrawal_approved',
                    $previousStatus,
                    Withdrawal::STATUS_APPROVED,
                    $admin,
                    [],
                    'Admin approved payout for transfer processing.'
                );

                // Initiate Paystack transfer
                $transferResult = $this->paystackService->initiateTransfer($withdrawal);

                if (! $transferResult['success']) {
                    // If transfer fails, reject the withdrawal
                    $failedFrom = $withdrawal->status;
                    $withdrawal->update(['status' => Withdrawal::STATUS_FAILED]);

                    app(CommercialReadinessService::class)->recordPayoutAudit(
                        $withdrawal->fresh(),
                        'withdrawal_transfer_failed',
                        $failedFrom,
                        Withdrawal::STATUS_FAILED,
                        $admin,
                        ['paystack_result' => $transferResult],
                        $transferResult['message'] ?? 'Transfer initiation failed.'
                    );

                    return [
                        'success' => false,
                        'message' => $transferResult['message'],
                    ];
                }

                app(CommercialReadinessService::class)->recordPayoutAudit(
                    $withdrawal->fresh(),
                    'withdrawal_transfer_started',
                    Withdrawal::STATUS_APPROVED,
                    Withdrawal::STATUS_PROCESSING,
                    $admin,
                    ['transfer_code' => $transferResult['transfer_code'] ?? null],
                    'Paystack transfer was initiated.'
                );

                return [
                    'success' => true,
                    'message' => 'Withdrawal processed successfully',
                    'transfer_code' => $transferResult['transfer_code'],
                ];

            });
        } catch (\Throwable $e) {
            Log::error('Withdrawal processing error: '.$e->getMessage());

            return [
                'success' => false,
                'message' => 'Failed to process withdrawal',
            ];
        }
    }

    /**
     * Get wallet statistics
     */
    public function getWalletStats(User $user): array
    {
        $userWallet = $user->wallet;
        $instructorWallet = $user->instructorWallet;

        $userBalance = $userWallet?->balance ?? 0;
        $instructorBalance = $instructorWallet?->balance ?? 0;

        // Get recent transactions
        $recentTransactions = collect();
        if ($userWallet) {
            $recentTransactions = $recentTransactions->merge(
                $userWallet->transactions()->limit(5)->get()
            );
        }
        if ($instructorWallet) {
            $recentTransactions = $recentTransactions->merge(
                $instructorWallet->transactions()->limit(5)->get()
            );
        }

        $recentTransactions = $recentTransactions->sortByDesc('created_at')->take(10);

        // Get pending withdrawals
        $pendingWithdrawals = $user->withdrawals()
            ->where('status', Withdrawal::STATUS_PENDING)
            ->sum('amount');

        return [
            'user_balance' => $userBalance,
            'instructor_balance' => $instructorBalance,
            'total_balance' => $userBalance + $instructorBalance,
            'pending_withdrawals' => $pendingWithdrawals,
            'recent_transactions' => $recentTransactions,
            'formatted_user_balance' => '₦'.number_format($userBalance, 2),
            'formatted_instructor_balance' => '₦'.number_format($instructorBalance, 2),
            'formatted_total_balance' => '₦'.number_format($userBalance + $instructorBalance, 2),
        ];
    }

    /**
     * Get platform revenue analytics
     */
    public function getRevenueAnalytics(?\DatePeriod $period = null): array
    {
        $period = $period ?? new \DatePeriod(
            new \DateTime('-30 days'),
            new \DateInterval('P1D'),
            new \DateTime
        );

        $startDate = $period->getStartDate();
        $endDate = $period->getEndDate();

        $courseType = (new Course)->getMorphClass();
        $courseSales = WalletTransaction::where('category', WalletTransaction::CATEGORY_COURSE_PURCHASE)->where('type', 'debit')->where('status', 'completed')
            ->where('transactionable_type', $courseType)->whereBetween('created_at', [$startDate, $endDate])->sum('amount');
        $instructorEarnings = WalletTransaction::where('category', WalletTransaction::CATEGORY_INSTRUCTOR_EARNING)->where('type', 'credit')->where('status', 'completed')
            ->where('transactionable_type', $courseType)->whereBetween('created_at', [$startDate, $endDate])->sum('amount');
        $affiliateCommissions = WalletTransaction::where('category', 'referral_commission')->where('type', 'credit')->where('status', 'completed')
            ->whereBetween('created_at', [$startDate, $endDate])->sum('amount');
        $platformCommission = $courseSales - $instructorEarnings - $affiliateCommissions;

        // Total withdrawals
        $totalWithdrawals = Withdrawal::where('status', Withdrawal::STATUS_COMPLETED)
            ->whereBetween('completed_at', [$startDate, $endDate])
            ->sum('amount');

        // Pending withdrawals
        $pendingWithdrawals = Withdrawal::where('status', Withdrawal::STATUS_PENDING)
            ->sum('amount');

        return [
            'total_course_sales' => $courseSales,
            'instructor_earnings' => $instructorEarnings,
            'platform_commission' => $platformCommission,
            'total_withdrawals' => $totalWithdrawals,
            'pending_withdrawals' => $pendingWithdrawals,
            'formatted' => [
                'total_course_sales' => '₦'.number_format($courseSales, 2),
                'instructor_earnings' => '₦'.number_format($instructorEarnings, 2),
                'platform_commission' => '₦'.number_format($platformCommission, 2),
                'total_withdrawals' => '₦'.number_format($totalWithdrawals, 2),
                'pending_withdrawals' => '₦'.number_format($pendingWithdrawals, 2),
            ],
        ];
    }
}
