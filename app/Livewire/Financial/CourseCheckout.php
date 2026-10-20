<?php

// Livewire/Financial/CourseCheckout.php
namespace App\Livewire\Financial;

use Livewire\Component;
use App\Models\Learning\Course;
use App\Services\WalletService;

#[\Livewire\Attributes\Layout('layouts.dashboard', ['title' => 'Course checkout'])]
class CourseCheckout extends Component
{
    public Course $course;
    public $paymentMethod = 'wallet';
    
    private WalletService $walletService;

    public function boot(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    public function mount(Course $course)
    {
        abort_unless($course->is_published && $course->is_approved, 404);
        $this->course = $course;
    }

    public function purchaseCourse()
    {
        $user = auth()->user();

        if ($this->paymentMethod === 'wallet') {
            $result = $this->walletService->purchaseCourse($user, $this->course);

            if ($result['success']) {
                return redirect()->route('course.view', $this->course)
                    ->with('success', 'Course purchased successfully!');
            } else {
                session()->flash('error', $result['message']);
            }
        }
        // Add other payment methods here (Paystack direct, etc.)
    }

    public function render()
    {
        $user = auth()->user();
        $walletBalance = $user->getOrCreateWallet()->balance;
        
        return view('livewire.financial.course-checkout', [
            'walletBalance' => $walletBalance,
            'formattedBalance' => '₦' . number_format($walletBalance, 2),
            'formattedPrice' => '₦' . number_format($this->course->price, 2),
            'courseTitle' => $this->course->title
        ]);
    }
}