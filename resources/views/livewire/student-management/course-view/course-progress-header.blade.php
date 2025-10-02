<div class="bg-white dark:bg-gray-800 rounded-xl p-6 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-700 shadow-lg transition-colors duration-300">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div class="flex-1">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $course->title }}</h1>
            <p class="text-gray-600 dark:text-gray-300 mt-2">{{ $course->subtitle }}</p>
            
            <div class="flex items-center mt-4 gap-4 text-sm">
                <span class="bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 px-3 py-1 rounded-full">
                    {{ $course->category->name ?? 'Uncategorized' }}
                </span>
                <span class="bg-gray-300 dark:bg-gray-600 text-gray-800 dark:text-gray-200 px-3 py-1 rounded-full capitalize">
                    {{ $course->difficulty_level }}
                </span>
                <span class="flex items-center text-gray-700 dark:text-gray-300">
                    <i class="fas fa-clock mr-1"></i>
                    {{ $course->formatted_duration }}
                </span>
            </div>

            @if($currentSection)
                <div class="mt-4 text-sm">
                    <span class="text-gray-600 dark:text-gray-400">Current Section:</span>
                    <span class="font-semibold text-gray-900 dark:text-white ml-1">{{ $currentSection->title }}</span>
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <!-- Progress Circle -->
            <div class="bg-gray-100 dark:bg-gray-700 p-4 rounded-full text-center min-w-[80px] border-2 border-gray-300 dark:border-gray-600">
                <span class="text-2xl font-bold block text-gray-900 dark:text-white">{{ $overallProgress }}%</span>
                <span class="text-xs text-gray-600 dark:text-gray-400">Complete</span>
            </div>

            <!-- Progress Stats -->
            <div class="text-right">
                @php
                    $stats = $this->getProgressStats();
                @endphp
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    {{ $stats['completed'] }}/{{ $stats['total'] }} Lessons
                </div>
                <div class="w-24 bg-gray-300 dark:bg-gray-600 rounded-full h-2 mt-1">
                    <div class="bg-gray-700 dark:bg-gray-300 h-2 rounded-full transition-all duration-300" 
                         style="width: {{ $overallProgress }}%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Overall Progress Bar -->
    <div class="mt-6">
        <div class="flex justify-between items-center mb-2">
            <span class="text-sm text-gray-600 dark:text-gray-400">Course Progress</span>
            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $overallProgress }}%</span>
        </div>
        <div class="w-full bg-gray-300 dark:bg-gray-600 rounded-full h-3">
            <div class="bg-gradient-to-r from-gray-600 to-gray-800 dark:from-gray-300 dark:to-gray-400 h-3 rounded-full transition-all duration-500" 
                 style="width: {{ $overallProgress }}%"></div>
        </div>
    </div>
</div>