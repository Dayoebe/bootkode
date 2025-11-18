<footer class="bg-slate-950 text-white py-12 relative overflow-hidden" x-data="footer()" x-init="init()">

    <!-- Animated Background Elements -->
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-20 left-5 w-32 h-32 bg-emerald-500 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute top-1/3 right-10 w-40 h-40 bg-amber-500 rounded-full blur-3xl animate-bounce"
            style="animation-duration: 4s;"></div>
        <div class="absolute bottom-10 left-1/3 w-36 h-36 bg-cyan-500 rounded-full blur-3xl animate-pulse"
            style="animation-delay: 1s;"></div>
    </div>

    <div class="px-4 sm:px-6 lg:px-8 relative z-10">
        <!-- Main Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-6 lg:gap-8 mb-12">

            <!-- Brand Section -->
            <div class="lg:col-span-3 space-y-6">
                <div class="flex items-center hover:scale-105 transition-transform duration-300">
                    <div class="bg-emerald-600 p-2.5 rounded-lg shadow-lg">
                        <i class="fas fa-code text-white text-lg"></i>
                    </div>
                    <span class="ml-3 text-2xl font-bold text-emerald-400">
                        BootKode
                    </span>
                </div>
                <p class="text-slate-300 text-sm leading-relaxed">
                    Empowering Africa's youth with digital skills, mentorship, and career opportunities through
                    accessible tech education.
                </p>
                <div class="flex space-x-3">
                    <a href="#" class="social-icon" x-on:mouseenter="hoverSocial('facebook')">
                        <i class="fab fa-facebook-f text-sm"></i>
                    </a>
                    <a href="#" class="social-icon" x-on:mouseenter="hoverSocial('twitter')">
                        <i class="fab fa-twitter text-sm"></i>
                    </a>
                    <a href="#" class="social-icon" x-on:mouseenter="hoverSocial('linkedin')">
                        <i class="fab fa-linkedin-in text-sm"></i>
                    </a>
                    <a href="#" class="social-icon" x-on:mouseenter="hoverSocial('instagram')">
                        <i class="fab fa-instagram text-sm"></i>
                    </a>
                    <a href="#" class="social-icon" x-on:mouseenter="hoverSocial('youtube')">
                        <i class="fab fa-youtube text-sm"></i>
                    </a>
                </div>
                <div class="mt-4 p-3 bg-slate-800/50 rounded-lg border border-slate-700" x-show="activeSocial"
                    x-transition>
                    <p class="text-xs text-slate-300" x-text="socialMessage"></p>
                </div>
            </div>

            <!-- Categories Section -->
            <div class="lg:col-span-2">
                <h4 class="text-white font-bold mb-4 text-sm uppercase tracking-wider flex items-center">
                    <span class="w-1 h-4 bg-emerald-500 mr-2 rounded"></span>
                    Categories
                </h4>
                <ul class="space-y-2.5 text-xs">
                    @php
                        $popularCategories = $popularCategories ?? \App\Models\Learning\CourseCategory::withCount('courses')
                            ->orderBy('courses_count', 'desc')
                            ->take(5)
                            ->get();
                    @endphp

                    @forelse($popularCategories as $category)
                        <li>
                            <button wire:click="viewCategory({{ $category->id }})"
                                class="text-slate-300 hover:text-emerald-400 transition-colors duration-200">
                                {{ $category->name }}
                                <span class="ml-2 bg-slate-700 text-slate-300 px-2 py-0.5 rounded text-xs">
                                    {{ $category->courses_count ?? 0 }}
                                </span>
                            </button>
                        </li>
                    @empty
                        <li class="text-slate-500 text-xs">No categories available</li>
                    @endforelse
                </ul>
            </div>

            <!-- Explore Section -->
            <div class="lg:col-span-2">
                <h4 class="text-white font-bold mb-4 text-sm uppercase tracking-wider flex items-center">
                    <span class="w-1 h-4 bg-amber-500 mr-2 rounded"></span>
                    Explore
                </h4>
                <ul class="space-y-2.5 text-xs">
                    <li><a href="{{ route('about') }}"
                            class="text-slate-300 hover:text-amber-400 transition-colors">About Us</a></li>
                    <li><a href="{{ route('contact') }}"
                            class="text-slate-300 hover:text-amber-400 transition-colors">Contact Us</a></li>
                    <li><a href="{{ route('statistics') }}"
                            class="text-slate-300 hover:text-amber-400 transition-colors">Statistics</a></li>
                    <li><a href="{{ route('guideline') }}"
                            class="text-slate-300 hover:text-amber-400 transition-colors">Guideline</a></li>
                </ul>
            </div>

            <!-- Blog Section -->
            <div class="lg:col-span-2">
                <h4 class="text-white font-bold mb-4 text-sm uppercase tracking-wider flex items-center">
                    <span class="w-1 h-4 bg-cyan-500 mr-2 rounded"></span>
                    Blog
                </h4>
                <ul class="space-y-2.5 text-xs">
                    <li><a href="{{ route('blog.index') }}"
                            class="text-slate-300 hover:text-cyan-400 transition-colors">Home</a></li>
                    @forelse($topCategories as $category)
                        <li>
                            <a href="{{ route('blog.category', $category) }}"
                                class="text-slate-300 hover:text-cyan-400 transition-colors">
                                {{ $category->name }}
                                <span
                                    class="ml-1 text-xs bg-slate-700 px-2 py-0.5 rounded">{{ $category->published_posts_count }}</span>
                            </a>
                        </li>
                    @empty
                        <li class="text-slate-500 text-xs">No categories available</li>
                    @endforelse
                </ul>
            </div>

            <!-- Newsletter Section -->
            <div class="lg:col-span-3 space-y-4">
                <h4 class="text-white font-bold text-sm uppercase tracking-wider flex items-center">
                    <span class="w-1 h-4 bg-rose-500 mr-2 rounded"></span>
                    Stay Updated
                </h4>
                <p class="text-slate-300 text-xs">Subscribe for exclusive content.</p>

                <form action="{{ route('newsletter.subscribe') }}" method="POST" class="space-y-2"
                    x-data="{ email: '', submitted: false }" @submit="submitted = true">
                    @csrf
                    <div class="relative">
                        <input type="email" name="email" placeholder="Your email" x-model="email"
                            class="w-full px-3 py-2 rounded-lg bg-slate-800 border border-slate-700 text-white placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                            required value="{{ old('email') }}">
                    </div>
                    <button type="submit"
                        class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-medium px-4 py-2 rounded-lg transition-all duration-300 text-sm"
                        :class="submitted ? 'bg-emerald-700' : ''">
                        <span x-text="submitted ? '✓ Subscribed' : 'Subscribe'"></span>
                    </button>
                </form>

                <!-- Messages -->
                @if(session('success'))
                    <div class="text-xs p-2 rounded bg-emerald-900 text-emerald-200">
                        ✓ {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="text-xs p-2 rounded bg-rose-900 text-rose-200">
                        ✕ {{ session('error') }}
                    </div>
                @endif
            </div>
        </div>

        <!-- Stats Row -->
        <div class="grid grid-cols-3 gap-3 mb-8 lg:mb-12">
            <div
                class="p-3 bg-slate-800/50 rounded-lg border border-slate-700 text-center hover:border-emerald-500 transition-colors">
                <div class="text-xl font-bold text-emerald-400" x-text="stats.courses"></div>
                <div class="text-xs text-slate-400 mt-1">Courses</div>
            </div>
            <div
                class="p-3 bg-slate-800/50 rounded-lg border border-slate-700 text-center hover:border-amber-500 transition-colors">
                <div class="text-xl font-bold text-amber-400" x-text="stats.students"></div>
                <div class="text-xs text-slate-400 mt-1">Students</div>
            </div>
            <div
                class="p-3 bg-slate-800/50 rounded-lg border border-slate-700 text-center hover:border-cyan-500 transition-colors">
                <div class="text-xl font-bold text-cyan-400" x-text="stats.countries"></div>
                <div class="text-xs text-slate-400 mt-1">Countries</div>
            </div>
        </div>

        <!-- Divider -->
        <div class="h-px bg-slate-800 mb-6 lg:mb-8"></div>

        <!-- Bottom Section -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 text-xs text-slate-400 items-center">
            <div class="text-center sm:text-left">
                © <span x-text="currentYear"></span> BootKode. All rights reserved.
            </div>
            <div class="hidden lg:flex justify-center gap-6">
                <a href="#" class="hover:text-emerald-400 transition-colors">Privacy Policy</a>
                <a href="#" class="hover:text-emerald-400 transition-colors">Terms</a>
                <a href="#" class="hover:text-emerald-400 transition-colors">Cookies</a>
            </div>
            <div class="text-center sm:text-right">
                <button @click="scrollToTop()"
                    class="inline-block bg-emerald-600 hover:bg-emerald-700 text-white p-2 rounded-full transition-all"
                    x-show="showBackToTop" x-transition>
                    <i class="fas fa-arrow-up text-xs"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive footer links for mobile -->
    <div
        class="lg:hidden px-4 sm:px-6 flex justify-center gap-6 text-xs text-slate-400 mt-6 border-t border-slate-800 pt-6 relative z-10">
        <a href="#" class="hover:text-emerald-400 transition-colors">Privacy</a>
        <a href="#" class="hover:text-emerald-400 transition-colors">Terms</a>
        <a href="#" class="hover:text-emerald-400 transition-colors">Cookies</a>
    </div>
</footer>

<script>
    function footer() {
        return {
            currentYear: new Date().getFullYear(),
            activeSocial: false,
            socialMessage: '',
            showBackToTop: false,
            stats: {
                courses: '250+',
                students: '10K+',
                countries: '50+'
            },

            init() {
                window.addEventListener('scroll', () => {
                    this.showBackToTop = window.scrollY > 300;
                });
            },

            hoverSocial(platform) {
                this.activeSocial = true;
                const messages = {
                    facebook: 'Join our Facebook community',
                    twitter: 'Follow for daily tech tips',
                    linkedin: 'Connect with professionals',
                    instagram: 'See student projects',
                    youtube: 'Watch tutorials'
                };
                this.socialMessage = messages[platform];
                setTimeout(() => this.activeSocial = false, 3000);
            },

            scrollToTop() {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        }
    }
</script>

<style>
    .social-icon {
        @apply w-9 h-9 bg-slate-800 rounded-full flex items-center justify-center text-slate-400 hover:text-white transition-all duration-300 border border-slate-700 hover:border-emerald-500;
    }

    .social-icon:hover {
        @apply bg-emerald-600;
    }
</style>