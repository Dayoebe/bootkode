<div class="container mx-auto px-4 py-6">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 animate__animated animate__fadeIn">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-6">Request New Certificate</h2>

        @if(!$requestSuccess)
            <div class="space-y-6">
                <div>
                    <label for="selectedCourse" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Select Course
                    </label>
                    <select
                        id="selectedCourse"
                        wire:model.live="selectedCourse"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                    >
                        <option value="">-- Select a Course --</option>
                        @foreach($availableCourses as $course)
                            <option value="{{ $course->id }}">{{ $course->title }}</option>
                        @endforeach
                    </select>
                    @error('selectedCourse') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                @if($selectedCourse)
                    <div>
                        <label for="selectedTemplate" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Select Certificate Template
                        </label>
                        <select
                            id="selectedTemplate"
                            wire:model="selectedTemplate"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                        >
                            <option value="">-- Select a Template --</option>
                            @foreach($availableTemplates as $template)
                                <option value="{{ $template->id }}">{{ $template->name }}</option>
                            @endforeach
                        </select>
                        @error('selectedTemplate') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="pt-4">
                        <button
                            wire:click="requestCertificate"
                            wire:loading.attr="disabled"
                            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50"
                        >
                            <span wire:loading.remove>Request Certificate</span>
                            <span wire:loading>
                                <i class="fas fa-spinner fa-spin mr-2"></i> Processing...
                            </span>
                        </button>
                    </div>
                @endif
            </div>
        @else
            <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-md p-4 mb-6 animate__animated animate__fadeIn">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-green-800 dark:text-green-200">
                            Certificate Request Submitted Successfully!
                        </h3>
                        <div class="mt-2 text-sm text-green-700 dark:text-green-300">
                            <p>
                                @if($certificateDetails->status === 'approved')
                                    Your certificate has been approved and is ready for download.
                                @else
                                    Your certificate request is pending approval. You'll be notified once it's approved.
                                @endif
                            </p>
                        </div>
                        <div class="mt-4">
                            <div class="-mx-2 -my-1.5 flex">
                                <a
                                    href="{{ route('certificates.index') }}"
                                    class="bg-green-50 dark:bg-green-800/50 px-2 py-1.5 rounded-md text-sm font-medium text-green-800 dark:text-green-200 hover:bg-green-100 dark:hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-green-50 dark:focus:ring-offset-green-900/50 focus:ring-green-600"
                                >
                                    View My Certificates
                                </a>
                                @if($certificateDetails->status === 'approved')
                                    <button
                                        wire:click="$dispatch('download-certificate', { certificateId: {{ $certificateDetails->id }} })"
                                        type="button"
                                        class="ml-3 bg-green-50 dark:bg-green-800/50 px-2 py-1.5 rounded-md text-sm font-medium text-green-800 dark:text-green-200 hover:bg-green-100 dark:hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-green-50 dark:focus:ring-offset-green-900/50 focus:ring-green-600"
                                    >
                                        Download Certificate
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-700 rounded-md shadow-sm p-4 border border-gray-200 dark:border-gray-600">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Certificate Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Course:</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $certificateDetails->course->title }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Template:</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $certificateDetails->template->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Status:</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white capitalize">
                            <span @class([
                                'px-2 inline-flex text-xs leading-5 font-semibold rounded-full',
                                'bg-green-100 text-green-800' => $certificateDetails->status === 'approved',
                                'bg-yellow-100 text-yellow-800' => $certificateDetails->status === 'pending',
                            ])>
                                {{ $certificateDetails->status }}
                            </span>
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Verification Code:</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $certificateDetails->verification_code }}</p>
                    </div>
                </div>
            </div>

            <div class="mt-6">
                <button
                    wire:click="$set('requestSuccess', false)"
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                >
                    Request Another Certificate
                </button>
            </div>
        @endif
    </div>
</div>