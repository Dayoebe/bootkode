{{-- resources/views/livewire/career/mock-interview/partials/analytics-tab.blade.php --}}

<div class="space-y-8">
    @php
        $userId = Auth::id();
        $completedInterviews = \App\Models\Mentorship\Mentorship\MockInterview::where('user_id', $userId)
            ->where('status', 'completed')
            ->orderBy('completed_at', 'desc')
            ->get();
        
        $allInterviews = \App\Models\Mentorship\Mentorship\MockInterview::where('user_id', $userId)->get();
        
        $averageScore = $completedInterviews->avg('overall_score') ?? 0;
        $technicalScore = $completedInterviews->avg('technical_score') ?? 0;
        $communicationScore = $completedInterviews->avg('communication_score') ?? 0;
        $problemSolvingScore = $completedInterviews->avg('problem_solving_score') ?? 0;
        $confidenceScore = $completedInterviews->avg('confidence_score') ?? 0;
        
        // Calculate improvement rate
        $recentInterviews = $completedInterviews->take(5);
        $olderInterviews = $completedInterviews->skip(5)->take(5);
        $recentAvg = $recentInterviews->avg('overall_score') ?? 0;
        $olderAvg = $olderInterviews->avg('overall_score') ?? 0;
        $improvementRate = $olderAvg > 0 ? max(0, round((($recentAvg - $olderAvg) / $olderAvg) * 100, 1)) : 0;
        
        // Calculate streak (consecutive completed interviews)
        $streak = 0;
        foreach ($completedInterviews as $interview) {
            $streak++;
        }
        
        // Count by type
        $technicalCount = $completedInterviews->where('type', 'technical')->count();
        $behavioralCount = $completedInterviews->where('type', 'behavioral')->count();
        $systemDesignCount = $completedInterviews->where('type', 'system_design')->count();
        $codingCount = $completedInterviews->where('type', 'coding')->count();
        
        // Performance by type
        $technicalAvg = $completedInterviews->where('type', 'technical')->avg('overall_score') ?? 0;
        $behavioralAvg = $completedInterviews->where('type', 'behavioral')->avg('overall_score') ?? 0;
        $systemDesignAvg = $completedInterviews->where('type', 'system_design')->avg('overall_score') ?? 0;
        $codingAvg = $completedInterviews->where('type', 'coding')->avg('overall_score') ?? 0;
    @endphp

    <!-- Performance Overview -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <div class="bg-themed-secondary border border-themed-primary rounded-2xl shadow-lg p-6">
            <div class="flex items-center">
                <div class="bg-blue-100 dark:bg-blue-900/30 p-3 rounded-full">
                    <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-themed-secondary">Overall Performance</p>
                    <p class="text-2xl font-bold text-themed-primary">{{ number_format($averageScore, 1) }}%</p>
                </div>
            </div>
        </div>

        <div class="bg-themed-secondary border border-themed-primary rounded-2xl shadow-lg p-6">
            <div class="flex items-center">
                <div class="bg-green-100 dark:bg-green-900/30 p-3 rounded-full">
                    <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-themed-secondary">Improvement Rate</p>
                    <p class="text-2xl font-bold text-themed-primary">+{{ $improvementRate }}%</p>
                </div>
            </div>
        </div>

        <div class="bg-themed-secondary border border-themed-primary rounded-2xl shadow-lg p-6">
            <div class="flex items-center">
                <div class="bg-purple-100 dark:bg-purple-900/30 p-3 rounded-full">
                    <svg class="w-8 h-8 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-themed-secondary">Total Interviews</p>
                    <p class="text-2xl font-bold text-themed-primary">{{ $completedInterviews->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-themed-secondary border border-themed-primary rounded-2xl shadow-lg p-6">
            <div class="flex items-center">
                <div class="bg-orange-100 dark:bg-orange-900/30 p-3 rounded-full">
                    <svg class="w-8 h-8 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z" />
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-themed-secondary">Completed</p>
                    <p class="text-2xl font-bold text-themed-primary">{{ $streak }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Analytics -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Score Breakdown -->
        <div class="bg-themed-secondary border border-themed-primary rounded-2xl shadow-lg p-6">
            <h3 class="text-xl font-bold text-themed-primary mb-6">Score Breakdown</h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-themed-secondary">Technical Skills</span>
                    <div class="flex items-center">
                        <div class="w-32 bg-themed-tertiary rounded-full h-2 mr-3">
                            <div class="bg-blue-600 dark:bg-blue-400 h-2 rounded-full" style="width: {{ $technicalScore }}%"></div>
                        </div>
                        <span class="font-semibold text-themed-primary">{{ number_format($technicalScore, 1) }}%</span>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-themed-secondary">Communication</span>
                    <div class="flex items-center">
                        <div class="w-32 bg-themed-tertiary rounded-full h-2 mr-3">
                            <div class="bg-green-600 dark:bg-green-400 h-2 rounded-full" style="width: {{ $communicationScore }}%"></div>
                        </div>
                        <span class="font-semibold text-themed-primary">{{ number_format($communicationScore, 1) }}%</span>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-themed-secondary">Problem Solving</span>
                    <div class="flex items-center">
                        <div class="w-32 bg-themed-tertiary rounded-full h-2 mr-3">
                            <div class="bg-purple-600 dark:bg-purple-400 h-2 rounded-full" style="width: {{ $problemSolvingScore }}%"></div>
                        </div>
                        <span class="font-semibold text-themed-primary">{{ number_format($problemSolvingScore, 1) }}%</span>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-themed-secondary">Confidence</span>
                    <div class="flex items-center">
                        <div class="w-32 bg-themed-tertiary rounded-full h-2 mr-3">
                            <div class="bg-orange-600 dark:bg-orange-400 h-2 rounded-full" style="width: {{ $confidenceScore }}%"></div>
                        </div>
                        <span class="font-semibold text-themed-primary">{{ number_format($confidenceScore, 1) }}%</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Interview Type Performance -->
        <div class="bg-themed-secondary border border-themed-primary rounded-2xl shadow-lg p-6">
            <h3 class="text-xl font-bold text-themed-primary mb-6">Performance by Interview Type</h3>
            <div class="space-y-4">
                @if($technicalCount > 0)
                    <div class="flex items-center justify-between">
                        <span class="text-themed-secondary">
                            Technical ({{ $technicalCount }})
                        </span>
                        <span class="font-semibold text-blue-600 dark:text-blue-400">{{ number_format($technicalAvg, 1) }}%</span>
                    </div>
                @endif
                
                @if($behavioralCount > 0)
                    <div class="flex items-center justify-between">
                        <span class="text-themed-secondary">
                            Behavioral ({{ $behavioralCount }})
                        </span>
                        <span class="font-semibold text-green-600 dark:text-green-400">{{ number_format($behavioralAvg, 1) }}%</span>
                    </div>
                @endif
                
                @if($systemDesignCount > 0)
                    <div class="flex items-center justify-between">
                        <span class="text-themed-secondary">
                            System Design ({{ $systemDesignCount }})
                        </span>
                        <span class="font-semibold text-purple-600 dark:text-purple-400">{{ number_format($systemDesignAvg, 1) }}%</span>
                    </div>
                @endif
                
                @if($codingCount > 0)
                    <div class="flex items-center justify-between">
                        <span class="text-themed-secondary">
                            Coding ({{ $codingCount }})
                        </span>
                        <span class="font-semibold text-orange-600 dark:text-orange-400">{{ number_format($codingAvg, 1) }}%</span>
                    </div>
                @endif

                @if($completedInterviews->isEmpty())
                    <div class="text-center py-6">
                        <p class="text-themed-secondary">No completed interviews yet. Start practicing to see your analytics!</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Recent Interviews Timeline -->
    @if($completedInterviews->isNotEmpty())
        <div class="bg-themed-secondary border border-themed-primary rounded-2xl shadow-lg p-6">
            <h3 class="text-xl font-bold text-themed-primary mb-6">Recent Interview Results</h3>
            <div class="space-y-3">
                @foreach($completedInterviews->take(10) as $interview)
                    <div class="flex items-center justify-between p-4 bg-themed-tertiary rounded-lg">
                        <div class="flex items-center flex-1">
                            <div class="text-2xl mr-4">{{ $interview->getTypeIcon() }}</div>
                            <div class="flex-1">
                                <h4 class="font-semibold text-themed-primary">{{ $interview->title }}</h4>
                                <p class="text-sm text-themed-secondary">{{ $interview->completed_at?->format('M d, Y H:i') ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-4">
                            <div class="text-right">
                                <p class="font-bold text-lg text-accent-themed-primary">{{ number_format($interview->overall_score ?? 0, 1) }}%</p>
                                <p class="text-xs text-themed-secondary">{{ $interview->difficulty_label }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <div class="bg-themed-secondary border border-themed-primary rounded-2xl shadow-lg p-8 text-center">
            <div class="mx-auto w-20 h-20 bg-themed-tertiary rounded-full flex items-center justify-center mb-4">
                <svg class="w-10 h-10 text-themed-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
            </div>
            <h3 class="text-2xl font-semibold text-themed-primary mb-2">No Data Yet</h3>
            <p class="text-themed-secondary mb-4">Complete your first mock interview to start tracking your analytics and progress.</p>
            <button wire:click="$set('activeTab', 'practice')" class="bg-accent-themed-primary hover:bg-accent-themed-secondary text-white px-6 py-2 rounded-lg transition-colors">
                Start Practice
            </button>
        </div>
    @endif
</div>