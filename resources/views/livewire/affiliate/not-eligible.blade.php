
{{-- resources/views/livewire/affiliate/not-eligible.blade.php --}}
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-xl shadow-lg p-8 text-center">
        <i class="fas fa-user-times text-6xl text-red-600 mb-6"></i>
        <h1 class="text-3xl font-bold text-gray-900 mb-4">Affiliate Program Not Available</h1>
        <p class="text-lg text-gray-600 mb-6">
            The affiliate program is currently available only to instructors and approved affiliate ambassadors.
        </p>
        
        <div class="bg-blue-50 rounded-lg p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">How to Become Eligible</h3>
            <div class="text-left space-y-3">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-graduation-cap text-blue-600"></i>
                    <span>Become an instructor by creating and publishing courses</span>
                </div>
                <div class="flex items-center space-x-3">
                    <i class="fas fa-envelope text-blue-600"></i>
                    <span>Contact our support team to apply for affiliate ambassador role</span>
                </div>
            </div>
        </div>

        <div class="space-x-4">
            <a href="{{ route('instructor.courses.create') }}" 
               class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                Become an Instructor
            </a>
            <a href="{{ route('support.contact') }}" 
               class="bg-gray-600 text-white px-6 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                Contact Support
            </a>
        </div>
    </div>
</div>