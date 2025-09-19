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
    <div class="bg-white rounded-lg shadow-sm mb-8">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                <button 
                    wire:click="setActiveTab('basic')"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center transition-colors duration-200
                           {{ $activeTab === 'basic' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    <i class="fas fa-user mr-2"></i>
                    Basic Information
                </button>
                <button 
                    wire:click="setActiveTab('address')"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center transition-colors duration-200
                           {{ $activeTab === 'address' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    <i class="fas fa-map-marker-alt mr-2"></i>
                    Address
                </button>
                <button 
                    wire:click="setActiveTab('professional')"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center transition-colors duration-200
                           {{ $activeTab === 'professional' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    <i class="fas fa-briefcase mr-2"></i>
                    Professional
                </button>
                <button 
                    wire:click="setActiveTab('settings')"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center transition-colors duration-200
                           {{ $activeTab === 'settings' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    <i class="fas fa-cogs mr-2"></i>
                    Preferences
                </button>
            </nav>
        </div>
    </div>

    <!-- Tab Content -->
    <div class="space-y-8">
        <!-- Basic Information Tab -->
        <div class="{{ $activeTab === 'basic' ? 'block' : 'hidden' }}">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Profile Picture -->
                <div class="lg:col-span-1">
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <i class="fas fa-camera text-indigo-600 mr-2"></i>
                            Profile Picture
                        </h3>
                        
                        <div class="text-center">
                            @if($profile_picture)
                                <img src="{{ asset('storage/' . $profile_picture) }}" 
                                     alt="Profile Picture" 
                                     class="w-32 h-32 rounded-full mx-auto object-cover border-4 border-indigo-100 mb-4">
                            @else
                                <div class="w-32 h-32 rounded-full mx-auto bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center text-white text-4xl font-bold mb-4">
                                    {{ substr(auth()->user()->name, 0, 1) }}
                                </div>
                            @endif
                            
                            <div class="space-y-3">
                                <input type="file" wire:model="new_profile_picture" id="profile_picture" class="hidden" accept="image/*">
                                <label for="profile_picture" 
                                       class="inline-flex items-center px-4 py-2 border border-indigo-300 rounded-md shadow-sm text-sm font-medium text-indigo-700 bg-indigo-50 hover:bg-indigo-100 cursor-pointer transition-colors duration-200">
                                    <i class="fas fa-upload mr-2"></i>
                                    Change Picture
                                </label>
                                
                                @if($new_profile_picture)
                                    <button wire:click="updateProfilePicture" 
                                            wire:loading.attr="disabled"
                                            class="block w-full bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors duration-200">
                                        <span wire:loading.remove>Save Picture</span>
                                        <span wire:loading>Saving...</span>
                                    </button>
                                @endif
                                
                                @if($profile_picture)
                                    <button wire:click="removeProfilePicture" 
                                            wire:confirm="Are you sure you want to remove your profile picture?"
                                            class="block w-full text-red-600 hover:text-red-700 text-sm transition-colors duration-200">
                                        Remove Picture
                                    </button>
                                @endif
                            </div>
                            
                            @error('new_profile_picture') 
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p> 
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Basic Info Form -->
                <div class="lg:col-span-2">
                    <form wire:submit.prevent="saveBasicInfo" class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                            <i class="fas fa-id-card text-indigo-600 mr-2"></i>
                            Basic Information
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Full Name -->
                            <div class="md:col-span-2">
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Full Name <span class="text-red-500">*</span>
                                </label>
                                <input wire:model="name" type="text" id="name"
                                       class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                    Email Address <span class="text-red-500">*</span>
                                </label>
                                <input wire:model="email" type="email" id="email"
                                       class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <!-- Phone -->
                            <div>
                                <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-2">
                                    Phone Number
                                </label>
                                <input wire:model="phone_number" type="text" id="phone_number"
                                       class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('phone_number') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <!-- Date of Birth -->
                            <div>
                                <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-2">
                                    Date of Birth
                                </label>
                                <input wire:model="date_of_birth" type="date" id="date_of_birth"
                                       class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('date_of_birth') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <!-- Occupation -->
                            <div>
                                <label for="occupation" class="block text-sm font-medium text-gray-700 mb-2">
                                    Occupation
                                </label>
                                <input wire:model="occupation" type="text" id="occupation"
                                       class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('occupation') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <!-- Education Level -->
                            <div class="md:col-span-2">
                                <label for="education_level" class="block text-sm font-medium text-gray-700 mb-2">
                                    Education Level
                                </label>
                                <select wire:model="education_level" id="education_level"
                                        class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Select Education Level</option>
                                    @foreach($this->getEducationLevels() as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('education_level') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <!-- Bio -->
                            <div class="md:col-span-2">
                                <label for="bio" class="block text-sm font-medium text-gray-700 mb-2">
                                    Bio
                                </label>
                                <textarea wire:model="bio" id="bio" rows="4"
                                          class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                          placeholder="Tell us about yourself..."></textarea>
                                @error('bio') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end">
                            <button type="submit" wire:loading.attr="disabled"
                                    class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors duration-200">
                                <span wire:loading.remove><i class="fas fa-save mr-2"></i>Save Changes</span>
                                <span wire:loading><i class="fas fa-circle-notch fa-spin mr-2"></i>Saving...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Address Tab -->
        <div class="{{ $activeTab === 'address' ? 'block' : 'hidden' }}">
            <form wire:submit.prevent="saveAddress" class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-map-marker-alt text-indigo-600 mr-2"></i>
                    Address Information
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Street Address -->
                    <div class="md:col-span-2">
                        <label for="address_street" class="block text-sm font-medium text-gray-700 mb-2">
                            Street Address
                        </label>
                        <input wire:model="address_street" type="text" id="address_street"
                               class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('address_street') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- City -->
                    <div>
                        <label for="address_city" class="block text-sm font-medium text-gray-700 mb-2">
                            City
                        </label>
                        <input wire:model="address_city" type="text" id="address_city"
                               class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('address_city') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- State/Province -->
                    <div>
                        <label for="address_state" class="block text-sm font-medium text-gray-700 mb-2">
                            State/Province
                        </label>
                        <input wire:model="address_state" type="text" id="address_state"
                               class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('address_state') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- Country -->
                    <div>
                        <label for="address_country" class="block text-sm font-medium text-gray-700 mb-2">
                            Country
                        </label>
                        <select wire:model="address_country" id="address_country"
                                class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Select Country</option>
                            @foreach($this->getCountries() as $code => $name)
                                <option value="{{ $code }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('address_country') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- Postal Code -->
                    <div>
                        <label for="address_postal_code" class="block text-sm font-medium text-gray-700 mb-2">
                            Postal Code
                        </label>
                        <input wire:model="address_postal_code" type="text" id="address_postal_code"
                               class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('address_postal_code') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit" wire:loading.attr="disabled"
                            class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors duration-200">
                        <span wire:loading.remove><i class="fas fa-save mr-2"></i>Save Address</span>
                        <span wire:loading><i class="fas fa-circle-notch fa-spin mr-2"></i>Saving...</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Professional Tab -->
        <div class="{{ $activeTab === 'professional' ? 'block' : 'hidden' }}">
            <form wire:submit.prevent="saveProfessionalInfo" class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-briefcase text-indigo-600 mr-2"></i>
                    Professional Information
                </h3>
                
                <div class="space-y-6">
                    <!-- Skills -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Skills
                        </label>
                        <div class="space-y-2">
                            @foreach($skills as $index => $skill)
                                <div class="flex items-center space-x-2">
                                    <input wire:model="skills.{{ $index }}" type="text"
                                           class="flex-1 border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                           placeholder="Enter a skill">
                                    <button type="button" wire:click="removeSkill({{ $index }})"
                                            class="text-red-600 hover:text-red-700 p-2">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            @endforeach
                            <button type="button" wire:click="addSkill"
                                    class="text-indigo-600 hover:text-indigo-700 text-sm flex items-center">
                                <i class="fas fa-plus mr-1"></i>
                                Add Skill
                            </button>
                        </div>
                        @error('skills.*') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- Social Links -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Website -->
                        <div>
                            <label for="website" class="block text-sm font-medium text-gray-700 mb-2">
                                Website
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-globe text-gray-400"></i>
                                </div>
                                <input wire:model="website" type="url" id="website"
                                       class="block w-full pl-10 border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                       placeholder="https://yourwebsite.com">
                            </div>
                            @error('website') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <!-- LinkedIn -->
                        <div>
                            <label for="linkedin" class="block text-sm font-medium text-gray-700 mb-2">
                                LinkedIn
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fab fa-linkedin text-gray-400"></i>
                                </div>
                                <input wire:model="linkedin" type="url" id="linkedin"
                                       class="block w-full pl-10 border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                       placeholder="https://linkedin.com/in/yourprofile">
                            </div>
                            @error('linkedin') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <!-- GitHub -->
                        <div>
                            <label for="github" class="block text-sm font-medium text-gray-700 mb-2">
                                GitHub
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fab fa-github text-gray-400"></i>
                                </div>
                                <input wire:model="github" type="url" id="github"
                                       class="block w-full pl-10 border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                       placeholder="https://github.com/yourusername">
                            </div>
                            @error('github') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <!-- Twitter -->
                        <div>
                            <label for="twitter" class="block text-sm font-medium text-gray-700 mb-2">
                                Twitter
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fab fa-twitter text-gray-400"></i>
                                </div>
                                <input wire:model="twitter" type="url" id="twitter"
                                       class="block w-full pl-10 border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                       placeholder="https://twitter.com/yourusername">
                            </div>
                            @error('twitter') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit" wire:loading.attr="disabled"
                            class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors duration-200">
                        <span wire:loading.remove><i class="fas fa-save mr-2"></i>Save Professional Info</span>
                        <span wire:loading><i class="fas fa-circle-notch fa-spin mr-2"></i>Saving...</span>
                    </button>
                </div>
            </form>
        </div>

        <!-- Settings Tab -->
        <div class="{{ $activeTab === 'settings' ? 'block' : 'hidden' }}">
            <form wire:submit.prevent="saveProfileSettings" class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                <h3 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                    <i class="fas fa-cogs text-indigo-600 mr-2"></i>
                    Profile Preferences
                </h3>
                
                <div class="space-y-6">
                    <!-- Timezone -->
                    <div>
                        <label for="timezone" class="block text-sm font-medium text-gray-700 mb-2">
                            Timezone <span class="text-red-500">*</span>
                        </label>
                        <select wire:model="timezone" id="timezone"
                                class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @foreach($this->getTimezones() as $tz => $name)
                                <option value="{{ $tz }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('timezone') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- Language -->
                    <div>
                        <label for="language" class="block text-sm font-medium text-gray-700 mb-2">
                            Language <span class="text-red-500">*</span>
                        </label>
                        <select wire:model="language" id="language"
                                class="block w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @foreach($this->getLanguages() as $code => $name)
                                <option value="{{ $code }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('language') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- Privacy Settings -->
                    <div class="border-t pt-6">
                        <h4 class="text-md font-medium text-gray-900 mb-4">Privacy Settings</h4>
                        
                        <div class="space-y-4">
                            <!-- Public Profile -->
                            <div class="flex items-start space-x-3">
                                <input wire:model="is_profile_public" type="checkbox" id="is_profile_public"
                                       class="mt-1 h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                <div class="flex-1">
                                    <label for="is_profile_public" class="text-sm font-medium text-gray-700">
                                        Make profile publicly visible
                                    </label>
                                    <p class="text-sm text-gray-500">
                                        Allow other users to view your profile information
                                    </p>
                                </div>
                            </div>

                            <!-- Show Email -->
                            <div class="flex items-start space-x-3">
                                <input wire:model="show_email_publicly" type="checkbox" id="show_email_publicly"
                                       class="mt-1 h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                <div class="flex-1">
                                    <label for="show_email_publicly" class="text-sm font-medium text-gray-700">
                                        Show email address publicly
                                    </label>
                                    <p class="text-sm text-gray-500">
                                        Display your email address on your public profile
                                    </p>
                                </div>
                            </div>

                            <!-- Show Phone -->
                            <div class="flex items-start space-x-3">
                                <input wire:model="show_phone_publicly" type="checkbox" id="show_phone_publicly"
                                       class="mt-1 h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                <div class="flex-1">
                                    <label for="show_phone_publicly" class="text-sm font-medium text-gray-700">
                                        Show phone number publicly
                                    </label>
                                    <p class="text-sm text-gray-500">
                                        Display your phone number on your public profile
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit" wire:loading.attr="disabled"
                            class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors duration-200">
                        <span wire:loading.remove><i class="fas fa-save mr-2"></i>Save Preferences</span>
                        <span wire:loading><i class="fas fa-circle-notch fa-spin mr-2"></i>Saving...</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>