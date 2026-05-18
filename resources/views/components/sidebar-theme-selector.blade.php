<div
    x-data="{
        theme: localStorage.getItem('theme') || 'light',
        open: false,
        themes: ['light', 'dark', 'sepia', 'ocean', 'forest'],
        applyTheme(value) {
            this.themes.forEach((themeName) => document.documentElement.classList.remove(themeName));
            document.documentElement.classList.add(value);
        },
        setTheme(value) {
            this.theme = value;
            localStorage.setItem('theme', value);
            this.applyTheme(value);
            this.open = false;
            window.dispatchEvent(new CustomEvent('theme-changed', { detail: { theme: value } }));
        }
    }"
    x-init="applyTheme(theme)"
    class="relative"
>
    <button
        type="button"
        @click="open = !open"
        class="grid h-10 w-10 place-items-center rounded-[8px] bg-white/10 text-slate-200 transition hover:bg-white/15"
        aria-label="Toggle theme"
    >
        <i x-show="theme === 'light'" class="fas fa-sun text-amber-300 text-sm"></i>
        <i x-show="theme === 'dark'" class="fas fa-moon text-sky-300 text-sm"></i>
        <i x-show="theme === 'sepia'" class="fas fa-book text-amber-200 text-sm"></i>
        <i x-show="theme === 'ocean'" class="fas fa-water text-cyan-300 text-sm"></i>
        <i x-show="theme === 'forest'" class="fas fa-tree text-emerald-300 text-sm"></i>
    </button>

    <div
        x-show="open"
        @click.away="open = false"
        x-transition.origin.bottom.right
        class="absolute bottom-full right-0 mb-2 w-48 overflow-hidden rounded-[8px] border border-white/10 bg-slate-900 p-2 shadow-2xl shadow-black/30"
        style="display: none;"
    >
        @foreach ([
            ['key' => 'light', 'label' => 'Light', 'icon' => 'fas fa-sun', 'color' => 'text-amber-300'],
            ['key' => 'dark', 'label' => 'Dark', 'icon' => 'fas fa-moon', 'color' => 'text-sky-300'],
            ['key' => 'sepia', 'label' => 'Sepia', 'icon' => 'fas fa-book', 'color' => 'text-amber-200'],
            ['key' => 'ocean', 'label' => 'Ocean', 'icon' => 'fas fa-water', 'color' => 'text-cyan-300'],
            ['key' => 'forest', 'label' => 'Forest', 'icon' => 'fas fa-tree', 'color' => 'text-emerald-300'],
        ] as $item)
            <button
                type="button"
                @click="setTheme('{{ $item['key'] }}')"
                class="flex w-full items-center gap-3 rounded-[8px] px-3 py-2.5 text-left text-sm font-bold text-slate-300 transition hover:bg-white/10 hover:text-white"
                :class="{ 'bg-white/10 text-white': theme === '{{ $item['key'] }}' }"
            >
                <i class="{{ $item['icon'] }} {{ $item['color'] }} w-5"></i>
                <span>{{ $item['label'] }}</span>
                <i x-show="theme === '{{ $item['key'] }}'" class="fas fa-check ml-auto text-xs text-teal-300"></i>
            </button>
        @endforeach
    </div>
</div>
