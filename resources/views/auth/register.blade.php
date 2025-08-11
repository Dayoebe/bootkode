<x-app-layout>
    <span
        class="bg-gradient-to-br from-blue-50 via-pink-50 to-gray-50 min-h-screen flex items-center justify-center p-4">
        <div class="w-full max-w-4xl">
            <div
                class="relative bg-white/90 backdrop-blur-sm rounded-3xl shadow-2xl overflow-hidden animate__animated animate__fadeInUp">
                <!-- Header -->
                <div class="bg-gradient-to-r from-blue-600 to-pink-600 p-8 text-center relative">
                    <div class="absolute top-0 left-0 w-full h-full opacity-20">
                        <i
                            class="fas fa-user-plus text-9xl absolute -top-4 -right-4 text-white animate__animated animate__fadeIn animate__delay-1s"></i>
                    </div>
                    <h1 class="text-3xl font-bold text-white mb-2 relative z-10">Create Your Account</h1>
                    <p class="text-blue-100 relative z-10">Join our community today</p>
                </div>

                <form method="POST" action="{{ route('register') }}" class="p-8 grid grid-cols-1 md:grid-cols-2 gap-6"
                    enctype="multipart/form-data">
                    @csrf

                    <!-- Personal Information Section -->
                    <div class="md:col-span-2">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-user-circle mr-2 text-blue-600"></i>
                            Personal Information
                        </h2>
                    </div>

                    <!-- Name -->
                    <div class="field-wrapper">
                        <x-input-label for="name" :value="__('Full Name')" class="flex items-center">
                            <i class="fas fa-signature mr-2 text-blue-600 text-sm"></i>
                        </x-input-label>
                        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name"
                            :value="old('name')" required autofocus autocomplete="name" />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <!-- Email -->
                    <div class="field-wrapper">
                        <x-input-label for="email" :value="__('Email')" class="flex items-center">
                            <i class="fas fa-envelope mr-2 text-blue-600 text-sm"></i>
                        </x-input-label>
                        <x-text-input id="email" class="block mt-1 w-full" type="email" name="email"
                            :value="old('email')" required autocomplete="username" />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <!-- Password -->
                    <div class="field-wrapper">
                        <x-input-label for="password" :value="__('Password')" class="flex items-center">
                            <i class="fas fa-lock mr-2 text-blue-600 text-sm"></i>
                        </x-input-label>
                        <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required
                            autocomplete="new-password" />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <!-- Confirm Password -->
                    <div class="field-wrapper">
                        <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="flex items-center">
                            <i class="fas fa-lock mr-2 text-blue-600 text-sm"></i>
                        </x-input-label>
                        <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password"
                            name="password_confirmation" required autocomplete="new-password" />
                        <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                    </div>

                    <!-- Role -->
                    <div class="field-wrapper">
                        <x-input-label for="role" :value="__('Role')" class="flex items-center">
                            <i class="fas fa-user-tag mr-2 text-blue-600 text-sm"></i>
                        </x-input-label>
                        <select id="role" name="role"
                            class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600"
                            required>
                            <option value="">Select your role</option>
                            <option value="{{ App\Models\User::ROLE_STUDENT }}"
                                {{ old('role') == App\Models\User::ROLE_STUDENT ? 'selected' : '' }}>Student</option>
                            <option value="{{ App\Models\User::ROLE_INSTRUCTOR }}"
                                {{ old('role') == App\Models\User::ROLE_INSTRUCTOR ? 'selected' : '' }}>Instructor
                            </option>
                            <option value="{{ App\Models\User::ROLE_MENTOR }}"
                                {{ old('role') == App\Models\User::ROLE_MENTOR ? 'selected' : '' }}>Mentor</option>
                        </select>
                        <x-input-error :messages="$errors->get('role')" class="mt-2" />
                    </div>

                    <!-- Date of Birth -->
                    <div class="field-wrapper">
                        <x-input-label for="date_of_birth" :value="__('Date of Birth')" class="flex items-center">
                            <i class="fas fa-birthday-cake mr-2 text-blue-600 text-sm"></i>
                        </x-input-label>
                        <x-text-input id="date_of_birth" class="block mt-1 w-full" type="date" name="date_of_birth"
                            :value="old('date_of_birth')" required />
                        <x-input-error :messages="$errors->get('date_of_birth')" class="mt-2" />
                    </div>

                    <!-- Phone Number -->
                    <div class="field-wrapper">
                        <x-input-label for="phone_number" :value="__('Phone Number')" class="flex items-center">
                            <i class="fas fa-phone mr-2 text-blue-600 text-sm"></i>
                        </x-input-label>
                        <x-text-input id="phone_number" class="block mt-1 w-full" type="tel" name="phone_number"
                            :value="old('phone_number')" required />
                        <x-input-error :messages="$errors->get('phone_number')" class="mt-2" />
                    </div>
                    <!-- Skills & Interests -->
                    <div class="field-wrapper">
                        <x-input-label for="skills" :value="__('Skills & Interests (Optional)')" class="flex items-center">
                            <i class="fas fa-lightbulb mr-2 text-yellow-500 text-sm"></i>
                        </x-input-label>
                        <x-text-input id="skills" name="skills" type="text" class="block mt-1 w-full"
                            placeholder="e.g. Laravel, Vue.js, Photography" value="{{ old('skills') }}" />
                        <x-input-error :messages="$errors->get('skills')" class="mt-2" />
                        <p class="mt-1 text-xs text-gray-500">Separate multiple skills with commas</p>
                    </div>
                    <!-- Bio -->
                    <div class="field-wrapper md:col-span-2">
                        <x-input-label for="bio" :value="__('Bio (Optional)')" class="flex items-center">
                            <i class="fas fa-info-circle mr-2 text-blue-600 text-sm"></i>
                        </x-input-label>
                        <textarea id="bio" name="bio"
                            class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600"
                            rows="3">{{ old('bio') }}</textarea>
                        <x-input-error :messages="$errors->get('bio')" class="mt-2" />
                    </div>

                    <!-- Address Section -->
                    <div class="md:col-span-2 mt-6">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-map-marker-alt mr-2 text-blue-600"></i>
                            Address Information
                        </h2>
                    </div>

                    <!-- Street Address -->
                    <div class="field-wrapper md:col-span-2">
                        <x-input-label for="address_street" :value="__('Street Address')" class="flex items-center">
                            <i class="fas fa-road mr-2 text-blue-600 text-sm"></i>
                        </x-input-label>
                        <x-text-input id="address_street" class="block mt-1 w-full" type="text"
                            name="address_street" :value="old('address_street')" required />
                        <x-input-error :messages="$errors->get('address_street')" class="mt-2" />
                    </div>

                    <!-- City -->
                    <div class="field-wrapper">
                        <x-input-label for="address_city" :value="__('City')" class="flex items-center">
                            <i class="fas fa-city mr-2 text-blue-600 text-sm"></i>
                        </x-input-label>
                        <x-text-input id="address_city" class="block mt-1 w-full" type="text" name="address_city"
                            :value="old('address_city')" required />
                        <x-input-error :messages="$errors->get('address_city')" class="mt-2" />
                    </div>

                    <!-- State/Province -->
                    <div class="field-wrapper">
                        <x-input-label for="address_state" :value="__('State/Province')" class="flex items-center">
                            <i class="fas fa-map mr-2 text-blue-600 text-sm"></i>
                        </x-input-label>
                        <x-text-input id="address_state" class="block mt-1 w-full" type="text"
                            name="address_state" :value="old('address_state')" required />
                        <x-input-error :messages="$errors->get('address_state')" class="mt-2" />
                    </div>

                    <!-- Country -->
                    <div class="field-wrapper">
                        <x-input-label for="address_country" :value="__('Country')" class="flex items-center">
                            <i class="fas fa-globe mr-2 text-blue-600 text-sm"></i>
                        </x-input-label>
                        <x-text-input id="address_country" class="block mt-1 w-full" type="text"
                            name="address_country" :value="old('address_country')" required />
                        <x-input-error :messages="$errors->get('address_country')" class="mt-2" />
                    </div>

                    <!-- Postal Code -->
                    <div class="field-wrapper">
                        <x-input-label for="address_postal_code" :value="__('Postal Code')" class="flex items-center">
                            <i class="fas fa-mail-bulk mr-2 text-blue-600 text-sm"></i>
                        </x-input-label>
                        <x-text-input id="address_postal_code" class="block mt-1 w-full" type="text"
                            name="address_postal_code" :value="old('address_postal_code')" required />
                        <x-input-error :messages="$errors->get('address_postal_code')" class="mt-2" />
                    </div>

                    <!-- Additional Information Section -->
                    <div class="md:col-span-2 mt-6">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-graduation-cap mr-2 text-blue-600"></i>
                            Additional Information
                        </h2>
                    </div>

                    <!-- Occupation -->
                    <div class="field-wrapper">
                        <x-input-label for="occupation" :value="__('Occupation (Optional)')" class="flex items-center">
                            <i class="fas fa-briefcase mr-2 text-blue-600 text-sm"></i>
                        </x-input-label>
                        <x-text-input id="occupation" class="block mt-1 w-full" type="text" name="occupation"
                            :value="old('occupation')" />
                        <x-input-error :messages="$errors->get('occupation')" class="mt-2" />
                    </div>

                    <!-- Education Level -->
                    <div class="field-wrapper">
                        <x-input-label for="education_level" :value="__('Education Level (Optional)')" class="flex items-center">
                            <i class="fas fa-university mr-2 text-blue-600 text-sm"></i>
                        </x-input-label>
                        <select id="education_level" name="education_level"
                            class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-blue-600 focus:ring-blue-600">
                            <option value="">Select education level</option>
                            <option value="High School"
                                {{ old('education_level') == 'High School' ? 'selected' : '' }}>High School</option>
                            <option value="Bachelor's Degree"
                                {{ old('education_level') == "Bachelor's Degree" ? 'selected' : '' }}>Bachelor's Degree
                            </option>
                            <option value="Master's Degree"
                                {{ old('education_level') == "Master's Degree" ? 'selected' : '' }}>Master's Degree
                            </option>
                            <option value="PhD" {{ old('education_level') == 'PhD' ? 'selected' : '' }}>PhD
                            </option>
                            <option value="Other" {{ old('education_level') == 'Other' ? 'selected' : '' }}>Other
                            </option>
                        </select>
                        <x-input-error :messages="$errors->get('education_level')" class="mt-2" />
                    </div>

                    <!-- Profile Picture -->
                    <div class="field-wrapper md:col-span-2">
                        <x-input-label for="profile_picture" :value="__('Profile Picture (Optional)')" class="flex items-center">
                            <i class="fas fa-camera mr-2 text-blue-600 text-sm"></i>
                        </x-input-label>
                        <input id="profile_picture"
                            class="block mt-1 w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                            type="file" name="profile_picture" accept="image/*" />
                        <x-input-error :messages="$errors->get('profile_picture')" class="mt-2" />
                    </div>

                    <!-- Terms and Conditions -->
                    <div class="md:col-span-2">
                        <label class="flex items-center">
                            <input type="checkbox" name="terms"
                                class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-600 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                required>
                            <span class="ml-2 text-sm text-gray-600">
                                I agree to the <a href="#" class="text-blue-600 hover:text-blue-800">Terms of
                                    Service</a> and <a href="#"
                                    class="text-blue-600 hover:text-blue-800">Privacy Policy</a>
                            </span>
                        </label>
                        <x-input-error :messages="$errors->get('terms')" class="mt-2" />
                    </div>

                    <!-- Submit Button -->
                    <div class="md:col-span-2">
                        <button type="submit"
                            class="w-full bg-gradient-to-r from-blue-600 to-pink-600 hover:from-blue-700 hover:to-pink-700 text-white font-bold py-4 px-6 rounded-xl hover:shadow-lg transform hover:scale-[1.01] transition-all duration-300">
                            <span class="flex items-center justify-center">
                                <i class="fas fa-user-plus mr-2"></i>
                                Register Now
                            </span>
                        </button>
                    </div>

                    <!-- Login Link -->
                    <div class="md:col-span-2 text-center text-sm text-gray-600">
                        Already have an account?
                        <a href="{{ route('login') }}"
                            class="text-blue-600 font-semibold hover:text-gray-600 transition-colors duration-300">
                            Log in here
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </span>

    <style>
        .field-wrapper {
            transition: transform .2s ease, box-shadow .2s ease;
            @apply rounded-lg p-1;
        }

        .field-wrapper:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px -1px rgba(102, 126, 234, 0.1), 0 2px 4px -1px rgba(102, 126, 234, 0.06);
        }
    </style>
</x-app-layout>
