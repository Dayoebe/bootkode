<x-app-layout>
    <div
        class="min-h-screen flex items-center justify-center p-4 bg-gradient-to-br from-blue-50 via-purple-50 to-indigo-100">
        <!-- Background Elements -->
        <div class="fixed inset-0 overflow-hidden pointer-events-none">
            <div class="absolute top-1/4 left-1/4 w-2 h-2 bg-white rounded-full opacity-60 animate-bounce delay-100">
            </div>
            <div class="absolute top-3/4 right-1/4 w-3 h-3 bg-pink-300 rounded-full opacity-60 animate-bounce delay-300">
            </div>
            <div class="absolute bottom-1/4 left-1/2 w-2 h-2 bg-white rounded-full opacity-60 animate-bounce delay-500">
            </div>
            <div
                class="absolute -top-40 -right-40 w-80 h-80 bg-purple-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-pulse">
            </div>
            <div
                class="absolute -bottom-40 -left-40 w-80 h-80 bg-pink-300 rounded-full mix-blend-multiply filter blur-xl opacity-70 animate-pulse delay-1000">
            </div>
        </div>

        <!-- Main Card -->
        <div class="w-full max-w-md relative z-10">
            <div
                class="bg-white/90 backdrop-blur-sm rounded-3xl shadow-xl overflow-hidden transition-all duration-300 hover:shadow-2xl">
                <!-- Header -->
                <div class="bg-gradient-to-r from-blue-600 to-indigo-700 p-6 text-center relative">
                    <div class="absolute top-0 left-0 w-full h-full opacity-20 flex items-center justify-center">
                        <i class="fas fa-user-lock text-7xl text-white"></i>
                    </div>
                    <h1 class="text-2xl font-bold text-white relative z-10">Welcome Back</h1>
                    <p class="text-blue-100 relative z-10 mt-1">Sign in to your account</p>
                </div>

                <!-- Form -->
                <form method="POST" action="{{ route('login') }}" class="p-6 space-y-6" x-data="{ showPassword: false }">
                    @csrf

                    <!-- Session Status -->
                    @if (session('status'))
                        <div class="mb-4 font-medium text-sm text-green-600 bg-green-50 p-3 rounded-lg">
                            <i class="fas fa-check-circle mr-2"></i> {{ session('status') }}
                        </div>
                    @endif

                    <!-- Validation Errors -->
                    @if ($errors->any())
                        <div class="mb-4 bg-red-50 p-3 rounded-lg">
                            <div class="font-medium text-red-600 flex items-center">
                                <i class="fas fa-exclamation-circle mr-2"></i>
                                {{ __('Whoops! Something went wrong.') }}
                            </div>
                            <ul class="mt-2 list-disc list-inside text-sm text-red-600 space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Email Field -->
                    <div
                        class="space-y-2 transition-all duration-300 hover:transform hover:-translate-y-1 hover:shadow-md">
                        <label for="email" class="text-sm font-semibold text-gray-700 flex items-center">
                            <i class="fas fa-envelope mr-2 text-blue-600"></i>
                            Email Address
                        </label>
                        <div class="relative">
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required
                                autofocus autocomplete="username"
                                class="w-full px-4 py-3 pl-12 border border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 focus:outline-none transition-all duration-300 bg-white"
                                placeholder="Enter your email">
                            <i class="fas fa-at absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        </div>
                    </div>

                    <!-- Password Field -->
                    <div
                        class="space-y-2 transition-all duration-300 hover:transform hover:-translate-y-1 hover:shadow-md">
                        <label for="password" class="text-sm font-semibold text-gray-700 flex items-center">
                            <i class="fas fa-lock mr-2 text-blue-600"></i>
                            Password
                        </label>
                        <div class="relative">
                            <input id="password" :type="showPassword ? 'text' : 'password'" name="password" required
                                autocomplete="current-password"
                                class="w-full px-4 py-3 pl-12 pr-12 border border-gray-200 rounded-xl focus:border-blue-500 focus:ring-2 focus:ring-blue-200 focus:outline-none transition-all duration-300 bg-white"
                                placeholder="Enter your password">
                            <i class="fas fa-key absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            <button type="button" @click="showPassword = !showPassword"
                                class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-blue-600 transition-colors duration-300"
                                :aria-label="showPassword ? 'Hide password' : 'Show password'">
                                <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'" class="text-lg"></i>
                            </button>
                        </div>
                    </div>





                    <!-- Remember Me & Forgot Password -->
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <label class="flex items-center cursor-pointer group">
                            <input type="checkbox" name="remember"
                                class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-600">
                            <span class="ml-2 text-sm text-gray-600 group-hover:text-blue-600 transition-colors">
                                Remember me
                            </span>
                        </label>
                        <a href="{{ route('password.request') }}"
                            class="text-sm text-blue-600 hover:text-gray-600 transition-colors">
                            Forgot password?
                        </a>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit"
                        class="w-full bg-gradient-to-r from-blue-600 to-indigo-700 text-white font-bold py-3 px-4 rounded-xl hover:shadow-lg transform hover:scale-[1.02] transition-all duration-300 flex items-center justify-center">
                        <span class="flex items-center">
                            <i class="fas fa-sign-in-alt mr-2"></i>
                            Sign In
                        </span>
                    </button>

                    <!-- Divider -->
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300"></div>
                        </div>
                        <div class="relative flex justify-center text-sm">
                            <span class="px-4 bg-white text-gray-500">Or continue with</span>
                        </div>
                    </div>

                    <!-- Social Buttons -->
                    @error('social')
                        <div class="mb-4 bg-red-50 p-3 rounded-lg">
                            <div class="font-medium text-red-600 flex items-center">
                                <i class="fas fa-exclamation-circle mr-2"></i>
                                {{ $message }}
                            </div>
                        </div>
                    @enderror
                    <div class="grid grid-cols-3 gap-3">
                        <a href="{{ route('login.google') }}"
                            class="p-3 rounded-xl bg-white border border-gray-200 transition-all duration-300 hover:border-blue-500 hover:shadow-md flex items-center justify-center"
                            aria-label="Login with Google">
                            <i class="fab fa-google text-red-500 text-lg"></i>
                        </a>
                        <a href="{{ route('login.facebook') }}"
                            class="p-3 rounded-xl bg-white border border-gray-200 transition-all duration-300 hover:border-blue-500 hover:shadow-md flex items-center justify-center"
                            aria-label="Login with Facebook">
                            <i class="fab fa-facebook text-blue-600 text-lg"></i>
                        </a>
                        <a href="{{ route('login.twitter') }}"
                            class="p-3 rounded-xl bg-white border border-gray-200 transition-all duration-300 hover:border-blue-500 hover:shadow-md flex items-center justify-center"
                            aria-label="Login with Twitter">
                            <i class="fab fa-twitter text-blue-400 text-lg"></i>
                        </a>
                    </div>

                    <!-- Sign Up Link -->
                    <p class="text-center text-sm text-gray-600">
                        Don't have an account?
                        <a href="{{ route('register') }}"
                            class="text-blue-600 font-semibold hover:text-gray-600 transition-colors">
                            Sign up now
                        </a>
                    </p>
                </form>
            </div>
        </div>
    </div>




    @if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif
</x-app-layout>
