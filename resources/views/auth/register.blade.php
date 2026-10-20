<x-app-layout>
    <div class="bk-shell grid gap-10 py-8 lg:grid-cols-[1fr_1fr] lg:gap-20 lg:py-14">
        <section class="max-w-xl lg:pt-8">
            <p class="bk-eyebrow">Learn. Build. Grow.</p>
            <h1 class="mt-4 font-display text-3xl font-bold leading-tight tracking-tight text-content sm:text-4xl">Your next skill starts here.</h1>
            <p class="mt-5 text-lg leading-8 text-content-muted">Create your BootKode learning account. Choose a course, build practical skills and keep your progress in one place.</p>
            <ol class="mt-8 divide-y divide-line">
                @foreach(['Find a course that matches your interests.', 'Learn through focused lessons and practice.', 'Return to your work and continue growing.'] as $step)
                    <li class="flex gap-4 py-4"><span class="font-bold text-brand">0{{ $loop->iteration }}</span><span class="text-content-muted">{{ $step }}</span></li>
                @endforeach
            </ol>
            <a href="{{ route('courses.index') }}" class="mt-6 inline-flex min-h-11 items-center font-bold text-brand underline">Explore courses first</a>
        </section>
        <section class="bk-panel p-5 sm:p-8" aria-labelledby="registration-heading">
            <h2 id="registration-heading" class="text-2xl font-bold">Create your account</h2>
            <p class="mt-2 text-sm text-content-muted">Already have an account? <a href="{{ route('login') }}" class="font-semibold text-brand underline">Sign in</a></p>
            <form method="POST" action="{{ route('register') }}" class="mt-7 space-y-5">
                @csrf
                @if($referralCode)<input type="hidden" name="referral_code" value="{{ $referralCode }}">@endif
                @foreach(['name' => ['Full name', 'text', 'name'], 'email' => ['Email address', 'email', 'email'], 'password' => ['Password', 'password', 'new-password'], 'password_confirmation' => ['Confirm password', 'password', 'new-password']] as $name => [$label, $type, $autocomplete])
                    <div>
                        <x-input-label :for="$name" :value="$label" />
                        <x-text-input :id="$name" :name="$name" :type="$type" :autocomplete="$autocomplete" :value="$type === 'password' ? null : old($name)" required class="mt-2" :aria-invalid="$errors->has($name) ? 'true' : 'false'" :aria-describedby="$name.'-error'" />
                        <x-input-error :id="$name.'-error'" :messages="$errors->get($name)" class="mt-2" />
                    </div>
                @endforeach
                <p class="text-sm text-content-muted">Use at least 8 characters for your password. You can complete your profile after signing in.</p>
                <x-input-error :messages="$errors->get('role')" />
                <x-input-error :messages="$errors->get('referral_code')" />
                <label class="flex items-start gap-3 text-sm text-content-muted"><input type="checkbox" name="terms" value="1" required class="mt-1 h-4 w-4 shrink-0 rounded border-line text-brand" @checked(old('terms'))><span>I confirm that I want to create a BootKode learning account.</span></label>
                <x-input-error :messages="$errors->get('terms')" />
                <x-primary-button class="w-full">Create learning account <span aria-hidden="true">→</span></x-primary-button>
            </form>
        </section>
    </div>
</x-app-layout>
