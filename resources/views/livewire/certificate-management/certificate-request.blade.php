<div>


    <div class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-indigo-900 p-6">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold text-white mb-2">Certificate Request</h1>
                <p class="text-gray-300">Complete your learning journey with an official certificate</p>
            </div>

            @if ($course)
                <!-- Course Information Card -->
                <div class="bg-gray-800 rounded-2xl p-8 mb-8 shadow-2xl border border-gray-700">
                    <div class="flex items-start gap-6">
                        @if ($course->thumbnail)
                            <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}"
                                class="w-24 h-24 rounded-xl object-cover">
                        @else
                            <div class="w-24 h-24 bg-indigo-600 rounded-xl flex items-center justify-center">
                                <i class="fas fa-graduation-cap text-white text-2xl"></i>
                            </div>
                        @endif

                        <div class="flex-1">
                            <h2 class="text-2xl font-bold text-white mb-2">{{ $course->title }}</h2>
                            <p class="text-gray-300 mb-4">{{ $course->subtitle }}</p>

                            <div class="flex flex-wrap gap-4 text-sm">
                                <span class="bg-indigo-600 text-white px-3 py-1 rounded-full">
                                    <i class="fas fa-user mr-1"></i>
                                    {{ $course->instructor->name }}
                                </span>
                                <span class="bg-gray-700 text-gray-300 px-3 py-1 rounded-full">
                                    <i class="fas fa-clock mr-1"></i>
                                    {{ $course->formatted_duration }}
                                </span>
                                @if ($course->difficulty_level)
                                    <span class="bg-gray-700 text-gray-300 px-3 py-1 rounded-full capitalize">
                                        <i class="fas fa-signal mr-1"></i>
                                        {{ $course->difficulty_level }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Progress Card -->
                <div class="bg-gray-800 rounded-2xl p-8 mb-8 shadow-2xl border border-gray-700">
                    <h3 class="text-xl font-bold text-white mb-6 flex items-center">
                        <i class="fas fa-chart-line mr-3 text-indigo-400"></i>
                        Course Progress
                    </h3>

                    <div class="space-y-6">
                        <!-- Progress Bar -->
                        <div>
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-gray-300">Overall Completion</span>
                                <span class="text-white font-bold">{{ $completionPercentage }}%</span>
                            </div>
                            <div class="w-full bg-gray-700 rounded-full h-3">
                                <div class="bg-gradient-to-r from-indigo-500 to-purple-500 h-3 rounded-full transition-all duration-500"
                                    style="width: {{ $completionPercentage }}%"></div>
                            </div>
                        </div>

                        <!-- Progress Stats -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-gray-700 rounded-lg p-4 text-center">
                                <div class="text-2xl font-bold text-white">{{ $completedLessons }}</div>
                                <div class="text-sm text-gray-400">Completed Lessons</div>
                            </div>
                            <div class="bg-gray-700 rounded-lg p-4 text-center">
                                <div class="text-2xl font-bold text-white">{{ $totalLessons }}</div>
                                <div class="text-sm text-gray-400">Total Lessons</div>
                            </div>
                            <div class="bg-gray-700 rounded-lg p-4 text-center">
                                <div class="text-2xl font-bold text-white">{{ $totalLessons - $completedLessons }}
                                </div>
                                <div class="text-sm text-gray-400">Remaining</div>
                            </div>
                        </div>

                        <!-- Completion Status -->
                        @if ($canRequestCertificate)
                            <div class="bg-green-900/30 border border-green-500 rounded-lg p-4">
                                <div class="flex items-center text-green-400">
                                    <i class="fas fa-check-circle mr-3 text-xl"></i>
                                    <div>
                                        <div class="font-semibold">Congratulations!</div>
                                        <div class="text-sm">You have completed the course and are eligible for a
                                            certificate.</div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="bg-yellow-900/30 border border-yellow-500 rounded-lg p-4">
                                <div class="flex items-center text-yellow-400">
                                    <i class="fas fa-clock mr-3 text-xl"></i>
                                    <div>
                                        <div class="font-semibold">Keep Learning!</div>
                                        <div class="text-sm">Complete all lessons to become eligible for a certificate.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Certificate Status Card -->
                <div class="bg-gray-800 rounded-2xl p-8 shadow-2xl border border-gray-700">
                    <h3 class="text-xl font-bold text-white mb-6 flex items-center">
                        <i class="fas fa-certificate mr-3 text-indigo-400"></i>
                        Certificate Status
                    </h3>

                    @if ($existingCertificate)
                        <div class="space-y-6">
                            <!-- Status Badge -->
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <i
                                        class="{{ $existingCertificate->status_icon }} mr-3 text-{{ $existingCertificate->status_color }}-400"></i>
                                    <div>
                                        <div class="font-semibold text-white capitalize">
                                            {{ str_replace('_', ' ', $existingCertificate->status) }}</div>
                                        <div class="text-sm text-gray-400">Certificate
                                            #{{ $existingCertificate->certificate_number }}</div>
                                    </div>
                                </div>
                                <span
                                    class="bg-{{ $existingCertificate->status_color }}-600 text-white px-4 py-2 rounded-full text-sm font-medium capitalize">
                                    {{ str_replace('_', ' ', $existingCertificate->status) }}
                                </span>
                            </div>

                            <!-- Certificate Details -->
                            <div class="bg-gray-700 rounded-lg p-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <span class="text-gray-400">Requested:</span>
                                        <span
                                            class="text-white ml-2">{{ $existingCertificate->requested_at->format('M j, Y') }}</span>
                                    </div>
                                    @if ($existingCertificate->approved_at)
                                        <div>
                                            <span class="text-gray-400">Approved:</span>
                                            <span
                                                class="text-white ml-2">{{ $existingCertificate->approved_at->format('M j, Y') }}</span>
                                        </div>
                                    @endif
                                    @if ($existingCertificate->grade)
                                        <div>
                                            <span class="text-gray-400">Grade:</span>
                                            <span
                                                class="text-white ml-2 font-semibold">{{ $existingCertificate->grade }}</span>
                                        </div>
                                    @endif
                                    <div>
                                        <span class="text-gray-400">Completion Date:</span>
                                        <span
                                            class="text-white ml-2">{{ $existingCertificate->completion_date->format('M j, Y') }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex gap-4">
                                @if ($existingCertificate->isRequested())
                                    <button wire:click="cancelRequest"
                                        class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors flex items-center">
                                        <i class="fas fa-times mr-2"></i>
                                        Cancel Request
                                    </button>
                                @elseif($existingCertificate->isApproved() && $existingCertificate->pdf_path)
                                    <button wire:click="downloadCertificate"
                                        class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors flex items-center">
                                        <i class="fas fa-download mr-2"></i>
                                        Download Certificate
                                    </button>
                                    <a href="{{ route('certificate.view', $existingCertificate->verification_code) }}"
                                        target="_blank"
                                        class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors flex items-center">
                                        <i class="fas fa-eye mr-2"></i>
                                        View Certificate
                                    </a>
                                @elseif($existingCertificate->isRejected())
                                    <div class="bg-red-900/30 border border-red-500 rounded-lg p-4 w-full">
                                        <div class="text-red-400 font-semibold mb-2">Request Rejected</div>
                                        <div class="text-red-300 text-sm">{{ $existingCertificate->rejection_reason }}
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Certificate Preview (if approved) -->
                            @if ($existingCertificate->isApproved())
                                <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-lg p-6 text-center">
                                    <div class="text-white mb-4">
                                        <i class="fas fa-certificate text-4xl mb-2"></i>
                                        <div class="text-xl font-bold">Certificate Ready!</div>
                                        <div class="text-sm opacity-90">Your official certificate has been approved and
                                            is ready for download.</div>
                                    </div>

                                    @if ($existingCertificate->qr_code_path)
                                        <div class="mt-4">
                                            <div class="text-sm text-white/80 mb-2">Verification QR Code:</div>
                                            <img src="{{ asset('storage/' . $existingCertificate->qr_code_path) }}"
                                                alt="Verification QR Code"
                                                class="mx-auto w-20 h-20 bg-white p-1 rounded">
                                            <div class="text-xs text-white/70 mt-1">Scan to verify authenticity</div>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @else
                        <!-- No Certificate Requested -->
                        <div class="text-center py-8">
                            @if ($canRequestCertificate)
                                <div class="space-y-6">
                                    <div>
                                        <i class="fas fa-award text-6xl text-indigo-400 mb-4"></i>
                                        <h4 class="text-2xl font-bold text-white mb-2">Ready to Get Certified?</h4>
                                        <p class="text-gray-300">You've completed the course! Request your official
                                            certificate now.</p>
                                    </div>

                                    <!-- Certificate Benefits -->
                                    <div class="bg-gray-700 rounded-lg p-6 text-left">
                                        <h5 class="text-lg font-semibold text-white mb-4">Your Certificate Includes:
                                        </h5>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div class="flex items-center text-gray-300">
                                                <i class="fas fa-check text-green-400 mr-3"></i>
                                                Official course completion verification
                                            </div>
                                            <div class="flex items-center text-gray-300">
                                                <i class="fas fa-check text-green-400 mr-3"></i>
                                                Unique certificate number
                                            </div>
                                            <div class="flex items-center text-gray-300">
                                                <i class="fas fa-check text-green-400 mr-3"></i>
                                                QR code for instant verification
                                            </div>
                                            <div class="flex items-center text-gray-300">
                                                <i class="fas fa-check text-green-400 mr-3"></i>
                                                Professional PDF format
                                            </div>
                                            <div class="flex items-center text-gray-300">
                                                <i class="fas fa-check text-green-400 mr-3"></i>
                                                Instructor and institution validation
                                            </div>
                                            <div class="flex items-center text-gray-300">
                                                <i class="fas fa-check text-green-400 mr-3"></i>
                                                Permanent verification record
                                            </div>
                                        </div>
                                    </div>

                                    <button wire:click="requestCertificate"
                                        class="px-8 py-4 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white rounded-lg font-semibold text-lg transition-all duration-200 transform hover:scale-105 shadow-lg flex items-center mx-auto">
                                        <i class="fas fa-certificate mr-3"></i>
                                        Request My Certificate
                                    </button>
                                </div>
                            @else
                                <div>
                                    <i class="fas fa-graduation-cap text-6xl text-gray-600 mb-4"></i>
                                    <h4 class="text-xl font-bold text-white mb-2">Complete the Course First</h4>
                                    <p class="text-gray-300 mb-6">Finish all lessons to unlock your certificate.</p>

                                    <a href="{{ route('student.course.view', $course->slug) }}"
                                        class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors inline-flex items-center">
                                        <i class="fas fa-play mr-2"></i>
                                        Continue Learning
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Back to Course -->
                <div class="text-center mt-8">
                    <a href="{{ route('student.course.view', $course->slug) }}"
                        class="inline-flex items-center text-indigo-400 hover:text-indigo-300 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Course
                    </a>
                </div>
            @else
                <!-- No Course Selected -->
                <div class="bg-gray-800 rounded-2xl p-12 text-center shadow-2xl border border-gray-700">
                    <i class="fas fa-search text-6xl text-gray-600 mb-6"></i>
                    <h3 class="text-2xl font-bold text-white mb-4">Select a Course</h3>
                    <p class="text-gray-300 mb-8">Choose a course to request a certificate for.</p>

                    <a href="{{ route('student.dashboard') }}"
                        class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors inline-flex items-center">
                        <i class="fas fa-home mr-2"></i>
                        Go to Dashboard
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Loading Overlay -->
    <div wire:loading class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-gray-800 rounded-lg p-8 flex items-center">
            <i class="fas fa-spinner fa-spin text-indigo-400 text-2xl mr-4"></i>
            <span class="text-white font-medium">Processing your request...</span>
        </div>
    </div>

    <style>
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .hover-lift:hover {
            transform: translateY(-2px);
        }

        .gradient-border {
            background: linear-gradient(45deg, #667eea 0%, #764ba2 100%);
            padding: 2px;
            border-radius: 12px;
        }

        .gradient-border-content {
            background: #1f2937;
            border-radius: 10px;
            padding: 24px;
        }
    </style>
</div>
