
<!-- resources/views/livewire/financial/course-checkout.blade.php -->
<div class="bg-themed-secondary rounded-lg shadow-sm p-6">
    <h1 class=" font-bold text-themed-primary mb-4 bk-page-title">Review your enrollment</h1>

    <!-- Course Details -->
    <div class="border-b border-themed-primary pb-4 mb-4">
        <div class="flex items-center space-x-4">
            <img src="{{ $course->thumbnail_url }}" alt="{{ $course->title }}" class="w-16 h-16 rounded-lg object-cover">
            <div>
                <h4 class="font-semibold text-themed-primary">{{ $course->title }}</h4>
                <p class="text-themed-secondary">{{ $course->instructor->name }}</p>
                <p class="text-xl font-bold text-green-600">{{ $formattedPrice }}</p>
            </div>
        </div>
    </div>

    <!-- Payment Options -->
    <div class="mb-6">
        <label class="block text-sm font-medium text-themed-primary mb-2">Payment Method</label>
        <div class="space-y-2">
            <label class="flex items-center">
                <input type="radio" wire:model="paymentMethod" value="wallet" class="focus:ring-green-500 h-4 w-4 text-green-600 border-themed-secondary">
                <span class="ml-2 block text-sm text-themed-primary">
                    Wallet Balance ({{ $formattedBalance }})
                    @if($walletBalance < $course->price)
                        <span class="text-red-600 text-xs">- Insufficient Balance</span>
                    @endif
                </span>
            </label>
        </div>
    </div>

    <!-- Purchase Button -->
    <div class="flex justify-between items-center">
        <div class="text-sm text-themed-secondary">
            @if($walletBalance >= $course->price)
                <p class="text-green-600">✓ You have sufficient balance</p>
            @else
                <p class="text-red-600">✗ Please fund your wallet first</p>
            @endif
        </div>
        <button
            wire:click="purchaseCourse"
            wire:loading.attr="disabled" wire:target="purchaseCourse"
            @if($walletBalance < $course->price) disabled @endif
            class="bk-button bk-button-primary"
        >
            @if($walletBalance >= $course->price)
                <span wire:loading.remove wire:target="purchaseCourse">{{ (float) $course->price > 0 ? 'Purchase course' : 'Enroll for free' }}</span><span wire:loading wire:target="purchaseCourse">Processing…</span>
            @else
                Insufficient Balance
            @endif
        </button>
    </div>

    @if($walletBalance < $course->price)
        <div class="mt-4 p-4 bg-brand/10 rounded-lg">
            <p class="text-brand text-sm">Need to fund your wallet?
                <a href="{{ route('wallet.index') }}" class="underline font-medium">Click here to add funds</a>
            </p>
        </div>
    @endif

    <!-- Flash Messages -->
    @if (session()->has('error'))
        <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
            <p class="text-red-800 text-sm">{{ session('error') }}</p>
        </div>
    @endif
</div>
