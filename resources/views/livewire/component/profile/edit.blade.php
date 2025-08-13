<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8" x-data="{ activeTab: '{{ $activeTab }}' }">
    <!-- Edit Profile Header -->
    <div class="bg-gradient-to-r from-gray-800 to-gray-700 p-6 rounded-2xl shadow-xl text-white mb-8">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
            <div>
                <h1 class="text-3xl font-bold text-white">Edit Profile</h1>
                <p class="text-gray-400 mt-2">Update your personal information and settings</p>
            </div>
            <div class="flex gap-3 w-full md:w-auto">
                <a href="{{ route('profile.view') }}" 
                   class="px-6 py-3 border border-gray-600 text-gray-300 rounded-xl hover:bg-gray-700 transition-colors duration-200 font-medium flex items-center justify-center w-full md:w-auto">
                    <i class="fas fa-arrow-left mr-2"></i> Cancel
                </a>
                <button wire:click="updateProfile" type="button"
                        class="px-6 py-3 bg-gradient-to-r from-blue-600 to-pink-600 text-white rounded-xl font-semibold hover:from-blue-700 hover:to-pink-700 transition-all duration-300 shadow-md flex items-center justify-center w-full md:w-auto">
                    <i class="fas fa-save mr-2"></i> Save Changes
                </button>
            </div>
        </div>
    </div>

    <!-- Tab Navigation -->
    <div class="mb-6 border-b border-gray-700">
        <nav class="flex space-x-8 overflow-x-auto pb-2">
            <button @click="activeTab = 'basic'" 
                    :class="{ 'border-blue-500 text-blue-400': activeTab === 'basic', 'border-transparent text-gray-400 hover:text-gray-300 hover:border-gray-400': activeTab !== 'basic' }"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center">
                <i class="fas fa-user-circle mr-2"></i> Basic Info
            </button>
            <button @click="activeTab = 'address'" 
                    :class="{ 'border-blue-500 text-blue-400': activeTab === 'address', 'border-transparent text-gray-400 hover:text-gray-300 hover:border-gray-400': activeTab !== 'address' }"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center">
                <i class="fas fa-map-marker-alt mr-2"></i> Address
            </button>
            <button @click="activeTab = 'education'" 
                    :class="{ 'border-blue-500 text-blue-400': activeTab === 'education', 'border-transparent text-gray-400 hover:text-gray-300 hover:border-gray-400': activeTab !== 'education' }"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center">
                <i class="fas fa-graduation-cap mr-2"></i> Education
            </button>
            <button @click="activeTab = 'social'" 
                    :class="{ 'border-blue-500 text-blue-400': activeTab === 'social', 'border-transparent text-gray-400 hover:text-gray-300 hover:border-gray-400': activeTab !== 'social' }"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center">
                <i class="fas fa-share-alt mr-2"></i> Social Links
            </button>
            <button @click="activeTab = 'photo'" 
                    :class="{ 'border-blue-500 text-blue-400': activeTab === 'photo', 'border-transparent text-gray-400 hover:text-gray-300 hover:border-gray-400': activeTab !== 'photo' }"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center">
                <i class="fas fa-camera mr-2"></i> Profile Photo
            </button>
        </nav>
    </div>

    <!-- Tab Content -->
    <div class="bg-gray-800 rounded-2xl shadow-xl overflow-hidden">
        <!-- Basic Info Tab -->
        <div x-show="activeTab === 'basic'" x-transition:enter="transition ease-out duration-300" 
             x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             class="p-6">
            <h2 class="text-xl font-bold text-white flex items-center mb-6">
                <i class="fas fa-user-circle text-blue-400 mr-3"></i> Basic Information
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-300 mb-2">Full Name *</label>
                    <div class="relative">
                        <input type="text" id="name" wire:model="name"
                               class="w-full px-4 py-3 pl-11 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-white placeholder-gray-400 shadow-sm transition duration-200">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-user text-gray-400"></i>
                        </div>
                    </div>
                    @error('name') <p class="mt-2 text-sm text-red-400">{{ $message }}</p> @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-300 mb-2">Email Address *</label>
                    <div class="relative">
                        <input type="email" id="email" wire:model="email"
                               class="w-full px-4 py-3 pl-11 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-white placeholder-gray-400 shadow-sm transition duration-200">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-gray-400"></i>
                        </div>
                    </div>
                    @error('email') <p class="mt-2 text-sm text-red-400">{{ $message }}</p> @enderror
                </div>

                <!-- Phone Number -->
                <div>
                    <label for="phone_number" class="block text-sm font-medium text-gray-300 mb-2">Phone Number</label>
                    <div class="relative">
                        <input type="tel" id="phone_number" wire:model="phone_number"
                               class="w-full px-4 py-3 pl-11 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-white placeholder-gray-400 shadow-sm transition duration-200">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-phone text-gray-400"></i>
                        </div>
                    </div>
                    @error('phone_number') <p class="mt-2 text-sm text-red-400">{{ $message }}</p> @enderror
                </div>

                <!-- Date of Birth -->
                <div>
                    <label for="date_of_birth" class="block text-sm font-medium text-gray-300 mb-2">Date of Birth</label>
                    <div class="relative">
                        <input type="date" id="date_of_birth" wire:model="date_of_birth"
                               class="w-full px-4 py-3 pl-11 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-white placeholder-gray-400 shadow-sm transition duration-200">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-birthday-cake text-gray-400"></i>
                        </div>
                    </div>
                    @error('date_of_birth') <p class="mt-2 text-sm text-red-400">{{ $message }}</p> @enderror
                </div>

                <!-- Bio -->
                <div class="md:col-span-2">
                    <label for="bio" class="block text-sm font-medium text-gray-300 mb-2">Bio</label>
                    <textarea id="bio" wire:model="bio" rows="3"
                              class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-white placeholder-gray-400 shadow-sm transition duration-200"></textarea>
                    <p class="text-xs text-gray-400 mt-1">Tell us a little about yourself (max 500 characters)</p>
                    @error('bio') <p class="mt-2 text-sm text-red-400">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <!-- Address Tab -->
        <div x-show="activeTab === 'address'" x-transition:enter="transition ease-out duration-300" 
             x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             class="p-6">
            <h2 class="text-xl font-bold text-white flex items-center mb-6">
                <i class="fas fa-map-marker-alt text-green-400 mr-3"></i> Address Information
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Street Address -->
                <div>
                    <label for="address_street" class="block text-sm font-medium text-gray-300 mb-2">Street Address</label>
                    <input type="text" id="address_street" wire:model="address_street"
                           class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-white placeholder-gray-400 shadow-sm transition duration-200">
                    @error('address_street') <p class="mt-2 text-sm text-red-400">{{ $message }}</p> @enderror
                </div>

                <!-- City -->
                <div>
                    <label for="address_city" class="block text-sm font-medium text-gray-300 mb-2">City</label>
                    <input type="text" id="address_city" wire:model="address_city"
                           class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-white placeholder-gray-400 shadow-sm transition duration-200">
                    @error('address_city') <p class="mt-2 text-sm text-red-400">{{ $message }}</p> @enderror
                </div>

                <!-- State/Province -->
                <div>
                    <label for="address_state" class="block text-sm font-medium text-gray-300 mb-2">State/Province</label>
                    <input type="text" id="address_state" wire:model="address_state"
                           class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-white placeholder-gray-400 shadow-sm transition duration-200">
                    @error('address_state') <p class="mt-2 text-sm text-red-400">{{ $message }}</p> @enderror
                </div>

                <!-- Country -->
                <div>
                    <label for="address_country" class="block text-sm font-medium text-gray-300 mb-2">Country</label>
                    <input type="text" id="address_country" wire:model="address_country"
                           class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-white placeholder-gray-400 shadow-sm transition duration-200">
                    @error('address_country') <p class="mt-2 text-sm text-red-400">{{ $message }}</p> @enderror
                </div>

                <!-- Postal Code -->
                <div>
                    <label for="address_postal_code" class="block text-sm font-medium text-gray-300 mb-2">Postal Code</label>
                    <input type="text" id="address_postal_code" wire:model="address_postal_code"
                           class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-white placeholder-gray-400 shadow-sm transition duration-200">
                    @error('address_postal_code') <p class="mt-2 text-sm text-red-400">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <!-- Education Tab -->
        <div x-show="activeTab === 'education'" x-transition:enter="transition ease-out duration-300" 
             x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             class="p-6">
            <h2 class="text-xl font-bold text-white flex items-center mb-6">
                <i class="fas fa-graduation-cap text-pink-400 mr-3"></i> Education & Career
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Occupation -->
                <div>
                    <label for="occupation" class="block text-sm font-medium text-gray-300 mb-2">Occupation</label>
                    <input type="text" id="occupation" wire:model="occupation"
                           class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-white placeholder-gray-400 shadow-sm transition duration-200">
                    @error('occupation') <p class="mt-2 text-sm text-red-400">{{ $message }}</p> @enderror
                </div>

                <!-- Education Level -->
                <div>
                    <label for="education_level" class="block text-sm font-medium text-gray-300 mb-2">Education Level</label>
                    <select id="education_level" wire:model="education_level"
                            class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-white shadow-sm transition duration-200">
                        <option value="">Select education level</option>
                        <option value="High School">High School</option>
                        <option value="Diploma">Diploma</option>
                        <option value="Bachelor's Degree">Bachelor's Degree</option>
                        <option value="Master's Degree">Master's Degree</option>
                        <option value="PhD">PhD</option>
                        <option value="Other">Other</option>
                    </select>
                    @error('education_level') <p class="mt-2 text-sm text-red-400">{{ $message }}</p> @enderror
                </div>
                
                <!-- Skills -->
                <div class="md:col-span-2">
                    <label for="skills" class="block text-sm font-medium text-gray-300 mb-2">Skills & Interests</label>
                    <textarea id="skills" wire:model="skills" rows="3"
                        class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-white placeholder-gray-400 shadow-sm transition duration-200"
                        placeholder="Separate multiple skills with commas (e.g. Laravel, Vue.js, Photography)"></textarea>
                    @error('skills') <p class="mt-2 text-sm text-red-400">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <!-- Social Links Tab -->
        <div x-show="activeTab === 'social'" x-transition:enter="transition ease-out duration-300" 
             x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             class="p-6">
            <h2 class="text-xl font-bold text-white flex items-center mb-6">
                <i class="fas fa-share-alt text-yellow-400 mr-3"></i> Social Links
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Twitter -->
                <div>
                    <label for="social_twitter" class="block text-sm font-medium text-gray-300 mb-2 flex items-center">
                        <i class="fab fa-twitter text-blue-400 mr-2"></i> Twitter
                    </label>
                    <input type="url" id="social_twitter" wire:model="social_links.twitter"
                           class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-white placeholder-gray-400 shadow-sm transition duration-200"
                           placeholder="https://twitter.com/username">
                    @error('social_links.twitter') <p class="mt-2 text-sm text-red-400">{{ $message }}</p> @enderror
                </div>

                <!-- Facebook -->
                <div>
                    <label for="social_facebook" class="block text-sm font-medium text-gray-300 mb-2 flex items-center">
                        <i class="fab fa-facebook-f text-blue-600 mr-2"></i> Facebook
                    </label>
                    <input type="url" id="social_facebook" wire:model="social_links.facebook"
                           class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-white placeholder-gray-400 shadow-sm transition duration-200"
                           placeholder="https://facebook.com/username">
                    @error('social_links.facebook') <p class="mt-2 text-sm text-red-400">{{ $message }}</p> @enderror
                </div>

                <!-- LinkedIn -->
                <div>
                    <label for="social_linkedin" class="block text-sm font-medium text-gray-300 mb-2 flex items-center">
                        <i class="fab fa-linkedin-in text-blue-500 mr-2"></i> LinkedIn
                    </label>
                    <input type="url" id="social_linkedin" wire:model="social_links.linkedin"
                           class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-white placeholder-gray-400 shadow-sm transition duration-200"
                           placeholder="https://linkedin.com/in/username">
                    @error('social_links.linkedin') <p class="mt-2 text-sm text-red-400">{{ $message }}</p> @enderror
                </div>

                <!-- GitHub -->
                <div>
                    <label for="social_github" class="block text-sm font-medium text-gray-300 mb-2 flex items-center">
                        <i class="fab fa-github text-gray-300 mr-2"></i> GitHub
                    </label>
                    <input type="url" id="social_github" wire:model="social_links.github"
                           class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-white placeholder-gray-400 shadow-sm transition duration-200"
                           placeholder="https://github.com/username">
                    @error('social_links.github') <p class="mt-2 text-sm text-red-400">{{ $message }}</p> @enderror
                </div>

                <!-- Instagram -->
                <div>
                    <label for="social_instagram" class="block text-sm font-medium text-gray-300 mb-2 flex items-center">
                        <i class="fab fa-instagram text-pink-500 mr-2"></i> Instagram
                    </label>
                    <input type="url" id="social_instagram" wire:model="social_links.instagram"
                           class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-white placeholder-gray-400 shadow-sm transition duration-200"
                           placeholder="https://instagram.com/username">
                    @error('social_links.instagram') <p class="mt-2 text-sm text-red-400">{{ $message }}</p> @enderror
                </div>

                <!-- Website -->
                <div>
                    <label for="social_website" class="block text-sm font-medium text-gray-300 mb-2 flex items-center">
                        <i class="fas fa-globe text-pink-400 mr-2"></i> Website
                    </label>
                    <input type="url" id="social_website" wire:model="social_links.website"
                           class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-white placeholder-gray-400 shadow-sm transition duration-200"
                           placeholder="https://yourwebsite.com">
                    @error('social_links.website') <p class="mt-2 text-sm text-red-400">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <!-- Profile Photo Tab -->
        <div x-show="activeTab === 'photo'" x-transition:enter="transition ease-out duration-300" 
             x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             class="p-6">
            <h2 class="text-xl font-bold text-white flex items-center mb-6">
                <i class="fas fa-camera text-red-400 mr-3"></i> Profile Photo
            </h2>
            
            <div class="flex flex-col md:flex-row items-center md:items-start gap-8">
                <div class="flex-shrink-0">
                    <div class="relative">
                        @if($temp_profile_picture)
                            <img src="{{ $temp_profile_picture }}" 
                                 class="h-40 w-40 rounded-full object-cover border-4 border-blue-500/20 shadow-lg">
                        @elseif($user->profile_picture)
                            <img src="{{ $user->profile_picture_url }}" 
                                 class="h-40 w-40 rounded-full object-cover border-4 border-blue-500/20 shadow-lg">
                        @else
                            <div class="h-40 w-40 rounded-full bg-gradient-to-r from-blue-500 to-pink-600 flex items-center justify-center text-white text-5xl font-bold shadow-lg">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        @endif
                        <span class="absolute bottom-0 right-0 bg-green-500 rounded-full h-5 w-5 border-2 border-gray-800"></span>
                    </div>
                </div>
                
                <div class="flex-1">
                    <div class="mb-4">
                        <label for="profile_picture" class="block text-sm font-medium text-gray-300 mb-2">Upload New Photo</label>
                        <input type="file" id="profile_picture" wire:model="profile_picture"
                               class="w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-500/20 file:text-blue-400 hover:file:bg-blue-500/30">
                        @error('profile_picture') <p class="mt-2 text-sm text-red-400">{{ $message }}</p> @enderror
                        <p class="mt-1 text-xs text-gray-400">JPG, PNG or GIF (Max: 2MB)</p>
                    </div>
                    
                    @if($user->profile_picture)
                        <button wire:click="deleteProfilePicture" type="button"
                                class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium transition-colors duration-200">
                            <i class="fas fa-trash-alt mr-1"></i> Remove Current Photo
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>