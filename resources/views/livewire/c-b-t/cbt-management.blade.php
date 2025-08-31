<div class="bg-dark-bg text-white min-h-screen" x-data="cbtManagement()">
    <!-- Animated Background -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-purple-500/10 rounded-full blur-3xl floating"></div>
        <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-blue-500/10 rounded-full blur-3xl floating"
            style="animation-delay: 1s;"></div>
    </div>

    <!-- Header with Glassmorphism -->
    <div class="gradient-bg p-8 mb-8 relative overflow-hidden">
        <div class="absolute inset-0 bg-black/20"></div>
        <div class="max-w-7xl mx-auto relative z-10">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-5xl font-bold mb-4 animate__animated animate__fadeInDown">
                        <i class="fas fa-brain mr-4 text-neon-blue"></i>CBT Management Hub
                    </h1>
                    <p class="text-xl opacity-90 animate__animated animate__fadeInUp animate__delay-1s">
                        Advanced Computer-Based Testing Platform with AI-Powered Analytics
                    </p>
                </div>
                <div class="hidden lg:block">
                    <div class="glassmorphism rounded-2xl p-6">
                        <div class="text-center">
                            <div class="text-3xl font-bold text-neon-blue" x-text="stats.total_exams || 0"></div>
                            <div class="text-sm opacity-75">Total Exams</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 pb-8">
        <!-- Navigation Tabs with Enhanced Design -->
        <div class="mb-8">
            <div class="glassmorphism rounded-3xl p-3 flex space-x-3 backdrop-blur-xl border border-white/10">
                <button @click="setActiveTab('overview')"
                    :class="activeTab === 'overview' ? 'bg-accent text-dark-bg shadow-2xl shadow-accent/25' : 'text-gray-300 hover:text-white hover:bg-white/10'"
                    class="flex-1 py-4 px-6 rounded-2xl font-semibold transition-all duration-500 transform hover:scale-105">
                    <i class="fas fa-chart-pie mr-3"></i> Analytics Dashboard
                </button>

                <button @click="setActiveTab('exams')"
                    :class="activeTab === 'exams' ? 'bg-accent text-dark-bg shadow-2xl shadow-accent/25' : 'text-gray-300 hover:text-white hover:bg-white/10'"
                    class="flex-1 py-4 px-6 rounded-2xl font-semibold transition-all duration-500 transform hover:scale-105">
                    <i class="fas fa-cogs mr-3"></i> Exam Studio
                </button>

                <button @click="setActiveTab('results')"
                    :class="activeTab === 'results' ? 'bg-accent text-dark-bg shadow-2xl shadow-accent/25' : 'text-gray-300 hover:text-white hover:bg-white/10'"
                    class="flex-1 py-4 px-6 rounded-2xl font-semibold transition-all duration-500 transform hover:scale-105">
                    <i class="fas fa-trophy mr-3"></i> Results Center
                </button>

                <button @click="setActiveTab('monitoring')"
                    :class="activeTab === 'monitoring' ? 'bg-accent text-dark-bg shadow-2xl shadow-accent/25' : 'text-gray-300 hover:text-white hover:bg-white/10'"
                    class="flex-1 py-4 px-6 rounded-2xl font-semibold transition-all duration-500 transform hover:scale-105">
                    <i class="fas fa-eye mr-3"></i> Live Monitor
                </button>
            </div>
        </div>

        <!-- Overview Tab -->
        <div x-show="activeTab === 'overview'" class="animate-fade-in">
            <!-- Enhanced Stats Cards with Animations -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div
                    class="bg-dark-surface rounded-3xl p-8 border border-blue-500/20 card-hover relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-blue-500/10 rounded-full -mr-16 -mt-16"></div>
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-6">
                            <div
                                class="w-16 h-16 bg-blue-600/20 rounded-2xl flex items-center justify-center pulse-ring">
                                <i class="fas fa-file-alt text-blue-400 text-2xl"></i>
                            </div>
                            <span class="text-3xl font-bold text-blue-400" x-text="stats.total_exams || 0"></span>
                        </div>
                        <h3 class="font-bold text-lg mb-2">Total Exams</h3>
                        <p class="text-gray-400 text-sm">All created examinations</p>
                        <div class="mt-4 text-xs text-blue-400">
                            <span x-text="stats.draft_exams || 0"></span> drafts •
                            <span x-text="stats.published_exams || 0"></span> published
                        </div>
                    </div>
                </div>

                <div
                    class="bg-dark-surface rounded-3xl p-8 border border-green-500/20 card-hover relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-green-500/10 rounded-full -mr-16 -mt-16"></div>
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-6">
                            <div
                                class="w-16 h-16 bg-green-600/20 rounded-2xl flex items-center justify-center pulse-ring">
                                <i class="fas fa-users text-green-400 text-2xl"></i>
                            </div>
                            <span class="text-3xl font-bold text-green-400"
                                x-text="stats.unique_participants || 0"></span>
                        </div>
                        <h3 class="font-bold text-lg mb-2">Active Participants</h3>
                        <p class="text-gray-400 text-sm">Unique test takers</p>
                        <div class="mt-4 text-xs text-green-400">
                            <span x-text="stats.completed_attempts || 0"></span> completed attempts
                        </div>
                    </div>
                </div>

                <div
                    class="bg-dark-surface rounded-3xl p-8 border border-purple-500/20 card-hover relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-purple-500/10 rounded-full -mr-16 -mt-16"></div>
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-6">
                            <div
                                class="w-16 h-16 bg-purple-600/20 rounded-2xl flex items-center justify-center pulse-ring">
                                <i class="fas fa-percentage text-purple-400 text-2xl"></i>
                            </div>
                            <span class="text-3xl font-bold text-purple-400"
                                x-text="Math.round(stats.average_pass_rate || 0) + '%'"></span>
                        </div>
                        <h3 class="font-bold text-lg mb-2">Pass Rate</h3>
                        <p class="text-gray-400 text-sm">Overall average</p>
                        <div class="mt-4 w-full bg-gray-700 rounded-full h-2">
                            <div class="bg-purple-500 h-2 rounded-full transition-all duration-1000"
                                :style="'width: ' + (stats.average_pass_rate || 0) + '%'"></div>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-dark-surface rounded-3xl p-8 border border-yellow-500/20 card-hover relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-yellow-500/10 rounded-full -mr-16 -mt-16"></div>
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-6">
                            <div
                                class="w-16 h-16 bg-yellow-600/20 rounded-2xl flex items-center justify-center pulse-ring">
                                <i class="fas fa-clock text-yellow-400 text-2xl"></i>
                            </div>
                            <span class="text-3xl font-bold text-yellow-400" x-text="stats.total_attempts || 0"></span>
                        </div>
                        <h3 class="font-bold text-lg mb-2">Total Attempts</h3>
                        <p class="text-gray-400 text-sm">All exam sessions</p>
                        <div class="mt-4 text-xs text-yellow-400">
                            <i class="fas fa-trending-up mr-1"></i>
                            <span x-text="stats.total_questions || 0"></span> total questions
                        </div>
                    </div>
                </div>
            </div>

            <!-- Interactive Charts Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <!-- Enhanced Monthly Trends -->
                <div class="bg-dark-surface rounded-3xl p-8 border border-gray-700/50">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-2xl font-bold flex items-center">
                            <i class="fas fa-chart-line mr-3 text-neon-blue"></i>Performance Trends
                        </h3>
                        <select x-model="analyticsDateRange" @change="updateCharts()"
                            class="bg-dark-card border border-gray-600 rounded-xl px-4 py-2 text-sm">
                            <option value="30days">Last 30 Days</option>
                            <option value="90days">Last 90 Days</option>
                            <option value="6months">Last 6 Months</option>
                            <option value="1year">Last Year</option>
                        </select>
                    </div>
                    <div class="h-80">
                        <canvas id="performanceTrendsChart"></canvas>
                    </div>
                </div>

                <!-- Exam Type Distribution -->
                <div class="bg-dark-surface rounded-3xl p-8 border border-gray-700/50">
                    <h3 class="text-2xl font-bold mb-6 flex items-center">
                        <i class="fas fa-chart-doughnut mr-3 text-neon-purple"></i>Exam Distribution
                    </h3>
                    <div class="h-80">
                        <canvas id="examDistributionChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Recent Activity & Popular Exams -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Recent Results with Enhanced UI -->
                <div class="bg-dark-surface rounded-3xl p-8 border border-gray-700/50">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-2xl font-bold flex items-center">
                            <i class="fas fa-clock mr-3 text-green-400"></i>Live Activity Feed
                        </h3>
                        <div class="w-3 h-3 bg-green-400 rounded-full animate-ping"></div>
                    </div>

                    <div class="space-y-4 custom-scrollbar max-h-96 overflow-y-auto"
                        x-show="recentResults && recentResults.length > 0">
                        <template x-for="result in recentResults" :key="result.id">
                            <div
                                class="flex items-center p-4 bg-gradient-to-r from-gray-800/30 to-gray-700/20 rounded-2xl hover:from-gray-700/40 hover:to-gray-600/30 transition-all duration-300 border border-gray-600/20">
                                <div class="relative mr-4">
                                    <div
                                        class="w-12 h-12 rounded-full bg-gradient-to-r from-blue-500 to-purple-500 flex items-center justify-center">
                                        <span class="text-sm font-bold" x-text="result.user.name.charAt(0)"></span>
                                    </div>
                                    <div :class="result.passed ? 'bg-green-400' : 'bg-red-400'"
                                        class="absolute -bottom-1 -right-1 w-4 h-4 rounded-full border-2 border-dark-surface">
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-semibold text-white" x-text="result.user.name"></h4>
                                    <p class="text-sm text-gray-400" x-text="result.exam.title"></p>
                                    <div class="flex items-center mt-1 space-x-2">
                                        <span class="text-xs bg-blue-900/30 text-blue-400 px-2 py-1 rounded-full"
                                            x-text="'Attempt #' + result.attempt_number"></span>
                                        <span class="text-xs text-gray-500"
                                            x-text="formatTimeAgo(result.completed_at)"></span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div :class="result.passed ? 'text-green-400' : 'text-red-400'"
                                        class="text-xl font-bold" x-text="result.percentage_score.toFixed(1) + '%'">
                                    </div>
                                    <div class="text-xs text-gray-500" x-text="result.grade"></div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div x-show="!recentResults || recentResults.length === 0" class="text-center py-12 text-gray-400">
                        <div
                            class="w-20 h-20 bg-gray-800/50 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-inbox text-3xl"></i>
                        </div>
                        <p class="text-lg font-medium mb-2">No Recent Activity</p>
                        <p class="text-sm">Results will appear here as students complete exams</p>
                    </div>
                </div>

                <!-- Popular Exams with Performance Indicators -->
                <div class="bg-dark-surface rounded-3xl p-8 border border-gray-700/50">
                    <h3 class="text-2xl font-bold mb-6 flex items-center">
                        <i class="fas fa-fire mr-3 text-orange-400"></i>Top Performing Exams
                    </h3>

                    <div class="space-y-4" x-show="popularExams && popularExams.length > 0">
                        <template x-for="(exam, index) in popularExams" :key="exam.id">
                            <div
                                class="flex items-center p-4 bg-gradient-to-r from-gray-800/30 to-gray-700/20 rounded-2xl hover:from-orange-900/20 hover:to-red-900/20 transition-all duration-300 border border-gray-600/20">
                                <div class="relative mr-4">
                                    <div
                                        class="w-10 h-10 rounded-full bg-gradient-to-r from-orange-500 to-red-500 flex items-center justify-center">
                                        <span class="text-sm font-bold" x-text="index + 1"></span>
                                    </div>
                                    <div
                                        class="absolute -top-1 -right-1 w-6 h-6 bg-yellow-400 rounded-full flex items-center justify-center">
                                        <i class="fas fa-crown text-xs text-yellow-900"></i>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-semibold text-white mb-1" x-text="exam.title"></h4>
                                    <div class="flex items-center space-x-2">
                                        <span :class="getTypeColor(exam.exam_type)"
                                            class="text-xs px-2 py-1 rounded-full font-medium"
                                            x-text="exam.exam_type.toUpperCase()"></span>
                                        <span :class="getDifficultyColor(exam.difficulty_level)"
                                            class="text-xs px-2 py-1 rounded-full font-medium"
                                            x-text="exam.difficulty_level"></span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-xl font-bold text-orange-400" x-text="exam.results_count"></div>
                                    <div class="text-xs text-gray-500">attempts</div>
                                    <div class="w-16 h-1 bg-gray-700 rounded-full mt-2">
                                        <div class="h-1 bg-orange-400 rounded-full transition-all duration-1000"
                                            :style="'width: ' + Math.min(100, (exam.results_count / Math.max(...popularExams.map(e => e.results_count))) * 100) + '%'">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Quick Actions Panel -->
            <div class="bg-dark-surface rounded-3xl p-8 border border-gray-700/50 mb-8">
                <h3 class="text-2xl font-bold mb-6 flex items-center">
                    <i class="fas fa-bolt mr-3 text-yellow-400"></i>Quick Actions
                </h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <button @click="createFromTemplate('utme_style')"
                        class="bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-500 hover:to-blue-600 p-6 rounded-2xl text-center transition-all duration-300 transform hover:scale-105">
                        <i class="fas fa-graduation-cap text-2xl mb-3"></i>
                        <div class="font-semibold">UTME Style Exam</div>
                        <div class="text-xs opacity-75">High-security format</div>
                    </button>

                    <button @click="createFromTemplate('practice_quiz')"
                        class="bg-gradient-to-r from-green-600 to-green-700 hover:from-green-500 hover:to-green-600 p-6 rounded-2xl text-center transition-all duration-300 transform hover:scale-105">
                        <i class="fas fa-dumbbell text-2xl mb-3"></i>
                        <div class="font-semibold">Practice Quiz</div>
                        <div class="text-xs opacity-75">Student-friendly</div>
                    </button>

                    <button @click="createFromTemplate('certification')"
                        class="bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-500 hover:to-purple-600 p-6 rounded-2xl text-center transition-all duration-300 transform hover:scale-105">
                        <i class="fas fa-certificate text-2xl mb-3"></i>
                        <div class="font-semibold">Certification</div>
                        <div class="text-xs opacity-75">Professional level</div>
                    </button>

                    <button @click="showExamModal = true"
                        class="bg-gradient-to-r from-accent to-green-600 hover:from-green-400 hover:to-green-500 text-dark-bg p-6 rounded-2xl text-center transition-all duration-300 transform hover:scale-105">
                        <i class="fas fa-plus text-2xl mb-3"></i>
                        <div class="font-semibold">Custom Exam</div>
                        <div class="text-xs opacity-75">Build from scratch</div>
                    </button>
                </div>
            </div>
        </div>

        <!-- Exam Studio Tab -->
        <div x-show="activeTab === 'exams'" class="animate-fade-in">
            <!-- Enhanced Header with Filters -->
            <div class="bg-dark-surface rounded-3xl p-6 mb-8 border border-gray-700/50">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <!-- Search and Filters -->
                    <div class="flex-1 min-w-80">
                        <div class="relative">
                            <i class="fas fa-search absolute left-4 top-4 text-gray-400"></i>
                            <input type="text" x-model="searchTerm"
                                placeholder="Search exams by title, code, or description..."
                                class="w-full pl-12 pr-4 py-4 bg-dark-card border border-gray-600 rounded-2xl focus:ring-2 focus:ring-accent focus:border-transparent text-lg">
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <select x-model="courseFilter"
                            class="px-4 py-3 bg-dark-card border border-gray-600 rounded-xl focus:ring-2 focus:ring-accent">
                            <option value="all">All Courses</option>
                            <template x-for="course in courses" :key="course.id">
                                <option :value="course.id" x-text="course.title"></option>
                            </template>
                        </select>

                        <select x-model="examTypeFilter"
                            class="px-4 py-3 bg-dark-card border border-gray-600 rounded-xl focus:ring-2 focus:ring-accent">
                            <option value="all">All Types</option>
                            <option value="practice">Practice</option>
                            <option value="mock">Mock</option>
                            <option value="final">Final</option>
                            <option value="certification">Certification</option>
                        </select>

                        <select x-model="statusFilter"
                            class="px-4 py-3 bg-dark-card border border-gray-600 rounded-xl focus:ring-2 focus:ring-accent">
                            <option value="all">All Status</option>
                            <option value="published">Published</option>
                            <option value="draft">Draft</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>

                        <button @click="showExamModal = true"
                            class="bg-gradient-to-r from-accent to-green-600 hover:from-green-400 hover:to-green-500 text-dark-bg px-8 py-3 rounded-xl font-bold transition-all transform hover:scale-105 shadow-lg shadow-accent/25">
                            <i class="fas fa-plus mr-2"></i>Create Exam
                        </button>
                    </div>
                </div>
            </div>

            <!-- Bulk Actions Bar -->
            <div x-show="selectedExams.length > 0" x-transition:enter="animate__animated animate__slideInDown"
                class="bg-gradient-to-r from-blue-900/50 to-purple-900/50 border border-blue-500/50 rounded-2xl p-6 mb-6 glassmorphism">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center mr-3">
                            <span class="text-sm font-bold" x-text="selectedExams.length"></span>
                        </div>
                        <span class="font-semibold text-lg">
                            <span x-text="selectedExams.length"></span> exam(s) selected
                        </span>
                    </div>
                    <div class="flex gap-3">
                        <select x-model="bulkAction"
                            class="px-4 py-2 bg-dark-surface border border-gray-600 rounded-xl text-sm">
                            <option value="">Choose action...</option>
                            <option value="publish">📤 Publish</option>
                            <option value="unpublish">📥 Unpublish</option>
                            <option value="activate">✅ Activate</option>
                            <option value="deactivate">❌ Deactivate</option>
                            <option value="delete">🗑️ Delete</option>
                        </select>
                        <button @click="performBulkAction()" :disabled="!bulkAction"
                            class="bg-blue-600 hover:bg-blue-700 disabled:bg-gray-600 text-white px-6 py-2 rounded-xl font-semibold transition-all transform hover:scale-105">
                            Apply
                        </button>
                        <button @click="selectedExams = []"
                            class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-xl font-semibold transition-all">
                            Clear
                        </button>
                    </div>
                </div>
            </div>

            <!-- Enhanced Exams Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6"
                x-show="exams.data && exams.data.length > 0">
                <template x-for="exam in exams.data" :key="exam.id">
                    <div
                        class="bg-dark-surface rounded-3xl border border-gray-700/50 overflow-hidden card-hover relative group">
                        <!-- Exam Thumbnail/Header -->
                        <div class="relative h-48 bg-gradient-to-br from-gray-800 to-gray-900 overflow-hidden">
                            <div class="absolute inset-0 bg-gradient-to-br from-blue-600/20 to-purple-600/20"></div>
                            <div class="absolute top-4 left-4 flex space-x-2">
                                <span :class="getTypeColor(exam.exam_type)"
                                    class="px-3 py-1 rounded-full text-xs font-bold"
                                    x-text="exam.exam_type.toUpperCase()"></span>
                                <span :class="getDifficultyColor(exam.difficulty_level)"
                                    class="px-3 py-1 rounded-full text-xs font-bold"
                                    x-text="exam.difficulty_level.toUpperCase()"></span>
                            </div>

                            <div class="absolute top-4 right-4">
                                <input type="checkbox" :value="exam.id" x-model="selectedExams"
                                    class="w-5 h-5 rounded border-2 border-white/50 bg-white/10 text-accent focus:ring-accent">
                            </div>

                            <div class="absolute bottom-4 left-4 right-4">
                                <h3 class="text-xl font-bold text-white mb-2" x-text="exam.title"></h3>
                                <p class="text-sm text-gray-300" x-text="exam.course?.title || 'General'"></p>
                            </div>

                            <!-- Status Indicators -->
                            <div class="absolute bottom-4 right-4 flex space-x-2">
                                <div x-show="exam.is_published" class="w-3 h-3 bg-green-400 rounded-full animate-pulse">
                                </div>
                                <div x-show="exam.is_active" class="w-3 h-3 bg-blue-400 rounded-full animate-pulse">
                                </div>
                            </div>
                        </div>

                        <!-- Exam Details -->
                        <div class="p-6">
                            <div class="grid grid-cols-2 gap-4 mb-6">
                                <div class="text-center p-3 bg-gray-800/30 rounded-xl">
                                    <div class="text-2xl font-bold text-blue-400" x-text="exam.questions_count"></div>
                                    <div class="text-xs text-gray-400">Questions</div>
                                </div>
                                <div class="text-center p-3 bg-gray-800/30 rounded-xl">
                                    <div class="text-2xl font-bold text-purple-400" x-text="exam.results_count"></div>
                                    <div class="text-xs text-gray-400">Attempts</div>
                                </div>
                            </div>

                            <!-- Exam Metadata -->
                            <div class="space-y-2 mb-6 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-400">Duration:</span>
                                    <span class="font-semibold" x-text="exam.duration_minutes + ' minutes'"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-400">Pass Rate:</span>
                                    <span class="font-semibold" x-text="exam.pass_percentage + '%'"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-400">Code:</span>
                                    <span class="font-mono text-accent" x-text="exam.exam_code"></span>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex flex-wrap gap-2">
                                <button @click="editExam(exam.id)"
                                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 px-3 rounded-xl text-sm font-semibold transition-all transform hover:scale-105">
                                    <i class="fas fa-edit mr-1"></i>Edit
                                </button>

                                <button x-show="!exam.is_published" @click="publishExam(exam.id)"
                                    class="flex-1 bg-green-600 hover:bg-green-700 text-white py-2 px-3 rounded-xl text-sm font-semibold transition-all transform hover:scale-105">
                                    <i class="fas fa-rocket mr-1"></i>Publish
                                </button>

                                <button x-show="exam.is_published" @click="unpublishExam(exam.id)"
                                    class="flex-1 bg-orange-600 hover:bg-orange-700 text-white py-2 px-3 rounded-xl text-sm font-semibold transition-all transform hover:scale-105">
                                    <i class="fas fa-pause mr-1"></i>Unpublish
                                </button>

                                <button @click="duplicateExam(exam.id)"
                                    class="bg-purple-600 hover:bg-purple-700 text-white py-2 px-3 rounded-xl text-sm font-semibold transition-all transform hover:scale-105">
                                    <i class="fas fa-copy"></i>
                                </button>

                                <button @click="showDropdown = showDropdown === exam.id ? null : exam.id"
                                    class="bg-gray-600 hover:bg-gray-700 text-white py-2 px-3 rounded-xl text-sm transition-all relative">
                                    <i class="fas fa-ellipsis-v"></i>

                                    <!-- Dropdown Menu -->
                                    <div x-show="showDropdown === exam.id" @click.away="showDropdown = null"
                                        x-transition:enter="animate__animated animate__fadeIn animate__faster"
                                        class="absolute right-0 top-full mt-2 w-48 bg-dark-card border border-gray-600 rounded-xl shadow-2xl z-10">
                                        <div class="py-2">
                                            <button @click="previewExam(exam.id)"
                                                class="w-full text-left px-4 py-2 hover:bg-gray-700 transition-colors">
                                                <i class="fas fa-eye mr-2 text-blue-400"></i>Preview
                                            </button>
                                            <button @click="generateExamReport(exam.id)"
                                                class="w-full text-left px-4 py-2 hover:bg-gray-700 transition-colors">
                                                <i class="fas fa-chart-bar mr-2 text-green-400"></i>Analytics
                                            </button>
                                            <button @click="exportExamData(exam.id, 'csv')"
                                                class="w-full text-left px-4 py-2 hover:bg-gray-700 transition-colors">
                                                <i class="fas fa-download mr-2 text-purple-400"></i>Export Data
                                            </button>
                                            <hr class="my-2 border-gray-600">
                                            <button @click="archiveExam(exam.id)"
                                                class="w-full text-left px-4 py-2 hover:bg-gray-700 transition-colors text-yellow-400">
                                                <i class="fas fa-archive mr-2"></i>Archive
                                            </button>
                                            <button @click="deleteExam(exam.id)"
                                                class="w-full text-left px-4 py-2 hover:bg-gray-700 transition-colors text-red-400">
                                                <i class="fas fa-trash mr-2"></i>Delete
                                            </button>
                                        </div>
                                    </div>
                                </button>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Empty State -->
            <div x-show="!exams.data || exams.data.length === 0" class="text-center py-20">
                <div
                    class="w-32 h-32 bg-gray-800/50 rounded-full flex items-center justify-center mx-auto mb-8 floating">
                    <i class="fas fa-clipboard-list text-5xl text-gray-400"></i>
                </div>
                <h3 class="text-3xl font-bold mb-4">No Exams Found</h3>
                <p class="text-gray-400 text-lg mb-8 max-w-md mx-auto">Start building your first exam to engage students
                    with modern computer-based testing</p>
                <button @click="showExamModal = true"
                    class="bg-gradient-to-r from-accent to-green-600 hover:from-green-400 hover:to-green-500 text-dark-bg px-8 py-4 rounded-2xl font-bold text-lg transition-all transform hover:scale-105 shadow-lg shadow-accent/25">
                    <i class="fas fa-plus mr-2"></i>Create Your First Exam
                </button>
            </div>
        </div>

        <!-- Results Center Tab -->
        <div x-show="activeTab === 'results'" class="animate-fade-in">
            <!-- Results Overview Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-dark-surface rounded-3xl p-6 border border-gray-700/50 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-green-500/10 rounded-full -mr-12 -mt-12"></div>
                    <div class="relative z-10">
                        <h3 class="text-lg font-semibold mb-4 flex items-center">
                            <i class="fas fa-chart-line mr-3 text-blue-400"></i>Performance Trends
                        </h3>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-400">This Month</span>
                                <span class="font-semibold text-blue-400">+15%</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-400">Average Score</span>
                                <span class="font-semibold">82.4%</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-400">Completion Rate</span>
                                <span class="font-semibold text-green-400">94.2%</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-dark-surface rounded-3xl p-6 border border-gray-700/50 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-purple-500/10 rounded-full -mr-12 -mt-12"></div>
                    <div class="relative z-10">
                        <h3 class="text-lg font-semibold mb-4 flex items-center">
                            <i class="fas fa-users mr-3 text-purple-400"></i>Participation
                        </h3>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-400">New Participants</span>
                                <span class="font-semibold text-purple-400">127</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-400">Returning</span>
                                <span class="font-semibold">89</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-400">Dropout Rate</span>
                                <span class="font-semibold text-yellow-400">5.8%</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-dark-surface rounded-3xl p-6 border border-gray-700/50 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-orange-500/10 rounded-full -mr-12 -mt-12"></div>
                    <div class="relative z-10">
                        <h3 class="text-lg font-semibold mb-4 flex items-center">
                            <i class="fas fa-clock mr-3 text-orange-400"></i>Time Analytics
                        </h3>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-400">Avg Duration</span>
                                <span class="font-semibold text-orange-400">45m 32s</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-400">Fastest</span>
                                <span class="font-semibold">12m 15s</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-400">Longest</span>
                                <span class="font-semibold">2h 18m</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Export Controls -->
            <div class="bg-dark-surface rounded-3xl p-6 mb-8 border border-gray-700/50">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-bold">Export Results</h3>
                    <div class="flex gap-4">
                        <select x-model="exportDateRange"
                            class="px-4 py-2 bg-dark-card border border-gray-600 rounded-xl">
                            <option value="all">All Time</option>
                            <option value="7days">Last 7 Days</option>
                            <option value="30days">Last 30 Days</option>
                            <option value="90days">Last 90 Days</option>
                        </select>

                        <button @click="exportResults('csv')"
                            class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-xl transition-all transform hover:scale-105">
                            <i class="fas fa-file-csv mr-2"></i>Export CSV
                        </button>

                        <button @click="exportResults('pdf')"
                            class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-xl transition-all transform hover:scale-105">
                            <i class="fas fa-file-pdf mr-2"></i>Export PDF
                        </button>
                    </div>
                </div>
            </div>

            <!-- Results Table -->
            <div class="bg-dark-surface rounded-3xl border border-gray-700/50 overflow-hidden">
                <div class="bg-dark-card p-6 border-b border-gray-700">
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-bold">Detailed Results</h3>
                        <input type="text" x-model="searchTerm" placeholder="Search results..."
                            class="px-4 py-2 bg-dark-surface border border-gray-600 rounded-xl focus:ring-2 focus:ring-accent">
                    </div>
                </div>

                <div class="overflow-x-auto custom-scrollbar">
                    <table class="w-full">
                        <thead class="bg-dark-card/50">
                            <tr class="text-left">
                                <th class="p-6 font-bold">Student</th>
                                <th class="p-6 font-bold">Exam</th>
                                <th class="p-6 font-bold">Score</th>
                                <th class="p-6 font-bold">Grade</th>
                                <th class="p-6 font-bold">Duration</th>
                                <th class="p-6 font-bold">Date</th>
                                <th class="p-6 font-bold">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700">
                            <template x-for="result in results.data" :key="result.id">
                                <tr class="hover:bg-gray-800/30 transition-all">
                                    <td class="p-6">
                                        <div class="flex items-center">
                                            <div
                                                class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full flex items-center justify-center mr-4">
                                                <span class="text-sm font-bold"
                                                    x-text="result.user.name.charAt(0)"></span>
                                            </div>
                                            <div>
                                                <div class="font-semibold text-lg" x-text="result.user.name"></div>
                                                <div class="text-sm text-gray-400" x-text="result.user.email"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-6">
                                        <div class="font-semibold" x-text="result.exam.title"></div>
                                        <div class="text-sm text-gray-400 flex items-center">
                                            <span x-text="'Attempt #' + result.attempt_number"></span>
                                            <span class="mx-2">•</span>
                                            <span x-text="result.exam.exam_code"></span>
                                        </div>
                                    </td>
                                    <td class="p-6">
                                        <div class="flex items-center">
                                            <div class="w-24 h-3 bg-gray-700 rounded-full overflow-hidden mr-4">
                                                <div :style="'width: ' + result.percentage_score + '%'"
                                                    :class="result.passed ? 'bg-green-500' : 'bg-red-500'"
                                                    class="h-full transition-all duration-1000"></div>
                                            </div>
                                            <span :class="result.passed ? 'text-green-400' : 'text-red-400'"
                                                class="font-bold text-lg"
                                                x-text="result.percentage_score.toFixed(1) + '%'"></span>
                                        </div>
                                    </td>
                                    <td class="p-6">
                                        <span
                                            class="px-4 py-2 rounded-full text-sm font-bold bg-blue-900/30 text-blue-400"
                                            x-text="result.grade"></span>
                                    </td>
                                    <td class="p-6">
                                        <div class="font-mono text-lg"
                                            x-text="formatDuration(result.time_spent_seconds)"></div>
                                        <div class="text-xs text-gray-500"
                                            x-text="'of ' + result.exam.duration_minutes + ' min'"></div>
                                    </td>
                                    <td class="p-6">
                                        <div class="font-semibold" x-text="formatDate(result.completed_at)"></div>
                                        <div class="text-xs text-gray-500" x-text="formatTimeAgo(result.completed_at)">
                                        </div>
                                    </td>
                                    <td class="p-6">
                                        <div class="flex gap-2">
                                            <button @click="viewResult(result.id)"
                                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-sm font-semibold transition-all transform hover:scale-105">
                                                <i class="fas fa-eye mr-1"></i>View
                                            </button>

                                            <button @click="emailResultToStudent(result.id)"
                                                :disabled="result.result_emailed"
                                                class="bg-green-600 hover:bg-green-700 disabled:bg-gray-600 text-white px-4 py-2 rounded-xl text-sm font-semibold transition-all">
                                                <i class="fas fa-envelope mr-1"></i>Email
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Live Monitor Tab -->
        <div x-show="activeTab === 'monitoring'" class="animate-fade-in">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <!-- Active Sessions -->
                <div class="bg-dark-surface rounded-3xl p-6 border border-gray-700/50">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-bold flex items-center">
                            <i class="fas fa-eye mr-3 text-green-400"></i>Live Sessions
                        </h3>
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-green-400 rounded-full animate-ping mr-2"></div>
                            <span class="text-sm text-green-400 font-semibold"
                                x-text="activeSessions.length + ' Active'"></span>
                        </div>
                    </div>

                    <div class="space-y-4 max-h-96 overflow-y-auto custom-scrollbar">
                        <template x-for="session in activeSessions" :key="session.id">
                            <div class="bg-gray-800/30 rounded-2xl p-4 border border-green-500/20">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div
                                            class="w-10 h-10 bg-gradient-to-r from-green-500 to-blue-500 rounded-full flex items-center justify-center mr-3">
                                            <span class="text-sm font-bold" x-text="session.user.name.charAt(0)"></span>
                                        </div>
                                        <div>
                                            <div class="font-semibold" x-text="session.user.name"></div>
                                            <div class="text-sm text-gray-400" x-text="session.exam.title"></div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-semibold text-green-400"
                                            x-text="formatDuration(session.elapsed_time)"></div>
                                        <div class="text-xs text-gray-500"
                                            x-text="session.current_question + '/' + session.total_questions"></div>
                                    </div>
                                </div>
                                <div class="mt-3 flex gap-2">
                                    <button @click="monitorSession(session.id)"
                                        class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-lg text-xs transition-all">
                                        Monitor
                                    </button>
                                    <button @click="terminateSession(session.id)"
                                        class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded-lg text-xs transition-all">
                                        Terminate
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Security Alerts -->
                <div class="bg-dark-surface rounded-3xl p-6 border border-gray-700/50">
                    <h3 class="text-xl font-bold mb-6 flex items-center">
                        <i class="fas fa-shield-alt mr-3 text-red-400"></i>Security Alerts
                    </h3>

                    <div class="space-y-4">
                        <div class="bg-red-900/20 border border-red-500/30 rounded-xl p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="font-semibold text-red-400">Tab Switching Detected</div>
                                    <div class="text-sm text-gray-400">John Doe - JavaScript Fundamentals</div>
                                </div>
                                <button class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded-lg text-xs">
                                    Investigate
                                </button>
                            </div>
                        </div>

                        <div class="bg-yellow-900/20 border border-yellow-500/30 rounded-xl p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="font-semibold text-yellow-400">Unusual Activity</div>
                                    <div class="text-sm text-gray-400">Jane Smith - Multiple rapid answers</div>
                                </div>
                                <button
                                    class="bg-yellow-600 hover:bg-yellow-700 text-white px-3 py-1 rounded-lg text-xs">
                                    Review
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- System Status -->
            <div class="bg-dark-surface rounded-3xl p-6 border border-gray-700/50">
                <h3 class="text-xl font-bold mb-6">System Status</h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="text-center">
                        <div
                            class="w-16 h-16 bg-green-500/20 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-server text-green-400 text-xl"></i>
                        </div>
                        <div class="font-semibold text-green-400">Server Status</div>
                        <div class="text-sm text-gray-400">All systems operational</div>
                    </div>

                    <div class="text-center">
                        <div
                            class="w-16 h-16 bg-blue-500/20 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-database text-blue-400 text-xl"></i>
                        </div>
                        <div class="font-semibold text-blue-400">Database</div>
                        <div class="text-sm text-gray-400">Response time: 45ms</div>
                    </div>

                    <div class="text-center">
                        <div
                            class="w-16 h-16 bg-purple-500/20 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-cloud text-purple-400 text-xl"></i>
                        </div>
                        <div class="font-semibold text-purple-400">Storage</div>
                        <div class="text-sm text-gray-400">78% utilized</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create/Edit Exam Modal -->
    <div x-show="showExamModal" x-transition:enter="animate__animated animate__fadeIn"
        x-transition:leave="animate__animated animate__fadeOut"
        class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-center justify-center p-4">

        <div
            class="bg-dark-surface rounded-3xl w-full max-w-6xl max-h-[95vh] overflow-hidden flex flex-col border border-gray-700 shadow-2xl">
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-dark-card to-gray-800 p-6 border-b border-gray-700">
                <div class="flex justify-between items-center">
                    <h2 class="text-2xl font-bold flex items-center">
                        <i class="fas fa-cogs mr-3 text-accent"></i>
                        <span x-text="editingExam ? 'Edit Exam' : 'Create New Exam'"></span>
                    </h2>
                    <button @click="closeExamModal()" class="text-gray-400 hover:text-white text-2xl transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            <div class="flex-1 overflow-auto custom-scrollbar">
                <div class="p-8">
                    <form @submit.prevent="saveExam()">
                        <!-- Basic Information Section -->
                        <div class="bg-gray-800/30 rounded-2xl p-6 mb-8">
                            <h3 class="text-xl font-bold mb-6 flex items-center">
                                <i class="fas fa-info-circle mr-3 text-blue-400"></i>Basic Information
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-bold mb-3 text-accent">Exam Title *</label>
                                    <input type="text" x-model="title" required placeholder="Enter exam title..."
                                        class="w-full px-4 py-4 bg-dark-card border border-gray-600 rounded-2xl focus:ring-2 focus:ring-accent focus:border-transparent text-lg">
                                </div>

                                <div>
                                    <label class="block text-sm font-bold mb-3 text-accent">Course *</label>
                                    <select x-model="course_id" required
                                        class="w-full px-4 py-4 bg-dark-card border border-gray-600 rounded-2xl focus:ring-2 focus:ring-accent">
                                        <option value="">Select Course</option>
                                        <template x-for="course in courses" :key="course.id">
                                            <option :value="course.id" x-text="course.title"></option>
                                        </template>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-bold mb-3 text-accent">Exam Type *</label>
                                    <select x-model="exam_type" required
                                        class="w-full px-4 py-4 bg-dark-card border border-gray-600 rounded-2xl focus:ring-2 focus:ring-accent">
                                        <option value="practice">Practice Test</option>
                                        <option value="mock">Mock Examination</option>
                                        <option value="final">Final Assessment</option>
                                        <option value="certification">Certification Exam</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-bold mb-3 text-accent">Difficulty Level *</label>
                                    <select x-model="difficulty_level" required
                                        class="w-full px-4 py-4 bg-dark-card border border-gray-600 rounded-2xl focus:ring-2 focus:ring-accent">
                                        <option value="beginner">Beginner</option>
                                        <option value="intermediate">Intermediate</option>
                                        <option value="advanced">Advanced</option>
                                        <option value="expert">Expert</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mt-6">
                                <label class="block text-sm font-bold mb-3 text-accent">Description</label>
                                <textarea x-model="description" rows="4"
                                    placeholder="Provide a detailed description of the exam..."
                                    class="w-full px-4 py-4 bg-dark-card border border-gray-600 rounded-2xl focus:ring-2 focus:ring-accent focus:border-transparent resize-none"></textarea>
                            </div>
                        </div>

                        <!-- Exam Configuration -->
                        <div class="bg-gray-800/30 rounded-2xl p-6 mb-8">
                            <h3 class="text-xl font-bold mb-6 flex items-center">
                                <i class="fas fa-sliders-h mr-3 text-purple-400"></i>Exam Configuration
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <label class="block text-sm font-bold mb-3 text-accent">Duration (minutes) *</label>
                                    <input type="number" x-model="duration_minutes" min="10" max="300" required
                                        class="w-full px-4 py-4 bg-dark-card border border-gray-600 rounded-2xl focus:ring-2 focus:ring-accent">
                                </div>

                                <div>
                                    <label class="block text-sm font-bold mb-3 text-accent">Pass Percentage *</label>
                                    <input type="number" x-model="pass_percentage" min="50" max="100" step="0.1"
                                        required
                                        class="w-full px-4 py-4 bg-dark-card border border-gray-600 rounded-2xl focus:ring-2 focus:ring-accent">
                                </div>

                                <div>
                                    <label class="block text-sm font-bold mb-3 text-accent">Max Attempts *</label>
                                    <input type="number" x-model="max_attempts" min="1" max="10" required
                                        class="w-full px-4 py-4 bg-dark-card border border-gray-600 rounded-2xl focus:ring-2 focus:ring-accent">
                                </div>

                                <div>
                                    <label class="block text-sm font-bold mb-3 text-accent">Questions Per Page *</label>
                                    <select x-model="questions_per_page" required
                                        class="w-full px-4 py-4 bg-dark-card border border-gray-600 rounded-2xl focus:ring-2 focus:ring-accent">
                                        <option value="1">1 Question</option>
                                        <option value="2">2 Questions</option>
                                        <option value="3">3 Questions</option>
                                        <option value="5">5 Questions</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-bold mb-3 text-accent">Max Participants</label>
                                    <input type="number" x-model="max_participants" min="1" placeholder="No limit"
                                        class="w-full px-4 py-4 bg-dark-card border border-gray-600 rounded-2xl focus:ring-2 focus:ring-accent">
                                </div>

                                <div>
                                    <label class="block text-sm font-bold mb-3 text-accent">Result Delivery</label>
                                    <select x-model="result_delivery"
                                        class="w-full px-4 py-4 bg-dark-card border border-gray-600 rounded-2xl focus:ring-2 focus:ring-accent">
                                        <option value="instant">Instant</option>
                                        <option value="scheduled">Scheduled Release</option>
                                        <option value="manual">Manual Review</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Advanced Settings -->
                        <div class="bg-gray-800/30 rounded-2xl p-6 mb-8">
                            <h3 class="text-xl font-bold mb-6 flex items-center">
                                <i class="fas fa-shield-alt mr-3 text-red-400"></i>Advanced Settings & Security
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div>
                                    <h4 class="font-semibold mb-4 text-blue-400">Question Settings</h4>
                                    <div class="space-y-4">
                                        <label class="flex items-center cursor-pointer">
                                            <input type="checkbox" x-model="randomize_questions"
                                                class="rounded border-gray-600 bg-gray-700 text-accent focus:ring-accent mr-3">
                                            <span>Randomize Question Order</span>
                                        </label>

                                        <label class="flex items-center cursor-pointer">
                                            <input type="checkbox" x-model="randomize_options"
                                                class="rounded border-gray-600 bg-gray-700 text-accent focus:ring-accent mr-3">
                                            <span>Randomize Answer Options</span>
                                        </label>

                                        <label class="flex items-center cursor-pointer">
                                            <input type="checkbox" x-model="allow_navigation"
                                                class="rounded border-gray-600 bg-gray-700 text-accent focus:ring-accent mr-3">
                                            <span>Allow Question Navigation</span>
                                        </label>

                                        <label class="flex items-center cursor-pointer">
                                            <input type="checkbox" x-model="allow_review"
                                                class="rounded border-gray-600 bg-gray-700 text-accent focus:ring-accent mr-3">
                                            <span>Allow Answer Review</span>
                                        </label>
                                    </div>
                                </div>

                                <div>
                                    <h4 class="font-semibold mb-4 text-green-400">Result Settings</h4>
                                    <div class="space-y-4">
                                        <label class="flex items-center cursor-pointer">
                                            <input type="checkbox" x-model="show_results_immediately"
                                                class="rounded border-gray-600 bg-gray-700 text-accent focus:ring-accent mr-3">
                                            <span>Show Results Immediately</span>
                                        </label>

                                        <label class="flex items-center cursor-pointer">
                                            <input type="checkbox" x-model="email_results"
                                                class="rounded border-gray-600 bg-gray-700 text-accent focus:ring-accent mr-3">
                                            <span>Email Results to Students</span>
                                        </label>

                                        <label class="flex items-center cursor-pointer">
                                            <input type="checkbox" x-model="show_correct_answers"
                                                class="rounded border-gray-600 bg-gray-700 text-accent focus:ring-accent mr-3">
                                            <span>Show Correct Answers</span>
                                        </label>

                                        <label class="flex items-center cursor-pointer">
                                            <input type="checkbox" x-model="show_explanations"
                                                class="rounded border-gray-600 bg-gray-700 text-accent focus:ring-accent mr-3">
                                            <span>Show Answer Explanations</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Security Features -->
                            <div class="mt-8 p-6 bg-red-900/10 border border-red-500/20 rounded-2xl">
                                <h4 class="font-semibold mb-4 text-red-400 flex items-center">
                                    <i class="fas fa-lock mr-2"></i>Security & Proctoring
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <label class="flex items-center cursor-pointer">
                                        <input type="checkbox" x-model="auto_submit"
                                            class="rounded border-gray-600 bg-gray-700 text-accent focus:ring-accent mr-3">
                                        <span>Auto-submit when time expires</span>
                                    </label>

                                    <label class="flex items-center cursor-pointer">
                                        <input type="checkbox" x-model="prevent_tab_switching"
                                            class="rounded border-gray-600 bg-gray-700 text-accent focus:ring-accent mr-3">
                                        <span>Detect tab switching</span>
                                    </label>

                                    <label class="flex items-center cursor-pointer">
                                        <input type="checkbox" x-model="webcam_monitoring"
                                            class="rounded border-gray-600 bg-gray-700 text-accent focus:ring-accent mr-3">
                                        <span>Webcam monitoring</span>
                                    </label>

                                    <label class="flex items-center cursor-pointer">
                                        <input type="checkbox" x-model="restrict_copy_paste"
                                            class="rounded border-gray-600 bg-gray-700 text-accent focus:ring-accent mr-3">
                                        <span>Disable copy/paste</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Scheduling Section -->
                        <div class="bg-gray-800/30 rounded-2xl p-6 mb-8">
                            <h3 class="text-xl font-bold mb-6 flex items-center">
                                <i class="fas fa-calendar-alt mr-3 text-yellow-400"></i>Scheduling & Availability
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-bold mb-3 text-accent">Start Date & Time</label>
                                    <input type="datetime-local" x-model="start_date"
                                        class="w-full px-4 py-4 bg-dark-card border border-gray-600 rounded-2xl focus:ring-2 focus:ring-accent">
                                </div>

                                <div>
                                    <label class="block text-sm font-bold mb-3 text-accent">End Date & Time</label>
                                    <input type="datetime-local" x-model="end_date"
                                        class="w-full px-4 py-4 bg-dark-card border border-gray-600 rounded-2xl focus:ring-2 focus:ring-accent">
                                </div>

                                <div>
                                    <label class="block text-sm font-bold mb-3 text-accent">Daily Start Time</label>
                                    <input type="time" x-model="start_time"
                                        class="w-full px-4 py-4 bg-dark-card border border-gray-600 rounded-2xl focus:ring-2 focus:ring-accent">
                                </div>

                                <div>
                                    <label class="block text-sm font-bold mb-3 text-accent">Daily End Time</label>
                                    <input type="time" x-model="end_time"
                                        class="w-full px-4 py-4 bg-dark-card border border-gray-600 rounded-2xl focus:ring-2 focus:ring-accent">
                                </div>
                            </div>

                            <!-- Available Days -->
                            <div class="mt-6">
                                <label class="block text-sm font-bold mb-3 text-accent">Available Days</label>
                                <div class="flex gap-2">
                                    <template x-for="(day, index) in ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']"
                                        :key="index">
                                        <label class="flex-1 cursor-pointer">
                                            <input type="checkbox" :value="index + 1" x-model="available_days"
                                                class="hidden">
                                            <div :class="available_days.includes(index + 1) ? 'bg-accent text-dark-bg' : 'bg-gray-700 text-gray-300'"
                                                class="text-center py-3 rounded-xl font-semibold transition-all transform hover:scale-105"
                                                x-text="day"></div>
                                        </label>
                                    </template>
                                </div>
                            </div>

                            <!-- Result Release Date (conditional) -->
                            <div x-show="result_delivery === 'scheduled'" class="mt-6">
                                <label class="block text-sm font-bold mb-3 text-accent">Result Release Date</label>
                                <input type="datetime-local" x-model="result_release_date"
                                    class="w-full px-4 py-4 bg-dark-card border border-gray-600 rounded-2xl focus:ring-2 focus:ring-accent">
                            </div>
                        </div>

                        <!-- Question Selection -->
                        <div class="bg-gray-800/30 rounded-2xl p-6 mb-8">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-xl font-bold flex items-center">
                                    <i class="fas fa-question-circle mr-3 text-green-400"></i>Question Bank Selection
                                </h3>
                                <button type="button" @click="showQuestionModal = true"
                                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-xl text-sm font-semibold transition-all">
                                    <i class="fas fa-plus mr-2"></i>Manage Questions
                                </button>
                            </div>

                            <div class="bg-dark-card rounded-2xl p-4">
                                <div class="flex items-center justify-between mb-4">
                                    <span class="font-semibold">Selected Questions</span>
                                    <span class="bg-accent text-dark-bg px-3 py-1 rounded-full text-sm font-bold"
                                        x-text="selectedQuestions.length"></span>
                                </div>

                                <div x-show="selectedQuestions.length === 0" class="text-center py-8 text-gray-400">
                                    <i class="fas fa-question text-3xl mb-3"></i>
                                    <p>No questions selected yet</p>
                                    <p class="text-sm">Click "Manage Questions" to add questions to this exam</p>
                                </div>

                                <div x-show="selectedQuestions.length > 0" class="text-sm text-gray-400">
                                    <i class="fas fa-check-circle text-green-400 mr-2"></i>
                                    <span x-text="selectedQuestions.length + ' question(s) ready for exam'"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Instructions -->
                        <div class="bg-gray-800/30 rounded-2xl p-6 mb-8">
                            <h3 class="text-xl font-bold mb-6 flex items-center">
                                <i class="fas fa-clipboard-list mr-3 text-orange-400"></i>Exam Instructions
                            </h3>

                            <textarea x-model="instructions" rows="6"
                                placeholder="Enter detailed instructions for students taking this exam..."
                                class="w-full px-4 py-4 bg-dark-card border border-gray-600 rounded-2xl focus:ring-2 focus:ring-accent focus:border-transparent resize-none"></textarea>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex justify-end space-x-4 pt-6 border-t border-gray-700">
                            <button type="button" @click="closeExamModal()"
                                class="bg-gray-600 hover:bg-gray-700 text-white px-8 py-4 rounded-2xl font-semibold transition-all transform hover:scale-105">
                                Cancel
                            </button>
                            <button type="submit"
                                class="bg-gradient-to-r from-accent to-green-600 hover:from-green-400 hover:to-green-500 text-dark-bg px-8 py-4 rounded-2xl font-bold transition-all transform hover:scale-105 shadow-lg shadow-accent/25">
                                <i class="fas fa-save mr-2"></i>
                                <span x-text="editingExam ? 'Update Exam' : 'Create Exam'"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Question Selection Modal -->
    <div x-show="showQuestionModal" x-transition:enter="animate__animated animate__fadeIn"
        x-transition:leave="animate__animated animate__fadeOut"
        class="fixed inset-0 bg-black/80 backdrop-blur-sm z-60 flex items-center justify-center p-4">

        <div
            class="bg-dark-surface rounded-3xl w-full max-w-5xl max-h-[90vh] overflow-hidden flex flex-col border border-gray-700">
            <!-- Question Modal Header -->
            <div class="bg-dark-card p-6 border-b border-gray-700">
                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-bold">Select Questions for Exam</h2>
                    <button @click="closeQuestionModal()" class="text-gray-400 hover:text-white text-xl">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            <div class="flex-1 overflow-auto p-6">
                <!-- Question Filters -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <input type="text" x-model="searchQuestions" placeholder="Search questions..."
                        class="px-4 py-3 bg-dark-card border border-gray-600 rounded-xl focus:ring-2 focus:ring-accent">

                    <select x-model="questionFilter" class="px-4 py-3 bg-dark-card border border-gray-600 rounded-xl">
                        <option value="all">All Types</option>
                        <option value="multiple_choice">Multiple Choice</option>
                        <option value="true_false">True/False</option>
                        <option value="essay">Essay</option>
                    </select>

                    <select x-model="questionDifficulty"
                        class="px-4 py-3 bg-dark-card border border-gray-600 rounded-xl">
                        <option value="all">All Difficulties</option>
                        <option value="easy">Easy</option>
                        <option value="medium">Medium</option>
                        <option value="hard">Hard</option>
                    </select>

                    <select x-model="questionCourse" class="px-4 py-3 bg-dark-card border border-gray-600 rounded-xl">
                        <option value="all">All Courses</option>
                        <template x-for="course in courses" :key="course.id">
                            <option :value="course.id" x-text="course.title"></option>
                        </template>
                    </select>
                </div>

                <!-- Question Selection Actions -->
                <div class="flex justify-between items-center mb-6">
                    <div class="flex gap-3">
                        <button @click="selectAllQuestions()"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-sm transition-all">
                            Select All
                        </button>
                        <button @click="clearQuestionSelection()"
                            class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-xl text-sm transition-all">
                            Clear Selection
                        </button>
                        <button @click="randomSelectQuestions(10)"
                            class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-xl text-sm transition-all">
                            Random 10
                        </button>
                    </div>
                    <div class="bg-accent text-dark-bg px-4 py-2 rounded-xl font-bold">
                        <span x-text="selectedQuestions.length"></span> Selected
                    </div>
                </div>

                <!-- Questions List -->
                <div class="space-y-4 max-h-96 overflow-y-auto custom-scrollbar">
                    <template x-for="question in availableQuestions" :key="question.id">
                        <div class="bg-dark-card rounded-2xl p-4 border border-gray-600">
                            <div class="flex items-start">
                                <input type="checkbox" :value="question.id" x-model="selectedQuestions"
                                    class="mt-1 mr-4 rounded border-gray-600 bg-gray-700 text-accent focus:ring-accent">

                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-2">
                                        <span :class="getQuestionTypeColor(question.question_type)"
                                            class="px-2 py-1 rounded-full text-xs font-bold"
                                            x-text="question.question_type.replace('_', ' ').toUpperCase()"></span>
                                        <span :class="getDifficultyColor(question.difficulty_level || 'medium')"
                                            class="px-2 py-1 rounded-full text-xs font-bold"
                                            x-text="(question.difficulty_level || 'medium').toUpperCase()"></span>
                                        <span class="text-xs text-gray-500"
                                            x-text="(question.points || 1) + ' pts'"></span>
                                    </div>

                                    <p class="text-white font-medium mb-2"
                                        x-text="question.question_text.substring(0, 120) + (question.question_text.length > 120 ? '...' : '')">
                                    </p>

                                    <div class="text-sm text-gray-400">
                                        <span x-text="question.assessment?.course?.title || 'General'"></span>
                                        <span class="mx-2">•</span>
                                        <span x-text="formatDate(question.created_at)"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Question Modal Actions -->
                <div class="flex justify-end space-x-4 pt-6 border-t border-gray-700 mt-6">
                    <button @click="closeQuestionModal()"
                        class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-xl transition-all">
                        Cancel
                    </button>
                    <button @click="closeQuestionModal()"
                        class="bg-accent hover:bg-accent-dark text-dark-bg px-6 py-3 rounded-xl font-semibold transition-all">
                        <i class="fas fa-check mr-2"></i>Confirm Selection
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Result Detail Modal -->
    <div x-show="showResultModal" x-transition:enter="animate__animated animate__fadeIn"
        x-transition:leave="animate__animated animate__fadeOut"
        class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-center justify-center p-4">

        <div
            class="bg-dark-surface rounded-3xl w-full max-w-4xl max-h-[90vh] overflow-hidden flex flex-col border border-gray-700">
            <!-- Result Modal Header -->
            <div class="bg-dark-card p-6 border-b border-gray-700">
                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-bold">Exam Result Details</h2>
                    <button @click="closeResultModal()" class="text-gray-400 hover:text-white text-xl">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            <div class="flex-1 overflow-auto p-6" x-show="selectedResult">
                <!-- Result Summary -->
                <div class="bg-gray-800/30 rounded-2xl p-6 mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="text-center">
                            <div class="text-3xl font-bold"
                                :class="selectedResult?.passed ? 'text-green-400' : 'text-red-400'"
                                x-text="selectedResult?.percentage_score?.toFixed(1) + '%'"></div>
                            <div class="text-sm text-gray-400">Final Score</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-blue-400" x-text="selectedResult?.grade"></div>
                            <div class="text-sm text-gray-400">Grade</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold text-purple-400"
                                x-text="selectedResult ? formatDuration(selectedResult.time_spent_seconds) : ''"></div>
                            <div class="text-sm text-gray-400">Time Spent</div>
                        </div>
                    </div>
                </div>

                <!-- Question-by-Question Analysis -->
                <div class="bg-gray-800/30 rounded-2xl p-6">
                    <h3 class="text-lg font-bold mb-4">Question Analysis</h3>
                    <div class="space-y-3 max-h-64 overflow-y-auto custom-scrollbar">
                        <template x-for="(answer, index) in selectedResult?.answers" :key="answer.id">
                            <div class="bg-dark-card rounded-xl p-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div :class="answer.is_correct ? 'bg-green-500' : 'bg-red-500'"
                                            class="w-8 h-8 rounded-full flex items-center justify-center mr-3">
                                            <i :class="answer.is_correct ? 'fas fa-check' : 'fas fa-times'"
                                                class="text-white text-sm"></i>
                                        </div>
                                        <span class="font-semibold" x-text="'Question ' + (index + 1)"></span>
                                    </div>
                                    <div class="text-sm">
                                        <span :class="answer.is_correct ? 'text-green-400' : 'text-red-400'"
                                            x-text="answer.points_awarded + ' pts'"></span>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        'dark-bg': '#0f0f23',
                        'dark-surface': '#1a1a2e',
                        'dark-card': '#16213e',
                        'accent': '#4ade80',
                        'accent-dark': '#16a34a',
                        'neon-blue': '#00d4ff',
                        'neon-purple': '#b466f2'
                    }
                }
            }
        }
    </script>
@endpush

@push('styles')
    <style>
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .glassmorphism {
            backdrop-filter: blur(16px);
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .card-hover {
            transition: all 0.3s ease;
        }

        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }

        .animate-fade-in {
            animation: fadeIn 0.3s ease-in-out;
        }

        .pulse-ring {
            animation: pulse-ring 2s infinite;
        }

        .floating {
            animation: floating 3s ease-in-out infinite;
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

        @keyframes pulse-ring {
            0% {
                transform: scale(0.8);
                opacity: 1;
            }

            50% {
                transform: scale(1.2);
                opacity: 0.3;
            }

            100% {
                transform: scale(0.8);
                opacity: 1;
            }
        }

        @keyframes floating {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #1f2937;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #4ade80;
            border-radius: 3px;
        }
    </style>
@endpush