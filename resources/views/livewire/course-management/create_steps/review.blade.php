<div x-show="currentStep === 5" x-transition:enter="transition ease-out duration-300"
x-transition:enter-start="opacity-0 transform translate-x-8"
x-transition:enter-end="opacity-100 transform translate-x-0"
x-transition:leave="transition ease-in duration-200"
x-transition:leave-start="opacity-100 transform translate-x-0"
x-transition:leave-end="opacity-0 transform -translate-x-8">

<div
    class="bg-themed-secondary rounded-lg p-4 sm:p-6 lg:p-8 shadow-sm border border-themed-primary transition-colors duration-300">
    <div class="flex items-center gap-2 sm:gap-3 mb-4 sm:mb-6">
        <div
            class="w-10 h-10 sm:w-12 sm:h-12 bg-yellow-500 rounded-full flex items-center justify-center flex-shrink-0">
            <i class="fas fa-check-circle text-white text-lg sm:text-xl" aria-hidden="true"></i>
        </div>
        <div>
            <h2
                class="text-xl sm:text-2xl font-bold text-themed-primary transition-colors duration-300">
                Review & Submit</h2>
            <p class="text-themed-secondary text-sm sm:text-base transition-colors duration-300">
                Review your course details before publishing</p>
        </div>
    </div>

    <!-- Course Preview Card -->
    <div
        class="bg-themed-tertiary rounded-lg p-4 sm:p-6 mb-6 sm:mb-8 border border-themed-primary transition-colors duration-300">
        <div class="flex flex-col lg:flex-row gap-4 sm:gap-6">
            <!-- Thumbnail -->

            <div class="flex-shrink-0">
                @if ($thumbnailPreview)
                    <img src="{{ $thumbnailPreview }}" alt="Course thumbnail"
                        class="w-full lg:w-48 h-32 object-cover rounded-lg shadow-sm">
                @elseif(($isEditMode ?? false) && isset($existingThumbnail) && $existingThumbnail && !($shouldRemoveThumbnail ?? false))
                    <img src="{{ asset('storage/' . $existingThumbnail) }}" alt="Course thumbnail"
                        class="w-full lg:w-48 h-32 object-cover rounded-lg shadow-sm">
                @else
                    <div
                        class="w-full lg:w-48 h-32 bg-themed-secondary rounded-lg flex items-center justify-center border border-themed-primary transition-colors duration-300">
                        <i
                            class="fas fa-image text-3xl text-themed-secondary transition-colors duration-300" aria-hidden="true"></i>
                    </div>
                @endif
            </div>

            <!-- Course Details -->
            <div class="flex-1">
                <h3
                    class="text-xl sm:text-2xl font-bold text-themed-primary mb-2 transition-colors duration-300">
                    {{ $title ?? 'Untitled Course' }}
                </h3>
                <p
                    class="text-themed-secondary mb-3 sm:mb-4 text-sm sm:text-base transition-colors duration-300">
                    {{ $subtitle ?? 'No subtitle provided' }}
                </p>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4 text-sm">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-tag text-brand dark:text-brand" aria-hidden="true"></i>
                        <span class="text-themed-primary transition-colors duration-300">
                            {{ $categories->where('id', $category_id)->first()->name ?? 'No category' }}
                        </span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-layer-group text-brand dark:text-brand" aria-hidden="true"></i>
                        <span class="text-themed-primary transition-colors duration-300">
                            {{ $difficultyLevels[$difficulty_level] ?? 'Not specified' }}
                        </span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-clock text-green-500 dark:text-green-400" aria-hidden="true"></i>
                        <span class="text-themed-primary transition-colors duration-300">
                            {{ $estimated_duration_minutes ?? 0 }} minutes
                        </span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-dollar-sign text-yellow-500 dark:text-yellow-400" aria-hidden="true"></i>
                        <span class="text-themed-primary transition-colors duration-300">
                            @if ($is_free)
                                Free
                            @elseif($is_premium)
                                Premium - ${{ number_format($price, 2) }}
                            @else
                                ${{ number_format($price, 2) }}
                            @endif
                        </span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-users text-brand dark:text-brand" aria-hidden="true"></i>
                        <span class="text-themed-primary transition-colors duration-300">
                            {{ $target_audience ?? 'Not specified' }}
                        </span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i class="fas fa-globe text-red-500 dark:text-red-400" aria-hidden="true"></i>
                        <span class="text-themed-primary transition-colors duration-300">
                            {{ $is_published ? 'Published' : 'Draft' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Review Sections -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
        <!-- Description & Outcomes -->
        <div class="space-y-4 sm:space-y-6">
            <div
                class="bg-themed-tertiary p-4 sm:p-6 rounded-lg border border-themed-primary transition-colors duration-300">
                <h4
                    class="text-lg font-semibold text-themed-primary mb-3 flex items-center gap-2 transition-colors duration-300">
                    <i class="fas fa-align-left text-brand dark:text-brand" aria-hidden="true"></i>
                    Description
                </h4>
                <p
                    class="text-themed-secondary text-sm sm:text-base leading-relaxed transition-colors duration-300">
                    {{ $description ?: 'No description provided' }}
                </p>
            </div>

            @if (!empty(array_filter($learning_outcomes)))
                <div
                    class="bg-themed-tertiary p-4 sm:p-6 rounded-lg border border-themed-primary transition-colors duration-300">
                    <h4
                        class="text-lg font-semibold text-themed-primary mb-3 flex items-center gap-2 transition-colors duration-300">
                        <i class="fas fa-lightbulb text-brand dark:text-brand" aria-hidden="true"></i>
                        Learning Outcomes
                    </h4>
                    <ul class="space-y-2">
                        @foreach ($learning_outcomes as $outcome)
                            @if (!empty(trim($outcome)))
                                <li
                                    class="flex items-start gap-2 text-themed-secondary text-sm sm:text-base transition-colors duration-300">
                                    <i
                                        class="fas fa-check text-green-500 dark:text-green-400 mt-1 flex-shrink-0" aria-hidden="true"></i>
                                    <span>{{ $outcome }}</span>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <!-- Requirements & Materials -->
        <div class="space-y-4 sm:space-y-6">
            @if (!empty(array_filter($prerequisites)))
                <div
                    class="bg-themed-tertiary p-4 sm:p-6 rounded-lg border border-themed-primary transition-colors duration-300">
                    <h4
                        class="text-lg font-semibold text-themed-primary mb-3 flex items-center gap-2 transition-colors duration-300">
                        <i class="fas fa-exclamation-circle text-orange-500 dark:text-orange-400" aria-hidden="true"></i>
                        Prerequisites
                    </h4>
                    <ul class="space-y-2">
                        @foreach ($prerequisites as $prerequisite)
                            @if (!empty(trim($prerequisite)))
                                <li
                                    class="flex items-start gap-2 text-themed-secondary text-sm sm:text-base transition-colors duration-300">
                                    <i
                                        class="fas fa-circle text-orange-500 dark:text-orange-400 text-xs mt-2 flex-shrink-0" aria-hidden="true"></i>
                                    <span>{{ $prerequisite }}</span>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (!empty(array_filter($materials_included)))
                <div
                    class="bg-themed-tertiary p-4 sm:p-6 rounded-lg border border-themed-primary transition-colors duration-300">
                    <h4
                        class="text-lg font-semibold text-themed-primary mb-3 flex items-center gap-2 transition-colors duration-300">
                        <i class="fas fa-box-open text-brand dark:text-brand" aria-hidden="true"></i>
                        Materials Included
                    </h4>
                    <ul class="space-y-2">
                        @foreach ($materials_included as $material)
                            @if (!empty(trim($material)))
                                <li
                                    class="flex items-start gap-2 text-themed-secondary text-sm sm:text-base transition-colors duration-300">
                                    <i
                                        class="fas fa-check-circle text-brand dark:text-brand mt-1 flex-shrink-0" aria-hidden="true"></i>
                                    <span>{{ $material }}</span>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (!empty(array_filter($tags)))
                <div
                    class="bg-themed-tertiary p-4 sm:p-6 rounded-lg border border-themed-primary transition-colors duration-300">
                    <h4
                        class="text-lg font-semibold text-themed-primary mb-3 flex items-center gap-2 transition-colors duration-300">
                        <i class="fas fa-tags text-brand dark:text-brand" aria-hidden="true"></i>
                        Tags
                    </h4>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($tags as $tag)
                            @if (!empty(trim($tag)))
                                <span
                                    class="px-3 py-1 bg-brand/10 dark:bg-action/50 text-brand dark:text-brand rounded-full text-sm font-medium transition-colors duration-300">
                                    {{ $tag }}
                                </span>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
</div>
