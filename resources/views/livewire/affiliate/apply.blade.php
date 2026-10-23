
{{-- resources/views/livewire/affiliate/apply.blade.php --}}
<div class="w-full min-w-0 py-8">
    <div class="bg-surface rounded-lg shadow-sm p-8">
        <div class="text-center mb-8">
            <i class="fas fa-share-alt text-6xl text-brand mb-4"></i>
            <h1 class=" font-bold text-content mb-4 bk-page-title">Join Our Affiliate Program</h1>
            <p class="text-lg text-content-muted">Earn commission by referring students to BootKode courses</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="text-center p-6 bg-green-50 rounded-lg">
                <i class="fas fa-percentage text-3xl text-green-600 mb-4"></i>
                <h3 class="text-lg font-semibold text-content mb-2">30% Commission</h3>
                <p class="text-content-muted">Earn 30% of our platform share from every sale</p>
            </div>
            <div class="text-center p-6 bg-brand/10 rounded-lg">
                <i class="fas fa-tools text-3xl text-brand mb-4"></i>
                <h3 class="text-lg font-semibold text-content mb-2">Marketing Tools</h3>
                <p class="text-content-muted">Get access to banners, links, and promotional materials</p>
            </div>
            <div class="text-center p-6 bg-brand/10 rounded-lg">
                <i class="fas fa-chart-line text-3xl text-brand mb-4"></i>
                <h3 class="text-lg font-semibold text-content mb-2">Real-time Analytics</h3>
                <p class="text-content-muted">Track your performance with detailed reports</p>
            </div>
        </div>

        <div class="bg-surface-muted rounded-lg p-6 mb-8">
            <h3 class="text-lg font-semibold text-content mb-4">How it Works</h3>
            <div class="space-y-4">
                <div class="flex items-start space-x-4">
                    <div class="bg-action text-action-foreground rounded-full w-8 h-8 flex items-center justify-center text-sm font-semibold">1</div>
                    <div>
                        <h4 class="font-medium text-content">Apply for Affiliate Status</h4>
                        <p class="text-content-muted">Submit your application and get approved instantly as an instructor</p>
                    </div>
                </div>
                <div class="flex items-start space-x-4">
                    <div class="bg-action text-action-foreground rounded-full w-8 h-8 flex items-center justify-center text-sm font-semibold">2</div>
                    <div>
                        <h4 class="font-medium text-content">Get Your Referral Link</h4>
                        <p class="text-content-muted">Receive a unique referral code and marketing materials</p>
                    </div>
                </div>
                <div class="flex items-start space-x-4">
                    <div class="bg-action text-action-foreground rounded-full w-8 h-8 flex items-center justify-center text-sm font-semibold">3</div>
                    <div>
                        <h4 class="font-medium text-content">Start Referring</h4>
                        <p class="text-content-muted">Share your link and earn commission on every successful purchase</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center">
            <button wire:click="applyForAffiliate"
                    class="bg-action text-action-foreground px-8 py-3 rounded-lg text-lg font-semibold hover:bg-action transition-colors">
                Apply Now - It's Free!
            </button>
            <p class="text-sm text-content-muted mt-4">
                @if(auth()->user()->hasRole(\App\Models\Core\User::ROLE_INSTRUCTOR))
                    As an instructor, your application will be auto-approved
                @else
                    Your application will be reviewed within 24-48 hours
                @endif
            </p>
        </div>
    </div>
</div>
