<div class="tab-content">
    <div class="mb-12">
        <div class="flex items-center mb-6">
            <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900 rounded-2xl flex items-center justify-center mr-6">
                <i class="fas fa-user-shield text-blue-600 dark:text-blue-300 text-2xl"></i>
            </div>
            <div>
                <h2 class="text-4xl font-bold text-gray-900 dark:text-white">Administrative Excellence</h2>
                <p class="text-xl text-blue-600 font-semibold">Manage, Optimize, Scale</p>
            </div>
        </div>
        <p class="text-lg text-gray-600 dark:text-gray-400 leading-relaxed max-w-4xl">
            Lead the BootKode ecosystem with comprehensive administrative tools. Manage {{ number_format($stats['totalUsers']) }} users, 
            oversee {{ number_format($stats['totalCourses']) }} courses, and drive strategic growth while maintaining educational excellence.
        </p>
    </div>

    <!-- Admin Stats -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-12">
        <div class="bg-blue-50 dark:bg-blue-900/20 p-6 rounded-2xl text-center">
            <div class="text-3xl font-bold text-blue-600 mb-2">{{ number_format($stats['totalUsers']) }}</div>
            <div class="text-sm text-gray-600 dark:text-gray-400">Platform Users</div>
        </div>
        <div class="bg-blue-50 dark:bg-blue-900/20 p-6 rounded-2xl text-center">
            <div class="text-3xl font-bold text-blue-600 mb-2">{{ number_format($stats['totalCourses']) }}</div>
            <div class="text-sm text-gray-600 dark:text-gray-400">Active Courses</div>
        </div>
        <div class="bg-blue-50 dark:bg-blue-900/20 p-6 rounded-2xl text-center">
            <div class="text-3xl font-bold text-blue-600 mb-2">{{ number_format($stats['issuedCertificates']) }}</div>
            <div class="text-sm text-gray-600 dark:text-gray-400">Certificates Managed</div>
        </div>
        <div class="bg-blue-50 dark:bg-blue-900/20 p-6 rounded-2xl text-center">
            <div class="text-3xl font-bold text-blue-600 mb-2">{{ $stats['systemUptime'] }}%</div>
            <div class="text-sm text-gray-600 dark:text-gray-400">System Uptime</div>
        </div>
    </div>

    <!-- Admin Roadmap -->
    <div class="mb-12">
        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-8 text-center">Administrative Workflow</h3>
        <div class="relative">
            <div class="absolute left-1/2 transform -translate-x-px h-full w-0.5 bg-blue-200 dark:bg-blue-800"></div>
            
            <!-- Step 1 -->
            <div class="relative flex items-center mb-12">
                <div class="flex-1 pr-8 text-right">
                    <div class="bg-blue-50 dark:bg-blue-900/20 p-6 rounded-2xl border border-blue-100 dark:border-blue-800">
                        <div class="flex items-center justify-end mb-4">
                            <h4 class="text-xl font-bold text-gray-900 dark:text-white mr-3">System Administration</h4>
                            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-800 rounded-lg flex items-center justify-center">
                                <i class="fas fa-cogs text-blue-600 dark:text-blue-300"></i>
                            </div>
                        </div>
                        <p class="text-gray-700 dark:text-gray-300 mb-4">
                            Monitor platform health, manage {{ number_format($stats['totalUsers']) }} user accounts, oversee system resources, 
                            and ensure optimal performance across all services.
                        </p>
                        <div class="space-y-3">
                            <div class="bg-white dark:bg-gray-700 p-3 rounded-lg">
                                <div class="font-medium text-blue-600">User Management</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">{{ number_format($stats['totalUsers']) }} active users</div>
                            </div>
                            <div class="bg-white dark:bg-gray-700 p-3 rounded-lg">
                                <div class="font-medium text-blue-600">System Monitoring</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">{{ $stats['systemUptime'] }}% uptime SLA</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold text-lg border-4 border-white dark:border-gray-800 shadow-lg z-10">
                    1
                </div>
                <div class="flex-1 pl-8"></div>
            </div>

            <!-- Step 2 -->
            <div class="relative flex items-center mb-12">
                <div class="flex-1 pr-8"></div>
                <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold text-lg border-4 border-white dark:border-gray-800 shadow-lg z-10">
                    2
                </div>
                <div class="flex-1 pl-8">
                    <div class="bg-blue-50 dark:bg-blue-900/20 p-6 rounded-2xl border border-blue-100 dark:border-blue-800">
                        <div class="flex items-center mb-4">
                            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-800 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-check-double text-blue-600 dark:text-blue-300"></i>
                            </div>
                            <h4 class="text-xl font-bold text-gray-900 dark:text-white">Quality Assurance</h4>
                        </div>
                        <p class="text-gray-700 dark:text-gray-300 mb-4">
                            Review and approve {{ number_format($stats['totalCourses']) }} courses, validate {{ number_format($stats['totalInstructors']) }} instructor credentials, 
                            and maintain educational standards for world-class learning.
                        </p>
                        <div class="space-y-2 text-sm">
                            <div class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-2"></i>
                                <span class="text-gray-600 dark:text-gray-400">Course content review</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-2"></i>
                                <span class="text-gray-600 dark:text-gray-400">Instructor verification</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-check text-green-500 mr-2"></i>
                                <span class="text-gray-600 dark:text-gray-400">Standards compliance</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 3 -->
            <div class="relative flex items-center mb-12">
                <div class="flex-1 pr-8 text-right">
                    <div class="bg-blue-50 dark:bg-blue-900/20 p-6 rounded-2xl border border-blue-100 dark:border-blue-800">
                        <div class="flex items-center justify-end mb-4">
                            <h4 class="text-xl font-bold text-gray-900 dark:text-white mr-3">Data & Analytics</h4>
                            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-800 rounded-lg flex items-center justify-center">
                                <i class="fas fa-chart-bar text-blue-600 dark:text-blue-300"></i>
                            </div>
                        </div>
                        <p class="text-gray-700 dark:text-gray-300 mb-4">
                            Analyze platform metrics, track {{ number_format($stats['totalEnrollments']) }} enrollments, monitor learning outcomes, 
                            and generate strategic insights for continuous improvement.
                        </p>
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div class="bg-white dark:bg-gray-700 p-3 rounded-lg">
                                <div class="font-medium text-gray-900 dark:text-white">Completion Rate</div>
                                <div class="text-gray-600 dark:text-gray-400">{{ $stats['successRate'] }}% average</div>
                            </div>
                            <div class="bg-white dark:bg-gray-700 p-3 rounded-lg">
                                <div class="font-medium text-gray-900 dark:text-white">User Satisfaction</div>
                                <div class="text-gray-600 dark:text-gray-400">{{ number_format($stats['averageRating'], 1) }}/5.0 rating</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold text-lg border-4 border-white dark:border-gray-800 shadow-lg z-10">
                    3
                </div>
                <div class="flex-1 pl-8"></div>
            </div>

            <!-- Step 4 -->
            <div class="relative flex items-center mb-12">
                <div class="flex-1 pr-8"></div>
                <div class="w-12 h-12 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold text-lg border-4 border-white dark:border-gray-800 shadow-lg z-10">
                    4
                </div>
                <div class="flex-1 pl-8">
                    <div class="bg-blue-50 dark:bg-blue-900/20 p-6 rounded-2xl border border-blue-100 dark:border-blue-800">
                        <div class="flex items-center mb-4">
                            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-800 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-headset text-blue-600 dark:text-blue-300"></i>
                            </div>
                            <h4 class="text-xl font-bold text-gray-900 dark:text-white">Community Management</h4>
                        </div>
                        <p class="text-gray-700 dark:text-gray-300 mb-4">
                            Moderate community interactions, resolve user disputes, manage support tickets, and 
                            ensure a safe learning environment for all platform users.
                        </p>
                        <div class="space-y-3">
                            <div class="flex items-center justify-between bg-white dark:bg-gray-700 p-3 rounded-lg">
                                <span class="text-gray-700 dark:text-gray-300">Support Ticket Resolution</span>
                                <span class="text-blue-600 font-medium">{{ $stats['avgResponseTime'] }} avg</span>
                            </div>
                            <div class="flex items-center justify-between bg-white dark:bg-gray-700 p-3 rounded-lg">
                                <span class="text-gray-700 dark:text-gray-300">Community Moderation</span>
                                <span class="text-green-500 font-medium">Active</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 5 -->
            <div class="relative flex items-center">
                <div class="flex-1 pr-8 text-right">
                    <div class="bg-blue-50 dark:bg-blue-900/20 p-6 rounded-2xl border border-blue-100 dark:border-blue-800">
                        <div class="flex items-center justify-end mb-4">
                            <h4 class="text-xl font-bold text-gray-900 dark:text-white mr-3">Strategic Planning</h4>
                            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-800 rounded-lg flex items-center justify-center">
                                <i class="fas fa-chess-king text-blue-600 dark:text-blue-300"></i>
                            </div>
                        </div>
                        <p class="text-gray-700 dark:text-gray-300 mb-4">
                            Drive platform growth through strategic initiatives, partnerships, feature development, 
                            and expansion across African markets and educational domains.
                        </p>
                        <div class="space-y-2 text-sm">
                            <div class="flex items-center justify-end">
                                <span class="text-gray-600 dark:text-gray-400 mr-2">Growth strategy execution</span>
                                <i class="fas fa-check text-green-500"></i>
                            </div>
                            <div class="flex items-center justify-end">
                                <span class="text-gray-600 dark:text-gray-400 mr-2">Partnership development</span>
                                <i class="fas fa-check text-green-500"></i>
                            </div>
                            <div class="flex items-center justify-end">
                                <span class="text-gray-600 dark:text-gray-400 mr-2">Market expansion</span>
                                <i class="fas fa-check text-green-500"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center text-white font-bold text-lg border-4 border-white dark:border-gray-800 shadow-lg z-10">
                    <i class="fas fa-crown"></i>
                </div>
                <div class="flex-1 pl-8"></div>
            </div>
        </div>
    </div>

    <!-- Admin Tools -->
    <div class="grid md:grid-cols-3 gap-8 mt-12">
        <div class="bg-blue-50 dark:bg-blue-900/10 p-6 rounded-2xl border border-blue-100 dark:border-blue-800">
            <div class="w-12 h-12 bg-blue-100 dark:bg-blue-800 rounded-xl flex items-center justify-center mb-4">
                <i class="fas fa-tachometer-alt text-blue-600 dark:text-blue-300 text-xl"></i>
            </div>
            <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Advanced Dashboard</h4>
            <p class="text-gray-600 dark:text-gray-400">Comprehensive analytics, real-time monitoring, and detailed reporting tools for data-driven decisions.</p>
        </div>
        <div class="bg-blue-50 dark:bg-blue-900/10 p-6 rounded-2xl border border-blue-100 dark:border-blue-800">
            <div class="w-12 h-12 bg-blue-100 dark:bg-blue-800 rounded-xl flex items-center justify-center mb-4">
                <i class="fas fa-shield-alt text-blue-600 dark:text-blue-300 text-xl"></i>
            </div>
            <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Security Management</h4>
            <p class="text-gray-600 dark:text-gray-400">Enterprise-grade security controls, audit logs, and compliance monitoring for platform protection.</p>
        </div>
        <div class="bg-blue-50 dark:bg-blue-900/10 p-6 rounded-2xl border border-blue-100 dark:border-blue-800">
            <div class="w-12 h-12 bg-blue-100 dark:bg-blue-800 rounded-xl flex items-center justify-center mb-4">
                <i class="fas fa-robot text-blue-600 dark:text-blue-300 text-xl"></i>
            </div>
            <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-2">AI-Powered Insights</h4>
            <p class="text-gray-600 dark:text-gray-400">Machine learning analytics for predictive insights, automated recommendations, and intelligent automation.</p>
        </div>
    </div>
</div>