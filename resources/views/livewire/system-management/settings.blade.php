<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="bg-gradient-to-r from-gray-800 to-gray-700 p-6 rounded-2xl shadow-xl text-white mb-8 animate__animated animate__fadeIn">
        <h1 class="text-3xl font-bold text-white">
            <i class="fas fa-cog mr-2"></i> Settings
        </h1>
        <p class="text-gray-400 mt-2">Manage your account preferences and security</p>
    </div>

    <!-- Tabs -->
    <div x-data="{ activeTab: @entangle('activeTab') }" class="mb-8">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                <button @click="activeTab = 'profile'"
                        :class="{ 'border-blue-500 text-blue-600': activeTab === 'profile', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'profile' }"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center">
                    <i class="fas fa-user mr-2"></i> Profile
                </button>
                <button @click="activeTab = 'notifications'"
                        :class="{ 'border-blue-500 text-blue-600': activeTab === 'notifications', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'notifications' }"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center">
                    <i class="fas fa-bell mr-2"></i> Notifications
                </button>
                <button @click="activeTab = 'security'"
                        :class="{ 'border-blue-500 text-blue-600': activeTab === 'security', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'security' }"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center">
                    <i class="fas fa-lock mr-2"></i> Security
                </button>
            </nav>
        </div>
    </div>

    <!-- Tab Content -->
    <div class="bg-white shadow rounded-lg p-6 animate__animated animate__fadeInUp">
        <!-- Profile Tab -->
        <div x-show="activeTab === 'profile'">
            <form wire:submit.prevent="saveProfile">
                <div class="space-y-6">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                        <input wire:model="name" type="text" id="name"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input wire:model="email" type="email" id="email"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- Bio -->
                    <div>
                        <label for="bio" class="block text-sm font-medium text-gray-700">Bio</label>
                        <textarea wire:model="bio" id="bio" rows="4"
                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                        @error('bio') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- Profile Picture -->
                    <div>
                        <label for="profile_picture" class="block text-sm font-medium text-gray-700">Profile Picture</label>
                        <input wire:model="profile_picture" type="file" id="profile_picture"
                               class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        @error('profile_picture') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        @if (Auth::user()->profile_picture)
                            <img src="{{ asset('storage/' . Auth::user()->profile_picture) }}"
                                 class="mt-2 h-24 w-24 rounded-full object-cover border-2 border-blue-500/20">
                        @endif
                    </div>

                    <!-- Social Links -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Social Links</label>
                        <div class="mt-1 space-y-4">
                            <div>
                                <label for="social_links.twitter" class="block text-sm text-gray-600">Twitter</label>
                                <input wire:model="social_links.twitter" type="url" id="social_links.twitter"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('social_links.twitter') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="social_links.linkedin" class="block text-sm text-gray-600">LinkedIn</label>
                                <input wire:model="social_links.linkedin" type="url" id="social_links.linkedin"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('social_links.linkedin') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label for="social_links.github" class="block text-sm text-gray-600">GitHub</label>
                                <input wire:model="social_links.github" type="url" id="social_links.github"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('social_links.github') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6">
                    <button type="submit" wire:loading.attr="disabled"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50">
                        <span wire:loading.remove><i class="fas fa-save mr-2"></i> Save Profile</span>
                        <span wire:loading><i class="fas fa-circle-notch fa-spin mr-2"></i> Saving...</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Notifications Tab -->
        <div x-show="activeTab === 'notifications'">
            <form wire:submit.prevent="saveNotificationPreferences">
                <div class="space-y-4">
                    <div class="flex items-center">
                        <input wire:model="receive_course_updates" type="checkbox" id="receive_course_updates"
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <label for="receive_course_updates" class="ml-2 text-sm text-gray-600">
                            Receive course update notifications <i class="fas fa-book-open ml-1 text-blue-600"></i>
                        </label>
                    </div>
                    <div class="flex items-center">
                        <input wire:model="receive_certificate_notifications" type="checkbox" id="receive_certificate_notifications"
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <label for="receive_certificate_notifications" class="ml-2 text-sm text-gray-600">
                            Receive certificate notifications <i class="fas fa-certificate ml-1 text-blue-600"></i>
                        </label>
                    </div>
                </div>

                <div class="mt-6">
                    <button type="submit" wire:loading.attr="disabled"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50">
                        <span wire:loading.remove><i class="fas fa-save mr-2"></i> Save Preferences</span>
                        <span wire:loading><i class="fas fa-circle-notch fa-spin mr-2"></i> Saving...</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Security Tab -->
        <div x-show="activeTab === 'security'">
            <form wire:submit.prevent="savePassword">
                <div class="space-y-6">
                    <div>
                        <label for="current_password" class="block text-sm font-medium text-gray-700">Current Password</label>
                        <input wire:model="current_password" type="password" id="current_password"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('current_password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="new_password" class="block text-sm font-medium text-gray-700">New Password</label>
                        <input wire:model="new_password" type="password" id="new_password"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('new_password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                        <input wire:model="new_password_confirmation" type="password" id="new_password_confirmation"
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('new_password_confirmation') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="mt-6">
                    <button type="submit" wire:loading.attr="disabled"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50">
                        <span wire:loading.remove><i class="fas fa-save mr-2"></i> Change Password</span>
                        <span wire:loading><i class="fas fa-circle-notch fa-spin mr-2"></i> Saving...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>