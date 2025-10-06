<div class="bg-gray-100 dark:bg-gray-900 rounded-xl p-6 transition-colors duration-300">
    <!-- Course Header -->
    <livewire:student-management.course-view.course-progress-header :course="$course"
        :overallProgress="$this->calculateOverallProgress()" :currentSection="$currentSection"
        :completedLessons="$completedLessons" wire:key="header-{{ $course->id }}" />


    @if ($this->getLastViewedLesson() && $this->getLastViewedLesson()->id !== $currentLesson?->id)
        <div class="mb-6 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl p-4 shadow-lg animate-fade-in">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3 text-white">
                    <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center">
                        <i class="fas fa-play-circle text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-lg">Continue Learning</h3>
                        <p class="text-sm opacity-90">
                            Resume from: <span class="font-medium">{{ $this->getLastViewedLesson()->title }}</span>
                        </p>
                    </div>
                </div>
                <button wire:click="continueFromLastLesson"
                    class="px-6 py-3 bg-white text-indigo-600 hover:bg-gray-100 rounded-lg font-semibold transition-all duration-200 shadow-md hover:shadow-lg flex items-center gap-2">
                    <i class="fas fa-arrow-right"></i>
                    Continue
                </button>
            </div>
        </div>
    @endif

    <!-- Certificate Earned Banner -->
    @if($certificateEarned ?? false)
        <div
            class="mb-6 bg-gradient-to-r from-green-500 to-emerald-600 rounded-xl p-6 text-white shadow-lg animate-fade-in">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center">
                        <i class="fas fa-trophy text-3xl"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold mb-1">🎉 Congratulations!</h3>
                        <p class="text-lg opacity-90">You've completed this course and earned your certificate!</p>
                    </div>
                </div>
                <a href="{{ route('student.certificates.index') }}"
                    class="px-6 py-3 bg-white text-green-600 hover:bg-gray-100 rounded-lg font-semibold transition-all duration-200 shadow-md hover:shadow-lg flex items-center gap-2">
                    <i class="fas fa-certificate"></i>
                    View Certificate
                </a>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mt-6">
        <!-- Sidebar - Course Navigation -->
        <div class="lg:col-span-1">
            <livewire:student-management.course-view.course-progress-sidebar :course="$course"
                :sections="$course->sections" :currentLesson="$currentLesson" :completedLessons="$completedLessons"
                :unlockedSections="$unlockedSections" :sectionCompletionThreshold="$sectionCompletionThreshold"
                wire:key="sidebar-{{ $course->id }}-{{ $currentLesson?->id ?? 'none' }}" />
        </div>

        <!-- Main Content - Lesson View -->
        <div class="lg:col-span-3">
            @if ($currentLesson)
                @php
                    $allLessons = $course->sections->flatMap->lessons;
                @endphp

                <livewire:student-management.course-view.lesson-content-viewer :lesson="$currentLesson"
                    :allLessons="$allLessons->toArray()" :completedLessons="$completedLessons"
                    :unlockedSections="$unlockedSections" wire:key="content-{{ $currentLesson->id }}" />
            @else
                <!-- Empty State -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-xl p-10 text-center border border-gray-200 dark:border-gray-700 transition-colors duration-300">
                    <i class="fas fa-book-open text-gray-400 dark:text-gray-500 text-4xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No lesson selected</h3>
                    <p class="text-gray-600 dark:text-gray-400">Select a lesson from the sidebar to begin learning.</p>
                </div>
            @endif
        </div>
    </div>
    
    <!-- Review Button & Modal -->
@if(Auth::check() && Auth::user()->enrollments()->where('course_id', $course->id)->exists())
<div class="mt-6" 
     x-data="{ 
         showReviewModal: false, 
         rating: @entangle('reviewRating').live, 
         comment: @entangle('reviewComment').live 
     }"
     wire:ignore.self>
    
    <!-- Review Button -->
    <button @click="showReviewModal = true"
            class="w-full bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white px-6 py-4 rounded-xl font-bold text-lg transition-all duration-300 transform hover:scale-105 shadow-lg flex items-center justify-center gap-3">
        <i class="fas fa-star"></i>
        {{ $course->hasReviewBy(Auth::id()) ? 'Update Your Review' : 'Rate This Course' }}
    </button>

    <!-- Review Modal -->
    <div x-show="showReviewModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0" 
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100" 
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4 z-50"
         style="display: none;">
        
        <div @click.away="showReviewModal = false"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95" 
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100" 
             x-transition:leave-end="opacity-0 scale-95"
             class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md p-6">
            
            <div class="text-center mb-6">
                <div class="w-20 h-20 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-star text-white text-3xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Rate This Course</h3>
                <p class="text-gray-600 dark:text-gray-400">Share your experience with {{ $course->title }}</p>
            </div>

            <div class="space-y-6">
                <!-- Star Rating -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-3 text-center">
                        Your Rating
                    </label>
                    <div class="flex items-center justify-center gap-3">
                        @for($i = 1; $i <= 5; $i++)
                            <button type="button" 
                                    wire:click="$set('reviewRating', {{ $i }})"
                                    class="text-4xl transition-all duration-200 transform hover:scale-125">
                                <i class="fas fa-star {{ $reviewRating >= $i ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}"></i>
                            </button>
                        @endfor
                    </div>
                    <p class="text-center mt-2 text-sm text-gray-600 dark:text-gray-400">
                        {{ $reviewRating }} out of 5 stars
                    </p>
                </div>

                <!-- Comment -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                        Your Review
                    </label>
                    <textarea wire:model="reviewComment"
                              rows="4"
                              placeholder="Share your thoughts about this course..."
                              class="w-full px-4 py-3 bg-white dark:bg-gray-700 border-2 border-gray-300 dark:border-gray-600 rounded-xl text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:border-blue-500 dark:focus:border-blue-400 focus:ring-2 focus:ring-blue-500/20 transition-all duration-300"></textarea>
                    <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 mt-1">
                        <span>Minimum 10 characters</span>
                        <span>{{ strlen($reviewComment ?? '') }}/1000</span>
                    </div>
                    @error('reviewComment') 
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Buttons -->
                <div class="flex gap-3">
                    <button type="button"
                            @click="showReviewModal = false"
                            class="flex-1 px-6 py-3 bg-gray-200 hover:bg-gray-300 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-800 dark:text-gray-200 font-semibold rounded-xl transition-all duration-300">
                        Cancel
                    </button>
                    <button type="button"
                            wire:click="submitReview"
                            @click="if($wire.submitReview()) { showReviewModal = false }"
                            wire:loading.attr="disabled"
                            class="flex-1 px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white font-bold rounded-xl transition-all duration-300 shadow-lg disabled:opacity-50">
                        <span wire:loading.remove wire:target="submitReview">Submit Review</span>
                        <span wire:loading wire:target="submitReview">
                            <i class="fas fa-spinner fa-spin"></i> Submitting...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
</div>