<x-app-layout>
    <div class="bk-shell grid gap-10 py-8 lg:grid-cols-2 lg:gap-20 lg:py-14">
        <section class="max-w-xl lg:pt-8">
            <p class="bk-eyebrow">Welcome back</p>
            <h1 class="mt-4 font-display text-3xl font-bold leading-tight tracking-tight sm:text-4xl">Pick up where you left off.</h1>
            <p class="mt-5 text-lg leading-8 text-content-muted">Sign in to return to your courses, review your progress and take your next step.</p>
            <div class="mt-8 border-l-2 border-brand pl-5"><p class="font-semibold">Learn. Build. Grow.</p><p class="mt-2 text-sm leading-6 text-content-muted">A little practice today moves your learning forward.</p></div>
            <a href="{{ route('courses.index') }}" class="mt-6 inline-flex min-h-11 items-center font-bold text-brand underline">Browse the course catalogue</a>
        </section>
        <section class="bk-panel p-5 sm:p-8" aria-labelledby="login-heading">
            <h2 id="login-heading" class="text-2xl font-bold">Sign in to BootKode</h2>
            <p class="mt-2 text-sm text-content-muted">New here? <a href="{{ route('register') }}" class="font-semibold text-brand underline">Create an account</a></p>
            <x-auth-session-status class="mt-5" :status="session('status')" />
            <x-input-error class="mt-5" :messages="$errors->get('social')" />
            <form method="POST" action="{{ route('login') }}" class="mt-7 space-y-5">
                @csrf
                <div>
                    <x-input-label for="email" value="Email address" />
                    <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" class="mt-2" :aria-invalid="$errors->has('email') ? 'true' : 'false'" aria-describedby="email-error" />
                    <x-input-error id="email-error" :messages="$errors->get('email')" class="mt-2" />
                </div>
                <div x-data="{ visible: false }">
                    <x-input-label for="password" value="Password" />
                    <div class="relative mt-2">
                        <x-text-input id="password" name="password" type="password" x-bind:type="visible ? 'text' : 'password'" autocomplete="current-password" required class="pr-20" :aria-invalid="$errors->has('password') ? 'true' : 'false'" aria-describedby="password-error" />
                        <button type="button" class="absolute inset-y-0 right-0 min-w-16 px-3 text-sm font-bold text-brand" @click="visible = !visible" :aria-pressed="visible" :aria-label="visible ? 'Hide password' : 'Show password'" x-text="visible ? 'Hide' : 'Show'">Show</button>
                    </div>
                    <x-input-error id="password-error" :messages="$errors->get('password')" class="mt-2" />
                </div>
                <div class="flex flex-wrap items-center justify-between gap-3 text-sm">
                    <label class="inline-flex min-h-11 items-center gap-2"><input type="checkbox" name="remember" class="h-4 w-4 rounded border-line text-brand" @checked(old('remember'))> Remember me</label>
                    <a href="{{ route('password.request') }}" class="inline-flex min-h-11 items-center font-bold text-brand underline">Forgot password?</a>
                </div>
                <x-primary-button class="w-full">Sign in <span aria-hidden="true">→</span></x-primary-button>
            </form>
        </section>
    </div>
</x-app-layout>
