<?php
// resources/views/certificates/verification-result.blade.php
?>
<x-app-layout>
<div class="min-h-screen bg-gray-900 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        @if($verificationData['valid'])
            <!-- Valid Certificate -->
            <div class="bg-gradient-to-r from-green-500 to-emerald-600 rounded-2xl p-8 mb-8">
                <div class="flex items-center justify-center mb-6">
                    <div class="bg-white bg-opacity-20 rounded-full p-4 mr-4">
                        <i class="fas fa-check-circle text-white text-3xl"></i>
                    </div>
                    <div class="text-center">
                        <h2 class="text-2xl font-bold text-white mb-2">Certificate Verified ✓</h2>
                        <p class="text-green-100">{{ $verificationData['message'] }}</p>
                    </div>
                </div>
            </div>

            <!-- Certificate Details -->
            <div class="bg-white bg-opacity-10 backdrop-blur-lg rounded-2xl p-8 border border-white border-opacity-20">
                <div class="grid md:grid-cols-2 gap-8">
                    <!-- Left Column -->
                    <div class="space-y-6">
                        <div>
                            <h3 class="text-lg font-semibold text-white mb-4 flex items-center">
                                <i class="fas fa-user mr-3 text-indigo-400"></i>
                                Certificate Holder
                            </h3>
                            <div class="bg-white bg-opacity-5 rounded-lg p-4">
                                <p class="text-2xl font-bold text-white">{{ $verificationData['certificate']['student_name'] }}</p>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold text-white mb-4 flex items-center">
                                <i class="fas fa-book mr-3 text-indigo-400"></i>
                                Course Information
                            </h3>
                            <div class="bg-white bg-opacity-5 rounded-lg p-4">
                                <p class="text-xl font-semibold text-white mb-2">{{ $verificationData['certificate']['course_title'] }}</p>
                                <p class="text-gray-300">Instructor: {{ $verificationData['certificate']['instructor_name'] }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-6">
                        <div>
                            <h3 class="text-lg font-semibold text-white mb-4 flex items-center">
                                <i class="fas fa-shield-alt mr-3 text-indigo-400"></i>
                                Verification Details
                            </h3>
                            <div class="bg-white bg-opacity-5 rounded-lg p-4 space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-gray-300">Certificate ID:</span>
                                    <span class="text-white font-mono text-sm">{{ $verificationData['certificate']['certificate_number'] }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-300">Grade:</span>
                                    <span class="text-white font-semibold">{{ $verificationData['certificate']['grade'] }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-300">Completion:</span>
                                    <span class="text-white">{{ $verificationData['certificate']['completion_date'] }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-300">Issued:</span>
                                    <span class="text-white">{{ $verificationData['certificate']['issued_date'] }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="space-y-3">
                            <a href="{{ route('certificate.view', $certificate->verification_code) }}" 
                               class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-3 px-4 rounded-lg transition-colors flex items-center justify-center">
                                <i class="fas fa-eye mr-2"></i>
                                View Full Certificate
                            </a>
                            <a href="{{ route('certificate.download', $certificate->verification_code) }}" 
                               class="w-full bg-gray-600 hover:bg-gray-700 text-white py-3 px-4 rounded-lg transition-colors flex items-center justify-center">
                                <i class="fas fa-download mr-2"></i>
                                Download PDF
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- Invalid Certificate -->
            <div class="bg-gradient-to-r from-red-500 to-red-600 rounded-2xl p-8 mb-8">
                <div class="flex items-center justify-center mb-6">
                    <div class="bg-white bg-opacity-20 rounded-full p-4 mr-4">
                        <i class="fas fa-times-circle text-white text-3xl"></i>
                    </div>
                    <div class="text-center">
                        <h2 class="text-2xl font-bold text-white mb-2">Certificate Not Valid</h2>
                        <p class="text-red-100">{{ $verificationData['message'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white bg-opacity-10 backdrop-blur-lg rounded-2xl p-8 text-center border border-white border-opacity-20">
                <p class="text-gray-300 mb-6">
                    The verification code you entered does not match our records, or the certificate may have been revoked.
                </p>
                <a href="{{ route('certificate.verify') }}" 
                   class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors">
                    <i class="fas fa-search mr-2"></i>
                    Try Again
                </a>
            </div>
        @endif
    </div>
</div>
</x-app-layout>
