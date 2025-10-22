<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-6 rounded-2xl shadow-xl text-white mb-8">
        <h1 class="text-3xl font-bold flex items-center">
            <i class="fas fa-user-cog mr-3"></i>
            Profile Settings
        </h1>
        <p class="text-indigo-100 mt-2">Manage your personal profile information and preferences</p>
    </div>

    <!-- Tabs -->
    <div class="bg-themed-secondary rounded-xl shadow-sm border border-themed-primary mb-8 overflow-hidden">
        <div class="border-b border-themed-primary">
            <nav class="flex space-x-1 px-4 sm:px-6 overflow-x-auto" aria-label="Tabs">
                <button 
                    wire:click="setActiveTab('basic')"
                    class="whitespace-nowrap py-4 px-3 sm:px-4 border-b-2 font-medium text-sm flex items-center transition-all duration-300
                           {{ $activeTab === 'basic' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400 bg-indigo-100/10 dark:bg-indigo-900/20' : 'border-transparent text-themed-secondary hover:text-themed-primary hover:border-themed-primary' }}">
                    <i class="fas fa-user mr-2"></i>
                    Basic Info
                </button>
                <button 
                    wire:click="setActiveTab('address')"
                    class="whitespace-nowrap py-4 px-3 sm:px-4 border-b-2 font-medium text-sm flex items-center transition-all duration-300
                           {{ $activeTab === 'address' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400 bg-indigo-100/10 dark:bg-indigo-900/20' : 'border-transparent text-themed-secondary hover:text-themed-primary hover:border-themed-primary' }}">
                    <i class="fas fa-map-marker-alt mr-2"></i>
                    Address
                </button>
                <button 
                    wire:click="setActiveTab('professional')"
                    class="whitespace-nowrap py-4 px-3 sm:px-4 border-b-2 font-medium text-sm flex items-center transition-all duration-300
                           {{ $activeTab === 'professional' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400 bg-indigo-100/10 dark:bg-indigo-900/20' : 'border-transparent text-themed-secondary hover:text-themed-primary hover:border-themed-primary' }}">
                    <i class="fas fa-briefcase mr-2"></i>
                    Professional
                </button>
                <button 
                    wire:click="setActiveTab('settings')"
                    class="whitespace-nowrap py-4 px-3 sm:px-4 border-b-2 font-medium text-sm flex items-center transition-all duration-300
                           {{ $activeTab === 'settings' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400 bg-indigo-100/10 dark:bg-indigo-900/20' : 'border-transparent text-themed-secondary hover:text-themed-primary hover:border-themed-primary' }}">
                    <i class="fas fa-cogs mr-2"></i>
                    Preferences
                </button>
            </nav>
        </div>
    </div>

    <!-- Tab Content -->
    <div class="space-y-8">
        <!-- Basic Information Tab -->
        @if($activeTab === 'basic')
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 animate__animated animate__fadeIn">
                <!-- Profile Picture -->
                <div class="lg:col-span-1">
                    <div class="bg-themed-secondary p-6 rounded-xl shadow-sm border border-themed-primary sticky top-8">
                        <h3 class="text-lg font-semibold text-themed-primary mb-4 flex items-center">
                            <i class="fas fa-camera text-indigo-600 dark:text-indigo-400 mr-2"></i>
                            Profile Picture
                        </h3>
                        
                        <div class="text-center">
                            @if($profile_picture)
                                <img src="{{ asset('storage/' . $profile_picture) }}" 
                                     alt="Profile Picture" 
                                     class="w-32 h-32 rounded-full mx-auto object-cover border-4 border-indigo-200 dark:border-indigo-900 mb-4 shadow-lg">
                            @else
                                <div class="w-32 h-32 rounded-full mx-auto bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-white text-4xl font-bold mb-4 shadow-lg">
                                    {{ substr(auth()->user()->name, 0, 1) }}
                                </div>
                            @endif
                            
                            <div class="space-y-3">
                                <input type="file" wire:model="new_profile_picture" id="profile_picture" class="hidden" accept="image/*">
                                <label for="profile_picture" 
                                       class="inline-flex items-center px-4 py-2 border border-indigo-300 dark:border-indigo-700 rounded-lg shadow-sm text-sm font-medium text-indigo-700 dark:text-indigo-400 bg-indigo-100/30 dark:bg-indigo-900/20 hover:bg-indigo-100/50 dark:hover:bg-indigo-900/30 cursor-pointer transition-all duration-200">
                                    <i class="fas fa-upload mr-2"></i>
                                    Change Picture
                                </label>
                                
                                @if($new_profile_picture)
                                    <button wire:click="updateProfilePicture" 
                                            wire:loading.attr="disabled"
                                            class="block w-full bg-green-600 hover:bg-green-700 dark:bg-green-700 dark:hover:bg-green-800 text-white px-4 py-2 rounded-lg transition-colors duration-200 font-medium">
                                        <span wire:loading.remove><i class="fas fa-save mr-2"></i>Save Picture</span>
                                        <span wire:loading><i class="fas fa-circle-notch fa-spin mr-2"></i>Saving...</span>
                                    </button>
                                @endif
                                
                                @if($profile_picture)
                                    <button wire:click="removeProfilePicture" 
                                            wire:confirm="Are you sure you want to remove your profile picture?"
                                            class="block w-full text-red-600 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 text-sm font-medium transition-colors duration-200">
                                        <i class="fas fa-trash mr-1"></i>Remove Picture
                                    </button>
                                @endif
                            </div>
                            
                            @error('new_profile_picture') 
                                <p class="mt-2 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> 
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Basic Info Form -->
                <div class="lg:col-span-2">
                    <form wire:submit.prevent="saveBasicInfo" class="bg-themed-secondary p-6 rounded-xl shadow-sm border border-themed-primary">
                        <h3 class="text-lg font-semibold text-themed-primary mb-6 flex items-center">
                            <i class="fas fa-id-card text-indigo-600 dark:text-indigo-400 mr-2"></i>
                            Basic Information
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Full Name -->
                            <div class="md:col-span-2">
                                <label for="name" class="block text-sm font-semibold text-themed-primary mb-2">
                                    Full Name <span class="text-red-600 dark:text-red-400">*</span>
                                </label>
                                <input wire:model="name" type="text" id="name"
                                       class="block w-full px-4 py-3 border border-themed-primary bg-themed-tertiary rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-themed-primary transition-all duration-200"
                                       placeholder="Your full name">
                                @error('name') <p class="mt-1 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-semibold text-themed-primary mb-2">
                                    Email Address <span class="text-red-600 dark:text-red-400">*</span>
                                </label>
                                <input wire:model="email" type="email" id="email"
                                       class="block w-full px-4 py-3 border border-themed-primary bg-themed-tertiary rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-themed-primary transition-all duration-200"
                                       placeholder="your@email.com">
                                @error('email') <p class="mt-1 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
                            </div>

                            <!-- Phone -->
                            <div>
                                <label for="phone_number" class="block text-sm font-semibold text-themed-primary mb-2">
                                    Phone Number
                                </label>
                                <input wire:model="phone_number" type="text" id="phone_number"
                                       class="block w-full px-4 py-3 border border-themed-primary bg-themed-tertiary rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-themed-primary transition-all duration-200"
                                       placeholder="+1 (555) 000-0000">
                                @error('phone_number') <p class="mt-1 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
                            </div>

                            <!-- Date of Birth -->
                            <div>
                                <label for="date_of_birth" class="block text-sm font-semibold text-themed-primary mb-2">
                                    Date of Birth
                                </label>
                                <input wire:model="date_of_birth" type="date" id="date_of_birth"
                                       class="block w-full px-4 py-3 border border-themed-primary bg-themed-tertiary rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-themed-primary transition-all duration-200">
                                @error('date_of_birth') <p class="mt-1 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
                            </div>

                            <!-- Occupation -->
                            <div>
                                <label for="occupation" class="block text-sm font-semibold text-themed-primary mb-2">
                                    Occupation
                                </label>
                                <input wire:model="occupation" type="text" id="occupation"
                                       class="block w-full px-4 py-3 border border-themed-primary bg-themed-tertiary rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-themed-primary transition-all duration-200"
                                       placeholder="Your job title">
                                @error('occupation') <p class="mt-1 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
                            </div>

                            <!-- Education Level -->
                            <div class="md:col-span-2">
                                <label for="education_level" class="block text-sm font-semibold text-themed-primary mb-2">
                                    Education Level
                                </label>
                                <select wire:model="education_level" id="education_level"
                                        class="block w-full px-4 py-3 border border-themed-primary bg-themed-secondary rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-themed-primary transition-all duration-200">
                                    <option value="">-- Select Education Level --</option>
                                    @foreach($this->getEducationLevels() as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('education_level') <p class="mt-1 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
                            </div>

                            <!-- Bio -->
                            <div class="md:col-span-2">
                                <label for="bio" class="block text-sm font-semibold text-themed-primary mb-2">
                                    Bio
                                </label>
                                <textarea wire:model="bio" id="bio" rows="4"
                                          class="block w-full px-4 py-3 border border-themed-primary bg-themed-tertiary rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-themed-primary transition-all duration-200"
                                          placeholder="Tell us about yourself..."></textarea>
                                @error('bio') <p class="mt-1 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end">
                            <button type="submit" wire:loading.attr="disabled"
                                    class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-700 dark:hover:bg-indigo-800 text-white rounded-lg transition-colors duration-200 font-medium">
                                <span wire:loading.remove><i class="fas fa-save mr-2"></i>Save Changes</span>
                                <span wire:loading><i class="fas fa-circle-notch fa-spin mr-2"></i>Saving...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        <!-- Address Tab -->
        @if($activeTab === 'address')
            <div class="animate__animated animate__fadeIn">
                <form wire:submit.prevent="saveAddress" class="bg-themed-secondary p-6 rounded-xl shadow-sm border border-themed-primary">
                    <h3 class="text-lg font-semibold text-themed-primary mb-6 flex items-center">
                        <i class="fas fa-map-marker-alt text-indigo-600 dark:text-indigo-400 mr-2"></i>
                        Address Information
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Street Address -->
                        <div class="md:col-span-2">
                            <label for="address_street" class="block text-sm font-semibold text-themed-primary mb-2">
                                Street Address
                            </label>
                            <input wire:model="address_street" type="text" id="address_street"
                                   class="block w-full px-4 py-3 border border-themed-primary bg-themed-tertiary rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-themed-primary transition-all duration-200"
                                   placeholder="123 Main Street">
                            @error('address_street') <p class="mt-1 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
                        </div>

                        <!-- City -->
                        <div>
                            <label for="address_city" class="block text-sm font-semibold text-themed-primary mb-2">
                                City
                            </label>
                            <input wire:model="address_city" type="text" id="address_city"
                                   class="block w-full px-4 py-3 border border-themed-primary bg-themed-tertiary rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-themed-primary transition-all duration-200"
                                   placeholder="New York">
                            @error('address_city') <p class="mt-1 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
                        </div>

                        <!-- State/Province -->
                        <div>
                            <label for="address_state" class="block text-sm font-semibold text-themed-primary mb-2">
                                State/Province
                            </label>
                            <input wire:model="address_state" type="text" id="address_state"
                                   class="block w-full px-4 py-3 border border-themed-primary bg-themed-tertiary rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-themed-primary transition-all duration-200"
                                   placeholder="NY">
                            @error('address_state') <p class="mt-1 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
                        </div>

                        <!-- Country -->
                        <div>
                            <label for="address_country" class="block text-sm font-semibold text-themed-primary mb-2">
                                Country
                            </label>
                            <select wire:model="address_country" id="address_country"
                                    class="block w-full px-4 py-3 border border-themed-primary bg-themed-secondary rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-themed-primary transition-all duration-200">
                                <option value="">-- Select Country --</option>
                                @foreach($this->getCountries() as $code => $name)
                                    <option value="{{ $code }}">{{ $name }}</option>
                                @endforeach
                            </select>
                            @error('address_country') <p class="mt-1 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
                        </div>

                        <!-- Postal Code -->
                        <div>
                            <label for="address_postal_code" class="block text-sm font-semibold text-themed-primary mb-2">
                                Postal Code
                            </label>
                            <input wire:model="address_postal_code" type="text" id="address_postal_code"
                                   class="block w-full px-4 py-3 border border-themed-primary bg-themed-tertiary rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-themed-primary transition-all duration-200"
                                   placeholder="10001">
                            @error('address_postal_code') <p class="mt-1 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="submit" wire:loading.attr="disabled"
                                class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-700 dark:hover:bg-indigo-800 text-white rounded-lg transition-colors duration-200 font-medium">
                            <span wire:loading.remove><i class="fas fa-save mr-2"></i>Save Address</span>
                            <span wire:loading><i class="fas fa-circle-notch fa-spin mr-2"></i>Saving...</span>
                        </button>
                    </div>
                </form>
            </div>
        @endif

        <!-- Professional Tab -->
        @if($activeTab === 'professional')
            <div class="animate__animated animate__fadeIn">
                <form wire:submit.prevent="saveProfessionalInfo" class="bg-themed-secondary p-6 rounded-xl shadow-sm border border-themed-primary">
                    <h3 class="text-lg font-semibold text-themed-primary mb-6 flex items-center">
                        <i class="fas fa-briefcase text-indigo-600 dark:text-indigo-400 mr-2"></i>
                        Professional Information
                    </h3>
                    
                    <div class="space-y-6">
                        <!-- Skills -->
                        <div>
                            <label class="block text-sm font-semibold text-themed-primary mb-3">
                                <i class="fas fa-star mr-2 text-indigo-600 dark:text-indigo-400"></i>Skills
                            </label>
                            <div class="space-y-2">
                                @foreach($skills as $index => $skill)
                                    <div class="flex items-center space-x-2">
                                        <input wire:model="skills.{{ $index }}" type="text"
                                               class="flex-1 px-4 py-2 border border-themed-primary bg-themed-tertiary rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-themed-primary"
                                               placeholder="Enter a skill (e.g., JavaScript, Leadership)">
                                        <button type="button" wire:click="removeSkill({{ $index }})"
                                                class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 p-2 rounded-lg hover:bg-red-100/30 dark:hover:bg-red-900/20 transition-colors">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                @endforeach
                                <button type="button" wire:click="addSkill"
                                        class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 text-sm flex items-center font-medium transition-colors">
                                    <i class="fas fa-plus mr-1"></i>
                                    Add Skill
                                </button>
                            </div>
                            @error('skills.*') <p class="mt-1 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
                        </div>

                        <!-- Social Links -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 border-t border-themed-primary pt-6">
                            <!-- Website -->
                            <div>
                                <label for="website" class="block text-sm font-semibold text-themed-primary mb-2">
                                    <i class="fas fa-globe mr-2 text-indigo-600 dark:text-indigo-400"></i>Website
                                </label>
                                <input wire:model="website" type="url" id="website"
                                       class="block w-full px-4 py-3 border border-themed-primary bg-themed-tertiary rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-themed-primary transition-all duration-200"
                                       placeholder="https://yourwebsite.com">
                                @error('website') <p class="mt-1 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
                            </div>

                            <!-- LinkedIn -->
                            <div>
                                <label for="linkedin" class="block text-sm font-semibold text-themed-primary mb-2">
                                    <i class="fab fa-linkedin mr-2 text-blue-600 dark:text-blue-400"></i>LinkedIn
                                </label>
                                <input wire:model="linkedin" type="url" id="linkedin"
                                       class="block w-full px-4 py-3 border border-themed-primary bg-themed-tertiary rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-themed-primary transition-all duration-200"
                                       placeholder="https://linkedin.com/in/yourprofile">
                                @error('linkedin') <p class="mt-1 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
                            </div>

                            <!-- GitHub -->
                            <div>
                                <label for="github" class="block text-sm font-semibold text-themed-primary mb-2">
                                    <i class="fab fa-github mr-2 text-gray-700 dark:text-gray-300"></i>GitHub
                                </label>
                                <input wire:model="github" type="url" id="github"
                                       class="block w-full px-4 py-3 border border-themed-primary bg-themed-tertiary rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-themed-primary transition-all duration-200"
                                       placeholder="https://github.com/yourusername">
                                @error('github') <p class="mt-1 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
                            </div>

                            <!-- Twitter -->
                            <div>
                                <label for="twitter" class="block text-sm font-semibold text-themed-primary mb-2">
                                    <i class="fab fa-twitter mr-2 text-blue-400"></i>Twitter
                                </label>
                                <input wire:model="twitter" type="url" id="twitter"
                                       class="block w-full px-4 py-3 border border-themed-primary bg-themed-tertiary rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-themed-primary transition-all duration-200"
                                       placeholder="https://twitter.com/yourusername">
                                @error('twitter') <p class="mt-1 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="submit" wire:loading.attr="disabled"
                                class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-700 dark:hover:bg-indigo-800 text-white rounded-lg transition-colors duration-200 font-medium">
                            <span wire:loading.remove><i class="fas fa-save mr-2"></i>Save Professional Info</span>
                            <span wire:loading><i class="fas fa-circle-notch fa-spin mr-2"></i>Saving...</span>
                        </button>
                    </div>
                </form>
            </div>
        @endif

        <!-- Settings/Preferences Tab -->
        @if($activeTab === 'settings')
            <div class="animate__animated animate__fadeIn">
                <form wire:submit.prevent="saveProfileSettings" class="bg-themed-secondary p-6 rounded-xl shadow-sm border border-themed-primary">
                    <h3 class="text-lg font-semibold text-themed-primary mb-6 flex items-center">
                        <i class="fas fa-sliders-h text-indigo-600 dark:text-indigo-400 mr-2"></i>
                        Profile Preferences
                    </h3>
                    
                    <div class="space-y-6">
                        <!-- Timezone -->
                        <div>
                            <label for="timezone" class="block text-sm font-semibold text-themed-primary mb-2">
                                <i class="fas fa-globe mr-2 text-indigo-600 dark:text-indigo-400"></i>Timezone <span class="text-red-600 dark:text-red-400">*</span>
                            </label>
                            <select wire:model="timezone" id="timezone"
                                    class="block w-full px-4 py-3 border border-themed-primary bg-themed-secondary rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-themed-primary transition-all duration-200">
                                @foreach($this->getTimezones() as $tz => $name)
                                    <option value="{{ $tz }}">{{ $name }}</option>
                                @endforeach
                            </select>
                            @error('timezone') <p class="mt-1 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
                        </div>

                        <!-- Language -->
                        <div>
                            <label for="language" class="block text-sm font-semibold text-themed-primary mb-2">
                                <i class="fas fa-language mr-2 text-indigo-600 dark:text-indigo-400"></i>Language <span class="text-red-600 dark:text-red-400">*</span>
                            </label>
                            <select wire:model="language" id="language"
                                    class="block w-full px-4 py-3 border border-themed-primary bg-themed-secondary rounded-lg shadow-sm focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-themed-primary transition-all duration-200">
                                @foreach($this->getLanguages() as $code => $name)
                                    <option value="{{ $code }}">{{ $name }}</option>
                                @endforeach
                            </select>
                            @error('language') <p class="mt-1 text-sm text-red-600"><i class="fas fa-exclamation-circle mr-1"></i>{{ $message }}</p> @enderror
                        </div>

                        <!-- Privacy Settings -->
                        <div class="border-t border-themed-primary pt-6">
                            <h4 class="text-md font-semibold text-themed-primary mb-4 flex items-center">
                                <i class="fas fa-lock mr-2 text-indigo-600 dark:text-indigo-400"></i>Privacy Settings
                            </h4>
                            
                            <div class="space-y-4">
                                <!-- Public Profile -->
                                <label class="flex items-start space-x-3 p-3 rounded-lg hover:bg-themed-tertiary transition-colors cursor-pointer group">
                                    <input wire:model="is_profile_public" type="checkbox" id="is_profile_public"
                                           class="mt-1 h-4 w-4 text-indigo-600 border-themed-primary rounded focus:ring-indigo-500">
                                    <div class="flex-1 group-hover:translate-x-1 transition-transform">
                                        <span class="text-sm font-medium text-themed-primary">Make profile publicly visible</span>
                                        <p class="text-xs text-themed-secondary">Allow other users to view your profile information</p>
                                    </div>
                                </label>

                                <!-- Show Email -->
                                <label class="flex items-start space-x-3 p-3 rounded-lg hover:bg-themed-tertiary transition-colors cursor-pointer group">
                                    <input wire:model="show_email_publicly" type="checkbox" id="show_email_publicly"
                                           class="mt-1 h-4 w-4 text-indigo-600 border-themed-primary rounded focus:ring-indigo-500">
                                    <div class="flex-1 group-hover:translate-x-1 transition-transform">
                                        <span class="text-sm font-medium text-themed-primary">Show email address publicly</span>
                                        <p class="text-xs text-themed-secondary">Display your email address on your public profile</p>
                                    </div>
                                </label>

                                <!-- Show Phone -->
                                <label class="flex items-start space-x-3 p-3 rounded-lg hover:bg-themed-tertiary transition-colors cursor-pointer group">
                                    <input wire:model="show_phone_publicly" type="checkbox" id="show_phone_publicly"
                                           class="mt-1 h-4 w-4 text-indigo-600 border-themed-primary rounded focus:ring-indigo-500">
                                    <div class="flex-1 group-hover:translate-x-1 transition-transform">
                                        <span class="text-sm font-medium text-themed-primary">Show phone number publicly</span>
                                        <p class="text-xs text-themed-secondary">Display your phone number on your public profile</p>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="submit" wire:loading.attr="disabled"
                                class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-700 dark:hover:bg-indigo-800 text-white rounded-lg transition-colors duration-200 font-medium">
                            <span wire:loading.remove><i class="fas fa-save mr-2"></i>Save Preferences</span>
                            <span wire:loading><i class="fas fa-circle-notch fa-spin mr-2"></i>Saving...</span>
                        </button>
                    </div>
                </form>
            </div>
        @endif
    </div>
</div>