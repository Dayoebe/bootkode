
<?php
// resources/views/certificates/invalid.blade.php
?>
<x-app-layout>
<div class="min-h-screen bg-gray-900 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl mx-auto text-center">
        <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-2xl p-12 mb-8">
            <div class="bg-white bg-opacity-20 rounded-full w-24 h-24 flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-ban text-white text-4xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-white mb-4">Certificate Invalid</h1>
            <p class="text-red-100 text-lg">{{ $message }}</p>
        </div>

        @if($certificate)
        <div class="bg-white bg-opacity-10 backdrop-blur-lg rounded-2xl p-8 border border-white border-opacity-20">
            <h3 class="text-xl font-semibold text-white mb-4">Certificate Information</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-300">Certificate Number:</span>
                    <span class="text-white font-mono">{{ $certificate->certificate_number }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-300">Status:</span>
                    <span class="text-red-400 capitalize">{{ str_replace('_', ' ', $certificate->status) }}</span>
                </div>
                @if($certificate->revoked_at)
                <div class="flex justify-between">
                    <span class="text-gray-300">Revoked Date:</span>
                    <span class="text-white">{{ $certificate->revoked_at->format('M j, Y') }}</span>
                </div>
                @endif
            </div>
        </div>
        @endif

        <div class="mt-8">
            <a href="{{ route('certificate.verify') }}" 
               class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors mr-4">
                <i class="fas fa-search mr-2"></i>
                Verify Another Certificate
            </a>
            <a href="{{ route('help.support') }}" 
               class="inline-flex items-center px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                <i class="fas fa-question-circle mr-2"></i>
                Contact Support
            </a>
        </div>
    </div>
</div>
</x-app-layout>
