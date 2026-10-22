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
                        @if($type === 'password')
                            <div x-data="{ visible: false }">
                                <div class="relative mt-2">
                                    <x-text-input :id="$name" :name="$name" :type="$type" x-bind:type="visible ? 'text' : 'password'" :autocomplete="$autocomplete" required class="pr-12" :aria-invalid="$errors->has($name) ? 'true' : 'false'" :aria-describedby="$name.'-error'" />
                                    <button type="button" class="absolute inset-y-0 right-0 inline-flex min-h-11 min-w-11 items-center justify-center rounded-r-lg text-brand hover:bg-surface-muted focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-brand" @click="visible = !visible" :aria-pressed="visible" :aria-label="visible ? 'Hide password' : 'Show password'">
                                        <svg x-show="!visible" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.5-6 9.75-6 9.75 6 9.75 6-3.5 6-9.75 6-9.75-6-9.75-6Z" />
                                            <circle cx="12" cy="12" r="3" />
                                        </svg>
                                        <svg x-show="visible" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m3 3 18 18M10.6 10.6a2 2 0 0 0 2.8 2.8M9.9 5.2A10.8 10.8 0 0 1 12 5c6.25 0 9.75 7 9.75 7a16.8 16.8 0 0 1-3.1 3.8M6.2 6.2C3.65 8.05 2.25 12 2.25 12s3.5 7 9.75 7c1.25 0 2.4-.28 3.45-.75" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        @else
                            <x-text-input :id="$name" :name="$name" :type="$type" :autocomplete="$autocomplete" :value="old($name)" required class="mt-2" :aria-invalid="$errors->has($name) ? 'true' : 'false'" :aria-describedby="$name.'-error'" />
                        @endif
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
