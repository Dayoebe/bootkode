<div class="bk-edge-to-edge bg-slate-50">
    <section class="relative overflow-hidden bg-slate-950 text-white">
        <x-icon-field density="dense" class="opacity-20" />
        <div class="bk-shell relative grid gap-8 py-12 sm:py-16 lg:grid-cols-[0.95fr_1.05fr] lg:items-end lg:py-20">
            <div>
                <span class="bk-eyebrow border-white/15 bg-white/10 text-white">Contact</span>
                <h1 class="bk-display mt-4 text-3xl font-black leading-tight text-white sm:text-5xl">Talk to BootKode</h1>
                <p class="mt-4 max-w-2xl text-base leading-7 text-slate-300">
                    Send a message about admissions, courses, mentorship, certificates, partnerships, or support. We will route it to the right person.
                </p>
            </div>

            <div class="grid gap-3 sm:grid-cols-3">
                <a href="mailto:oyetoke.ebenezer@gmail.com" class="rounded-[8px] border border-white/10 bg-white/10 p-4 text-white transition hover:bg-white/15">
                    <i class="fas fa-envelope text-teal-300"></i>
                    <span class="mt-3 block text-sm font-black">Email</span>
                    <span class="mt-1 block truncate text-xs text-slate-300">oyetoke.ebenezer@gmail.com</span>
                </a>
                <a href="tel:+2349030036438" class="rounded-[8px] border border-white/10 bg-white/10 p-4 text-white transition hover:bg-white/15">
                    <i class="fas fa-phone text-sky-300"></i>
                    <span class="mt-3 block text-sm font-black">Phone</span>
                    <span class="mt-1 block text-xs text-slate-300">+234 903 003 6438</span>
                </a>
                <div class="rounded-[8px] border border-white/10 bg-white/10 p-4">
                    <i class="fas fa-location-dot text-rose-300"></i>
                    <span class="mt-3 block text-sm font-black">Base</span>
                    <span class="mt-1 block text-xs text-slate-300">Lagos, Nigeria</span>
                </div>
            </div>
        </div>
    </section>

    <section class="py-10 sm:py-14 lg:py-16">
        <div class="bk-shell grid gap-6 lg:grid-cols-[0.8fr_1.2fr]">
            <aside class="space-y-4">
                <div class="bk-card overflow-hidden">
                    <x-learning-visual label="Support studio" class="rounded-none border-0 shadow-none" />
                    <div class="p-5">
                        <h2 class="text-xl font-black text-slate-950">BootKode Support Desk</h2>
                        <p class="mt-1 text-sm font-bold text-teal-700">Admissions, courses, mentorship, and platform help</p>
                        <p class="mt-3 text-sm leading-6 text-slate-600">
                            BootKode is built around practical technology education, learner support, and career-focused skill development.
                        </p>
                    </div>
                </div>

                <div class="bk-soft-card p-5">
                    <h3 class="font-black text-slate-950">Best for</h3>
                    <div class="mt-4 grid gap-2">
                        @foreach (['Course guidance', 'Mentorship', 'Certificate questions', 'Partnerships', 'Technical support'] as $item)
                            <div class="flex items-center gap-3 rounded-[8px] bg-white px-3 py-3 text-sm font-bold text-slate-700">
                                <i class="fas fa-check text-teal-700"></i>
                                {{ $item }}
                            </div>
                        @endforeach
                    </div>
                </div>
            </aside>

            <div class="bk-card p-5 sm:p-6 lg:p-8">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <span class="bk-eyebrow">Message</span>
                        <h2 class="bk-display mt-3 text-3xl font-black text-slate-950">Send your request</h2>
                    </div>
                    <p class="text-sm font-semibold text-slate-500">Usually reviewed within 24 hours.</p>
                </div>

                @if (session('success'))
                    <div class="mt-6 rounded-[8px] border border-teal-200 bg-teal-50 px-4 py-3 text-sm font-semibold text-teal-900">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="mt-6 rounded-[8px] border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-900">
                        {{ session('error') }}
                    </div>
                @endif

                <form wire:submit.prevent="submit" class="mt-6 grid gap-5">
                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label for="name" class="text-sm font-black text-slate-800">Name</label>
                            <input wire:model.blur="name" id="name" type="text" class="bk-input mt-2" placeholder="Your full name" autocomplete="name">
                            @error('name') <p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="email" class="text-sm font-black text-slate-800">Email</label>
                            <input wire:model.blur="email" id="email" type="email" class="bk-input mt-2" placeholder="you@example.com" autocomplete="email">
                            @error('email') <p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label for="subject" class="text-sm font-black text-slate-800">Subject</label>
                        <input wire:model.blur="subject" id="subject" type="text" class="bk-input mt-2" placeholder="What should we help with?">
                        @error('subject') <p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="message" class="text-sm font-black text-slate-800">Message</label>
                        <textarea wire:model.blur="message" id="message" rows="7" class="bk-input mt-2 resize-none" placeholder="Write the details here"></textarea>
                        @error('message') <p class="mt-2 text-sm font-semibold text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <button type="submit" class="bk-primary-btn w-full" wire:loading.attr="disabled">
                        <span wire:loading.remove>Send message</span>
                        <span wire:loading>Sending...</span>
                        <i class="fas fa-paper-plane text-sm" wire:loading.remove></i>
                        <i class="fas fa-spinner fa-spin text-sm" wire:loading></i>
                    </button>
                </form>
            </div>
        </div>
    </section>
</div>
