<?php 

// WalletService.php - Business Logic Layer
namespace App\Services;

use App\Models\Core\User;
use App\Models\Marketplace\Wallet;
use App\Models\Marketplace\WalletTransaction;
use App\Models\Learning\Course;
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
                'customer_name' => $user->name
            ];

            return $this->paystackService->initializeWalletFunding($data);

        } catch (\Exception $e) {
            Log::error('Wallet funding initiation error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to initiate wallet funding'
            ];
        }
    }

    /**
     * Process course purchase from wallet
     */
    public function purchaseCourse(User $student, Course $course): array
    {
        DB::beginTransaction();
        
        try {
            $studentWallet = $student->getOrCreateWallet();

            if (!$studentWallet->hasSufficientBalance($course->price)) {
                return [
                    'success' => false,
                    'message' => 'Insufficient wallet balance'
                ];
            }

            // Process the purchase with revenue split
            $splitAmounts = $course->processPurchase($student, $studentWallet);

            // Enroll student in course (assuming you have this relationship)
            if (method_exists($student, 'courses')) {
                $student->courses()->attach($course->id, [
                    'enrolled_at' => now(),
                    'payment_method' => 'wallet',
                    'amount_paid' => $course->price
                ]);
            }

            DB::commit();

            return [
                'success' => true,
                'message' => 'Course purchased successfully',
                'split_amounts' => $splitAmounts,
                'remaining_balance' => $studentWallet->fresh()->balance
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Course purchase error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Request withdrawal for instructor
     */
    public function requestWithdrawal(User $instructor, float $amount, array $bankDetails): array
    {
        DB::beginTransaction();
        
        try {
            $instructorWallet = $instructor->getOrCreateInstructorWallet();

            if (!$instructorWallet->hasSufficientBalance($amount)) {
                return [
                    'success' => false,
                    'message' => 'Insufficient balance for withdrawal'
                ];
            }

            // Resolve account details with Paystack
            $accountResolution = $this->paystackService->resolveAccountNumber(
                $bankDetails['account_number'],
                $bankDetails['bank_code']
            );

            if (!$accountResolution['success']) {
                return [
                    'success' => false,
                    'message' => 'Invalid bank account details'
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
                'status' => Withdrawal::STATUS_PENDING
            ]);

            DB::commit();

            return [
                'success' => true,
                'message' => 'Withdrawal request submitted successfully',
                'withdrawal' => $withdrawal
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Withdrawal request error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Failed to process withdrawal request'
            ];
        }
    }

    /**
     * Process withdrawal (Admin action)
     */
    public function processWithdrawal(Withdrawal $withdrawal, User $admin): array
    {
        DB::beginTransaction();
        
        try {
            if (!$withdrawal->canBeApproved()) {
                return [
                    'success' => false,
                    'message' => 'Withdrawal cannot be processed'
                ];
            }

            // Approve withdrawal
            $withdrawal->approve($admin->id);

            // Initiate Paystack transfer
            $transferResult = $this->paystackService->initiateTransfer($withdrawal);

            if (!$transferResult['success']) {
                // If transfer fails, reject the withdrawal
                $withdrawal->update(['status' => Withdrawal::STATUS_FAILED]);
                
                return [
                    'success' => false,
                    'message' => $transferResult['message']
                ];
            }

            DB::commit();

            return [
                'success' => true,
                'message' => 'Withdrawal processed successfully',
                'transfer_code' => $transferResult['transfer_code']
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Withdrawal processing error: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Failed to process withdrawal'
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
            'formatted_user_balance' => '₦' . number_format($userBalance, 2),
            'formatted_instructor_balance' => '₦' . number_format($instructorBalance, 2),
            'formatted_total_balance' => '₦' . number_format($userBalance + $instructorBalance, 2)
        ];
    }

    /**
     * Get platform revenue analytics
     */
    public function getRevenueAnalytics(\DatePeriod $period = null): array
    {
        $period = $period ?? new \DatePeriod(
            new \DateTime('-30 days'),
            new \DateInterval('P1D'),
            new \DateTime()
        );

        $startDate = $period->getStartDate();
        $endDate = $period->getEndDate();

        // Total course sales
        $courseSales = WalletTransaction::where('category', WalletTransaction::CATEGORY_COURSE_PURCHASE)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount');

        // Instructor earnings
        $instructorEarnings = WalletTransaction::where('category', WalletTransaction::CATEGORY_INSTRUCTOR_EARNING)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount');

        // Platform commission (estimated as course sales - instructor earnings)
        $platformCommission = $courseSales - $instructorEarnings;

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
                'total_course_sales' => '₦' . number_format($courseSales, 2),
                'instructor_earnings' => '₦' . number_format($instructorEarnings, 2),
                'platform_commission' => '₦' . number_format($platformCommission, 2),
                'total_withdrawals' => '₦' . number_format($totalWithdrawals, 2),
                'pending_withdrawals' => '₦' . number_format($pendingWithdrawals, 2)
            ]
        ];
    }
}