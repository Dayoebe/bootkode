<!-- Certificates Tab -->
<div x-show="activeTab === 'certificates'" x-transition.opacity.duration.300ms class="py-4 sm:py-6 lg:py-8">
    <div class="flex items-center justify-between mb-6 sm:mb-8">
        <div class="flex items-center">
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-r from-amber-500 to-yellow-600 dark:from-amber-600 dark:to-yellow-700 rounded-xl flex items-center justify-center mr-3 sm:mr-4 shadow-lg">
                <i class="fas fa-certificate text-white text-lg sm:text-xl"></i>
            </div>
            <div>
                <h2 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white transition-colors duration-300">Certificates</h2>
                <p class="text-sm sm:text-base text-gray-600 dark:text-gray-400 transition-colors duration-300">Your earned certificates and achievements</p>
            </div>
        </div>
        @if (isset($certificates) && $certificates->count() > 0)
            <a href="{{ route('student.certificates.index') }}" 
               class="px-4 py-2 bg-amber-100 dark:bg-amber-500/20 hover:bg-amber-200 dark:hover:bg-amber-500/30 border border-amber-200 dark:border-amber-500/30 text-amber-700 dark:text-amber-300 rounded-lg text-sm font-medium transition-all duration-300 flex items-center gap-2">
                <span>View All</span>
                <i class="fas fa-arrow-right"></i>
            </a>
        @endif
    </div>

    <!-- Certificate Stats -->
    @if(isset($certificateStats))
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6 sm:mb-8">
        <!-- Total Certificates -->
        <div class="bg-gradient-to-br from-amber-50 to-yellow-50 dark:from-amber-500/10 dark:to-yellow-500/10 p-4 sm:p-6 rounded-xl border border-amber-200 dark:border-amber-500/30 backdrop-blur-sm transition-colors duration-300">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-amber-600 dark:text-amber-300 font-semibold text-xs sm:text-sm transition-colors duration-300">Total</h3>
                <div class="w-8 h-8 bg-amber-100 dark:bg-amber-500/20 rounded-lg flex items-center justify-center transition-colors duration-300">
                    <i class="fas fa-certificate text-amber-500 dark:text-amber-400 text-sm"></i>
                </div>
            </div>
            <p class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white transition-colors duration-300">{{ $certificateStats['total'] ?? 0 }}</p>
        </div>

        <!-- Approved Certificates -->
        <div class="bg-gradient-to-br from-emerald-50 to-green-50 dark:from-emerald-500/10 dark:to-green-500/10 p-4 sm:p-6 rounded-xl border border-emerald-200 dark:border-emerald-500/30 backdrop-blur-sm transition-colors duration-300">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-emerald-600 dark:text-emerald-300 font-semibold text-xs sm:text-sm transition-colors duration-300">Approved</h3>
                <div class="w-8 h-8 bg-emerald-100 dark:bg-emerald-500/20 rounded-lg flex items-center justify-center transition-colors duration-300">
                    <i class="fas fa-check-circle text-emerald-500 dark:text-emerald-400 text-sm"></i>
                </div>
            </div>
            <p class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white transition-colors duration-300">{{ $certificateStats['approved'] ?? 0 }}</p>
        </div>

        <!-- Pending Certificates -->
        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-500/10 dark:to-indigo-500/10 p-4 sm:p-6 rounded-xl border border-blue-200 dark:border-blue-500/30 backdrop-blur-sm transition-colors duration-300">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-blue-600 dark:text-blue-300 font-semibold text-xs sm:text-sm transition-colors duration-300">Pending</h3>
                <div class="w-8 h-8 bg-blue-100 dark:bg-blue-500/20 rounded-lg flex items-center justify-center transition-colors duration-300">
                    <i class="fas fa-clock text-blue-500 dark:text-blue-400 text-sm"></i>
                </div>
            </div>
            <p class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white transition-colors duration-300">{{ $certificateStats['pending'] ?? 0 }}</p>
        </div>

        <!-- Rejected Certificates -->
        <div class="bg-gradient-to-br from-red-50 to-pink-50 dark:from-red-500/10 dark:to-pink-500/10 p-4 sm:p-6 rounded-xl border border-red-200 dark:border-red-500/30 backdrop-blur-sm transition-colors duration-300">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-red-600 dark:text-red-300 font-semibold text-xs sm:text-sm transition-colors duration-300">Rejected</h3>
                <div class="w-8 h-8 bg-red-100 dark:bg-red-500/20 rounded-lg flex items-center justify-center transition-colors duration-300">
                    <i class="fas fa-times-circle text-red-500 dark:text-red-400 text-sm"></i>
                </div>
            </div>
            <p class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white transition-colors duration-300">{{ $certificateStats['rejected'] ?? 0 }}</p>
        </div>
    </div>
    @endif

    @if (isset($certificates) && $certificates->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
            @foreach ($certificates as $certificate)
                <div class="group bg-white dark:bg-gray-800/50 rounded-xl p-4 sm:p-6 hover:shadow-xl transition-all duration-300 border border-gray-200 dark:border-gray-700/50 backdrop-blur-sm hover:-translate-y-1
                    @if($certificate->status === 'approved') hover:shadow-amber-500/20 dark:hover:shadow-amber-500/10
                    @elseif($certificate->status === 'requested') hover:shadow-blue-500/20 dark:hover:shadow-blue-500/10
                    @else hover:shadow-red-500/20 dark:hover:shadow-red-500/10
                    @endif">
                    
                    <!-- Certificate Header -->
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-2">
                                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br 
                                    @if($certificate->status === 'approved') from-amber-500/10 to-yellow-500/10 border border-amber-500/20
                                    @elseif($certificate->status === 'requested') from-blue-500/10 to-indigo-500/10 border border-blue-500/20
                                    @else from-red-500/10 to-pink-500/10 border border-red-500/20
                                    @endif
                                    dark:from-opacity-20 dark:to-opacity-20 rounded-lg flex items-center justify-center transition-colors duration-300">
                                    <i class="fas fa-certificate 
                                        @if($certificate->status === 'approved') text-amber-500 dark:text-amber-400
                                        @elseif($certificate->status === 'requested') text-blue-500 dark:text-blue-400
                                        @else text-red-500 dark:text-red-400
                                        @endif text-lg sm:text-xl"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-gray-900 dark:text-white font-bold text-sm sm:text-base line-clamp-2 mb-1 transition-colors duration-300">
                                        {{ $certificate->course->title }}
                                    </h4>
                                    @if($certificate->course->category)
                                        <p class="text-xs text-gray-600 dark:text-gray-400 transition-colors duration-300">
                                            <i class="fas fa-folder-open mr-1"></i>{{ $certificate->course->category->name }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Status Badge -->
                    <div class="mb-4">
                        @if($certificate->status === 'approved')
                            <span class="inline-flex items-center px-3 py-1.5 bg-emerald-100 dark:bg-emerald-500/20 border border-emerald-200 dark:border-emerald-500/30 text-emerald-700 dark:text-emerald-300 rounded-full text-xs font-semibold transition-colors duration-300">
                                <i class="fas fa-check-circle mr-1.5"></i>Approved
                            </span>
                        @elseif($certificate->status === 'requested')
                            <span class="inline-flex items-center px-3 py-1.5 bg-blue-100 dark:bg-blue-500/20 border border-blue-200 dark:border-blue-500/30 text-blue-700 dark:text-blue-300 rounded-full text-xs font-semibold transition-colors duration-300">
                                <i class="fas fa-clock mr-1.5"></i>Pending Review
                            </span>
                        @elseif($certificate->status === 'rejected')
                            <span class="inline-flex items-center px-3 py-1.5 bg-red-100 dark:bg-red-500/20 border border-red-200 dark:border-red-500/30 text-red-700 dark:text-red-300 rounded-full text-xs font-semibold transition-colors duration-300">
                                <i class="fas fa-times-circle mr-1.5"></i>Rejected
                            </span>
                        @elseif($certificate->status === 'revoked')
                            <span class="inline-flex items-center px-3 py-1.5 bg-gray-100 dark:bg-gray-500/20 border border-gray-200 dark:border-gray-500/30 text-gray-700 dark:text-gray-300 rounded-full text-xs font-semibold transition-colors duration-300">
                                <i class="fas fa-ban mr-1.5"></i>Revoked
                            </span>
                        @endif
                    </div>

                    <!-- Certificate Details -->
                    <div class="space-y-2 mb-4 text-xs sm:text-sm">
                        @if($certificate->certificate_number)
                            <div class="flex items-center text-gray-600 dark:text-gray-400 transition-colors duration-300">
                                <i class="fas fa-hashtag mr-2 w-4"></i>
                                <span class="font-mono">{{ $certificate->certificate_number }}</span>
                            </div>
                        @endif
                        @if($certificate->grade)
                            <div class="flex items-center text-gray-600 dark:text-gray-400 transition-colors duration-300">
                                <i class="fas fa-award mr-2 w-4"></i>
                                <span>Grade: <strong class="text-gray-900 dark:text-white">{{ $certificate->grade }}</strong></span>
                            </div>
                        @endif
                        @if($certificate->issued_date)
                            <div class="flex items-center text-gray-600 dark:text-gray-400 transition-colors duration-300">
                                <i class="fas fa-calendar mr-2 w-4"></i>
                                <span>Issued: {{ $certificate->issued_date->format('M d, Y') }}</span>
                            </div>
                        @elseif($certificate->requested_at)
                            <div class="flex items-center text-gray-600 dark:text-gray-400 transition-colors duration-300">
                                <i class="fas fa-calendar mr-2 w-4"></i>
                                <span>Requested: {{ $certificate->requested_at->format('M d, Y') }}</span>
                            </div>
                        @endif
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center gap-2 pt-4 border-t border-gray-200 dark:border-gray-700/50">
                        @if($certificate->status === 'approved')
                            <button wire:click="viewCertificate({{ $certificate->id }})" 
                                    class="flex-1 px-3 py-2 bg-gradient-to-r from-amber-500 to-yellow-600 hover:from-amber-600 hover:to-yellow-700 dark:from-amber-600 dark:to-yellow-700 dark:hover:from-amber-700 dark:hover:to-yellow-800 text-white rounded-lg text-xs font-semibold transition-all duration-300 shadow-md hover:shadow-lg flex items-center justify-center gap-2 group/btn">
                                <i class="fas fa-eye"></i>
                                <span>View</span>
                                <i class="fas fa-external-link-alt text-xs opacity-0 -translate-x-2 group-hover/btn:opacity-100 group-hover/btn:translate-x-0 transition-all duration-300"></i>
                            </button>
                            <button wire:click="downloadCertificate({{ $certificate->id }})" 
                                    class="px-3 py-2 bg-gray-100 dark:bg-gray-700/50 hover:bg-amber-100 dark:hover:bg-amber-500/20 border border-gray-200 dark:border-gray-600/50 hover:border-amber-300 dark:hover:border-amber-500/30 text-gray-600 dark:text-gray-400 hover:text-amber-600 dark:hover:text-amber-400 rounded-lg text-xs font-medium transition-all duration-300">
                                <i class="fas fa-download"></i>
                            </button>
                        @elseif($certificate->status === 'requested')
                            <div class="flex-1 px-3 py-2 bg-blue-50 dark:bg-blue-500/10 border border-blue-200 dark:border-blue-500/30 text-blue-600 dark:text-blue-400 rounded-lg text-xs text-center transition-colors duration-300">
                                <i class="fas fa-hourglass-half mr-1"></i>
                                Awaiting approval
                            </div>
                        @else
                            <div class="flex-1 px-3 py-2 bg-gray-50 dark:bg-gray-700/30 border border-gray-200 dark:border-gray-600/30 text-gray-600 dark:text-gray-400 rounded-lg text-xs text-center transition-colors duration-300">
                                <i class="fas fa-info-circle mr-1"></i>
                                {{ ucfirst($certificate->status) }}
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <!-- Empty State -->
        <div class="bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-800/20 dark:to-gray-900/20 rounded-xl p-8 sm:p-12 md:p-16 text-center border border-gray-200 dark:border-gray-700/50 backdrop-blur-sm">
            <div class="w-20 h-20 sm:w-24 sm:h-24 bg-gradient-to-br from-amber-100 to-yellow-100 dark:from-amber-500/20 dark:to-yellow-500/20 rounded-full flex items-center justify-center mx-auto mb-4 sm:mb-6 shadow-lg">
                <i class="fas fa-certificate text-amber-500 dark:text-amber-400 text-3xl sm:text-4xl"></i>
            </div>
            <h3 class="text-xl sm:text-2xl text-gray-900 dark:text-white font-bold mb-2">No certificates yet</h3>
            <p class="text-sm sm:text-base text-gray-600 dark:text-gray-400 mb-6 sm:mb-8 max-w-md mx-auto">Complete courses to earn certificates and showcase your achievements</p>
            <a href="{{ route('student.enrolled-courses') }}" 
               class="inline-flex items-center px-6 py-3 sm:px-8 sm:py-4 bg-gradient-to-r from-amber-500 to-yellow-600 hover:from-amber-600 hover:to-yellow-700 dark:from-amber-600 dark:to-yellow-700 dark:hover:from-amber-700 dark:hover:to-yellow-800 text-white rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl hover:shadow-amber-500/25 dark:hover:shadow-amber-500/20 text-sm sm:text-base group">
                <i class="fas fa-graduation-cap mr-2"></i> 
                <span>View My Courses</span>
                <i class="fas fa-arrow-right ml-2 opacity-0 -translate-x-2 group-hover:opacity-100 group-hover:translate-x-0 transition-all duration-300"></i>
            </a>
        </div>
    @endif
</div>