<x-app-layout>
    <div class="bk-auth-surface bk-edge-to-edge min-h-[calc(100svh-8rem)] px-4 py-8 sm:px-6 sm:py-10 lg:px-10">
        <div class="mx-auto w-full max-w-3xl">
            <div class="relative mb-8 overflow-hidden rounded-[8px] border border-slate-200 bg-slate-950 p-5 text-white shadow-2xl shadow-slate-950/10 sm:p-6">
                <x-icon-field class="opacity-10" />
                <div class="relative flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="min-w-0">
                        <span class="inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1 text-xs font-black text-teal-100">
                            <span class="h-1.5 w-1.5 rounded-full bg-teal-300"></span>
                            New BootKode account
                        </span>
                        <h1 class="bk-display mt-3 text-2xl font-black leading-tight sm:text-3xl">Set up your learning workspace.</h1>
                        <p class="mt-2 max-w-xl text-sm leading-6 text-slate-300">Create one profile for courses, mentorship, certificates, community, and career tools.</p>
                    </div>
                    <a href="{{ route('login') }}" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-[8px] border border-white/15 bg-white/10 px-4 text-sm font-black text-white transition hover:bg-white/15">
                        <i class="fas fa-right-to-bracket text-xs"></i>
                        Sign in
                    </a>
                </div>
                <div class="relative mt-5 grid gap-2 sm:grid-cols-3">
                    @foreach ([
                        ['label' => 'Learning path', 'icon' => 'fa-route', 'class' => 'bg-blue-500'],
                        ['label' => 'Mentor access', 'icon' => 'fa-message', 'class' => 'bg-emerald-500'],
                        ['label' => 'Certificate proof', 'icon' => 'fa-certificate', 'class' => 'bg-rose-500'],
                    ] as $item)
                        <div class="bk-signal-line rounded-[8px] border border-white/10 bg-white/10 p-3" style="--i: {{ $loop->index }}">
                            <span class="grid h-9 w-9 place-items-center rounded-[8px] {{ $item['class'] }} text-white">
                                <i class="fas {{ $item['icon'] }} text-sm"></i>
                            </span>
                            <p class="mt-2 text-sm font-black">{{ $item['label'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Progress Indicator -->
            <div class="mb-8 rounded-[8px] border border-slate-200 bg-white p-4 shadow-lg shadow-slate-950/5">
                <div class="flex items-center justify-between mb-8">
                    <div class="flex-1">
                        <div class="flex items-center">
                            <div class="flex items-center justify-center w-10 h-10 rounded-full text-sm font-semibold transition-all duration-300"
                                id="step1-indicator"
                                :class="currentStep === 1 ? 'bg-slate-900 text-white' : 'bg-slate-200 text-slate-900'">
                                1
                            </div>
                            <p class="ml-3 text-sm font-medium text-slate-900">Personal</p>
                        </div>
                    </div>

                    <div class="flex-1 flex items-center">
                        <div class="h-0.5 flex-grow" id="progress-line-1"
                            :class="currentStep >= 2 ? 'bg-slate-900' : 'bg-slate-200'"></div>
                    </div>

                    <div class="flex-1">
                        <div class="flex items-center">
                            <div class="flex items-center justify-center w-10 h-10 rounded-full text-sm font-semibold transition-all duration-300"
                                id="step2-indicator"
                                :class="currentStep === 2 ? 'bg-slate-900 text-white' : (currentStep > 2 ? 'bg-slate-900 text-white' : 'bg-slate-200 text-slate-900')">
                                2
                            </div>
                            <p class="ml-3 text-sm font-medium text-slate-900">Address</p>
                        </div>
                    </div>

                    <div class="flex-1 flex items-center">
                        <div class="h-0.5 flex-grow" id="progress-line-2"
                            :class="currentStep >= 3 ? 'bg-slate-900' : 'bg-slate-200'"></div>
                    </div>

                    <div class="flex-1">
                        <div class="flex items-center">
                            <div class="flex items-center justify-center w-10 h-10 rounded-full text-sm font-semibold transition-all duration-300"
                                id="step3-indicator"
                                :class="currentStep === 3 ? 'bg-slate-900 text-white' : (currentStep > 3 ? 'bg-slate-900 text-white' : 'bg-slate-200 text-slate-900')">
                                3
                            </div>
                            <p class="ml-3 text-sm font-medium text-slate-900">Additional</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Container -->
            <div class="overflow-hidden rounded-[8px] border border-slate-200 bg-white shadow-2xl shadow-slate-950/10">
                <!-- Header -->
                <div class="px-8 py-10 border-b border-slate-100">
                    <h1 class="text-3xl font-bold text-slate-900 mb-2" id="form-title">Create Your Account</h1>
                    <p class="text-slate-600" id="form-subtitle">Tell us about yourself to get started</p>
                </div>

                <!-- Form -->
                <form method="POST" action="{{ route('register') }}" class="p-8" enctype="multipart/form-data">
                    @csrf

                    <!-- STEP 1: Personal Information -->
                    <div id="step-1" class="step-content">
                        <div class="space-y-6">
                            <!-- Name -->
                            <div>
                                <label for="name" class="block text-sm font-semibold text-slate-900 mb-2">Full Name</label>
                                <input
                                    id="name"
                                    class="w-full px-4 py-3 rounded-lg border border-slate-300 text-slate-900 placeholder-slate-400 transition-all duration-200 focus:outline-none focus:border-slate-900 focus:ring-2 focus:ring-slate-900 focus:ring-opacity-10"
                                    type="text"
                                    name="name"
                                    value="{{ old('name') }}"
                                    placeholder="John Doe"
                                    required
                                    autocomplete="name" />
                                @error('name')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-semibold text-slate-900 mb-2">Email Address</label>
                                <input
                                    id="email"
                                    class="w-full px-4 py-3 rounded-lg border border-slate-300 text-slate-900 placeholder-slate-400 transition-all duration-200 focus:outline-none focus:border-slate-900 focus:ring-2 focus:ring-slate-900 focus:ring-opacity-10"
                                    type="email"
                                    name="email"
                                    value="{{ old('email') }}"
                                    placeholder="john@example.com"
                                    required
                                    autocomplete="username" />
                                @error('email')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Password -->
                            <div>
                                <label for="password" class="block text-sm font-semibold text-slate-900 mb-2">Password</label>
                                <div class="relative">
                                    <input
                                        id="password"
                                        class="w-full px-4 py-3 pr-12 rounded-lg border border-slate-300 text-slate-900 placeholder-slate-400 transition-all duration-200 focus:outline-none focus:border-slate-900 focus:ring-2 focus:ring-slate-900 focus:ring-opacity-10"
                                        type="password"
                                        name="password"
                                        placeholder="Enter a strong password"
                                        required
                                        autocomplete="new-password" />
                                    <button
                                        type="button"
                                        onclick="togglePasswordVisibility('password', 'toggle-icon-1')"
                                        class="absolute right-4 top-1/2 transform -translate-y-1/2 text-slate-500 hover:text-slate-700 transition-colors">
                                        <svg id="toggle-icon-1" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </button>
                                </div>
                                @error('password')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Confirm Password -->
                            <div>
                                <label for="password_confirmation" class="block text-sm font-semibold text-slate-900 mb-2">Confirm Password</label>
                                <div class="relative">
                                    <input
                                        id="password_confirmation"
                                        class="w-full px-4 py-3 pr-12 rounded-lg border border-slate-300 text-slate-900 placeholder-slate-400 transition-all duration-200 focus:outline-none focus:border-slate-900 focus:ring-2 focus:ring-slate-900 focus:ring-opacity-10"
                                        type="password"
                                        name="password_confirmation"
                                        placeholder="Confirm your password"
                                        required
                                        autocomplete="new-password" />
                                    <button
                                        type="button"
                                        onclick="togglePasswordVisibility('password_confirmation', 'toggle-icon-2')"
                                        class="absolute right-4 top-1/2 transform -translate-y-1/2 text-slate-500 hover:text-slate-700 transition-colors">
                                        <svg id="toggle-icon-2" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </button>
                                </div>
                                @error('password_confirmation')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Role -->
                            <div>
                                <label for="role" class="block text-sm font-semibold text-slate-900 mb-2">What's your role?</label>
                                <select
                                    id="role"
                                    name="role"
                                    class="w-full px-4 py-3 rounded-lg border border-slate-300 text-slate-900 transition-all duration-200 focus:outline-none focus:border-slate-900 focus:ring-2 focus:ring-slate-900 focus:ring-opacity-10"
                                    >
                                    <option value="">Select your role</option>
                                    <option value="{{ App\Models\Core\User::ROLE_STUDENT }}" {{ old('role') == App\Models\Core\User::ROLE_STUDENT ? 'selected' : '' }}>Student</option>
                                    <option value="{{ App\Models\Core\User::ROLE_INSTRUCTOR }}" {{ old('role') == App\Models\Core\User::ROLE_INSTRUCTOR ? 'selected' : '' }}>Instructor</option>
                                    <option value="{{ App\Models\Core\User::ROLE_MENTOR }}" {{ old('role') == App\Models\Core\User::ROLE_MENTOR ? 'selected' : '' }}>Mentor</option>
                                </select>
                                @error('role')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Date of Birth -->
                            <div>
                                <label for="date_of_birth" class="block text-sm font-semibold text-slate-900 mb-2">Date of Birth</label>
                                <input
                                    id="date_of_birth"
                                    class="w-full px-4 py-3 rounded-lg border border-slate-300 text-slate-900 placeholder-slate-400 transition-all duration-200 focus:outline-none focus:border-slate-900 focus:ring-2 focus:ring-slate-900 focus:ring-opacity-10"
                                    type="date"
                                    name="date_of_birth"
                                    value="{{ old('date_of_birth') }}"
                                    />
                                <p class="mt-2 text-xs text-slate-500">We need this to personalize your learning experience</p>
                                @error('date_of_birth')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Phone Number -->
                            <div>
                                <label for="phone_number" class="block text-sm font-semibold text-slate-900 mb-2">Phone Number</label>
                                <input
                                    id="phone_number"
                                    class="w-full px-4 py-3 rounded-lg border border-slate-300 text-slate-900 placeholder-slate-400 transition-all duration-200 focus:outline-none focus:border-slate-900 focus:ring-2 focus:ring-slate-900 focus:ring-opacity-10"
                                    type="tel"
                                    name="phone_number"
                                    value="{{ old('phone_number') }}"
                                    placeholder="+234 8012345678"
                                     />
                                @error('phone_number')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Skills -->
                            <div>
                                <label for="skills" class="block text-sm font-semibold text-slate-900 mb-2">Skills & Interests (Optional)</label>
                                <input
                                    id="skills"
                                    class="w-full px-4 py-3 rounded-lg border border-slate-300 text-slate-900 placeholder-slate-400 transition-all duration-200 focus:outline-none focus:border-slate-900 focus:ring-2 focus:ring-slate-900 focus:ring-opacity-10"
                                    type="text"
                                    name="skills"
                                    value="{{ old('skills') }}"
                                    placeholder="e.g. Laravel, Vue.js, Photography" />
                                <p class="mt-2 text-xs text-slate-500">Separate multiple skills with commas</p>
                                @error('skills')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Bio -->
                            <div>
                                <label for="bio" class="block text-sm font-semibold text-slate-900 mb-2">Bio (Optional)</label>
                                <textarea
                                    id="bio"
                                    name="bio"
                                    class="w-full px-4 py-3 rounded-lg border border-slate-300 text-slate-900 placeholder-slate-400 transition-all duration-200 focus:outline-none focus:border-slate-900 focus:ring-2 focus:ring-slate-900 focus:ring-opacity-10"
                                    rows="3"
                                    placeholder="Tell us a bit about yourself...">{{ old('bio') }}</textarea>
                                @error('bio')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- STEP 2: Address Information -->
                    <div id="step-2" class="step-content hidden">
                        <div class="space-y-6">
                            <!-- Street Address -->
                            <div>
                                <label for="address_street" class="block text-sm font-semibold text-slate-900 mb-2">Street Address</label>
                                <input
                                    id="address_street"
                                    class="w-full px-4 py-3 rounded-lg border border-slate-300 text-slate-900 placeholder-slate-400 transition-all duration-200 focus:outline-none focus:border-slate-900 focus:ring-2 focus:ring-slate-900 focus:ring-opacity-10"
                                    type="text"
                                    name="address_street"
                                    value="{{ old('address_street') }}"
                                    placeholder="123 Main Street" />
                                @error('address_street')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- City and State Grid -->
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <label for="address_city" class="block text-sm font-semibold text-slate-900 mb-2">City</label>
                                    <input
                                        id="address_city"
                                        class="w-full px-4 py-3 rounded-lg border border-slate-300 text-slate-900 placeholder-slate-400 transition-all duration-200 focus:outline-none focus:border-slate-900 focus:ring-2 focus:ring-slate-900 focus:ring-opacity-10"
                                        type="text"
                                        name="address_city"
                                        value="{{ old('address_city') }}"
                                        placeholder="Lagos" />
                                    @error('address_city')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="address_state" class="block text-sm font-semibold text-slate-900 mb-2">State/Province</label>
                                    <input
                                        id="address_state"
                                        class="w-full px-4 py-3 rounded-lg border border-slate-300 text-slate-900 placeholder-slate-400 transition-all duration-200 focus:outline-none focus:border-slate-900 focus:ring-2 focus:ring-slate-900 focus:ring-opacity-10"
                                        type="text"
                                        name="address_state"
                                        value="{{ old('address_state') }}"
                                        placeholder="Lagos" />
                                    @error('address_state')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Country and Postal Code Grid -->
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <label for="address_country" class="block text-sm font-semibold text-slate-900 mb-2">Country</label>
                                    <input
                                        id="address_country"
                                        class="w-full px-4 py-3 rounded-lg border border-slate-300 text-slate-900 placeholder-slate-400 transition-all duration-200 focus:outline-none focus:border-slate-900 focus:ring-2 focus:ring-slate-900 focus:ring-opacity-10"
                                        type="text"
                                        name="address_country"
                                        value="{{ old('address_country') }}"
                                        placeholder="Nigeria" />
                                    @error('address_country')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="address_postal_code" class="block text-sm font-semibold text-slate-900 mb-2">Postal Code</label>
                                    <input
                                        id="address_postal_code"
                                        class="w-full px-4 py-3 rounded-lg border border-slate-300 text-slate-900 placeholder-slate-400 transition-all duration-200 focus:outline-none focus:border-slate-900 focus:ring-2 focus:ring-slate-900 focus:ring-opacity-10"
                                        type="text"
                                        name="address_postal_code"
                                        value="{{ old('address_postal_code') }}"
                                        placeholder="100001" />
                                    @error('address_postal_code')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 3: Additional Information -->
                    <div id="step-3" class="step-content hidden">
                        <div class="space-y-6">
                            <!-- Occupation -->
                            <div>
                                <label for="occupation" class="block text-sm font-semibold text-slate-900 mb-2">Occupation (Optional)</label>
                                <input
                                    id="occupation"
                                    class="w-full px-4 py-3 rounded-lg border border-slate-300 text-slate-900 placeholder-slate-400 transition-all duration-200 focus:outline-none focus:border-slate-900 focus:ring-2 focus:ring-slate-900 focus:ring-opacity-10"
                                    type="text"
                                    name="occupation"
                                    value="{{ old('occupation') }}"
                                    placeholder="Software Developer" />
                                @error('occupation')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Education Level -->
                            <div>
                                <label for="education_level" class="block text-sm font-semibold text-slate-900 mb-2">Education Level (Optional)</label>
                                <select
                                    id="education_level"
                                    name="education_level"
                                    class="w-full px-4 py-3 rounded-lg border border-slate-300 text-slate-900 transition-all duration-200 focus:outline-none focus:border-slate-900 focus:ring-2 focus:ring-slate-900 focus:ring-opacity-10">
                                    <option value="">Select education level</option>
                                    <option value="High School" {{ old('education_level') == 'High School' ? 'selected' : '' }}>High School</option>
                                    <option value="Bachelor's Degree" {{ old('education_level') == "Bachelor's Degree" ? 'selected' : '' }}>Bachelor's Degree</option>
                                    <option value="Master's Degree" {{ old('education_level') == "Master's Degree" ? 'selected' : '' }}>Master's Degree</option>
                                    <option value="PhD" {{ old('education_level') == 'PhD' ? 'selected' : '' }}>PhD</option>
                                    <option value="Other" {{ old('education_level') == 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                                @error('education_level')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Profile Picture -->
                            <div>
                                <label for="profile_picture" class="block text-sm font-semibold text-slate-900 mb-2">Profile Picture (Optional)</label>
                                <div class="relative">
                                    <input
                                        id="profile_picture"
                                        class="w-full px-4 py-3 rounded-lg border border-slate-300 text-slate-900 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-slate-100 file:text-slate-900 hover:file:bg-slate-200 transition-colors cursor-pointer"
                                        type="file"
                                        name="profile_picture"
                                        accept="image/*" />
                                </div>
                                @error('profile_picture')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Referral Code Alert -->
                            @if($referralCode && $validation && $validation['valid'])
                                <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-lg">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-semibold text-emerald-900">You're being referred by {{ $validation['referrer_name'] }}</p>
                                            <p class="mt-1 text-sm text-emerald-800">Welcome! You're signing up through a referral link.</p>
                                        </div>
                                    </div>
                                    <input type="hidden" name="referral_code" value="{{ $referralCode }}">
                                </div>
                            @elseif($referralCode && $validation && !$validation['valid'])
                                <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            <svg class="h-5 w-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-semibold text-red-900">Invalid Referral Code</p>
                                            <p class="mt-1 text-sm text-red-800">The referral code is invalid or expired. You can still register normally.</p>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Terms and Conditions -->
                            <div class="pt-4 border-t border-slate-200">
                                <label class="flex items-start cursor-pointer group">
                                    <input
                                        type="checkbox"
                                        name="terms"
                                        class="mt-1 w-5 h-5 rounded border-slate-300 text-slate-900 focus:ring-2 focus:ring-slate-900 focus:ring-opacity-10 cursor-pointer"
                                        required />
                                    <span class="ml-3 text-sm text-slate-600 group-hover:text-slate-900 transition-colors">
                                        I agree to the <a href="#" class="font-semibold text-slate-900 hover:underline">Terms of Service</a> and <a href="#" class="font-semibold text-slate-900 hover:underline">Privacy Policy</a>
                                    </span>
                                </label>
                                @error('terms')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Navigation Buttons -->
                    <div class="flex gap-4 mt-8 pt-8 border-t border-slate-100">
                        <button
                            type="button"
                            id="prev-btn"
                            onclick="previousStep()"
                            class="hidden inline-flex items-center gap-2 rounded-[8px] border border-slate-300 px-6 py-3 font-semibold text-slate-900 transition-all duration-200 hover:bg-slate-50">
                            <i class="fas fa-arrow-left text-xs"></i>
                            Back
                        </button>
                        <button
                            type="button"
                            id="next-btn"
                            onclick="nextStep()"
                            class="ml-auto inline-flex items-center gap-2 rounded-[8px] bg-slate-900 px-6 py-3 font-semibold text-white transition-all duration-200 hover:bg-slate-800 hover:shadow-lg">
                            Continue
                            <i class="fas fa-arrow-right text-xs"></i>
                        </button>
                        <button
                            type="submit"
                            id="submit-btn"
                            class="ml-auto hidden inline-flex items-center gap-2 rounded-[8px] bg-slate-900 px-8 py-3 font-semibold text-white transition-all duration-200 hover:bg-slate-800 hover:shadow-lg">
                            <i class="fas fa-user-plus text-xs"></i>
                            Create Account
                        </button>
                    </div>

                    <!-- Login Link -->
                    <div class="text-center mt-6 text-sm text-slate-600">
                        Already have an account?
                        <a href="{{ route('login') }}" class="font-semibold text-slate-900 hover:underline">
                            Log in
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Toggle password visibility
        function togglePasswordVisibility(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);

            if (input.type === 'password') {
                input.type = 'text';
                // Change to eye-off icon
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-4.803m5.596-3.856a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 9.172L21 21m-10.5-1.5L3 3m8.498-1.498l7.08 7.08M3 21l7.08-7.08"></path>';
            } else {
                input.type = 'password';
                // Change back to eye icon
                icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>';
            }
        }

        let currentStep = 1;
        const totalSteps = 3;

        function showStep(step) {
            // Hide all steps
            document.querySelectorAll('.step-content').forEach(el => {
                el.classList.add('hidden');
            });

            // Show current step
            document.getElementById(`step-${step}`).classList.remove('hidden');

            // Update progress
            updateProgress();
            updateButtons();
            updateTitle();
        }

        function updateProgress() {
            for (let i = 1; i <= totalSteps; i++) {
                const indicator = document.getElementById(`step${i}-indicator`);
                if (i === currentStep) {
                    indicator.className = 'flex items-center justify-center w-10 h-10 rounded-full text-sm font-semibold bg-slate-900 text-white transition-all duration-300';
                } else if (i < currentStep) {
                    indicator.className = 'flex items-center justify-center w-10 h-10 rounded-full text-sm font-semibold bg-slate-900 text-white transition-all duration-300';
                } else {
                    indicator.className = 'flex items-center justify-center w-10 h-10 rounded-full text-sm font-semibold bg-slate-200 text-slate-900 transition-all duration-300';
                }
            }

            for (let i = 1; i < totalSteps; i++) {
                const line = document.getElementById(`progress-line-${i}`);
                if (i < currentStep) {
                    line.className = 'h-0.5 flex-grow bg-slate-900 transition-all duration-300';
                } else {
                    line.className = 'h-0.5 flex-grow bg-slate-200 transition-all duration-300';
                }
            }
        }

        function updateButtons() {
            const prevBtn = document.getElementById('prev-btn');
            const nextBtn = document.getElementById('next-btn');
            const submitBtn = document.getElementById('submit-btn');

            if (currentStep === 1) {
                prevBtn.classList.add('hidden');
            } else {
                prevBtn.classList.remove('hidden');
            }

            if (currentStep === totalSteps) {
                nextBtn.classList.add('hidden');
                submitBtn.classList.remove('hidden');
            } else {
                nextBtn.classList.remove('hidden');
                submitBtn.classList.add('hidden');
            }
        }

        function updateTitle() {
            const titles = [
                'Create Your Account',
                'Where Do You Live?',
                'Almost Done!'
            ];
            const subtitles = [
                'Tell us about yourself to get started',
                'Help us know your location',
                'A few more details to complete your profile'
            ];

            document.getElementById('form-title').textContent = titles[currentStep - 1];
            document.getElementById('form-subtitle').textContent = subtitles[currentStep - 1];
        }

        function nextStep() {
            if (currentStep < totalSteps) {
                currentStep++;
                showStep(currentStep);
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        }

        function previousStep() {
            if (currentStep > 1) {
                currentStep--;
                showStep(currentStep);
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        }

        // Form validation before moving to next step
        function validateStep(step) {
            const inputs = document.querySelectorAll(`#step-${step} input[required], #step-${step} select[required], #step-${step} textarea[required]`);
            let isValid = true;

            inputs.forEach(input => {
                if (!input.value.trim()) {
                    isValid = false;
                    input.classList.add('border-red-500', 'focus:ring-red-500');
                } else {
                    input.classList.remove('border-red-500', 'focus:ring-red-500');
                }
            });

            return isValid;
        }

        // Enhanced nextStep with validation
        const originalNextStep = nextStep;
        nextStep = function() {
            if (validateStep(currentStep)) {
                if (currentStep < totalSteps) {
                    currentStep++;
                    showStep(currentStep);
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            } else {
                // Shake animation for invalid form
                const formContent = document.getElementById(`step-${currentStep}`);
                formContent.classList.add('shake');
                setTimeout(() => formContent.classList.remove('shake'), 500);
            }
        };

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            showStep(1);
        });
    </script>

    <style>
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }

        .shake {
            animation: shake 0.5s ease-in-out;
        }
    </style>
</x-app-layout>
