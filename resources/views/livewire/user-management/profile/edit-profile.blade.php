<!-- Single root container for all edit tabs -->
<div class="w-full max-w-full overflow-hidden">
    <!-- Basic Info Tab -->
    <div x-show="activeTab === 'basic'" x-transition.opacity.duration.300ms class="p-4 sm:p-6 lg:p-8">
        <div class="flex flex-col sm:flex-row sm:items-center mb-6 sm:mb-8">
            <div class="w-12 h-12 bg-gradient-to-r from-accent-themed-primary to-accent-themed-secondary rounded-xl flex items-center justify-center mb-3 sm:mb-0 sm:mr-4 flex-shrink-0">
                <i class="fas fa-user-circle text-white text-xl"></i>
            </div>
            <div class="min-w-0 flex-1">
                <h2 class="text-xl sm:text-2xl font-bold text-themed-primary transition-colors duration-300">
                    Basic Information</h2>
                <p class="text-sm sm:text-base text-themed-secondary transition-colors duration-300 mt-1">
                    Update your personal details</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 lg:gap-8">
            <!-- Name -->
            <div class="w-full">
                <label for="name" class="block text-sm font-semibold text-themed-primary mb-2 sm:mb-3 transition-colors duration-300">Full Name *</label>
                <div class="relative w-full">
                    <input type="text" id="name" wire:model="name"
                        class="w-full px-3 sm:px-4 py-3 sm:py-4 pl-10 sm:pl-12 bg-themed-secondary border border-themed-primary rounded-xl focus:ring-2 focus:ring-accent-themed-primary focus:border-accent-themed-primary text-themed-primary placeholder-themed-tertiary shadow-sm transition-all duration-200 backdrop-blur-sm text-sm sm:text-base">
                    <div class="absolute inset-y-0 left-0 pl-3 sm:pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-user text-themed-tertiary text-sm"></i>
                    </div>
                </div>
                @error('name')
                    <p class="mt-2 text-sm text-red-500 flex items-center transition-colors duration-300">
                        <i class="fas fa-exclamation-circle mr-1 flex-shrink-0"></i>
                        <span class="break-words">{{ $message }}</span>
                    </p>
                @enderror
            </div>

            <!-- Email -->
            <div class="w-full">
                <label for="email" class="block text-sm font-semibold text-themed-primary mb-2 sm:mb-3 transition-colors duration-300">Email Address *</label>
                <div class="relative w-full">
                    <input type="email" id="email" wire:model="email"
                        class="w-full px-3 sm:px-4 py-3 sm:py-4 pl-10 sm:pl-12 bg-themed-secondary border border-themed-primary rounded-xl focus:ring-2 focus:ring-accent-themed-primary focus:border-accent-themed-primary text-themed-primary placeholder-themed-tertiary shadow-sm transition-all duration-200 backdrop-blur-sm text-sm sm:text-base">
                    <div class="absolute inset-y-0 left-0 pl-3 sm:pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-envelope text-themed-tertiary text-sm"></i>
                    </div>
                </div>
                @error('email')
                    <p class="mt-2 text-sm text-red-500 flex items-center transition-colors duration-300">
                        <i class="fas fa-exclamation-circle mr-1 flex-shrink-0"></i>
                        <span class="break-words">{{ $message }}</span>
                    </p>
                @enderror
            </div>

            <!-- Phone Number -->
            <div class="w-full">
                <label for="phone_number" class="block text-sm font-semibold text-themed-primary mb-2 sm:mb-3 transition-colors duration-300">Phone Number</label>
                <div class="relative w-full">
                    <input type="tel" id="phone_number" wire:model="phone_number"
                        class="w-full px-3 sm:px-4 py-3 sm:py-4 pl-10 sm:pl-12 bg-themed-secondary border border-themed-primary rounded-xl focus:ring-2 focus:ring-accent-themed-primary focus:border-accent-themed-primary text-themed-primary placeholder-themed-tertiary shadow-sm transition-all duration-200 backdrop-blur-sm text-sm sm:text-base">
                    <div class="absolute inset-y-0 left-0 pl-3 sm:pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-phone text-themed-tertiary text-sm"></i>
                    </div>
                </div>
                @error('phone_number')
                    <p class="mt-2 text-sm text-red-500 flex items-center transition-colors duration-300">
                        <i class="fas fa-exclamation-circle mr-1 flex-shrink-0"></i>
                        <span class="break-words">{{ $message }}</span>
                    </p>
                @enderror
            </div>

            <!-- Date of Birth -->
            <div class="w-full">
                <label for="date_of_birth" class="block text-sm font-semibold text-themed-primary mb-2 sm:mb-3 transition-colors duration-300">Date of Birth</label>
                <div class="relative w-full">
                    <input type="date" id="date_of_birth" wire:model="date_of_birth"
                        class="w-full px-3 sm:px-4 py-3 sm:py-4 pl-10 sm:pl-12 bg-themed-secondary border border-themed-primary rounded-xl focus:ring-2 focus:ring-accent-themed-primary focus:border-accent-themed-primary text-themed-primary placeholder-themed-tertiary shadow-sm transition-all duration-200 backdrop-blur-sm text-sm sm:text-base">
                    <div class="absolute inset-y-0 left-0 pl-3 sm:pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-birthday-cake text-themed-tertiary text-sm"></i>
                    </div>
                </div>
                @error('date_of_birth')
                    <p class="mt-2 text-sm text-red-500 flex items-center transition-colors duration-300">
                        <i class="fas fa-exclamation-circle mr-1 flex-shrink-0"></i>
                        <span class="break-words">{{ $message }}</span>
                    </p>
                @enderror
            </div>

            <!-- Bio -->
            <div class="lg:col-span-2 w-full">
                <label for="bio" class="block text-sm font-semibold text-themed-primary mb-2 sm:mb-3 transition-colors duration-300">Bio</label>
                <div class="relative w-full">
                    <textarea id="bio" wire:model="bio" rows="4"
                        class="w-full px-3 sm:px-4 py-3 sm:py-4 bg-themed-secondary border border-themed-primary rounded-xl focus:ring-2 focus:ring-accent-themed-primary focus:border-accent-themed-primary text-themed-primary placeholder-themed-tertiary shadow-sm transition-all duration-200 backdrop-blur-sm resize-none text-sm sm:text-base"
                        placeholder="Tell us a little about yourself..."></textarea>
                </div>
                <p class="text-xs text-themed-secondary mt-2 flex items-center transition-colors duration-300">
                    <i class="fas fa-info-circle mr-1 flex-shrink-0"></i>
                    Maximum 500 characters
                </p>
                @error('bio')
                    <p class="mt-2 text-sm text-red-500 flex items-center transition-colors duration-300">
                        <i class="fas fa-exclamation-circle mr-1 flex-shrink-0"></i>
                        <span class="break-words">{{ $message }}</span>
                    </p>
                @enderror
            </div>
        </div>
    </div>

    <!-- Address Tab -->
    <div x-show="activeTab === 'address'" x-transition.opacity.duration.300ms class="p-4 sm:p-6 lg:p-8">
        <div class="flex flex-col sm:flex-row sm:items-center mb-6 sm:mb-8">
            <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-emerald-600 rounded-xl flex items-center justify-center mb-3 sm:mb-0 sm:mr-4 flex-shrink-0">
                <i class="fas fa-map-marker-alt text-white text-xl"></i>
            </div>
            <div class="min-w-0 flex-1">
                <h2 class="text-xl sm:text-2xl font-bold text-themed-primary transition-colors duration-300">
                    Address Information</h2>
                <p class="text-sm sm:text-base text-themed-secondary transition-colors duration-300 mt-1">
                    Update your location details</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 lg:gap-8">
            <!-- Street Address -->
            <div class="lg:col-span-2 w-full">
                <label for="address_street" class="block text-sm font-semibold text-themed-primary mb-2 sm:mb-3 transition-colors duration-300">Street Address</label>
                <div class="relative w-full">
                    <input type="text" id="address_street" wire:model="address_street"
                        class="w-full px-3 sm:px-4 py-3 sm:py-4 pl-10 sm:pl-12 bg-themed-secondary border border-themed-primary rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 text-themed-primary placeholder-themed-tertiary shadow-sm transition-all duration-200 backdrop-blur-sm text-sm sm:text-base"
                        placeholder="123 Main Street">
                    <div class="absolute inset-y-0 left-0 pl-3 sm:pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-road text-themed-tertiary text-sm"></i>
                    </div>
                </div>
                @error('address_street')
                    <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- City -->
            <div class="w-full">
                <label for="address_city" class="block text-sm font-semibold text-themed-primary mb-2 sm:mb-3 transition-colors duration-300">City</label>
                <div class="relative w-full">
                    <input type="text" id="address_city" wire:model="address_city"
                        class="w-full px-3 sm:px-4 py-3 sm:py-4 pl-10 sm:pl-12 bg-themed-secondary border border-themed-primary rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 text-themed-primary placeholder-themed-tertiary shadow-sm transition-all duration-200 backdrop-blur-sm text-sm sm:text-base"
                        placeholder="New York">
                    <div class="absolute inset-y-0 left-0 pl-3 sm:pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-city text-themed-tertiary text-sm"></i>
                    </div>
                </div>
                @error('address_city')
                    <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- State/Province -->
            <div class="w-full">
                <label for="address_state" class="block text-sm font-semibold text-themed-primary mb-2 sm:mb-3 transition-colors duration-300">State/Province</label>
                <div class="relative w-full">
                    <input type="text" id="address_state" wire:model="address_state"
                        class="w-full px-3 sm:px-4 py-3 sm:py-4 pl-10 sm:pl-12 bg-themed-secondary border border-themed-primary rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 text-themed-primary placeholder-themed-tertiary shadow-sm transition-all duration-200 backdrop-blur-sm text-sm sm:text-base"
                        placeholder="New York">
                    <div class="absolute inset-y-0 left-0 pl-3 sm:pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-map text-themed-tertiary text-sm"></i>
                    </div>
                </div>
                @error('address_state')
                    <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Country -->
            <div class="w-full">
                <label for="address_country" class="block text-sm font-semibold text-themed-primary mb-2 sm:mb-3 transition-colors duration-300">Country</label>
                <div class="relative w-full">
                    <input type="text" id="address_country" wire:model="address_country"
                        class="w-full px-3 sm:px-4 py-3 sm:py-4 pl-10 sm:pl-12 bg-themed-secondary border border-themed-primary rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 text-themed-primary placeholder-themed-tertiary shadow-sm transition-all duration-200 backdrop-blur-sm text-sm sm:text-base"
                        placeholder="United States">
                    <div class="absolute inset-y-0 left-0 pl-3 sm:pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-globe text-themed-tertiary text-sm"></i>
                    </div>
                </div>
                @error('address_country')
                    <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Postal Code -->
            <div class="w-full">
                <label for="address_postal_code" class="block text-sm font-semibold text-themed-primary mb-2 sm:mb-3 transition-colors duration-300">Postal Code</label>
                <div class="relative w-full">
                    <input type="text" id="address_postal_code" wire:model="address_postal_code"
                        class="w-full px-3 sm:px-4 py-3 sm:py-4 pl-10 sm:pl-12 bg-themed-secondary border border-themed-primary rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 text-themed-primary placeholder-themed-tertiary shadow-sm transition-all duration-200 backdrop-blur-sm text-sm sm:text-base"
                        placeholder="10001">
                    <div class="absolute inset-y-0 left-0 pl-3 sm:pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-mail-bulk text-themed-tertiary text-sm"></i>
                    </div>
                </div>
                @error('address_postal_code')
                    <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    <!-- Education Tab -->
    <div x-show="activeTab === 'education'" x-transition.opacity.duration.300ms class="p-4 sm:p-6 lg:p-8">
        <div class="flex flex-col sm:flex-row sm:items-center mb-6 sm:mb-8">
            <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-pink-600 rounded-xl flex items-center justify-center mb-3 sm:mb-0 sm:mr-4 flex-shrink-0">
                <i class="fas fa-graduation-cap text-white text-xl"></i>
            </div>
            <div class="min-w-0 flex-1">
                <h2 class="text-xl sm:text-2xl font-bold text-themed-primary transition-colors duration-300">
                    Education & Career</h2>
                <p class="text-sm sm:text-base text-themed-secondary transition-colors duration-300 mt-1">
                    Update your professional background</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 lg:gap-8">
            <!-- Occupation -->
            <div class="w-full">
                <label for="occupation" class="block text-sm font-semibold text-themed-primary mb-2 sm:mb-3 transition-colors duration-300">Occupation</label>
                <div class="relative w-full">
                    <input type="text" id="occupation" wire:model="occupation"
                        class="w-full px-3 sm:px-4 py-3 sm:py-4 pl-10 sm:pl-12 bg-themed-secondary border border-themed-primary rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-themed-primary placeholder-themed-tertiary shadow-sm transition-all duration-200 backdrop-blur-sm text-sm sm:text-base"
                        placeholder="Software Developer">
                    <div class="absolute inset-y-0 left-0 pl-3 sm:pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-briefcase text-themed-tertiary text-sm"></i>
                    </div>
                </div>
                @error('occupation')
                    <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Education Level -->
            <div class="w-full">
                <label for="education_level" class="block text-sm font-semibold text-themed-primary mb-2 sm:mb-3 transition-colors duration-300">Education Level</label>
                <div class="relative w-full">
                    <select id="education_level" wire:model="education_level"
                        class="w-full px-3 sm:px-4 py-3 sm:py-4 pl-10 sm:pl-12 pr-8 sm:pr-10 bg-themed-secondary border border-themed-primary rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-themed-primary shadow-sm transition-all duration-200 backdrop-blur-sm appearance-none text-sm sm:text-base">
                        <option value="">Select education level</option>
                        <option value="High School">High School</option>
                        <option value="Diploma">Diploma</option>
                        <option value="Bachelor's Degree">Bachelor's Degree</option>
                        <option value="Master's Degree">Master's Degree</option>
                        <option value="PhD">PhD</option>
                        <option value="Other">Other</option>
                    </select>
                    <div class="absolute inset-y-0 left-0 pl-3 sm:pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-graduation-cap text-themed-tertiary text-sm"></i>
                    </div>
                    <div class="absolute inset-y-0 right-0 pr-3 sm:pr-4 flex items-center pointer-events-none">
                        <i class="fas fa-chevron-down text-themed-tertiary text-sm"></i>
                    </div>
                </div>
                @error('education_level')
                    <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Skills -->
            <div class="lg:col-span-2 w-full">
                <label for="skills" class="block text-sm font-semibold text-themed-primary mb-2 sm:mb-3 transition-colors duration-300">Skills & Interests</label>
                <div class="relative w-full">
                    <textarea id="skills" wire:model="skills" rows="4"
                        class="w-full px-3 sm:px-4 py-3 sm:py-4 bg-themed-secondary border border-themed-primary rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-themed-primary placeholder-themed-tertiary shadow-sm transition-all duration-200 backdrop-blur-sm resize-none text-sm sm:text-base"
                        placeholder="Laravel, Vue.js, Photography, Digital Marketing..."></textarea>
                </div>
                <p class="text-xs text-themed-secondary mt-2 flex items-center transition-colors duration-300">
                    <i class="fas fa-info-circle mr-1 flex-shrink-0"></i>
                    Separate multiple skills with commas
                </p>
                @error('skills')
                    <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    <!-- Social Links Tab -->
    <div x-show="activeTab === 'social'" x-transition.opacity.duration.300ms class="p-4 sm:p-6 lg:p-8">
        <div class="flex flex-col sm:flex-row sm:items-center mb-6 sm:mb-8">
            <div class="w-12 h-12 bg-gradient-to-r from-pink-500 to-red-600 rounded-xl flex items-center justify-center mb-3 sm:mb-0 sm:mr-4 flex-shrink-0">
                <i class="fas fa-share-alt text-white text-xl"></i>
            </div>
            <div class="min-w-0 flex-1">
                <h2 class="text-xl sm:text-2xl font-bold text-themed-primary transition-colors duration-300">
                    Social Links</h2>
                <p class="text-sm sm:text-base text-themed-secondary transition-colors duration-300 mt-1">
                    Connect your social media profiles</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 lg:gap-8">
            <!-- Twitter -->
            <div class="w-full">
                <label for="social_twitter" class="block text-sm font-semibold text-themed-primary mb-2 sm:mb-3 flex items-center transition-colors duration-300">
                    <i class="fab fa-twitter text-blue-400 mr-2 flex-shrink-0"></i> Twitter / X
                </label>
                <div class="relative w-full">
                    <input type="url" id="social_twitter" wire:model="social_links.twitter"
                        class="w-full px-3 sm:px-4 py-3 sm:py-4 pl-10 sm:pl-12 bg-themed-secondary border border-themed-primary rounded-xl focus:ring-2 focus:ring-pink-500 focus:border-pink-500 text-themed-primary placeholder-themed-tertiary shadow-sm transition-all duration-200 backdrop-blur-sm text-sm sm:text-base"
                        placeholder="https://twitter.com/username">
                    <div class="absolute inset-y-0 left-0 pl-3 sm:pl-4 flex items-center pointer-events-none">
                        <i class="fab fa-twitter text-blue-400 text-sm"></i>
                    </div>
                </div>
                @error('social_links.twitter')
                    <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Facebook -->
            <div class="w-full">
                <label for="social_facebook" class="block text-sm font-semibold text-themed-primary mb-2 sm:mb-3 flex items-center transition-colors duration-300">
                    <i class="fab fa-facebook-f text-blue-600 mr-2 flex-shrink-0"></i> Facebook
                </label>
                <div class="relative w-full">
                    <input type="url" id="social_facebook" wire:model="social_links.facebook"
                        class="w-full px-3 sm:px-4 py-3 sm:py-4 pl-10 sm:pl-12 bg-themed-secondary border border-themed-primary rounded-xl focus:ring-2 focus:ring-pink-500 focus:border-pink-500 text-themed-primary placeholder-themed-tertiary shadow-sm transition-all duration-200 backdrop-blur-sm text-sm sm:text-base"
                        placeholder="https://facebook.com/username">
                    <div class="absolute inset-y-0 left-0 pl-3 sm:pl-4 flex items-center pointer-events-none">
                        <i class="fab fa-facebook-f text-blue-600 text-sm"></i>
                    </div>
                </div>
                @error('social_links.facebook')
                    <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- LinkedIn -->
            <div class="w-full">
                <label for="social_linkedin" class="block text-sm font-semibold text-themed-primary mb-2 sm:mb-3 flex items-center transition-colors duration-300">
                    <i class="fab fa-linkedin-in text-blue-500 mr-2 flex-shrink-0"></i> LinkedIn
                </label>
                <div class="relative w-full">
                    <input type="url" id="social_linkedin" wire:model="social_links.linkedin"
                        class="w-full px-3 sm:px-4 py-3 sm:py-4 pl-10 sm:pl-12 bg-themed-secondary border border-themed-primary rounded-xl focus:ring-2 focus:ring-pink-500 focus:border-pink-500 text-themed-primary placeholder-themed-tertiary shadow-sm transition-all duration-200 backdrop-blur-sm text-sm sm:text-base"
                        placeholder="https://linkedin.com/in/username">
                    <div class="absolute inset-y-0 left-0 pl-3 sm:pl-4 flex items-center pointer-events-none">
                        <i class="fab fa-linkedin-in text-blue-500 text-sm"></i>
                    </div>
                </div>
                @error('social_links.linkedin')
                    <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- GitHub -->
            <div class="w-full">
                <label for="social_github" class="block text-sm font-semibold text-themed-primary mb-2 sm:mb-3 flex items-center transition-colors duration-300">
                    <i class="fab fa-github text-themed-primary mr-2 flex-shrink-0"></i> GitHub
                </label>
                <div class="relative w-full">
                    <input type="url" id="social_github" wire:model="social_links.github"
                        class="w-full px-3 sm:px-4 py-3 sm:py-4 pl-10 sm:pl-12 bg-themed-secondary border border-themed-primary rounded-xl focus:ring-2 focus:ring-pink-500 focus:border-pink-500 text-themed-primary placeholder-themed-tertiary shadow-sm transition-all duration-200 backdrop-blur-sm text-sm sm:text-base"
                        placeholder="https://github.com/username">
                    <div class="absolute inset-y-0 left-0 pl-3 sm:pl-4 flex items-center pointer-events-none">
                        <i class="fab fa-github text-themed-primary text-sm"></i>
                    </div>
                </div>
                @error('social_links.github')
                    <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Instagram -->
            <div class="w-full">
                <label for="social_instagram" class="block text-sm font-semibold text-themed-primary mb-2 sm:mb-3 flex items-center transition-colors duration-300">
                    <i class="fab fa-instagram text-pink-500 mr-2 flex-shrink-0"></i> Instagram
                </label>
                <div class="relative w-full">
                    <input type="url" id="social_instagram" wire:model="social_links.instagram"
                        class="w-full px-3 sm:px-4 py-3 sm:py-4 pl-10 sm:pl-12 bg-themed-secondary border border-themed-primary rounded-xl focus:ring-2 focus:ring-pink-500 focus:border-pink-500 text-themed-primary placeholder-themed-tertiary shadow-sm transition-all duration-200 backdrop-blur-sm text-sm sm:text-base"
                        placeholder="https://instagram.com/username">
                    <div class="absolute inset-y-0 left-0 pl-3 sm:pl-4 flex items-center pointer-events-none">
                        <i class="fab fa-instagram text-pink-500 text-sm"></i>
                    </div>
                </div>
                @error('social_links.instagram')
                    <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- Website -->
            <div class="w-full">
                <label for="social_website" class="block text-sm font-semibold text-themed-primary mb-2 sm:mb-3 flex items-center transition-colors duration-300">
                    <i class="fas fa-globe text-pink-400 mr-2 flex-shrink-0"></i> Website
                </label>
                <div class="relative w-full">
                    <input type="url" id="social_website" wire:model="social_links.website"
                        class="w-full px-3 sm:px-4 py-3 sm:py-4 pl-10 sm:pl-12 bg-themed-secondary border border-themed-primary rounded-xl focus:ring-2 focus:ring-pink-500 focus:border-pink-500 text-themed-primary placeholder-themed-tertiary shadow-sm transition-all duration-200 backdrop-blur-sm text-sm sm:text-base"
                        placeholder="https://yourwebsite.com">
                    <div class="absolute inset-y-0 left-0 pl-3 sm:pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-globe text-pink-400 text-sm"></i>
                    </div>
                </div>
                @error('social_links.website')
                    <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    <!-- Profile Photo Tab -->
    <div x-show="activeTab === 'photo'" x-transition.opacity.duration.300ms class="p-4 sm:p-6 lg:p-8">
        <div class="flex flex-col sm:flex-row sm:items-center mb-6 sm:mb-8">
            <div class="w-12 h-12 bg-gradient-to-r from-red-500 to-orange-600 rounded-xl flex items-center justify-center mb-3 sm:mb-0 sm:mr-4 flex-shrink-0">
                <i class="fas fa-camera text-white text-xl"></i>
            </div>
            <div class="min-w-0 flex-1">
                <h2 class="text-xl sm:text-2xl font-bold text-themed-primary transition-colors duration-300">
                    Profile Photo</h2>
                <p class="text-sm sm:text-base text-themed-secondary transition-colors duration-300 mt-1">
                    Upload and manage your profile picture</p>
            </div>
        </div>

        <div class="flex flex-col items-center gap-8 lg:flex-row lg:items-start lg:gap-12">
            <!-- Current Photo -->
            <div class="flex-shrink-0 text-center w-full lg:w-auto">
                <div class="relative mb-6 mx-auto w-fit">
                    @if ($temp_profile_picture)
                        <img src="{{ $temp_profile_picture }}"
                            class="w-36 h-36 sm:w-48 sm:h-48 rounded-2xl object-cover border-4 border-red-500/30 shadow-2xl">
                        <div class="absolute -top-2 -right-2 w-8 h-8 bg-green-500 rounded-full border-4 border-themed-secondary flex items-center justify-center transition-colors duration-300">
                            <i class="fas fa-check text-white text-sm"></i>
                        </div>
                    @elseif($user->profile_picture)
                    
                    <img src="{{ $user->profile_picture }}"
                            class="w-36 h-36 sm:w-48 sm:h-48 rounded-2xl object-cover border-4 border-themed-primary shadow-2xl transition-colors duration-300">
                    @else
                        <div class="w-36 h-36 sm:w-48 sm:h-48 rounded-2xl bg-gradient-to-br from-red-500 to-orange-600 flex items-center justify-center text-white text-4xl sm:text-6xl font-bold shadow-2xl">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    @endif
                </div>

                @if ($user->profile_picture)
                    <button wire:click="deleteProfilePicture"
                        class="px-4 sm:px-6 py-2 sm:py-3 bg-red-50 hover:bg-red-100 border border-red-200 text-red-600 rounded-xl font-medium transition-all duration-300 backdrop-blur-sm text-sm sm:text-base">
                        <i class="fas fa-trash-alt mr-2"></i> Remove Photo
                    </button>
                @endif
            </div>

            <!-- Upload Section -->
            <div class="flex-1 w-full">
                <div class="bg-themed-tertiary p-6 sm:p-8 rounded-2xl border border-themed-primary backdrop-blur-sm transition-colors duration-300">
                    <h3 class="text-lg font-semibold text-themed-primary mb-6 flex items-center transition-colors duration-300">
                        <i class="fas fa-cloud-upload-alt text-red-400 mr-2 flex-shrink-0"></i> Upload New Photo
                    </h3>

                    <div class="mb-6">
                        <label for="profile_picture" class="block text-sm font-semibold text-themed-primary mb-4 transition-colors duration-300">Choose Photo</label>
                        <div class="relative w-full">
                            <input type="file" id="profile_picture" wire:model="profile_picture"
                                class="w-full text-sm text-themed-secondary file:mr-4 file:py-2 sm:file:py-3 file:px-4 sm:file:px-6 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-red-50 file:text-red-600 hover:file:bg-red-100 file:transition-all file:duration-200 backdrop-blur-sm transition-colors duration-300">
                        </div>
                        @error('profile_picture')
                            <p class="mt-2 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Upload Guidelines -->
                    <div class="bg-accent-themed-primary/10 p-4 rounded-xl border border-accent-themed-primary/20 transition-colors duration-300">
                        <h4 class="text-sm font-semibold text-themed-primary mb-2 flex items-center transition-colors duration-300">
                            <i class="fas fa-info-circle text-accent-themed-primary mr-2 flex-shrink-0"></i> Photo Guidelines
                        </h4>
                        <ul class="text-xs sm:text-sm text-themed-secondary space-y-1 transition-colors duration-300">
                            <li>• Accepted formats: JPG, PNG, GIF</li>
                            <li>• Maximum file size: 2MB</li>
                            <li>• Recommended dimensions: 400x400px</li>
                            <li>• Use a clear, professional photo</li>
                        </ul>
                    </div>

                    <!-- Loading State -->
                    <div wire:loading wire:target="profile_picture" class="mt-4 flex items-center text-accent-themed-primary transition-colors duration-300">
                        <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-accent-themed-primary mr-2 flex-shrink-0"></div>
                        <span class="text-sm">Processing image...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>