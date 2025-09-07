
<!-- resources/views/livewire/financial/course-checkout.blade.php -->
<div class="bg-white rounded-lg shadow-lg p-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Complete Your Purchase</h3>
    
    <!-- Course Details -->
    <div class="border-b border-gray-200 pb-4 mb-4">
        <div class="flex items-center space-x-4">
            <img src="{{ $course->thumbnail }}" alt="{{ $course->title }}" class="w-16 h-16 rounded-lg object-cover">
            <div>
                <h4 class="font-semibold text-gray-900">{{ $course->title }}</h4>
                <p class="text-gray-600">{{ $course->instructor->name }}</p>
                <p class="text-xl font-bold text-green-600">{{ $formattedPrice }}</p>
            </div>
        </div>
    </div>

    <!-- Payment Options -->
    <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-2">Payment Method</label>
        <div class="space-y-2">
            <label class="flex items-center">
                <input type="radio" wire:model="paymentMethod" value="wallet" class="focus:ring-green-500 h-4 w-4 text-green-600 border-gray-300">
                <span class="ml-2 block text-sm text-gray-900">
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
        <div class="text-sm text-gray-600">
            @if($walletBalance >= $course->price)
                <p class="text-green-600">✓ You have sufficient balance</p>
            @else
                <p class="text-red-600">✗ Please fund your wallet first</p>
            @endif
        </div>
        <button 
            wire:click="purchaseCourse"
            @if($walletBalance < $course->price) disabled @endif
            class="bg-green-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-green-700 disabled:bg-gray-400 disabled:cursor-not-allowed transition-colors"
        >
            @if($walletBalance >= $course->price)
                Purchase Course
            @else
                Insufficient Balance
            @endif
        </button>
    </div>

    @if($walletBalance < $course->price)
        <div class="mt-4 p-4 bg-blue-50 rounded-lg">
            <p class="text-blue-800 text-sm">Need to fund your wallet? 
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