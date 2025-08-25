<!-- Single root div for the entire component -->
<div class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 p-4">
    <div class="" x-data="{ activeTab: @entangle('activeTab') }">
        <!-- Header Section -->
        <div
            class="relative bg-gradient-to-r from-blue-900/80 via-purple-900/80 to-pink-900/80 backdrop-blur-sm rounded-3xl shadow-2xl border border-gray-700/50 overflow-hidden mb-8">
            <!-- Background Pattern -->
            <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width="60" height="60" viewBox="0 0 60 60"
                xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg fill="%23ffffff"
                fill-opacity="0.05"%3E%3Ccircle cx="30" cy="30" r="2" /%3E%3C/g%3E%3C/g%3E%3C/svg%3E')]
                opacity-20"></div>

            <div class="relative z-10 p-8">
                <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-8">
                    <!-- Profile Info -->
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-6 flex-1">
                        <div class="relative group">
                            @if ($user->profile_picture)
                                <img src="{{ asset('storage/' . $user->profile_picture) }}"
                                    class="w-24 h-24 lg:w-32 lg:h-32 rounded-full object-cover border-4 border-white/20 shadow-xl group-hover:scale-105 transition-transform duration-300">
                            @else
                                <div
                                    class="w-24 h-24 lg:w-32 lg:h-32 rounded-full bg-gradient-to-br from-blue-500 via-purple-600 to-pink-500 flex items-center justify-center text-white text-4xl lg:text-5xl font-bold shadow-xl group-hover:scale-105 transition-transform duration-300">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                            @endif

                            <!-- Status Indicator -->
                            <div
                                class="absolute bottom-0 right-0 w-6 h-6 lg:w-8 lg:h-8 bg-green-500 rounded-full border-4 border-gray-800 flex items-center justify-center">
                                <div class="w-2 h-2 bg-white rounded-full animate-pulse"></div>
                            </div>
                        </div>

                        <div class="flex-1 min-w-0">
                            <h1 class="text-3xl lg:text-4xl font-bold text-white mb-2 truncate">{{ $user->name }}
                            </h1>

                            <!-- Badges -->
                            <div class="flex flex-wrap items-center gap-2 mb-3">
                                <span
                                    class="px-4 py-1.5 rounded-full text-sm font-medium bg-gradient-to-r from-blue-500/20 to-purple-500/20 border border-blue-500/30 text-blue-300 backdrop-blur-sm">
                                    <i class="fas fa-user-tag mr-1"></i>
                                    {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                                </span>

                                @if ($user->email_verified_at)
                                    <span
                                        class="px-3 py-1.5 rounded-full text-sm font-medium bg-green-500/20 border border-green-500/30 text-green-300 backdrop-blur-sm flex items-center">
                                        <i class="fas fa-check-circle mr-1"></i> Verified
                                    </span>
                                @else
                                    <span
                                        class="px-3 py-1.5 rounded-full text-sm font-medium bg-yellow-500/20 border border-yellow-500/30 text-yellow-300 backdrop-blur-sm flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i> Unverified
                                    </span>
                                @endif

                                @if ($user->is_active)
                                    <span
                                        class="px-3 py-1.5 rounded-full text-sm font-medium bg-emerald-500/20 border border-emerald-500/30 text-emerald-300 backdrop-blur-sm flex items-center">
                                        <i class="fas fa-circle mr-1 text-xs"></i> Active
                                    </span>
                                @endif
                            </div>

                            @if ($user->bio)
                                <p class="text-gray-300 text-sm lg:text-base leading-relaxed">{{ $user->bio }}
                                </p>
                            @endif
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">
                        @if ($isEditing)
                            <button wire:click="toggleEditMode"
                                class="px-6 py-3 bg-gray-700/50 hover:bg-gray-700 border border-gray-600 text-gray-200 rounded-xl font-medium transition-all duration-300 backdrop-blur-sm flex items-center justify-center">
                                <i class="fas fa-times mr-2"></i> Cancel
                            </button>
                            <button wire:click="updateProfile"
                                class="px-6 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-emerald-500/25 flex items-center justify-center">
                                <i class="fas fa-save mr-2"></i> Save Changes
                            </button>
                        @else
                            <button wire:click="toggleEditMode"
                                class="px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-blue-500/25 flex items-center justify-center">
                                <i class="fas fa-edit mr-2"></i> Edit Profile
                            </button>
                            <button
                                class="px-6 py-3 bg-gray-700/50 hover:bg-gray-700 border border-gray-600 text-gray-200 rounded-xl font-medium transition-all duration-300 backdrop-blur-sm flex items-center justify-center">
                                <i class="fas fa-share-alt mr-2"></i> Share
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab Navigation -->
        <div class="bg-gray-800/50 backdrop-blur-sm rounded-2xl border border-gray-700/50 mb-8 overflow-hidden">
            <nav class="flex overflow-x-auto scrollbar-hide p-2">
                @php
                    $tabs = [
                        'personal' => [
                            'icon' => 'fas fa-user-circle',
                            'label' => 'Personal Info',
                            'color' => 'blue',
                        ],
                        'education' => [
                            'icon' => 'fas fa-graduation-cap',
                            'label' => 'Education',
                            'color' => 'purple',
                        ],
                        'social' => ['icon' => 'fas fa-share-alt', 'label' => 'Social Links', 'color' => 'pink'],
                        'activity' => ['icon' => 'fas fa-chart-line', 'label' => 'Activity', 'color' => 'emerald'],
                        'progress' => ['icon' => 'fas fa-chart-pie', 'label' => 'Progress', 'color' => 'orange'],
                        'resources' => ['icon' => 'fas fa-bookmark', 'label' => 'Resources', 'color' => 'yellow'],
                        'wishlist' => ['icon' => 'fas fa-heart', 'label' => 'Wishlist', 'color' => 'red'],
                        'certificates' => [
                            'icon' => 'fas fa-certificate',
                            'label' => 'Certificates',
                            'color' => 'amber',
                        ],
                        'settings' => ['icon' => 'fas fa-cog', 'label' => 'Settings', 'color' => 'gray'],
                    ];

                    if ($isEditing) {
                        $tabs = [
                            'basic' => ['icon' => 'fas fa-user-circle', 'label' => 'Basic Info', 'color' => 'blue'],
                            'address' => [
                                'icon' => 'fas fa-map-marker-alt',
                                'label' => 'Address',
                                'color' => 'green',
                            ],
                            'education' => [
                                'icon' => 'fas fa-graduation-cap',
                                'label' => 'Education',
                                'color' => 'purple',
                            ],
                            'social' => [
                                'icon' => 'fas fa-share-alt',
                                'label' => 'Social Links',
                                'color' => 'pink',
                            ],
                            'photo' => ['icon' => 'fas fa-camera', 'label' => 'Profile Photo', 'color' => 'red'],
                        ];
                    }
                @endphp

                @foreach ($tabs as $key => $tab)
                    <button @click="activeTab = '{{ $key }}'"
                        :class="{
                            'bg-gradient-to-r from-{{ $tab['color'] }}-500 to-{{ $tab['color'] }}-600 text-white shadow-lg': activeTab === '{{ $key }}',
                            'text-gray-400 hover:text-gray-300 hover:bg-gray-700/50': activeTab !== '{{ $key }}'
                        }"
                        class="whitespace-nowrap px-6 py-3 rounded-xl font-medium text-sm transition-all duration-300 flex items-center mx-1">
                        <i class="{{ $tab['icon'] }} mr-2"></i> {{ $tab['label'] }}
                    </button>
                @endforeach
            </nav>
        </div>

        <!-- Tab Content -->
        <div class="bg-gray-800/50 backdrop-blur-sm rounded-2xl border border-gray-700/50 overflow-hidden shadow-2xl">
            @if ($isEditing)
                <!-- Edit Mode Content -->
                @include('livewire.user-management.profile.profile.edit-content')
            @else
                <!-- View Mode Content -->
                @include('livewire.user-management.profile.profile.view-content')
            @endif
        </div>
    </div>

    <style>
        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }

        .backdrop-blur-sm {
            backdrop-filter: blur(8px);
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        .animate-float {
            animation: float 3s ease-in-out infinite;
        }
    </style>
</div>
