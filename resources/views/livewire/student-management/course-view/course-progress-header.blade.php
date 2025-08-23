<div class="bg-gradient-to-r from-indigo-800 to-purple-700 rounded-xl p-6 text-white">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div class="flex-1">
            <h1 class="text-2xl font-bold">{{ $course->title }}</h1>
            <p class="text-indigo-200 mt-2">{{ $course->subtitle }}</p>
            
            <div class="flex items-center mt-4 gap-4 text-sm">
                <span class="bg-indigo-600 px-3 py-1 rounded-full">
                    {{ $course->category->name ?? 'Uncategorized' }}
                </span>
                <span class="bg-purple-600 px-3 py-1 rounded-full capitalize">
                    {{ $course->difficulty_level }}
                </span>
                <span class="flex items-center">
                    <i class="fas fa-clock mr-1"></i>
                    {{ $course->formatted_duration }}
                </span>
            </div>

            @if($currentSection)
                <div class="mt-4 text-sm">
                    <span class="text-indigo-200">Current Section:</span>
                    <span class="font-semibold ml-1">{{ $currentSection->title }}</span>
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <!-- Progress Circle -->
            <div class="bg-white/10 p-4 rounded-full text-center min-w-[80px]">
                <span class="text-2xl font-bold block">{{ $overallProgress }}%</span>
                <span class="text-xs text-indigo-200">Complete</span>
            </div>

            <!-- Progress Stats -->
            <div class="text-right">
                @php
                    $stats = $this->getProgressStats();
                @endphp
                <div class="text-sm text-indigo-200">
                    {{ $stats['completed'] }}/{{ $stats['total'] }} Lessons
                </div>
                <div class="w-24 bg-indigo-600 rounded-full h-2 mt-1">
                    <div class="bg-white h-2 rounded-full transition-all duration-300" 
                         style="width: {{ $overallProgress }}%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Overall Progress Bar -->
    <div class="mt-6">
        <div class="flex justify-between items-center mb-2">
            <span class="text-sm text-indigo-200">Course Progress</span>
            <span class="text-sm font-medium">{{ $overallProgress }}%</span>
        </div>
        <div class="w-full bg-indigo-600 rounded-full h-3">
            <div class="bg-gradient-to-r from-green-400 to-blue-400 h-3 rounded-full transition-all duration-500" 
                 style="width: {{ $overallProgress }}%"></div>
        </div>
    </div>
</div>