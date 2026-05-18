@php
    $courseCount = \App\Models\Learning\Course::query()->where('is_published', true)->where('is_approved', true)->count();
    $lessonCount = \App\Models\Learning\Lesson::query()
        ->whereHas('section.course', fn ($query) => $query->where('is_published', true)->where('is_approved', true))
        ->count();
    $categoryCount = \App\Models\Learning\CourseCategory::query()->has('courses')->count();

    $courseUrl = auth()->check() ? route('student.course-catalog') : route('register');
@endphp

<footer class="bk-public-footer border-t border-slate-200 bg-white">
    <div class="bk-shell py-12 sm:py-14">
        <div class="grid gap-10 lg:grid-cols-[1.25fr_2fr_1fr] lg:items-start">
            <div>
                <a href="{{ url('/') }}" class="inline-flex items-center gap-3">
                    <span class="grid h-11 w-11 place-items-center rounded-[8px] bg-slate-950 text-white">
                        <i class="fas fa-code text-sm"></i>
                    </span>
                    <span>
                        <span class="block text-lg font-black text-slate-950">BootKode</span>
                        <span class="text-xs font-bold uppercase tracking-[0.18em] text-teal-700">Academy</span>
                    </span>
                </a>

                <p class="mt-4 max-w-sm text-sm leading-6 text-slate-600">
                    Africa-ready tech education with structured roadmaps, mentorship, verified certificates, and career-focused project evidence.
                </p>

                <div class="mt-5 grid grid-cols-3 gap-2">
                    <div class="rounded-[8px] bg-slate-50 p-3">
                        <p class="text-lg font-black text-slate-950">{{ number_format($courseCount) }}</p>
                        <p class="text-[11px] font-semibold text-slate-500">Courses</p>
                    </div>
                    <div class="rounded-[8px] bg-slate-50 p-3">
                        <p class="text-lg font-black text-slate-950">{{ number_format($lessonCount) }}</p>
                        <p class="text-[11px] font-semibold text-slate-500">Lessons</p>
                    </div>
                    <div class="rounded-[8px] bg-slate-50 p-3">
                        <p class="text-lg font-black text-slate-950">{{ number_format($categoryCount) }}</p>
                        <p class="text-[11px] font-semibold text-slate-500">Tracks</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-6 sm:grid-cols-4">
                <div>
                    <h3 class="text-xs font-black uppercase tracking-[0.16em] text-slate-400">Learn</h3>
                    <ul class="mt-4 space-y-3 text-sm font-semibold text-slate-600">
                        <li><a href="{{ $courseUrl }}" class="hover:text-teal-700">Courses</a></li>
                        <li><a href="{{ route('guideline') }}" class="hover:text-teal-700">Guidelines</a></li>
                        <li><a href="{{ route('certificate.verify') }}" class="hover:text-teal-700">Verify certificate</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-xs font-black uppercase tracking-[0.16em] text-slate-400">Platform</h3>
                    <ul class="mt-4 space-y-3 text-sm font-semibold text-slate-600">
                        <li><a href="{{ route('marketplace.browse') }}" class="hover:text-teal-700">Marketplace</a></li>
                        <li><a href="{{ route('blog.index') }}" class="hover:text-teal-700">Blog</a></li>
                        <li><a href="{{ route('statistics') }}" class="hover:text-teal-700">Statistics</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-xs font-black uppercase tracking-[0.16em] text-slate-400">Company</h3>
                    <ul class="mt-4 space-y-3 text-sm font-semibold text-slate-600">
                        <li><a href="{{ route('about') }}" class="hover:text-teal-700">About</a></li>
                        <li><a href="{{ route('contact') }}" class="hover:text-teal-700">Contact</a></li>
                        @auth
                            <li><a href="{{ route(auth()->user()->getDashboardRouteName()) }}" class="hover:text-teal-700">Dashboard</a></li>
                        @else
                            <li><a href="{{ route('login') }}" class="hover:text-teal-700">Log in</a></li>
                        @endauth
                    </ul>
                </div>

                <div>
                    <h3 class="text-xs font-black uppercase tracking-[0.16em] text-slate-400">Contact</h3>
                    <ul class="mt-4 space-y-3 text-sm font-semibold text-slate-600">
                        <li><a href="mailto:oyetoke.ebenezer@gmail.com" class="hover:text-teal-700">Email support</a></li>
                        <li><a href="tel:+2349030036438" class="hover:text-teal-700">Call BootKode</a></li>
                        <li><a href="{{ route('contact') }}" class="hover:text-teal-700">Send message</a></li>
                    </ul>
                </div>
            </div>

            <div class="rounded-[8px] bg-slate-950 p-5 text-white">
                <p class="text-xs font-black uppercase tracking-[0.16em] text-teal-200">Stay close</p>
                <h3 class="mt-2 text-xl font-black leading-tight">Get course updates and practical learning notes.</h3>
                <form action="{{ route('newsletter.subscribe') }}" method="POST" class="mt-5 space-y-3">
                    @csrf
                    <label for="footer-email" class="sr-only">Email address</label>
                    <input
                        id="footer-email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="you@example.com"
                        required
                        class="h-12 w-full rounded-[8px] border border-white/10 bg-white/10 px-4 text-sm text-white placeholder:text-slate-400 outline-none transition focus:border-teal-300 focus:ring-2 focus:ring-teal-300/30"
                    >
                    <button type="submit" class="h-12 w-full rounded-[8px] bg-white px-4 text-sm font-black text-slate-950 transition hover:bg-slate-100">
                        Subscribe
                    </button>
                </form>
            </div>
        </div>

        @if (session('success') || session('error'))
            <div class="mt-8 rounded-[8px] border {{ session('success') ? 'border-teal-200 bg-teal-50 text-teal-900' : 'border-red-200 bg-red-50 text-red-900' }} px-4 py-3 text-sm font-semibold">
                {{ session('success') ?? session('error') }}
            </div>
        @endif

        <div class="mt-10 flex flex-col gap-3 border-t border-slate-200 pt-6 text-sm text-slate-500 sm:flex-row sm:items-center sm:justify-between">
            <p>© {{ now()->year }} BootKode. All rights reserved.</p>
            <p class="font-semibold text-slate-600">Code. Certify. Conquer.</p>
        </div>
    </div>
</footer>
