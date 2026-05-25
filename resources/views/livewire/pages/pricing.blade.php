<div class="bk-edge-to-edge bg-slate-50">
    <section class="bg-slate-950 text-white">
        <div class="bk-shell grid gap-8 py-12 lg:grid-cols-[0.95fr_1.05fr] lg:items-center lg:py-16">
            <div>
                <span class="bk-eyebrow border-white/20 bg-white/10 text-white">Pricing & packages</span>
                <h1 class="bk-display mt-5 text-4xl font-black leading-tight text-white sm:text-6xl">BootKode packages</h1>
                <p class="mt-5 max-w-2xl text-base leading-7 text-slate-200 sm:text-lg">
                    Clear commercial paths for learners, career builders, institutions, and marketplace vendors, backed by receipts, invoices, refunds, payouts, and revenue reporting.
                </p>
                <div class="mt-7 flex flex-col gap-3 sm:flex-row">
                    <a href="{{ auth()->check() ? route(auth()->user()->getDashboardRouteName()) : route('register') }}" class="bk-primary-btn bg-white text-slate-950 hover:bg-slate-100">
                        {{ auth()->check() ? 'Open dashboard' : 'Start free' }}
                        <i class="fas fa-arrow-right text-sm"></i>
                    </a>
                    <a href="{{ route('contact') }}" class="bk-secondary-btn border-white/30 bg-white/10 text-white hover:bg-white/15">
                        Talk to BootKode
                    </a>
                </div>
            </div>
            <x-learning-visual variant="dark" label="Commercial command center" class="hidden md:block lg:ml-auto" />
        </div>
    </section>

    <section class="bg-white py-10 sm:py-14">
        <div class="bk-shell">
            <div class="grid gap-4 lg:grid-cols-4">
                @foreach($packages as $package)
                    <article class="rounded-[8px] border {{ $package['is_featured'] ? 'border-teal-700 shadow-lg shadow-teal-950/10' : 'border-slate-200' }} bg-white p-5">
                        @if($package['is_featured'])
                            <span class="inline-flex rounded-full bg-teal-50 px-3 py-1 text-xs font-black uppercase tracking-[0.12em] text-teal-800">Most useful</span>
                        @endif
                        <p class="mt-4 text-xs font-black uppercase tracking-[0.14em] text-slate-500">{{ $package['audience'] }}</p>
                        <h2 class="mt-2 text-xl font-black text-slate-950">{{ $package['name'] }}</h2>
                        <p class="mt-3 text-3xl font-black text-slate-950">{{ $package['formatted_price'] }}</p>
                        <p class="mt-3 min-h-20 text-sm leading-6 text-slate-600">{{ $package['description'] }}</p>
                        <ul class="mt-5 space-y-3 text-sm font-semibold text-slate-700">
                            @foreach($package['features'] as $feature)
                                <li class="flex items-start gap-2">
                                    <i class="fas fa-check mt-1 text-teal-700"></i>
                                    <span>{{ $feature }}</span>
                                </li>
                            @endforeach
                        </ul>
                        <a href="{{ $package['cta_url'] }}" class="mt-6 inline-flex w-full items-center justify-center rounded-[8px] {{ $package['is_featured'] ? 'bg-teal-700 text-white hover:bg-teal-800' : 'border border-slate-200 text-slate-900 hover:border-teal-300 hover:bg-teal-50' }} px-4 py-3 text-sm font-black transition">
                            {{ $package['cta_label'] }}
                        </a>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="border-y border-slate-200 bg-slate-50 py-10 sm:py-14">
        <div class="bk-shell grid gap-4 md:grid-cols-2 lg:grid-cols-4">
            @foreach([
                ['title' => 'Receipts', 'icon' => 'fa-receipt', 'copy' => 'Successful payments can produce account-visible receipts.'],
                ['title' => 'Invoices', 'icon' => 'fa-file-invoice', 'copy' => 'Orders and commercial packages have a document trail.'],
                ['title' => 'Refunds', 'icon' => 'fa-rotate-left', 'copy' => 'Refunds are recorded with reason, status, processor, and credit notes.'],
                ['title' => 'Payouts', 'icon' => 'fa-money-check-dollar', 'copy' => 'Instructor and vendor withdrawals keep an audit history.'],
            ] as $item)
                <article class="rounded-[8px] border border-slate-200 bg-white p-5">
                    <span class="grid h-11 w-11 place-items-center rounded-[8px] bg-slate-950 text-white">
                        <i class="fas {{ $item['icon'] }}"></i>
                    </span>
                    <h2 class="mt-4 font-black text-slate-950">{{ $item['title'] }}</h2>
                    <p class="mt-2 text-sm leading-6 text-slate-600">{{ $item['copy'] }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="bg-white py-10 sm:py-14">
        <div class="bk-shell flex flex-col gap-4 rounded-[8px] border border-slate-200 p-6 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-black text-slate-950">Need a school or corporate plan?</h2>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">
                    BootKode supports cohorts, bulk enrollment, license limits, course assignment, and completion reporting.
                </p>
            </div>
            <a href="{{ route('contact') }}" class="bk-primary-btn bg-slate-950 text-white hover:bg-slate-800">Request a plan</a>
        </div>
    </section>
</div>
