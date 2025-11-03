<x-app-layout>
    <div class="min-h-screen bg-white flex items-center justify-center p-4">
        <div class="w-full max-w-md">
            <!-- Main Card -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <!-- Header -->
                <div class="px-8 py-10 border-b border-slate-100">
                    <h1 class="text-3xl font-bold text-slate-900 mb-2">Welcome Back</h1>
                    <p class="text-slate-600">Sign in to your account to continue</p>
                </div>

                <!-- Form -->
                <form method="POST" action="{{ route('login') }}" class="p-8 space-y-6">
                    @csrf

                    <!-- Session Status -->
                    @if (session('status'))
                        <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-lg">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-emerald-900">{{ session('status') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Validation Errors -->
                    @if ($errors->any())
                        <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-900">Something went wrong</h3>
                                    <ul class="mt-2 list-disc list-inside text-sm text-red-800 space-y-1">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    @error('social')
                        <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-red-900">{{ $message }}</p>
                                </div>
                            </div>
                        </div>
                    @enderror

                    <!-- Email Field -->
                    <div>
                        <label for="email" class="block text-sm font-semibold text-slate-900 mb-2">Email Address</label>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            autocomplete="username"
                            class="w-full px-4 py-3 rounded-lg border border-slate-300 text-slate-900 placeholder-slate-400 transition-all duration-200 focus:outline-none focus:border-slate-900 focus:ring-2 focus:ring-slate-900 focus:ring-opacity-10"
                            placeholder="john@example.com" />
                    </div>

                    <!-- Password Field -->
                    <div>
                        <label for="password" class="block text-sm font-semibold text-slate-900 mb-2">Password</label>
                        <div class="relative">
                            <input
                                id="password"
                                type="password"
                                name="password"
                                required
                                autocomplete="current-password"
                                class="w-full px-4 py-3 pr-12 rounded-lg border border-slate-300 text-slate-900 placeholder-slate-400 transition-all duration-200 focus:outline-none focus:border-slate-900 focus:ring-2 focus:ring-slate-900 focus:ring-opacity-10"
                                placeholder="Enter your password" />
                            <button
                                type="button"
                                onclick="togglePasswordVisibility('password', 'password-toggle-icon')"
                                class="absolute right-4 top-1/2 transform -translate-y-1/2 text-slate-500 hover:text-slate-700 transition-colors"
                                aria-label="Toggle password visibility">
                                <svg id="password-toggle-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me & Forgot Password -->
                    <div class="flex items-center justify-between">
                        <label class="flex items-center cursor-pointer">
                            <input
                                type="checkbox"
                                name="remember"
                                class="w-4 h-4 rounded border-slate-300 text-slate-900 focus:ring-2 focus:ring-slate-900 focus:ring-opacity-10 cursor-pointer" />
                            <span class="ml-2 text-sm text-slate-600">Remember me</span>
                        </label>
                        <a href="{{ route('password.request') }}" class="text-sm font-semibold text-slate-900 hover:underline">
                            Forgot password?
                        </a>
                    </div>

                    <!-- Submit Button -->
                    <button
                        type="submit"
                        class="w-full px-6 py-3 rounded-lg bg-slate-900 text-white font-semibold hover:bg-slate-800 transition-all duration-200 hover:shadow-lg">
                        Sign In
                    </button>

                    <!-- Divider -->
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-slate-200"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-2 bg-white text-slate-600">Or continue with</span>
                        </div>
                    </div>

                    <!-- Social Login Buttons - Disabled -->
                    <div class="grid grid-cols-3 gap-3">
                        <button
                            type="button"
                            onclick="showNotAvailableMessage()"
                            class="py-3 px-4 rounded-lg border border-slate-300 text-slate-900 font-semibold transition-all duration-200 flex items-center justify-center gap-2 disabled-social-btn"
                            aria-label="Sign in with Google" disabled>
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                                <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                                <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                            </svg>
                        </button>
                        <button
                            type="button"
                            onclick="showNotAvailableMessage()"
                            class="py-3 px-4 rounded-lg border border-slate-300 text-slate-900 font-semibold transition-all duration-200 flex items-center justify-center gap-2 disabled-social-btn"
                            aria-label="Sign in with Facebook" disabled>
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="#1877F2">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                        </button>
                        <button
                            type="button"
                            onclick="showNotAvailableMessage()"
                            class="py-3 px-4 rounded-lg border border-slate-300 text-slate-900 font-semibold transition-all duration-200 flex items-center justify-center gap-2 disabled-social-btn"
                            aria-label="Sign in with Twitter" disabled>
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="#000">
                                <path d="M23.953 4.57a10 10 0 002.856-10.86 10.002 10.002 0 01-2.856 10.86m7.783-1.707a10 10 0 002.454-10.86 9.996 9.996 0 01-2.454 10.86M15.75 17.5a6.5 6.5 0 11-13 0 6.5 6.5 0 0113 0z"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Notification Message (Hidden by default) -->
                    <div id="not-available-message" class="hidden p-3 bg-amber-50 border border-amber-200 rounded-lg text-center">
                        <p class="text-sm font-medium text-amber-900">This feature is not available at the moment.</p>
                    </div>

                    <!-- Sign Up Link -->
                    <p class="text-center text-sm text-slate-600">
                        Don't have an account?
                        <a href="{{ route('register') }}" class="font-semibold text-slate-900 hover:underline">
                            Sign up now
                        </a>
                    </p>
                </form>
            </div>
        </div>
    </div>

    <script>
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

        function showNotAvailableMessage() {
            const messageElement = document.getElementById('not-available-message');
            messageElement.classList.remove('hidden');
            
            // Hide the message after 3 seconds
            setTimeout(() => {
                messageElement.classList.add('hidden');
            }, 3000);
        }
    </script>

    <style>
        .disabled-social-btn {
            opacity: 0.6;
            cursor: not-allowed;
        }
        
        .disabled-social-btn:hover {
            background-color: #f8fafc !important;
            transform: none !important;
        }
    </style>
</x-app-layout>