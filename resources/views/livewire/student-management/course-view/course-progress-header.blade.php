<div class="bg-themed-secondary rounded-xl p-6 text-themed-primary border border-themed-primary shadow-lg transition-colors duration-300">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div class="flex-1">
            <h1 class="text-2xl font-bold text-themed-primary">{{ $course->title }}</h1>
            <p class="text-themed-secondary mt-2">{{ $course->subtitle }}</p>
            
            <div class="flex items-center mt-4 gap-4 text-sm flex-wrap">
                <span class="bg-themed-tertiary text-themed-primary px-3 py-1 rounded-full border border-themed-secondary">
                    {{ $course->category->name ?? 'Uncategorized' }}
                </span>
                <span class="bg-themed-tertiary text-themed-primary px-3 py-1 rounded-full capitalize border border-themed-secondary">
                    {{ $course->difficulty_level }}
                </span>
                <span class="flex items-center text-themed-secondary">
                    <i class="fas fa-clock mr-1"></i>
                    {{ $course->formatted_duration }}
                </span>
            </div>

            @if($currentSection)
                <div class="mt-4 text-sm">
                    <span class="text-themed-secondary">Current Section:</span>
                    <span class="font-semibold text-themed-primary ml-1">{{ $currentSection->title }}</span>
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <!-- Progress Circle -->
            <div class="bg-themed-tertiary p-4 rounded-full text-center min-w-[80px] border-2 border-themed-secondary transition-colors duration-300">
                <span class="text-2xl font-bold block text-themed-primary">{{ $overallProgress }}%</span>
                <span class="text-xs text-themed-tertiary">Complete</span>
            </div>

            <!-- Progress Stats -->
            <div class="text-right">
                @php
                    $stats = $this->getProgressStats();
                @endphp
                <div class="text-sm text-themed-secondary">
                    {{ $stats['completed'] }}/{{ $stats['total'] }} Lessons
                </div>
                <div class="w-24 bg-themed-tertiary rounded-full h-2 mt-1 border border-themed-secondary">
                    <div class="bg-gradient-to-r from-accent-themed-primary to-accent-themed-secondary h-2 rounded-full transition-all duration-300" 
                         style="width: {{ $overallProgress }}%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Overall Progress Bar -->
    <div class="mt-6">
        <div class="flex justify-between items-center mb-2">
            <span class="text-sm text-themed-secondary">Course Progress</span>
            <span class="text-sm font-medium text-themed-primary">{{ $overallProgress }}%</span>
        </div>
        <div class="w-full bg-themed-tertiary rounded-full h-3 border border-themed-secondary">
            <div class="bg-gradient-to-r from-accent-themed-primary to-accent-themed-secondary h-3 rounded-full transition-all duration-500" 
                 style="width: {{ $overallProgress }}%"></div>
        </div>
    </div>

    <style>
        * {
            transition: background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease;
        }
    </style>
</div>
