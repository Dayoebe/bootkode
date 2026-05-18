<div
    x-data="{
        theme: localStorage.getItem('theme') || 'light',
        showThemeMenu: false,
        themes: ['light', 'dark', 'sepia', 'ocean', 'forest'],
        applyTheme(value) {
            this.themes.forEach((themeName) => document.documentElement.classList.remove(themeName));
            document.documentElement.classList.add(value);
        },
        setTheme(value) {
            this.theme = value;
            localStorage.setItem('theme', value);
            this.applyTheme(value);
            this.showThemeMenu = false;
            window.dispatchEvent(new CustomEvent('theme-changed', { detail: { theme: value } }));
        }
    }"
    x-init="applyTheme(theme)"
    class="relative"
>
    <button
        type="button"
        @click="showThemeMenu = !showThemeMenu"
        class="grid h-11 w-11 place-items-center rounded-[8px] border border-themed-primary bg-themed-secondary text-themed-secondary transition hover:bg-themed-tertiary hover:text-themed-primary"
        title="Change Theme"
        aria-label="Change theme"
    >
        <i x-show="theme === 'light'" class="fas fa-sun text-amber-500"></i>
        <i x-show="theme === 'dark'" class="fas fa-moon text-sky-300"></i>
        <i x-show="theme === 'sepia'" class="fas fa-book text-amber-700"></i>
        <i x-show="theme === 'ocean'" class="fas fa-water text-sky-600"></i>
        <i x-show="theme === 'forest'" class="fas fa-tree text-emerald-600"></i>
    </button>

    <div
        x-show="showThemeMenu"
        @click.away="showThemeMenu = false"
        x-transition.origin.top.right
        class="absolute right-0 mt-3 w-56 overflow-hidden rounded-[8px] border border-themed-primary bg-themed-secondary p-2 shadow-2xl shadow-slate-950/10"
        style="display: none;"
    >
        <div class="px-3 py-2">
            <p class="text-xs font-black uppercase text-themed-tertiary">Theme</p>
        </div>

        @foreach ([
            ['key' => 'light', 'label' => 'Light', 'icon' => 'fas fa-sun', 'color' => 'text-amber-500'],
            ['key' => 'dark', 'label' => 'Dark', 'icon' => 'fas fa-moon', 'color' => 'text-sky-400'],
            ['key' => 'sepia', 'label' => 'Sepia', 'icon' => 'fas fa-book', 'color' => 'text-amber-700'],
            ['key' => 'ocean', 'label' => 'Ocean', 'icon' => 'fas fa-water', 'color' => 'text-sky-600'],
            ['key' => 'forest', 'label' => 'Forest', 'icon' => 'fas fa-tree', 'color' => 'text-emerald-600'],
        ] as $item)
            <button
                type="button"
                @click="setTheme('{{ $item['key'] }}')"
                class="flex w-full items-center gap-3 rounded-[8px] px-3 py-2.5 text-left text-sm font-bold text-themed-secondary transition hover:bg-themed-tertiary hover:text-themed-primary"
                :class="{ 'bg-themed-tertiary text-themed-primary': theme === '{{ $item['key'] }}' }"
            >
                <i class="{{ $item['icon'] }} {{ $item['color'] }} w-5"></i>
                <span>{{ $item['label'] }}</span>
                <i x-show="theme === '{{ $item['key'] }}'" class="fas fa-check ml-auto text-xs text-teal-600"></i>
            </button>
        @endforeach
    </div>
</div>
