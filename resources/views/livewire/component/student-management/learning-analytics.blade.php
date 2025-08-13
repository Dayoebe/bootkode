<div class="bg-gray-800 rounded-xl p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-white">Learning Analytics</h1>
        <select wire:model="timeRange" wire:change="prepareChartData" 
                class="bg-gray-700 border border-gray-600 text-white rounded-lg px-3 py-1 text-sm">
            <option value="week">Last Week</option>
            <option value="month">Last Month</option>
            <option value="year">Last Year</option>
        </select>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-gray-700/50 p-4 rounded-lg border border-gray-600">
            <h3 class="text-gray-400 text-sm">Courses Enrolled</h3>
            <p class="text-2xl font-bold text-white">{{ $stats['totalCourses'] }}</p>
        </div>
        <div class="bg-gray-700/50 p-4 rounded-lg border border-gray-600">
            <h3 class="text-gray-400 text-sm">Courses Completed</h3>
            <p class="text-2xl font-bold text-green-400">{{ $stats['completedCourses'] }}</p>
        </div>
        <div class="bg-gray-700/50 p-4 rounded-lg border border-gray-600">
            <h3 class="text-gray-400 text-sm">Lessons Completed</h3>
            <p class="text-2xl font-bold text-blue-400">{{ $stats['totalLessons'] }}</p>
        </div>
        <div class="bg-gray-700/50 p-4 rounded-lg border border-gray-600">
            <h3 class="text-gray-400 text-sm">Day Streak</h3>
            <p class="text-2xl font-bold text-yellow-400">{{ $stats['activeStreak'] }} days</p>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Activity Chart -->
        <div class="bg-gray-700/30 p-4 rounded-lg border border-gray-600">
            <h3 class="text-lg font-semibold text-white mb-4">Your Activity</h3>
            <canvas 
                x-data="{
                    chart: null,
                    init() {
                        this.chart = new Chart(this.$el, {
                            type: 'bar',
                            data: @js($activityData),
                            options: {
                                responsive: true,
                                scales: {
                                    y: {
                                        beginAtZero: true
                                    }
                                }
                            }
                        });
                        $wire.on('refreshCharts', () => {
                            this.chart.data = @this.activityData;
                            this.chart.update();
                        });
                    }
                }"
                wire:ignore
                x-init="init"
                class="w-full h-64">
            </canvas>
        </div>

        <!-- Progress Chart -->
        <div class="bg-gray-700/30 p-4 rounded-lg border border-gray-600">
            <h3 class="text-lg font-semibold text-white mb-4">Top Courses Progress</h3>
            <canvas 
                x-data="{
                    chart: null,
                    init() {
                        this.chart = new Chart(this.$el, {
                            type: 'bar',
                            data: @js($progressData),
                            options: {
                                responsive: true,
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        max: 100
                                    }
                                }
                            }
                        });
                        $wire.on('refreshCharts', () => {
                            this.chart.data = @this.progressData;
                            this.chart.update();
                        });
                    }
                }"
                wire:ignore
                x-init="init"
                class="w-full h-64">
            </canvas>
        </div>
    </div>

    <!-- Time Spent Chart -->
    <div class="bg-gray-700/30 p-4 rounded-lg border border-gray-600 mb-6">
        <h3 class="text-lg font-semibold text-white mb-4">Time Distribution</h3>
        <canvas 
            x-data="{
                chart: null,
                init() {
                    this.chart = new Chart(this.$el, {
                        type: 'pie',
                        data: @js($timeSpentData),
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    position: 'right',
                                }
                            }
                        }
                    });
                    $wire.on('refreshCharts', () => {
                        this.chart.data = @this.timeSpentData;
                        this.chart.update();
                    });
                }
            }"
            wire:ignore
            x-init="init"
            class="w-full h-80">
        </canvas>
    </div>

    <!-- Course Progress Table -->
    <div class="bg-gray-700/30 rounded-lg border border-gray-600 overflow-hidden">
        <h3 class="text-lg font-semibold text-white p-4 border-b border-gray-600">All Courses Progress</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-600">
                <thead class="bg-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">Course</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">Progress</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">Last Accessed</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-400 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-600">
                    @foreach($courses as $course)
                    <tr>
                        <td class="px-4 py-3 text-sm text-white">{{ $course->title }}</td>
                        <td class="px-4 py-3">
                            <div class="w-full bg-gray-600 rounded-full h-2">
                                <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $course->pivot->progress }}%"></div>
                            </div>
                            <span class="text-xs text-gray-400 mt-1">{{ $course->pivot->progress }}%</span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-400">{{ $course->pivot->updated_at->format('M d, Y') }}</td>
                        <td class="px-4 py-3 text-sm">
                            <a href="{{ route('course.view', $course->slug) }}" class="text-blue-400 hover:text-blue-300">Continue</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush