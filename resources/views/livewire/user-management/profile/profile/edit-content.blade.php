<!-- Single root container for all edit tabs -->
<div>
    <!-- Basic Info Tab -->
    <div x-show="activeTab === 'basic'" x-transition.opacity.duration.300ms class="p-8">
        <div class="flex items-center mb-8">
            <div
                class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl flex items-center justify-center mr-4">
                <i class="fas fa-user-circle text-white text-xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-white">Basic Information</h2>
                <p class="text-gray-400">Update your personal details</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-semibold text-gray-300 mb-3">Full Name *</label>
                <div class="relative">
                    <input type="text" id="name" wire:model="name"
                        class="w-full px-4 py-4 pl-12 bg-gray-700/50 border border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-white placeholder-gray-400 shadow-sm transition-all duration-200 backdrop-blur-sm">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-user text-gray-400"></i>
                    </div>
                </div>
                @error('name')
                    <p class="mt-2 text-sm text-red-400 flex items-center"><i
                            class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                @enderror
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-semibold text-gray-300 mb-3">Email Address *</label>
                <div class="relative">
                    <input type="email" id="email" wire:model="email"
                        class="w-full px-4 py-4 pl-12 bg-gray-700/50 border border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-white placeholder-gray-400 shadow-sm transition-all duration-200 backdrop-blur-sm">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-envelope text-gray-400"></i>
                    </div>
                </div>
                @error('email')
                    <p class="mt-2 text-sm text-red-400 flex items-center"><i
                            class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                @enderror
            </div>

            <!-- Phone Number -->
            <div>
                <label for="phone_number" class="block text-sm font-semibold text-gray-300 mb-3">Phone Number</label>
                <div class="relative">
                    <input type="tel" id="phone_number" wire:model="phone_number"
                        class="w-full px-4 py-4 pl-12 bg-gray-700/50 border border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-white placeholder-gray-400 shadow-sm transition-all duration-200 backdrop-blur-sm">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-phone text-gray-400"></i>
                    </div>
                </div>
                @error('phone_number')
                    <p class="mt-2 text-sm text-red-400 flex items-center"><i
                            class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                @enderror
            </div>

            <!-- Date of Birth -->
            <div>
                <label for="date_of_birth" class="block text-sm font-semibold text-gray-300 mb-3">Date of Birth</label>
                <div class="relative">
                    <input type="date" id="date_of_birth" wire:model="date_of_birth"
                        class="w-full px-4 py-4 pl-12 bg-gray-700/50 border border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-white placeholder-gray-400 shadow-sm transition-all duration-200 backdrop-blur-sm">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-birthday-cake text-gray-400"></i>
                    </div>
                </div>
                @error('date_of_birth')
                    <p class="mt-2 text-sm text-red-400 flex items-center"><i
                            class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                @enderror
            </div>

            <!-- Bio -->
            <div class="lg:col-span-2">
                <label for="bio" class="block text-sm font-semibold text-gray-300 mb-3">Bio</label>
                <div class="relative">
                    <textarea id="bio" wire:model="bio" rows="4"
                        class="w-full px-4 py-4 bg-gray-700/50 border border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-white placeholder-gray-400 shadow-sm transition-all duration-200 backdrop-blur-sm resize-none"
                        placeholder="Tell us a little about yourself..."></textarea>
                </div>
                <p class="text-xs text-gray-400 mt-2 flex items-center">
                    <i class="fas fa-info-circle mr-1"></i>
                    Maximum 500 characters
                </p>
                @error('bio')
                    <p class="mt-2 text-sm text-red-400 flex items-center"><i
                            class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    <!-- Address Tab -->
    <div x-show="activeTab === 'address'" x-transition.opacity.duration.300ms class="p-8">
        <div class="flex items-center mb-8">
            <div
                class="w-12 h-12 bg-gradient-to-r from-green-500 to-emerald-600 rounded-xl flex items-center justify-center mr-4">
                <i class="fas fa-map-marker-alt text-white text-xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-white">Address Information</h2>
                <p class="text-gray-400">Update your location details</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Street Address -->
            <div class="lg:col-span-2">
                <label for="address_street" class="block text-sm font-semibold text-gray-300 mb-3">Street
                    Address</label>
                <div class="relative">
                    <input type="text" id="address_street" wire:model="address_street"
                        class="w-full px-4 py-4 pl-12 bg-gray-700/50 border border-gray-600 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 text-white placeholder-gray-400 shadow-sm transition-all duration-200 backdrop-blur-sm"
                        placeholder="123 Main Street">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-road text-gray-400"></i>
                    </div>
                </div>
                @error('address_street')
                    <p class="mt-2 text-sm text-red-400 flex items-center"><i
                            class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                @enderror
            </div>

            <!-- City -->
            <div>
                <label for="address_city" class="block text-sm font-semibold text-gray-300 mb-3">City</label>
                <div class="relative">
                    <input type="text" id="address_city" wire:model="address_city"
                        class="w-full px-4 py-4 pl-12 bg-gray-700/50 border border-gray-600 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 text-white placeholder-gray-400 shadow-sm transition-all duration-200 backdrop-blur-sm"
                        placeholder="New York">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-city text-gray-400"></i>
                    </div>
                </div>
                @error('address_city')
                    <p class="mt-2 text-sm text-red-400 flex items-center"><i
                            class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                @enderror
            </div>

            <!-- State/Province -->
            <div>
                <label for="address_state" class="block text-sm font-semibold text-gray-300 mb-3">State/Province</label>
                <div class="relative">
                    <input type="text" id="address_state" wire:model="address_state"
                        class="w-full px-4 py-4 pl-12 bg-gray-700/50 border border-gray-600 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 text-white placeholder-gray-400 shadow-sm transition-all duration-200 backdrop-blur-sm"
                        placeholder="New York">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-map text-gray-400"></i>
                    </div>
                </div>
                @error('address_state')
                    <p class="mt-2 text-sm text-red-400 flex items-center"><i
                            class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                @enderror
            </div>

            <!-- Country -->
            <div>
                <label for="address_country" class="block text-sm font-semibold text-gray-300 mb-3">Country</label>
                <div class="relative">
                    <input type="text" id="address_country" wire:model="address_country"
                        class="w-full px-4 py-4 pl-12 bg-gray-700/50 border border-gray-600 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 text-white placeholder-gray-400 shadow-sm transition-all duration-200 backdrop-blur-sm"
                        placeholder="United States">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-globe text-gray-400"></i>
                    </div>
                </div>
                @error('address_country')
                    <p class="mt-2 text-sm text-red-400 flex items-center"><i
                            class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                @enderror
            </div>

            <!-- Postal Code -->
            <div>
                <label for="address_postal_code" class="block text-sm font-semibold text-gray-300 mb-3">Postal
                    Code</label>
                <div class="relative">
                    <input type="text" id="address_postal_code" wire:model="address_postal_code"
                        class="w-full px-4 py-4 pl-12 bg-gray-700/50 border border-gray-600 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 text-white placeholder-gray-400 shadow-sm transition-all duration-200 backdrop-blur-sm"
                        placeholder="10001">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-mail-bulk text-gray-400"></i>
                    </div>
                </div>
                @error('address_postal_code')
                    <p class="mt-2 text-sm text-red-400 flex items-center"><i
                            class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    <!-- Education Tab -->
    <div x-show="activeTab === 'education'" x-transition.opacity.duration.300ms class="p-8">
        <div class="flex items-center mb-8">
            <div
                class="w-12 h-12 bg-gradient-to-r from-purple-500 to-pink-600 rounded-xl flex items-center justify-center mr-4">
                <i class="fas fa-graduation-cap text-white text-xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-white">Education & Career</h2>
                <p class="text-gray-400">Update your professional background</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Occupation -->
            <div>
                <label for="occupation" class="block text-sm font-semibold text-gray-300 mb-3">Occupation</label>
                <div class="relative">
                    <input type="text" id="occupation" wire:model="occupation"
                        class="w-full px-4 py-4 pl-12 bg-gray-700/50 border border-gray-600 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-white placeholder-gray-400 shadow-sm transition-all duration-200 backdrop-blur-sm"
                        placeholder="Software Developer">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-briefcase text-gray-400"></i>
                    </div>
                </div>
                @error('occupation')
                    <p class="mt-2 text-sm text-red-400 flex items-center"><i
                            class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                @enderror
            </div>

            <!-- Education Level -->
            <div>
                <label for="education_level" class="block text-sm font-semibold text-gray-300 mb-3">Education
                    Level</label>
                <div class="relative">
                    <select id="education_level" wire:model="education_level"
                        class="w-full px-4 py-4 pl-12 bg-gray-700/50 border border-gray-600 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-white shadow-sm transition-all duration-200 backdrop-blur-sm appearance-none">
                        <option value="">Select education level</option>
                        <option value="High School">High School</option>
                        <option value="Diploma">Diploma</option>
                        <option value="Bachelor's Degree">Bachelor's Degree</option>
                        <option value="Master's Degree">Master's Degree</option>
                        <option value="PhD">PhD</option>
                        <option value="Other">Other</option>
                    </select>
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-graduation-cap text-gray-400"></i>
                    </div>
                    <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                        <i class="fas fa-chevron-down text-gray-400"></i>
                    </div>
                </div>
                @error('education_level')
                    <p class="mt-2 text-sm text-red-400 flex items-center"><i
                            class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                @enderror
            </div>

            <!-- Skills -->
            <div class="lg:col-span-2">
                <label for="skills" class="block text-sm font-semibold text-gray-300 mb-3">Skills &
                    Interests</label>
                <div class="relative">
                    <textarea id="skills" wire:model="skills" rows="4"
                        class="w-full px-4 py-4 bg-gray-700/50 border border-gray-600 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-white placeholder-gray-400 shadow-sm transition-all duration-200 backdrop-blur-sm resize-none"
                        placeholder="Laravel, Vue.js, Photography, Digital Marketing..."></textarea>
                </div>
                <p class="text-xs text-gray-400 mt-2 flex items-center">
                    <i class="fas fa-info-circle mr-1"></i>
                    Separate multiple skills with commas
                </p>
                @error('skills')
                    <p class="mt-2 text-sm text-red-400 flex items-center"><i
                            class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    <!-- Social Links Tab -->
    <div x-show="activeTab === 'social'" x-transition.opacity.duration.300ms class="p-8">
        <div class="flex items-center mb-8">
            <div
                class="w-12 h-12 bg-gradient-to-r from-pink-500 to-red-600 rounded-xl flex items-center justify-center mr-4">
                <i class="fas fa-share-alt text-white text-xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-white">Social Links</h2>
                <p class="text-gray-400">Connect your social media profiles</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Twitter -->
            <div>
                <label for="social_twitter" class="block text-sm font-semibold text-gray-300 mb-3 flex items-center">
                    <i class="fab fa-twitter text-blue-400 mr-2"></i> Twitter / X
                </label>
                <div class="relative">
                    <input type="url" id="social_twitter" wire:model="social_links.twitter"
                        class="w-full px-4 py-4 pl-12 bg-gray-700/50 border border-gray-600 rounded-xl focus:ring-2 focus:ring-pink-500 focus:border-pink-500 text-white placeholder-gray-400 shadow-sm transition-all duration-200 backdrop-blur-sm"
                        placeholder="https://twitter.com/username">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fab fa-twitter text-blue-400"></i>
                    </div>
                </div>
                @error('social_links.twitter')
                    <p class="mt-2 text-sm text-red-400 flex items-center"><i
                            class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                @enderror
            </div>

            <!-- Facebook -->
            <div>
                <label for="social_facebook" class="block text-sm font-semibold text-gray-300 mb-3 flex items-center">
                    <i class="fab fa-facebook-f text-blue-600 mr-2"></i> Facebook
                </label>
                <div class="relative">
                    <input type="url" id="social_facebook" wire:model="social_links.facebook"
                        class="w-full px-4 py-4 pl-12 bg-gray-700/50 border border-gray-600 rounded-xl focus:ring-2 focus:ring-pink-500 focus:border-pink-500 text-white placeholder-gray-400 shadow-sm transition-all duration-200 backdrop-blur-sm"
                        placeholder="https://facebook.com/username">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fab fa-facebook-f text-blue-600"></i>
                    </div>
                </div>
                @error('social_links.facebook')
                    <p class="mt-2 text-sm text-red-400 flex items-center"><i
                            class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                @enderror
            </div>

            <!-- LinkedIn -->
            <div>
                <label for="social_linkedin" class="block text-sm font-semibold text-gray-300 mb-3 flex items-center">
                    <i class="fab fa-linkedin-in text-blue-500 mr-2"></i> LinkedIn
                </label>
                <div class="relative">
                    <input type="url" id="social_linkedin" wire:model="social_links.linkedin"
                        class="w-full px-4 py-4 pl-12 bg-gray-700/50 border border-gray-600 rounded-xl focus:ring-2 focus:ring-pink-500 focus:border-pink-500 text-white placeholder-gray-400 shadow-sm transition-all duration-200 backdrop-blur-sm"
                        placeholder="https://linkedin.com/in/username">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fab fa-linkedin-in text-blue-500"></i>
                    </div>
                </div>
                @error('social_links.linkedin')
                    <p class="mt-2 text-sm text-red-400 flex items-center"><i
                            class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                @enderror
            </div>

            <!-- GitHub -->
            <div>
                <label for="social_github" class="block text-sm font-semibold text-gray-300 mb-3 flex items-center">
                    <i class="fab fa-github text-gray-300 mr-2"></i> GitHub
                </label>
                <div class="relative">
                    <input type="url" id="social_github" wire:model="social_links.github"
                        class="w-full px-4 py-4 pl-12 bg-gray-700/50 border border-gray-600 rounded-xl focus:ring-2 focus:ring-pink-500 focus:border-pink-500 text-white placeholder-gray-400 shadow-sm transition-all duration-200 backdrop-blur-sm"
                        placeholder="https://github.com/username">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fab fa-github text-gray-300"></i>
                    </div>
                </div>
                @error('social_links.github')
                    <p class="mt-2 text-sm text-red-400 flex items-center"><i
                            class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                @enderror
            </div>

            <!-- Instagram -->
            <div>
                <label for="social_instagram"
                    class="block text-sm font-semibold text-gray-300 mb-3 flex items-center">
                    <i class="fab fa-instagram text-pink-500 mr-2"></i> Instagram
                </label>
                <div class="relative">
                    <input type="url" id="social_instagram" wire:model="social_links.instagram"
                        class="w-full px-4 py-4 pl-12 bg-gray-700/50 border border-gray-600 rounded-xl focus:ring-2 focus:ring-pink-500 focus:border-pink-500 text-white placeholder-gray-400 shadow-sm transition-all duration-200 backdrop-blur-sm"
                        placeholder="https://instagram.com/username">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fab fa-instagram text-pink-500"></i>
                    </div>
                </div>
                @error('social_links.instagram')
                    <p class="mt-2 text-sm text-red-400 flex items-center"><i
                            class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                @enderror
            </div>

            <!-- Website -->
            <div>
                <label for="social_website" class="block text-sm font-semibold text-gray-300 mb-3 flex items-center">
                    <i class="fas fa-globe text-pink-400 mr-2"></i> Website
                </label>
                <div class="relative">
                    <input type="url" id="social_website" wire:model="social_links.website"
                        class="w-full px-4 py-4 pl-12 bg-gray-700/50 border border-gray-600 rounded-xl focus:ring-2 focus:ring-pink-500 focus:border-pink-500 text-white placeholder-gray-400 shadow-sm transition-all duration-200 backdrop-blur-sm"
                        placeholder="https://yourwebsite.com">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-globe text-pink-400"></i>
                    </div>
                </div>
                @error('social_links.website')
                    <p class="mt-2 text-sm text-red-400 flex items-center"><i
                            class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    <!-- Profile Photo Tab -->
    <div x-show="activeTab === 'photo'" x-transition.opacity.duration.300ms class="p-8">
        <div class="flex items-center mb-8">
            <div
                class="w-12 h-12 bg-gradient-to-r from-red-500 to-orange-600 rounded-xl flex items-center justify-center mr-4">
                <i class="fas fa-camera text-white text-xl"></i>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-white">Profile Photo</h2>
                <p class="text-gray-400">Upload and manage your profile picture</p>
            </div>
        </div>

        <div class="flex flex-col lg:flex-row items-center lg:items-start gap-12">
            <!-- Current Photo -->
            <div class="flex-shrink-0 text-center">
                <div class="relative mb-6">
                    @if ($temp_profile_picture)
                        <img src="{{ $temp_profile_picture }}"
                            class="w-48 h-48 rounded-2xl object-cover border-4 border-red-500/30 shadow-2xl">
                        <div
                            class="absolute -top-2 -right-2 w-8 h-8 bg-green-500 rounded-full border-4 border-gray-800 flex items-center justify-center">
                            <i class="fas fa-check text-white text-sm"></i>
                        </div>
                    @elseif($user->profile_picture)
                        <img src="{{ asset('storage/' . $user->profile_picture) }}"
                            class="w-48 h-48 rounded-2xl object-cover border-4 border-gray-600 shadow-2xl">
                    @else
                        <div
                            class="w-48 h-48 rounded-2xl bg-gradient-to-br from-red-500 to-orange-600 flex items-center justify-center text-white text-6xl font-bold shadow-2xl">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    @endif
                </div>

                @if ($user->profile_picture)
                    <button wire:click="deleteProfilePicture"
                        class="px-6 py-3 bg-red-600/20 hover:bg-red-600/30 border border-red-600 text-red-400 rounded-xl font-medium transition-all duration-300 backdrop-blur-sm">
                        <i class="fas fa-trash-alt mr-2"></i> Remove Photo
                    </button>
                @endif
            </div>

            <!-- Upload Section -->
            <div class="flex-1 w-full">
                <div
                    class="bg-gradient-to-br from-gray-700/30 to-gray-800/30 p-8 rounded-2xl border border-gray-600/50 backdrop-blur-sm">
                    <h3 class="text-lg font-semibold text-white mb-6 flex items-center">
                        <i class="fas fa-cloud-upload-alt text-red-400 mr-2"></i> Upload New Photo
                    </h3>

                    <div class="mb-6">
                        <label for="profile_picture" class="block text-sm font-semibold text-gray-300 mb-4">Choose
                            Photo</label>
                        <div class="relative">
                            <input type="file" id="profile_picture" wire:model="profile_picture"
                                class="w-full text-sm text-gray-400 file:mr-4 file:py-3 file:px-6 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-red-500/20 file:text-red-400 hover:file:bg-red-500/30 file:transition-all file:duration-200 backdrop-blur-sm">
                        </div>
                        @error('profile_picture')
                            <p class="mt-2 text-sm text-red-400 flex items-center"><i
                                    class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Upload Guidelines -->
                    <div class="bg-gray-800/50 p-4 rounded-xl border border-gray-700/50">
                        <h4 class="text-sm font-semibold text-gray-300 mb-2 flex items-center">
                            <i class="fas fa-info-circle text-blue-400 mr-2"></i> Photo Guidelines
                        </h4>
                        <ul class="text-xs text-gray-400 space-y-1">
                            <li>• Accepted formats: JPG, PNG, GIF</li>
                            <li>• Maximum file size: 2MB</li>
                            <li>• Recommended dimensions: 400x400px</li>
                            <li>• Use a clear, professional photo</li>
                        </ul>
                    </div>

                    <!-- Loading State -->
                    <div wire:loading wire:target="profile_picture" class="mt-4 flex items-center text-blue-400">
                        <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-blue-400 mr-2"></div>
                        <span class="text-sm">Processing image...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
