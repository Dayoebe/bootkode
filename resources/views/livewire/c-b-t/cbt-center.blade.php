<div class="bg-dark-bg text-white min-h-screen" x-data="cbtCenter()">
    <div class="container mx-auto px-4 py-8">
        <!-- Header Section -->
        <div class="gradient-bg rounded-3xl p-8 mb-8 relative overflow-hidden">
            <div class="absolute inset-0 opacity-10">
                <div class="floating absolute top-10 left-10 text-6xl"><i class="fas fa-laptop-code"></i></div>
                <div class="floating absolute top-20 right-20 text-4xl" style="animation-delay: 1s;"><i class="fas fa-brain"></i></div>
                <div class="floating absolute bottom-10 right-10 text-5xl" style="animation-delay: 2s;"><i class="fas fa-graduation-cap"></i></div>
            </div>
            
            <div class="relative z-10 flex items-center justify-between">
                <div>
                    <h1 class="text-5xl font-bold mb-4 animate__animated animate__fadeInDown">
                        <i class="fas fa-laptop-code mr-4"></i>CBT Center
                    </h1>
                    <p class="text-xl opacity-90 animate__animated animate__fadeInUp animate__delay-1s">
                        Experience next-generation computer-based testing with advanced analytics and instant results
                    </p>
                    
                    <!-- Quick Stats -->
                    <div class="flex gap-6 mt-6 animate__animated animate__fadeInUp animate__delay-2s" x-show="activeTab === 'dashboard'">
                        <div class="glassmorphism rounded-xl px-4 py-2">
                            <div class="text-2xl font-bold" x-text="dashboardStats.total_exams_taken || 0"></div>
                            <div class="text-sm opacity-75">Exams Taken</div>
                        </div>
                        <div class="glassmorphism rounded-xl px-4 py-2">
                            <div class="text-2xl font-bold text-accent" x-text="dashboardStats.exams_passed || 0"></div>
                            <div class="text-sm opacity-75">Passed</div>
                        </div>
                        <div class="glassmorphism rounded-xl px-4 py-2">
                            <div class="text-2xl font-bold text-yellow-400" x-text="(dashboardStats.average_score || 0) + '%'"></div>
                            <div class="text-sm opacity-75">Avg Score</div>
                        </div>
                    </div>
                </div>
                
                <div class="relative">
                    <div class="pulse-ring absolute inset-0 rounded-full bg-white opacity-25"></div>
                    <div class="w-24 h-24 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
                        <i class="fas fa-trophy text-3xl text-yellow-300"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="mb-8">
            <div class="bg-dark-surface rounded-2xl p-2 flex space-x-2 backdrop-blur-xl border border-gray-700/50">
                <button @click="setActiveTab('dashboard')" 
                        :class="activeTab === 'dashboard' ? 'bg-accent text-dark-bg shadow-lg' : 'text-gray-300 hover:text-white hover:bg-gray-700'"
                        class="flex-1 py-4 px-6 rounded-xl font-semibold transition-all duration-300">
                    <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
                </button>
                
                <button @click="setActiveTab('exams')" 
                        :class="activeTab === 'exams' ? 'bg-accent text-dark-bg shadow-lg' : 'text-gray-300 hover:text-white hover:bg-gray-700'"
                        class="flex-1 py-4 px-6 rounded-xl font-semibold transition-all duration-300">
                    <i class="fas fa-clipboard-check mr-2"></i> Available Exams
                </button>
                
                <button @click="setActiveTab('results')" 
                        :class="activeTab === 'results' ? 'bg-accent text-dark-bg shadow-lg' : 'text-gray-300 hover:text-white hover:bg-gray-700'"
                        class="flex-1 py-4 px-6 rounded-xl font-semibold transition-all duration-300">
                    <i class="fas fa-chart-line mr-2"></i> My Results
                </button>
                
                <button x-show="isAdmin" @click="setActiveTab('management')" 
                        :class="activeTab === 'management' ? 'bg-accent text-dark-bg shadow-lg' : 'text-gray-300 hover:text-white hover:bg-gray-700'"
                        class="flex-1 py-4 px-6 rounded-xl font-semibold transition-all duration-300">
                    <i class="fas fa-cogs mr-2"></i> Management
                </button>
            </div>
        </div>

        <!-- Dashboard Tab -->
        <div x-show="activeTab === 'dashboard'" x-transition:enter="animate__animated animate__fadeIn">
            <!-- Welcome Section -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
                <!-- Quick Actions -->
                <div class="lg:col-span-2">
                    <div class="bg-dark-surface rounded-2xl p-6 border border-gray-700/50">
                        <h3 class="text-2xl font-bold mb-6 flex items-center">
                            <i class="fas fa-rocket mr-3 text-accent"></i>Quick Actions
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <button @click="setActiveTab('exams')" 
                                    class="exam-card bg-gradient-to-r from-blue-600 to-blue-800 p-6 rounded-xl hover:from-blue-700 hover:to-blue-900 transition-all">
                                <i class="fas fa-play-circle text-3xl mb-3"></i>
                                <h4 class="font-semibold mb-2">Start Practice Exam</h4>
                                <p class="text-sm opacity-75">Test your knowledge with practice questions</p>
                            </button>
                            
                            <button @click="setActiveTab('exams')" 
                                    class="exam-card bg-gradient-to-r from-purple-600 to-purple-800 p-6 rounded-xl hover:from-purple-700 hover:to-purple-900 transition-all">
                                <i class="fas fa-certificate text-3xl mb-3"></i>
                                <h4 class="font-semibold mb-2">Certification Exam</h4>
                                <p class="text-sm opacity-75">Get certified with official exams</p>
                            </button>
                            
                            <button @click="setActiveTab('results')" 
                                    class="exam-card bg-gradient-to-r from-green-600 to-green-800 p-6 rounded-xl hover:from-green-700 hover:to-green-900 transition-all">
                                <i class="fas fa-chart-bar text-3xl mb-3"></i>
                                <h4 class="font-semibold mb-2">View Progress</h4>
                                <p class="text-sm opacity-75">Track your performance and growth</p>
                            </button>
                            
                            <button @click="setActiveTab('exams')" 
                                    class="exam-card bg-gradient-to-r from-orange-600 to-orange-800 p-6 rounded-xl hover:from-orange-700 hover:to-orange-900 transition-all">
                                <i class="fas fa-bullseye text-3xl mb-3"></i>
                                <h4 class="font-semibold mb-2">Mock Exams</h4>
                                <p class="text-sm opacity-75">Simulate real exam conditions</p>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Performance Overview -->
                <div class="bg-dark-surface rounded-2xl p-6 border border-gray-700/50">
                    <h3 class="text-xl font-bold mb-6 flex items-center">
                        <i class="fas fa-trophy mr-3 text-yellow-400"></i>Performance
                    </h3>
                    
                    <!-- Circular Progress -->
                    <div class="relative w-32 h-32 mx-auto mb-6">
                        <svg class="transform -rotate-90 w-32 h-32">
                            <circle cx="64" cy="64" r="56" stroke="currentColor" stroke-width="8" fill="transparent" class="text-gray-700"/>
                            <circle cx="64" cy="64" r="56" stroke="currentColor" stroke-width="8" fill="transparent" 
                                    :stroke-dasharray="351.86" 
                                    :stroke-dashoffset="351.86 - (351.86 * (dashboardStats.average_score || 0) / 100)"
                                    class="text-accent transition-all duration-1000"/>
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center flex-col">
                            <span class="text-2xl font-bold" x-text="(dashboardStats.average_score || 0) + '%'"></span>
                            <span class="text-xs text-gray-400">Avg Score</span>
                        </div>
                    </div>
                    
                    <!-- Stats List -->
                    <div class="space-y-3">
                        <div class="flex justify-between items-center p-3 bg-gray-800/50 rounded-lg">
                            <span class="text-sm">Exams Taken</span>
                            <span class="font-semibold" x-text="dashboardStats.total_exams_taken || 0"></span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-gray-800/50 rounded-lg">
                            <span class="text-sm">Pass Rate</span>
                            <span class="font-semibold text-accent" 
                                  x-text="dashboardStats.total_exams_taken ? Math.round((dashboardStats.exams_passed / dashboardStats.total_exams_taken) * 100) + '%' : '0%'"></span>
                        </div>
                        <div class="flex justify-between items-center p-3 bg-gray-800/50 rounded-lg">
                            <span class="text-sm">Study Time</span>
                            <span class="font-semibold" x-text="formatStudyTime(dashboardStats.total_study_time || 0)"></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Achievements -->
            <div class="bg-dark-surface rounded-2xl p-6 border border-gray-700/50 mb-8" x-show="dashboardStats.recent_achievements && dashboardStats.recent_achievements.length > 0">
                <h3 class="text-xl font-bold mb-6 flex items-center">
                    <i class="fas fa-medal mr-3 text-yellow-400"></i>Recent Achievements
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <template x-for="achievement in dashboardStats.recent_achievements" :key="achievement.id">
                        <div class="bg-gradient-to-r from-yellow-600/20 to-orange-600/20 p-4 rounded-xl border border-yellow-500/30">
                            <div class="text-2xl mb-2" x-text="achievement.icon"></div>
                            <h4 class="font-semibold text-yellow-400 mb-1" x-text="achievement.title"></h4>
                            <p class="text-sm text-gray-300" x-text="achievement.description"></p>
                            <div class="text-xs text-gray-400 mt-2" x-text="formatDate(achievement.earned_at)"></div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Available Exams Tab -->
        <div x-show="activeTab === 'exams'" x-transition:enter="animate__animated animate__fadeIn">
            <!-- Filters and Search -->
            <div class="bg-dark-surface rounded-2xl p-6 mb-6 border border-gray-700/50">
                <div class="flex flex-wrap gap-4 items-center">
                    <!-- Search -->
                    <div class="flex-1 min-w-64">
                        <div class="relative">
                            <i class="fas fa-search absolute left-3 top-3.5 text-gray-400"></i>
                            <input type="text" x-model="searchTerm" 
                                   placeholder="Search exams..." 
                                   class="w-full pl-10 pr-4 py-3 bg-gray-800 border border-gray-600 rounded-xl focus:ring-2 focus:ring-accent focus:border-transparent">
                        </div>
                    </div>
                    
                    <!-- Type Filter -->
                    <select x-model="filterType" class="px-4 py-3 bg-gray-800 border border-gray-600 rounded-xl focus:ring-2 focus:ring-accent">
                        <option value="all">All Types</option>
                        <option value="practice">Practice</option>
                        <option value="mock">Mock</option>
                        <option value="final">Final</option>
                        <option value="certification">Certification</option>
                    </select>
                    
                    <!-- Difficulty Filter -->
                    <select x-model="filterDifficulty" class="px-4 py-3 bg-gray-800 border border-gray-600 rounded-xl focus:ring-2 focus:ring-accent">
                        <option value="all">All Levels</option>
                        <option value="beginner">Beginner</option>
                        <option value="intermediate">Intermediate</option>
                        <option value="advanced">Advanced</option>
                        <option value="expert">Expert</option>
                    </select>
                    
                    <!-- Sort -->
                    <select x-model="sortBy" class="px-4 py-3 bg-gray-800 border border-gray-600 rounded-xl focus:ring-2 focus:ring-accent">
                        <option value="created_at">Newest</option>
                        <option value="title">Title</option>
                        <option value="difficulty_level">Difficulty</option>
                        <option value="duration_minutes">Duration</option>
                    </select>
                </div>
            </div>

            <!-- Exam Cards Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <template x-for="exam in availableExams" :key="exam.id">
                    <div class="exam-card bg-dark-surface rounded-2xl p-6 border border-gray-700/50 hover:border-accent/50 cursor-pointer">
                        <!-- Exam Header -->
                        <div class="flex justify-between items-start mb-4">
                            <div class="flex-1">
                                <h3 class="text-xl font-bold mb-2 line-clamp-2" x-text="exam.title"></h3>
                                <p class="text-sm text-gray-400 mb-3" x-text="exam.course?.title || 'General'"></p>
                            </div>
                            <div class="ml-4">
                                <span :class="getDifficultyColor(exam.difficulty_level)" 
                                      class="px-2 py-1 text-xs rounded-full font-medium" 
                                      x-text="exam.difficulty_level"></span>
                            </div>
                        </div>

                        <!-- Exam Stats -->
                        <div class="grid grid-cols-3 gap-2 mb-4">
                            <div class="bg-gray-800/50 p-3 rounded-lg text-center">
                                <i class="fas fa-clock text-blue-400 mb-1"></i>
                                <div class="text-xs text-gray-400">Duration</div>
                                <div class="font-semibold text-sm" x-text="exam.formatted_duration"></div>
                            </div>
                            <div class="bg-gray-800/50 p-3 rounded-lg text-center">
                                <i class="fas fa-list-ol text-green-400 mb-1"></i>
                                <div class="text-xs text-gray-400">Questions</div>
                                <div class="font-semibold text-sm" x-text="exam.total_questions"></div>
                            </div>
                            <div class="bg-gray-800/50 p-3 rounded-lg text-center">
                                <i class="fas fa-percentage text-yellow-400 mb-1"></i>
                                <div class="text-xs text-gray-400">Pass %</div>
                                <div class="font-semibold text-sm" x-text="exam.pass_percentage + '%'"></div>
                            </div>
                        </div>

                        <!-- Exam Description -->
                        <p class="text-gray-300 text-sm mb-4 line-clamp-3" x-text="exam.description"></p>

                        <!-- Exam Type Badge -->
                        <div class="flex items-center justify-between mb-4">
                            <span :class="getTypeColor(exam.exam_type)" 
                                  class="px-3 py-1 rounded-full text-xs font-medium flex items-center">
                                <i :class="getTypeIcon(exam.exam_type)" class="mr-2"></i>
                                <span x-text="exam.exam_type.replace('_', ' ')"></span>
                            </span>
                            <div class="text-xs text-gray-400">
                                <i class="fas fa-users mr-1"></i>
                                <span x-text="exam.results_count"></span> attempts
                            </div>
                        </div>

                        <!-- Action Button -->
                        <button @click="startExam(exam.id)" 
                                class="w-full bg-gradient-to-r from-accent to-accent-dark hover:from-accent-dark hover:to-accent text-dark-bg font-semibold py-3 rounded-xl transition-all duration-300 transform hover:scale-105">
                            <i class="fas fa-play mr-2"></i>Start Exam
                        </button>
                    </div>
                </template>
            </div>

            <!-- Empty State -->
            <div x-show="!availableExams.length" class="text-center py-16">
                <div class="w-24 h-24 bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-search text-3xl text-gray-400"></i>
                </div>
                <h3 class="text-xl font-semibold mb-2">No Exams Found</h3>
                <p class="text-gray-400">Try adjusting your search criteria or filters</p>
            </div>
        </div>

        <!-- Results Tab -->
        <div x-show="activeTab === 'results'" x-transition:enter="animate__animated animate__fadeIn">
            <div class="bg-dark-surface rounded-2xl border border-gray-700/50 overflow-hidden">
                <!-- Results Header -->
                <div class="bg-gradient-to-r from-gray-800 to-gray-900 p-6 border-b border-gray-700">
                    <h2 class="text-2xl font-bold flex items-center">
                        <i class="fas fa-chart-line mr-3 text-accent"></i>My Exam Results
                    </h2>
                    <p class="text-gray-400 mt-1">Track your performance and progress over time</p>
                </div>

                <!-- Results List -->
                <div class="divide-y divide-gray-700">
                    <template x-for="result in userResults" :key="result.id">
                        <div class="p-6 hover:bg-gray-800/30 transition-all cursor-pointer" @click="viewResult(result.id)">
                            <div class="flex items-center justify-between">
                                <!-- Result Info -->
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold mb-1" x-text="result.exam.title"></h3>
                                    <p class="text-gray-400 text-sm mb-2" x-text="result.exam.course?.title || 'General'"></p>
                                    
                                    <!-- Score Display -->
                                    <div class="flex items-center gap-4">
                                        <div class="flex items-center">
                                            <div class="w-16 h-2 bg-gray-700 rounded-full overflow-hidden">
                                                <div :style="'width: ' + result.percentage_score + '%'" 
                                                     :class="result.passed ? 'bg-green-500' : 'bg-red-500'" 
                                                     class="h-full transition-all duration-500"></div>
                                            </div>
                                            <span :class="result.passed ? 'text-green-400' : 'text-red-400'" 
                                                  class="ml-3 font-bold" x-text="result.percentage_score.toFixed(1) + '%'"></span>
                                        </div>
                                        
                                        <span :class="result.passed ? 'bg-green-900/30 text-green-400' : 'bg-red-900/30 text-red-400'" 
                                              class="px-2 py-1 rounded-full text-xs font-medium" 
                                              x-text="result.passed ? 'PASSED' : 'FAILED'"></span>
                                    </div>
                                </div>

                                <!-- Result Stats -->
                                <div class="text-right">
                                    <div class="text-lg font-bold" x-text="result.correct_answers + '/' + result.total_questions"></div>
                                    <div class="text-sm text-gray-400">Correct</div>
                                    <div class="text-xs text-gray-500 mt-1" x-text="formatDate(result.completed_at)"></div>
                                </div>
                            </div>

                            <!-- Additional Stats Row -->
                            <div class="flex gap-6 mt-4 text-sm">
                                <div class="flex items-center text-gray-400">
                                    <i class="fas fa-clock mr-2"></i>
                                    <span x-text="formatDuration(result.time_spent_seconds)"></span>
                                </div>
                                <div class="flex items-center text-gray-400">
                                    <i class="fas fa-redo mr-2"></i>
                                    <span>Attempt #</span><span x-text="result.attempt_number"></span>
                                </div>
                                <div class="flex items-center text-gray-400">
                                    <i class="fas fa-medal mr-2"></i>
                                    <span x-text="result.grade"></span>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Empty Results State -->
                <div x-show="!userResults.length" class="p-16 text-center">
                    <div class="w-20 h-20 bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-chart-bar text-2xl text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-semibold mb-2">No Results Yet</h3>
                    <p class="text-gray-400 mb-6">Take your first exam to see results here</p>
                    <button @click="setActiveTab('exams')" 
                            class="bg-accent hover:bg-accent-dark text-dark-bg px-6 py-3 rounded-xl font-semibold transition-all">
                        Browse Exams
                    </button>
                </div>
            </div>
        </div>

        <!-- Management Tab (Admin Only) -->
        <div x-show="activeTab === 'management' && isAdmin" x-transition:enter="animate__animated animate__fadeIn">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Admin Stats -->
                <div class="bg-dark-surface rounded-2xl p-6 border border-gray-700/50">
                    <h3 class="text-lg font-bold mb-4 flex items-center">
                        <i class="fas fa-chart-pie mr-3 text-blue-400"></i>Overview
                    </h3>
                    
                    <div class="space-y-4">
                        <div class="bg-gray-800/50 p-4 rounded-lg">
                            <div class="text-2xl font-bold text-blue-400">24</div>
                            <div class="text-sm text-gray-400">Active Exams</div>
                        </div>
                        <div class="bg-gray-800/50 p-4 rounded-lg">
                            <div class="text-2xl font-bold text-green-400">1,543</div>
                            <div class="text-sm text-gray-400">Total Attempts</div>
                        </div>
                        <div class="bg-gray-800/50 p-4 rounded-lg">
                            <div class="text-2xl font-bold text-yellow-400">76.8%</div>
                            <div class="text-sm text-gray-400">Avg Pass Rate</div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-dark-surface rounded-2xl p-6 border border-gray-700/50">
                    <h3 class="text-lg font-bold mb-4 flex items-center">
                        <i class="fas fa-tools mr-3 text-orange-400"></i>Quick Actions
                    </h3>
                    
                    <div class="space-y-3">
                        <button class="w-full bg-blue-600 hover:bg-blue-700 text-white p-3 rounded-lg text-sm font-medium transition-all">
                            <i class="fas fa-plus mr-2"></i>Create New Exam
                        </button>
                        <button class="w-full bg-green-600 hover:bg-green-700 text-white p-3 rounded-lg text-sm font-medium transition-all">
                            <i class="fas fa-download mr-2"></i>Export Results
                        </button>
                        <button class="w-full bg-purple-600 hover:bg-purple-700 text-white p-3 rounded-lg text-sm font-medium transition-all">
                            <i class="fas fa-cog mr-2"></i>Settings
                        </button>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="bg-dark-surface rounded-2xl p-6 border border-gray-700/50">
                    <h3 class="text-lg font-bold mb-4 flex items-center">
                        <i class="fas fa-clock mr-3 text-green-400"></i>Recent Activity
                    </h3>
                    
                    <div class="space-y-3 text-sm">
                        <div class="flex items-center p-2 bg-gray-800/30 rounded">
                            <div class="w-2 h-2 bg-green-400 rounded-full mr-3"></div>
                            <div class="flex-1">
                                <div class="font-medium">John completed "Java Fundamentals"</div>
                                <div class="text-gray-400 text-xs">2 minutes ago</div>
                            </div>
                        </div>
                        <div class="flex items-center p-2 bg-gray-800/30 rounded">
                            <div class="w-2 h-2 bg-blue-400 rounded-full mr-3"></div>
                            <div class="flex-1">
                                <div class="font-medium">New exam "Python Basics" created</div>
                                <div class="text-gray-400 text-xs">15 minutes ago</div>
                            </div>
                        </div>
                        <div class="flex items-center p-2 bg-gray-800/30 rounded">
                            <div class="w-2 h-2 bg-yellow-400 rounded-full mr-3"></div>
                            <div class="flex-1">
                                <div class="font-medium">Sarah started "React Advanced"</div>
                                <div class="text-gray-400 text-xs">1 hour ago</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Exam Taking Modal -->
    <div x-show="examStarted" 
         x-transition:enter="animate__animated animate__fadeIn" 
         x-transition:leave="animate__animated animate__fadeOut"
         class="fixed inset-0 bg-black/90 z-50 flex items-center justify-center p-4">
        
        <div class="bg-dark-surface rounded-2xl w-full max-w-6xl max-h-[95vh] overflow-hidden flex flex-col border border-gray-700">
            <!-- Exam Header -->
            <div class="bg-dark-card p-6 border-b border-gray-700 flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-bold" x-text="selectedExam?.title"></h2>
                    <p class="text-gray-400" x-text="selectedExam?.course?.title"></p>
                </div>
                
                <div class="flex items-center space-x-6">
                    <!-- Timer -->
                    <div class="bg-red-900/30 text-red-400 px-4 py-2 rounded-xl flex items-center font-mono text-lg">
                        <i class="fas fa-clock mr-2"></i>
                        <span x-text="formatTime(timeRemaining)"></span>
                    </div>
                    
                    <!-- Progress -->
                    <div class="bg-blue-900/30 text-blue-400 px-4 py-2 rounded-xl">
                        <span x-text="currentQuestionIndex + 1"></span> / <span x-text="examQuestions.length"></span>
                    </div>
                    
                    <!-- Menu Button -->
                    <button @click="showQuestionPalette = !showQuestionPalette" 
                            class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded-xl transition-all">
                        <i class="fas fa-th-large"></i>
                    </button>
                </div>
            </div>

            <!-- Question Content -->
            <div class="flex-1 overflow-auto p-6" x-show="examQuestions[currentQuestionIndex]">
                <template x-if="examQuestions[currentQuestionIndex]">
                    <div>
                        <!-- Question Text -->
                        <div class="mb-6">
                            <div class="flex items-start justify-between mb-4">
                                <h3 class="text-lg font-medium text-white leading-relaxed" 
                                    x-text="examQuestions[currentQuestionIndex].text"></h3>
                                
                                <button @click="toggleFlag(examQuestions[currentQuestionIndex].id)" 
                                        :class="flaggedQuestions.includes(examQuestions[currentQuestionIndex].id) ? 'text-yellow-400' : 'text-gray-400'"
                                        class="ml-4 hover:text-yellow-400 transition-colors">
                                    <i class="fas fa-flag text-xl"></i>
                                </button>
                            </div>
                            
                            <div class="text-sm text-gray-400 mb-4">
                                Points: <span x-text="examQuestions[currentQuestionIndex].points"></span>
                            </div>
                        </div>

                        <!-- Answer Options -->
                        <div class="space-y-3" x-show="examQuestions[currentQuestionIndex].type === 'multiple_choice'">
                            <template x-for="(option, index) in examQuestions[currentQuestionIndex].options" :key="index">
                                <label class="flex items-start p-4 rounded-xl border-2 cursor-pointer transition-all hover:bg-gray-800/50"
                                       :class="userAnswers[examQuestions[currentQuestionIndex].id] === index ? 'border-accent bg-accent/10' : 'border-gray-700 hover:border-gray-600'">
                                    <input type="radio" 
                                           :name="'question_' + examQuestions[currentQuestionIndex].id"
                                           :value="index"
                                           x-model="userAnswers[examQuestions[currentQuestionIndex].id]"
                                           @change="saveAnswer(examQuestions[currentQuestionIndex].id, $event.target.value)"
                                           class="mt-1 mr-4 text-accent">
                                    <div class="flex-1">
                                        <div class="flex items-center">
                                            <span class="w-6 h-6 bg-gray-700 text-white text-xs rounded-full flex items-center justify-center mr-3" 
                                                  x-text="String.fromCharCode(65 + index)"></span>
                                            <span x-text="option"></span>
                                        </div>
                                    </div>
                                </label>
                            </template>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Navigation Footer -->
            <div class="bg-dark-card p-6 border-t border-gray-700 flex justify-between items-center">
                <div class="flex items-center space-x-4">
                    <button @click="previousQuestion()" 
                            :disabled="currentQuestionIndex === 0"
                            class="bg-gray-700 hover:bg-gray-600 disabled:bg-gray-800 disabled:text-gray-600 text-white px-6 py-3 rounded-xl transition-all">
                        <i class="fas fa-arrow-left mr-2"></i>Previous
                    </button>
                </div>

                <div class="text-center">
                    <div class="text-sm text-gray-400 mb-2">Progress</div>
                    <div class="w-64 h-2 bg-gray-700 rounded-full overflow-hidden">
                        <div :style="'width: ' + getProgressPercentage() + '%'" 
                             class="h-full bg-accent transition-all duration-300"></div>
                    </div>
                </div>

                <div class="flex items-center space-x-4">
                    <button @click="nextQuestion()" 
                            :disabled="currentQuestionIndex === examQuestions.length - 1"
                            x-show="currentQuestionIndex < examQuestions.length - 1"
                            class="bg-accent hover:bg-accent-dark text-dark-bg px-6 py-3 rounded-xl font-semibold transition-all">
                        Next<i class="fas fa-arrow-right ml-2"></i>
                    </button>
                    
                    <button @click="showConfirmSubmit = true" 
                            x-show="currentQuestionIndex === examQuestions.length - 1"
                            class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-xl font-semibold transition-all">
                        <i class="fas fa-check-circle mr-2"></i>Submit Exam
                    </button>
                </div>
            </div>
        </div>

        <!-- Question Palette -->
        <div x-show="showQuestionPalette" 
             x-transition:enter="animate__animated animate__slideInRight" 
             x-transition:leave="animate__animated animate__slideOutRight"
             class="fixed right-4 top-4 bottom-4 w-80 bg-dark-surface rounded-2xl border border-gray-700 p-6 overflow-auto">
            
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-bold">Questions</h3>
                <button @click="showQuestionPalette = false" class="text-gray-400 hover:text-white">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="grid grid-cols-5 gap-2">
                <template x-for="(question, index) in examQuestions" :key="question.id">
                    <button @click="navigateToQuestion(index); showQuestionPalette = false" 
                            :class="getQuestionStatusClass(question.id, index)"
                            class="w-12 h-12 rounded-lg font-medium transition-all text-sm">
                        <span x-text="index + 1"></span>
                    </button>
                </template>
            </div>

            <div class="mt-6 space-y-2 text-sm">
                <div class="flex items-center"><div class="w-3 h-3 bg-accent rounded mr-2"></div>Answered</div>
                <div class="flex items-center"><div class="w-3 h-3 bg-yellow-500 rounded mr-2"></div>Flagged</div>
                <div class="flex items-center"><div class="w-3 h-3 bg-blue-500 rounded mr-2"></div>Current</div>
                <div class="flex items-center"><div class="w-3 h-3 bg-gray-600 rounded mr-2"></div>Not Answered</div>
            </div>
        </div>
    </div>

    <!-- Confirm Submit Modal -->
    <div x-show="showConfirmSubmit" 
         x-transition:enter="animate__animated animate__fadeIn" 
         x-transition:leave="animate__animated animate__fadeOut"
         class="fixed inset-0 bg-black/80 z-60 flex items-center justify-center p-4">
        
        <div class="bg-dark-surface rounded-2xl p-8 max-w-md w-full border border-gray-700">
            <div class="text-center">
                <i class="fas fa-exclamation-triangle text-4xl text-yellow-400 mb-4"></i>
                <h3 class="text-xl font-bold mb-4">Submit Exam?</h3>
                <p class="text-gray-400 mb-6">
                    Are you sure you want to submit your exam? This action cannot be undone.
                </p>
                
                <div class="text-sm text-gray-500 mb-6">
                    <div>Answered: <span x-text="Object.values(userAnswers).filter(a => a !== null).length"></span> / <span x-text="examQuestions.length"></span></div>
                    <div>Time remaining: <span x-text="formatTime(timeRemaining)"></span></div>
                </div>

                <div class="flex gap-4">
                    <button @click="showConfirmSubmit = false" 
                            class="flex-1 bg-gray-700 hover:bg-gray-600 text-white py-3 rounded-xl transition-all">
                        Continue Exam
                    </button>
                    <button @click="submitExam(); showConfirmSubmit = false" 
                            class="flex-1 bg-green-600 hover:bg-green-700 text-white py-3 rounded-xl font-semibold transition-all">
                        Submit Now
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Result Detail Modal -->
    <div x-show="showResultDetail" 
         x-transition:enter="animate__animated animate__fadeIn" 
         x-transition:leave="animate__animated animate__fadeOut"
         class="fixed inset-0 bg-black/80 z-50 flex items-center justify-center p-4">
        
        <div class="bg-dark-surface rounded-2xl w-full max-w-4xl max-h-[90vh] overflow-hidden flex flex-col border border-gray-700" x-show="showResultDetail">
            <!-- Header -->
            <div class="bg-dark-card p-6 border-b border-gray-700 flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-bold" x-text="showResultDetail?.exam?.title"></h2>
                    <p class="text-gray-400">Exam Results</p>
                </div>
                <button @click="closeResult()" class="text-gray-400 hover:text-white text-xl">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="flex-1 overflow-auto p-6">
                <template x-if="showResultDetail">
                    <div>
                        <!-- Score Overview -->
                        <div class="text-center mb-8">
                            <div class="relative inline-block">
                                <!-- Circular Progress -->
                                <svg class="w-32 h-32 transform -rotate-90">
                                    <circle cx="64" cy="64" r="56" stroke="currentColor" stroke-width="8" fill="transparent" class="text-gray-700"/>
                                    <circle cx="64" cy="64" r="56" stroke="currentColor" stroke-width="8" fill="transparent" 
                                            :stroke-dasharray="351.86" 
                                            :stroke-dashoffset="351.86 - (351.86 * showResultDetail.percentage_score / 100)"
                                            :class="showResultDetail.passed ? 'text-green-500' : 'text-red-500'"
                                            class="transition-all duration-1000"/>
                                </svg>
                                <div class="absolute inset-0 flex items-center justify-center flex-col">
                                    <span class="text-3xl font-bold" 
                                          :class="showResultDetail.passed ? 'text-green-400' : 'text-red-400'"
                                          x-text="showResultDetail.percentage_score.toFixed(1) + '%'"></span>
                                    <span class="text-sm text-gray-400">Score</span>
                                </div>
                            </div>

                            <div class="mt-4">
                                <span :class="showResultDetail.passed ? 'bg-green-900/30 text-green-400' : 'bg-red-900/30 text-red-400'" 
                                      class="px-6



                                          @push('styles')        
    <style>
        .gradient-bg { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .glassmorphism { backdrop-filter: blur(16px); background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.2); }
        .exam-card { transition: all 0.3s ease; }
        .exam-card:hover { transform: translateY(-8px); box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3); }
        .pulse-ring { animation: pulse-ring 1.25s cubic-bezier(0.215, 0.61, 0.355, 1) infinite; }
        @keyframes pulse-ring { 0% { transform: scale(0.33); } 40%, 50% { opacity: 0; } 100% { transform: scale(1.2); opacity: 0; } }
        .floating { animation: floating 3s ease-in-out infinite; }
        @keyframes floating { 0%, 100% { transform: translateY(0px); } 50% { transform: translateY(-10px); } }
        .shimmer { animation: shimmer 2s infinite linear; }
        @keyframes shimmer { 0% { background-position: -468px 0; } 100% { background-position: 468px 0; } }
    </style>
        @endpush
        @push('scripts')
            
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
                        'accent-dark': '#16a34a'
                    }
                }
            }
        }
    </script>
@endpush
