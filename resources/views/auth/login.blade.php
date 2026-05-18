<x-app-layout>
    <div class="bk-auth-surface bk-edge-to-edge min-h-[calc(100svh-8rem)] px-4 py-10 sm:px-6 lg:px-10">
        <div class="bk-shell grid min-h-[640px] overflow-hidden rounded-[8px] border border-slate-200 bg-white shadow-2xl shadow-slate-950/10 lg:grid-cols-[0.95fr_1.05fr]">
            <section class="relative hidden bg-slate-950 text-white lg:block">
                <x-icon-field density="dense" class="opacity-25" />
                <div class="relative flex h-full flex-col justify-between gap-8 p-10">
                    <a href="{{ route('home') }}" class="inline-flex items-center gap-3">
                        <span class="grid h-11 w-11 place-items-center rounded-[8px] bg-white text-slate-950">
                            <i class="fas fa-code text-sm"></i>
                        </span>
                        <span>
                            <span class="block text-lg font-black">BootKode</span>
                            <span class="text-xs font-black uppercase text-teal-200">Academy</span>
                        </span>
                    </a>

                    <div>
                        <span class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-3 py-1 text-xs font-black text-teal-100">
                            <span class="h-1.5 w-1.5 rounded-full bg-teal-300"></span>
                            Learning workspace
                        </span>
                        <h1 class="bk-display mt-4 text-4xl font-black leading-tight">Continue your path with fewer distractions.</h1>
                        <p class="mt-4 max-w-md text-sm leading-6 text-slate-300">
                            Sign in to resume courses, messages, certificates, mentorship, and career tools from one focused workspace.
                        </p>
                    </div>

                    <x-learning-visual variant="dark" label="Sign-in launchpad" />

                    <div class="grid grid-cols-3 gap-3">
                        @foreach ([
                            ['label' => 'Courses', 'icon' => 'fa-book-open', 'class' => 'bg-blue-500'],
                            ['label' => 'Mentors', 'icon' => 'fa-user-graduate', 'class' => 'bg-emerald-500'],
                            ['label' => 'Proof', 'icon' => 'fa-certificate', 'class' => 'bg-rose-500'],
                        ] as $item)
                            <div class="rounded-[8px] border border-white/10 bg-white/10 p-3">
                                <span class="grid h-8 w-8 place-items-center rounded-[8px] {{ $item['class'] }} text-white">
                                    <i class="fas {{ $item['icon'] }} text-sm"></i>
                                </span>
                                <p class="mt-2 text-sm font-black">{{ $item['label'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            <section class="flex items-center px-5 py-8 sm:px-8 lg:px-12">
                <div class="mx-auto w-full max-w-md">
                    <div class="mb-8">
                        <span class="bk-eyebrow">Welcome back</span>
                        <h2 class="bk-display mt-4 text-3xl font-black text-slate-950">Sign in to BootKode</h2>
                        <p class="mt-2 text-sm leading-6 text-slate-600">Use your learning account to continue from where you stopped.</p>
                        <div class="mt-4 grid gap-2 sm:grid-cols-3">
                            @foreach ([
                                ['label' => 'Progress saved', 'icon' => 'fa-cloud-arrow-up', 'class' => 'bg-sky-500'],
                                ['label' => 'Mentor ready', 'icon' => 'fa-message', 'class' => 'bg-emerald-500'],
                                ['label' => 'Proof tracked', 'icon' => 'fa-certificate', 'class' => 'bg-rose-500'],
                            ] as $item)
                                <span class="bk-signal-line flex items-center gap-2 rounded-[8px] border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-black text-slate-700" style="--i: {{ $loop->index }}">
                                    <span class="grid h-7 w-7 shrink-0 place-items-center rounded-[8px] {{ $item['class'] }} text-white">
                                        <i class="fas {{ $item['icon'] }} text-[11px]"></i>
                                    </span>
                                    {{ $item['label'] }}
                                </span>
                            @endforeach
                        </div>
                    </div>

                    @if (session('status'))
                        <div class="mb-5 rounded-[8px] border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-900">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mb-5 rounded-[8px] border border-rose-200 bg-rose-50 px-4 py-3">
                            <h3 class="text-sm font-black text-rose-900">Something went wrong</h3>
                            <ul class="mt-2 list-inside list-disc space-y-1 text-sm text-rose-800">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @error('social')
                        <div class="mb-5 rounded-[8px] border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-900">
                            {{ $message }}
                        </div>
                    @enderror

                    <form method="POST" action="{{ route('login') }}" class="space-y-5">
                        @csrf

                        <div>
                            <label for="email" class="mb-2 block text-sm font-black text-slate-900">Email Address</label>
                            <input
                                id="email"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                required
                                autofocus
                                autocomplete="username"
                                class="bk-input rounded-[8px]"
                                placeholder="you@example.com"
                            >
                        </div>

                        <div>
                            <label for="password" class="mb-2 block text-sm font-black text-slate-900">Password</label>
                            <div class="relative">
                                <input
                                    id="password"
                                    type="password"
                                    name="password"
                                    required
                                    autocomplete="current-password"
                                    class="bk-input rounded-[8px] pr-12"
                                    placeholder="Enter your password"
                                >
                                <button
                                    type="button"
                                    onclick="togglePasswordVisibility('password', 'password-toggle-icon')"
                                    class="absolute right-2 top-1/2 grid h-10 w-10 -translate-y-1/2 place-items-center rounded-[8px] text-slate-500 transition hover:bg-slate-100 hover:text-slate-900"
                                    aria-label="Toggle password visibility"
                                >
                                    <i id="password-toggle-icon" class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <label class="inline-flex cursor-pointer items-center gap-2 text-sm font-semibold text-slate-600">
                                <input
                                    type="checkbox"
                                    name="remember"
                                    class="h-4 w-4 rounded border-slate-300 text-teal-700 focus:ring-teal-600"
                                >
                                Remember me
                            </label>
                            <a href="{{ route('password.request') }}" class="text-sm font-black text-slate-950 hover:text-teal-700">
                                Forgot password?
                            </a>
                        </div>

                        <button type="submit" class="bk-primary-btn w-full rounded-[8px]">
                            Sign In
                            <i class="fas fa-arrow-right text-sm"></i>
                        </button>

                        <p class="text-center text-sm font-semibold text-slate-600">
                            Don't have an account?
                            <a href="{{ route('register') }}" class="font-black text-slate-950 hover:text-teal-700">Create one</a>
                        </p>
                    </form>
                </div>
            </section>
        </div>
    </div>

    <script>
        function togglePasswordVisibility(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);

            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'fas fa-eye-slash';
                return;
            }

            input.type = 'password';
            icon.className = 'fas fa-eye';
        }
    </script>
</x-app-layout>
