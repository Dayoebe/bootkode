<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <div class="text-center mb-10">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                Certificate Verification
            </h1>
            <p class="text-gray-600 dark:text-gray-400">
                Verify the authenticity of BootKode certificates
            </p>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 mb-8 animate__animated animate__fadeIn">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="md:col-span-2">
                    <label for="uuid" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Certificate ID
                    </label>
                    <input
                        type="text"
                        id="uuid"
                        wire:model="uuid"
                        placeholder="Enter Certificate ID"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                    >
                </div>
                
                <div class="flex items-end">
                    <button
                        wire:click="verifyCertificate"
                        class="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        Verify Certificate
                    </button>
                </div>
            </div>

            <div class="mt-4 text-center">
                <p class="text-gray-600 dark:text-gray-400 text-sm">
                    OR
                </p>
            </div>

            <div class="mt-4">
                <label for="verificationCode" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Verification Code
                </label>
                <input
                    type="text"
                    id="verificationCode"
                    wire:model="verificationCode"
                    placeholder="Enter Verification Code"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                >
            </div>
        </div>

        @if($verificationResult)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 animate__animated animate__fadeInUp">
                @if($verificationResult === 'valid')
                    <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-md p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-green-800 dark:text-green-200">
                                    Certificate Verified Successfully!
                                </h3>
                                <div class="mt-2 text-sm text-green-700 dark:text-green-300">
                                    <p>
                                        This certificate is valid and issued by BootKode Academy.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Certificate Information</h4>
                            <div class="space-y-1">
                                <p class="text-sm">
                                    <span class="text-gray-500 dark:text-gray-400">ID:</span> 
                                    <span class="font-medium text-gray-900 dark:text-white">{{ $certificate->uuid }}</span>
                                </p>
                                <p class="text-sm">
                                    <span class="text-gray-500 dark:text-gray-400">Verification Code:</span> 
                                    <span class="font-medium text-gray-900 dark:text-white">{{ $certificate->verification_code }}</span>
                                </p>
                                <p class="text-sm">
                                    <span class="text-gray-500 dark:text-gray-400">Issue Date:</span> 
                                    <span class="font-medium text-gray-900 dark:text-white">{{ $certificate->issue_date->format('M d, Y') }}</span>
                                </p>
                                @if($certificate->expiry_date)
                                    <p class="text-sm">
                                        <span class="text-gray-500 dark:text-gray-400">Expiry Date:</span> 
                                        <span class="font-medium text-gray-900 dark:text-white">{{ $certificate->expiry_date->format('M d, Y') }}</span>
                                    </p>
                                @endif
                            </div>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Recipient Information</h4>
                            <div class="space-y-1">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $certificate->user->name }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $certificate->user->email }}</p>
                            </div>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Course Information</h4>
                            <div class="space-y-1">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $certificate->course->title }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Template: {{ $certificate->template->name }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="text-center">
                        <a 
                            href="#"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring focus:ring-blue-300 disabled:opacity-25 transition"
                        >
                            <i class="fas fa-download mr-2"></i> Download Certificate
                        </a>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                            Verified on {{ now()->format('M d, Y h:i A') }}
                        </p>
                    </div>
                @elseif($verificationResult === 'invalid')
                    <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-md p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800 dark:text-red-200">
                                    Certificate Verification Failed
                                </h3>
                                <div class="mt-2 text-sm text-red-700 dark:text-red-300">
                                    <p>
                                        This certificate is not valid. It may have been revoked or not yet approved.
                                    </p>
                                    @if($certificate && $certificate->rejection_reason)
                                        <p class="mt-2">
                                            <strong>Reason:</strong> {{ $certificate->rejection_reason }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-md p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">
                                    Certificate Not Found
                                </h3>
                                <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">
                                    <p>
                                        No certificate was found with the provided information. Please check the Certificate ID or Verification Code and try again.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>