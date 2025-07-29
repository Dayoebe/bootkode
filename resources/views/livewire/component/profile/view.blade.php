<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Profile Header -->
    <div class="bg-gray-800 p-6 rounded-lg shadow-xl text-white flex flex-col md:flex-row items-start md:items-center justify-between mb-8">
        <div class="bg-gray-800 p-6 rounded-lg shadow-xl text-white flex items-center space-x-4 mb-4 md:mb-0">
            <div class="relative">
                <div class="h-24 w-24 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center text-white text-4xl font-bold shadow-lg">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <span class="absolute bottom-0 right-0 bg-green-500 rounded-full h-5 w-5 border-2 border-gray-800"></span>
            </div>
            <div class="">
                <h1 class="text-3xl font-bold text-white">{{ $user->name }}</h1>
                <div class="flex items-center space-x-2 mt-1">
                    <span class="px-3 py-1 rounded-full text-xs font-medium bg-gray-700 text-blue-300">
                        {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                    </span>
                    @if($user->email_verified_at)
                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-green-900/30 text-green-400 flex items-center">
                            <i class="fas fa-check-circle mr-1"></i> Verified
                        </span>
                    @else
                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-yellow-900/30 text-yellow-400 flex items-center">
                            <i class="fas fa-exclamation-circle mr-1"></i> Unverified
                        </span>
                    @endif
                </div>
            </div>
        </div>
        <a href="{{ route('profile.edit') }}" 
           class="px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white rounded-xl font-semibold shadow-lg transition-all duration-300 flex items-center">
            <i class="fas fa-user-edit mr-2"></i> Edit Profile
        </a>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Personal Information Card -->
        <div class="bg-gray-800 rounded-2xl shadow-xl overflow-hidden col-span-1 lg:col-span-2">
            <div class="p-6 border-b border-gray-700">
                <h2 class="text-xl font-bold text-white flex items-center">
                    <i class="fas fa-user-circle text-blue-400 mr-3"></i> Personal Information
                </h2>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-sm font-medium text-gray-400 mb-1">Full Name</p>
                    <p class="text-lg text-white">{{ $user->name }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-400 mb-1">Email Address</p>
                    <p class="text-lg text-white">{{ $user->email }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-400 mb-1">Account Created</p>
                    <p class="text-lg text-white">{{ $user->created_at->format('M d, Y') }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-400 mb-1">Last Updated</p>
                    <p class="text-lg text-white">{{ $user->updated_at->diffForHumans() }}</p>
                </div>
            </div>
        </div>

        <!-- Account Status Card -->
        <div class="bg-gray-800 rounded-2xl shadow-xl overflow-hidden">
            <div class="p-6 border-b border-gray-700">
                <h2 class="text-xl font-bold text-white flex items-center">
                    <i class="fas fa-shield-alt text-green-400 mr-3"></i> Account Security
                </h2>
            </div>
            <div class="p-6 space-y-4">
                <div class="flex items-center justify-between p-4 bg-gray-700/50 rounded-lg">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-envelope text-purple-400"></i>
                        <span class="text-white">Email Verification</span>
                    </div>
                    @if($user->email_verified_at)
                        <span class="text-green-400 text-sm font-medium">Verified</span>
                    @else
                        <a href="{{ route('verification.notice') }}" class="text-blue-400 hover:underline text-sm">Verify Now</a>
                    @endif
                </div>
                
                <div class="flex items-center justify-between p-4 bg-gray-700/50 rounded-lg">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-lock text-yellow-400"></i>
                        <span class="text-white">Password</span>
                    </div>
                    <span class="text-gray-400 text-sm">Last changed: {{ $user->updated_at->diffForHumans() }}</span>
                </div>
                
                <div class="flex items-center justify-between p-4 bg-gray-700/50 rounded-lg">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-clock text-blue-400"></i>
                        <span class="text-white">Account Activity</span>
                    </div>
                    <span class="text-gray-400 text-sm">Active now</span>
                </div>
            </div>
        </div>

        <!-- Additional Sections -->
        <div class="bg-gray-800 rounded-2xl shadow-xl overflow-hidden lg:col-span-3">
            <div class="p-6 border-b border-gray-700">
                <h2 class="text-xl font-bold text-white flex items-center">
                    <i class="fas fa-chart-line text-purple-400 mr-3"></i> Activity Overview
                </h2>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-gradient-to-br from-gray-800 to-gray-700 p-5 rounded-xl border border-gray-700">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-gray-400 font-medium">Courses Enrolled</h3>
                        <i class="fas fa-book-open text-blue-400"></i>
                    </div>
                    <p class="text-3xl font-bold text-white">12</p>
                    <p class="text-sm text-gray-400 mt-2">+2 this month</p>
                </div>
                
                <div class="bg-gradient-to-br from-gray-800 to-gray-700 p-5 rounded-xl border border-gray-700">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-gray-400 font-medium">Certificates</h3>
                        <i class="fas fa-award text-yellow-400"></i>
                    </div>
                    <p class="text-3xl font-bold text-white">3</p>
                    <p class="text-sm text-gray-400 mt-2">1 pending</p>
                </div>
                
                <div class="bg-gradient-to-br from-gray-800 to-gray-700 p-5 rounded-xl border border-gray-700">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-gray-400 font-medium">Learning Hours</h3>
                        <i class="fas fa-clock text-green-400"></i>
                    </div>
                    <p class="text-3xl font-bold text-white">47.5</p>
                    <p class="text-sm text-gray-400 mt-2">Weekly average: 8.2h</p>
                </div>
            </div>
        </div>
    </div>
</div>