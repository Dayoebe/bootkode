<!-- resources/views/livewire/components/email-verification-alert.blade.php -->

@if($showAlert && auth()->check() && !auth()->user()->hasVerifiedEmail())
<div class="fixed top-20 right-6 z-50 max-w-md" 
     x-data="{ show: true }" 
     x-show="show"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="transform opacity-0 translate-y-2"
     x-transition:enter-end="transform opacity-100 translate-y-0"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="transform opacity-100 translate-y-0"
     x-transition:leave-end="transform opacity-0 translate-y-2">
    
    <div class="bg-gradient-to-r from-amber-50 to-orange-50 dark:from-amber-900/30 dark:to-orange-900/30 border-l-4 border-orange-500 dark:border-orange-400 shadow-lg rounded-r-lg overflow-hidden">
        <!-- Header -->
        <div class="p-4 pb-3">
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-start gap-3 flex-1">
                    <!-- Icon -->
                    <div class="flex-shrink-0 mt-0.5">
                        <div class="flex items-center justify-center h-8 w-8 rounded-full bg-orange-100 dark:bg-orange-900/50">
                            <i class="fas fa-exclamation-triangle text-orange-600 dark:text-orange-400 text-sm"></i>
                        </div>
                    </div>
                    
                    <!-- Content -->
                    <div class="flex-1">
                        <h3 class="text-sm font-semibold text-orange-900 dark:text-orange-100 mb-1">
                            Email Not Verified
                        </h3>
                        <p class="text-sm text-orange-800 dark:text-orange-200">
                            Your email address hasn't been verified yet. Check {{ $email }} for a verification link.
                        </p>
                    </div>
                </div>
                
                <!-- Close Button -->
                <button @click="show = false; $wire.dismissAlert()"
                        class="flex-shrink-0 text-orange-500 dark:text-orange-400 hover:text-orange-700 dark:hover:text-orange-300 transition-colors"
                        aria-label="Close alert">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
        </div>

        <!-- Actions -->
        <div class="px-4 pb-4 pt-2 border-t border-orange-200 dark:border-orange-800/50 space-y-2">
            <!-- Resend Button -->
            <button wire:click="resendVerificationEmail"
                    wire:loading.attr="disabled"
                    class="w-full px-3 py-2 bg-orange-600 dark:bg-orange-500 hover:bg-orange-700 dark:hover:bg-orange-600 disabled:opacity-50 disabled:cursor-not-allowed text-white text-sm font-medium rounded-lg transition-colors duration-200 flex items-center justify-center gap-2">
                <i class="fas fa-paper-plane" wire:loading.class="hidden" wire:target="resendVerificationEmail"></i>
                <span wire:loading.remove wire:target="resendVerificationEmail">Resend Verification Email</span>
                <span wire:loading wire:target="resendVerificationEmail" class="flex items-center gap-2">
                    <i class="fas fa-spinner fa-spin"></i>
                    Sending...
                </span>
            </button>

            <!-- Info Text -->
            <p class="text-xs text-orange-700 dark:text-orange-300 text-center">
                <i class="fas fa-info-circle mr-1"></i>
                Didn't receive? Check spam folder or try again.
            </p>

            <!-- Verification Status Indicator -->
            <div class="bg-white dark:bg-orange-950/50 rounded p-2 text-xs text-orange-700 dark:text-orange-200 border border-orange-200 dark:border-orange-800/50">
                <span class="flex items-center gap-2">
                    <span class="inline-block w-2 h-2 rounded-full bg-orange-500 dark:bg-orange-400 animate-pulse"></span>
                    <span>Verification pending</span>
                </span>
            </div>
        </div>

        <!-- Footer Message -->
        <div class="px-4 py-2 bg-orange-100/50 dark:bg-orange-900/20 border-t border-orange-200 dark:border-orange-800/50">
            <p class="text-xs text-orange-700 dark:text-orange-300">
                ✓ Once verified, you'll have full access to all features
            </p>
        </div>
    </div>
</div>

<!-- Optional: Session-based flash message alerts -->
@if(session('status') === 'verification-link-sent')
<div class="fixed top-20 right-6 z-50 max-w-md"
     x-data="{ show: true }"
     x-show="show"
     x-init="setTimeout(() => show = false, 5000)"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="transform opacity-0 translate-y-2"
     x-transition:enter-end="transform opacity-100 translate-y-0">
    
    <div class="bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/30 dark:to-emerald-900/30 border-l-4 border-green-500 dark:border-green-400 shadow-lg rounded-r-lg p-4">
        <div class="flex items-center gap-3">
            <div class="flex-shrink-0">
                <div class="flex items-center justify-center h-8 w-8 rounded-full bg-green-100 dark:bg-green-900/50">
                    <i class="fas fa-check text-green-600 dark:text-green-400"></i>
                </div>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-green-900 dark:text-green-100">
                    Verification email sent!
                </h3>
                <p class="text-sm text-green-800 dark:text-green-200">
                    Check your inbox and click the verification link.
                </p>
            </div>
        </div>
    </div>
</div>
@endif

@if(session('error'))
<div class="fixed top-20 right-6 z-50 max-w-md"
     x-data="{ show: true }"
     x-show="show"
     x-init="setTimeout(() => show = false, 5000)"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="transform opacity-0 translate-y-2"
     x-transition:enter-end="transform opacity-100 translate-y-0">
    
    <div class="bg-gradient-to-r from-red-50 to-rose-50 dark:from-red-900/30 dark:to-rose-900/30 border-l-4 border-red-500 dark:border-red-400 shadow-lg rounded-r-lg p-4">
        <div class="flex items-center gap-3">
            <div class="flex-shrink-0">
                <div class="flex items-center justify-center h-8 w-8 rounded-full bg-red-100 dark:bg-red-900/50">
                    <i class="fas fa-times text-red-600 dark:text-red-400"></i>
                </div>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-red-900 dark:text-red-100">
                    Error
                </h3>
                <p class="text-sm text-red-800 dark:text-red-200">
                    {{ session('error') }}
                </p>
            </div>
        </div>
    </div>
</div>
@endif