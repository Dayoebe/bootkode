<div class="bg-themed-primary min-h-screen transition-colors duration-300">
    <div class="px-4 sm:px-6 lg:px-8 py-6 space-y-6">
        
        <!-- Page Header -->
        <div class="bg-themed-secondary rounded-2xl shadow-lg border border-themed-primary p-6 animate__animated animate__fadeInDown transition-colors duration-300">
            <div class="flex items-center gap-4">
                <div class="bg-gradient-to-br from-blue-500 to-purple-500 p-4 rounded-2xl shadow-lg">
                    <i class="fas fa-chart-line text-white text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-themed-primary transition-colors duration-300">
                        Review Analytics
                    </h1>
                    <p class="text-themed-secondary mt-1 transition-colors duration-300">
                        Insights and trends from student reviews
                    </p>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-themed-secondary rounded-2xl shadow-lg border border-themed-primary p-6 transition-colors duration-300">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-bold text-themed-primary mb-2 transition-colors duration-300">Course</label>
                    <select wire:model.live="courseId" class="w-full bg-themed-tertiary border-2 border-themed-primary rounded-xl px-4 py-3 text-themed-primary placeholder-themed-secondary focus:border-accent-themed-primary focus:ring-2 focus:ring-accent-themed-primary/20 transition-all duration-300">
                        <option value="">Select a course</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}">{{ $course->title }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-bold text-themed-primary mb-2 transition-colors duration-300">Time Range</label>
                    <select wire:model.live="timeRange" class="w-full bg-themed-tertiary border-2 border-themed-primary rounded-xl px-4 py-3 text-themed-primary placeholder-themed-secondary focus:border-accent-themed-primary focus:ring-2 focus:ring-accent-themed-primary/20 transition-all duration-300">
                        <option value="7">Last 7 days</option>
                        <option value="30">Last 30 days</option>
                        <option value="90">Last 90 days</option>
                        <option value="365">Last year</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-bold text-themed-primary mb-2 transition-colors duration-300">Metric</label>
                    <select wire:model.live="selectedMetric" class="w-full bg-themed-tertiary border-2 border-themed-primary rounded-xl px-4 py-3 text-themed-primary placeholder-themed-secondary focus:border-accent-themed-primary focus:ring-2 focus:ring-accent-themed-primary/20 transition-all duration-300">
                        <option value="rating">Average Rating</option>
                        <option value="sentiment">Sentiment Score</option>
                        <option value="response_rate">Response Rate</option>
                    </select>
                </div>
            </div>
        </div>

        @if($selectedCourse)
            <!-- Key Metrics -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                <div class="bg-themed-secondary rounded-xl shadow-md border border-themed-primary p-6 transform hover:scale-105 transition-all duration-300 animate__animated animate__fadeInUp" style="animation-delay: 0.1s">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-themed-secondary text-sm font-medium mb-1 transition-colors duration-300">
                                Average Rating
                            </p>
                            <h3 class="text-3xl font-bold text-accent-themed-primary transition-colors duration-300">
                                {{ number_format($instructorMetrics['avg_rating'], 1) }}
                            </h3>
                            <div class="flex mt-2">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star text-yellow-400 {{ $instructorMetrics['avg_rating'] >= $i ? '' : 'opacity-30' }}"></i>
                                @endfor
                            </div>
                        </div>
                        <div class="bg-yellow-100/50 p-4 rounded-xl transition-colors duration-300">
                            <i class="fas fa-star text-yellow-600 text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-themed-secondary rounded-xl shadow-md border border-themed-primary p-6 transform hover:scale-105 transition-all duration-300 animate__animated animate__fadeInUp" style="animation-delay: 0.2s">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-themed-secondary text-sm font-medium mb-1 transition-colors duration-300">
                                Total Reviews
                            </p>
                            <h3 class="text-3xl font-bold text-accent-themed-primary transition-colors duration-300">
                                {{ $instructorMetrics['total_reviews'] }}
                            </h3>
                            <p class="text-sm text-themed-tertiary mt-2 transition-colors duration-300">
                                {{ $instructorMetrics['replied_reviews'] }} replied
                            </p>
                        </div>
                        <div class="bg-blue-100/50 p-4 rounded-xl transition-colors duration-300">
                            <i class="fas fa-comments text-blue-600 text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-themed-secondary rounded-xl shadow-md border border-themed-primary p-6 transform hover:scale-105 transition-all duration-300 animate__animated animate__fadeInUp" style="animation-delay: 0.3s">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-themed-secondary text-sm font-medium mb-1 transition-colors duration-300">
                                Response Rate
                            </p>
                            <h3 class="text-3xl font-bold text-accent-themed-primary transition-colors duration-300">
                                {{ number_format($instructorMetrics['response_rate'], 0) }}%
                            </h3>
                            <div class="w-full bg-themed-tertiary rounded-full h-2 mt-2 transition-colors duration-300">
                                <div class="bg-green-500 h-2 rounded-full transition-all duration-500" style="width: {{ $instructorMetrics['response_rate'] }}%"></div>
                            </div>
                        </div>
                        <div class="bg-green-100/50 p-4 rounded-xl transition-colors duration-300">
                            <i class="fas fa-reply text-green-600 text-2xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-themed-secondary rounded-xl shadow-md border border-themed-primary p-6 transform hover:scale-105 transition-all duration-300 animate__animated animate__fadeInUp" style="animation-delay: 0.4s">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-themed-secondary text-sm font-medium mb-1 transition-colors duration-300">
                                Avg Response Time
                            </p>
                            <h3 class="text-3xl font-bold text-accent-themed-primary transition-colors duration-300">
                                {{ $instructorMetrics['avg_response_time_hours'] ?? 'N/A' }}
                            </h3>
                            <p class="text-sm text-themed-tertiary mt-2 transition-colors duration-300">hours</p>
                        </div>
                        <div class="bg-purple-100/50 p-4 rounded-xl transition-colors duration-300">
                            <i class="fas fa-clock text-purple-600 text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rating Trends Chart -->
            <div class="bg-themed-secondary rounded-2xl shadow-lg border border-themed-primary p-6 transition-colors duration-300">
                <h3 class="text-lg font-bold text-themed-primary mb-4 transition-colors duration-300">Rating Trends</h3>
                <div class="h-64" x-data="{ 
                    chartData: @js($ratingTrends),
                    init() {
                        console.log(this.chartData);
                    }
                }">
                    <canvas id="ratingTrendsChart"></canvas>
                </div>
            </div>

            <!-- Satisfaction Metrics -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Sentiment Analysis -->
                <div class="bg-themed-secondary rounded-2xl shadow-lg border border-themed-primary p-6 transition-colors duration-300">
                    <h3 class="text-lg font-bold text-themed-primary mb-4 flex items-center transition-colors duration-300">
                        <i class="fas fa-smile text-green-500 mr-2"></i>
                        Sentiment Analysis
                    </h3>
                    <div class="text-center py-8">
                        <div class="text-6xl font-bold mb-2 transition-colors duration-300
                            {{ $satisfactionMetrics['sentiment_score'] > 0.3 ? 'text-green-600' : ($satisfactionMetrics['sentiment_score'] < -0.3 ? 'text-red-600' : 'text-yellow-600') }}">
                            {{ number_format($satisfactionMetrics['sentiment_score'], 2) }}
                        </div>
                        <p class="text-themed-secondary transition-colors duration-300">
                            {{ $satisfactionMetrics['sentiment_score'] > 0.3 ? 'Positive' : ($satisfactionMetrics['sentiment_score'] < -0.3 ? 'Negative' : 'Neutral') }}
                        </p>
                        <div class="mt-4">
                            <span class="px-4 py-2 rounded-full text-sm font-bold transition-colors duration-300
                                {{ $satisfactionMetrics['trend'] === 'increasing' ? 'bg-green-100/50 text-green-800' : ($satisfactionMetrics['trend'] === 'decreasing' ? 'bg-red-100/50 text-red-800' : 'bg-themed-tertiary text-themed-secondary') }}">
                                <i class="fas fa-{{ $satisfactionMetrics['trend'] === 'increasing' ? 'arrow-up' : ($satisfactionMetrics['trend'] === 'decreasing' ? 'arrow-down' : 'minus') }} mr-1"></i>
                                Trend: {{ ucfirst($satisfactionMetrics['trend']) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Top Keywords -->
                <div class="bg-themed-secondary rounded-2xl shadow-lg border border-themed-primary p-6 transition-colors duration-300">
                    <h3 class="text-lg font-bold text-themed-primary mb-4 flex items-center transition-colors duration-300">
                        <i class="fas fa-tags text-accent-themed-primary mr-2"></i>
                        Most Common Keywords
                    </h3>
                    <div class="space-y-3">
                        @foreach($satisfactionMetrics['top_keywords'] as $keyword => $count)
                            <div class="flex items-center justify-between">
                                <span class="text-themed-primary font-medium transition-colors duration-300">{{ $keyword }}</span>
                                <div class="flex items-center">
                                    <div class="w-32 bg-themed-tertiary rounded-full h-2 mr-3 transition-colors duration-300">
                                        <div class="bg-accent-themed-primary h-2 rounded-full transition-all duration-500" style="width: {{ min(($count / max(array_values($satisfactionMetrics['top_keywords']))) * 100, 100) }}%"></div>
                                    </div>
                                    <span class="text-sm text-themed-secondary w-8 text-right transition-colors duration-300">{{ $count }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Rating Distribution -->
            <div class="bg-themed-secondary rounded-2xl shadow-lg border border-themed-primary p-6 transition-colors duration-300">
                <h3 class="text-lg font-bold text-themed-primary mb-4 transition-colors duration-300">Rating Distribution</h3>
                <div class="space-y-3">
                    @foreach([5,4,3,2,1] as $star)
                        @php
                            $count = $instructorMetrics['rating_distribution'][$star] ?? 0;
                            $percentage = $instructorMetrics['total_reviews'] > 0 ? ($count / $instructorMetrics['total_reviews']) * 100 : 0;
                        @endphp
                        <div class="flex items-center gap-4">
                            <div class="w-12 text-sm font-bold text-themed-primary transition-colors duration-300">
                                {{ $star }} <i class="fas fa-star text-yellow-400 text-xs"></i>
                            </div>
                            <div class="flex-1 bg-themed-tertiary rounded-full h-4 transition-colors duration-300">
                                <div class="bg-gradient-to-r from-yellow-400 to-yellow-600 h-4 rounded-full transition-all duration-500" 
                                     style="width: {{ $percentage }}%"></div>
                            </div>
                            <div class="w-24 text-sm text-themed-secondary text-right transition-colors duration-300">
                                {{ $count }} ({{ number_format($percentage, 1) }}%)
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="text-center py-20 bg-themed-secondary rounded-2xl shadow-lg border border-themed-primary transition-colors duration-300">
                <i class="fas fa-chart-line text-themed-tertiary text-6xl mb-4"></i>
                <h3 class="text-xl font-bold text-themed-primary mb-2 transition-colors duration-300">No Course Selected</h3>
                <p class="text-themed-secondary transition-colors duration-300">Select a course above to view analytics</p>
            </div>
        @endif

        <!-- Loading Overlay -->
        <div wire:loading class="fixed inset-0 bg-black/30 backdrop-blur-sm flex items-center justify-center z-50">
            <div class="bg-themed-secondary rounded-2xl p-8 flex items-center shadow-2xl border border-themed-primary transition-colors duration-300">
                <div class="relative mr-4">
                    <div class="animate-spin rounded-full h-12 w-12 border-4 border-themed-tertiary"></div>
                    <div class="animate-spin rounded-full h-12 w-12 border-4 border-accent-themed-primary border-t-transparent absolute top-0"></div>
                </div>
                <span class="text-themed-primary font-semibold transition-colors duration-300">Loading analytics...</span>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('livewire:navigated', function() {
        const ctx = document.getElementById('ratingTrendsChart');
        if (ctx && @js($ratingTrends)) {
            const data = @js($ratingTrends);
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.map(d => d.date),
                    datasets: [{
                        label: 'Average Rating',
                        data: data.map(d => d.rating),
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 5
                        }
                    }
                }
            });
        }
    });
</script>
@endpush