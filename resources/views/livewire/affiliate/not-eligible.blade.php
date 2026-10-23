
{{-- resources/views/livewire/affiliate/not-eligible.blade.php --}}
<div class="w-full min-w-0 py-8">
    <div class="bg-surface rounded-lg shadow-sm p-8 text-center">
        <i aria-hidden="true" class="fas fa-user-times text-6xl text-red-600 mb-6"></i>
        <h1 class=" font-bold text-content mb-4 bk-page-title">Affiliate Program Not Available</h1>
        <p class="text-lg text-content-muted mb-6">
            The affiliate program is currently available only to instructors and approved affiliate ambassadors.
        </p>

        <div class="bg-brand/10 rounded-lg p-6 mb-6">
            <h3 class="text-lg font-semibold text-content mb-4">How to Become Eligible</h3>
            <div class="text-left space-y-3">
                <div class="flex items-center space-x-3">
                    <i aria-hidden="true" class="fas fa-graduation-cap text-brand"></i>
                    <span>Become an instructor by creating and publishing courses</span>
                </div>
                <div class="flex items-center space-x-3">
                    <i aria-hidden="true" class="fas fa-envelope text-brand"></i>
                    <span>Contact our support team to apply for affiliate ambassador role</span>
                </div>
            </div>
        </div>

        <div class="space-x-4">
            <a href="{{ route('instructor.courses.create') }}"
               class="bg-action text-action-foreground px-6 py-2 rounded-lg hover:bg-action transition-colors">
                Become an Instructor
            </a>
            <a href="{{ route('support.contact') }}"
               class="bg-gray-600 text-white px-6 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                Contact Support
            </a>
        </div>
    </div>
</div>
