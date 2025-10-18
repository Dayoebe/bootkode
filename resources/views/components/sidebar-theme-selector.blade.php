<div x-data="{ 
    theme: localStorage.getItem('theme') || 'light',
    open: false,
    setTheme(newTheme) {
        this.theme = newTheme;
        localStorage.setItem('theme', newTheme);
        document.documentElement.className = newTheme;
        this.open = false;
        window.dispatchEvent(new CustomEvent('theme-changed', { detail: { theme: newTheme } }));
    }
}" 
x-init="document.documentElement.className = theme"
class="relative">
    <!-- Theme Toggle Button -->
    <button 
        @click="open = !open"
        class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
        aria-label="Toggle theme">
        <i x-show="theme === 'light'" class="fas fa-sun text-yellow-500 text-sm"></i>
        <i x-show="theme === 'dark'" class="fas fa-moon text-indigo-400 text-sm"></i>
        <i x-show="theme === 'sepia'" class="fas fa-book text-amber-600 text-sm"></i>
        <i x-show="theme === 'ocean'" class="fas fa-water text-cyan-500 text-sm"></i>
        <i x-show="theme === 'forest'" class="fas fa-tree text-green-600 text-sm"></i>
    </button>

    <!-- Dropdown Menu - Opens UPWARD -->
    <div 
        x-show="open"
        @click.away="open = false"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 transform translate-y-2"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 transform translate-y-0"
        x-transition:leave-end="opacity-0 transform translate-y-2"
        class="absolute bottom-full right-0 mb-2 w-48 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 py-2 z-50"
        style="display: none;">
        
        <div class="px-3 py-2 border-b border-gray-200 dark:border-gray-700">
            <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Theme</p>
        </div>

        <!-- Light Theme -->
        <button 
            @click="setTheme('light')"
            class="w-full flex items-center px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
            :class="{ 'bg-gray-100 dark:bg-gray-700': theme === 'light' }">
            <i class="fas fa-sun text-yellow-500 w-5"></i>
            <span class="ml-3 text-sm text-gray-700 dark:text-gray-300">Light</span>
            <i x-show="theme === 'light'" class="fas fa-check text-blue-600 dark:text-blue-400 ml-auto text-xs"></i>
        </button>

        <!-- Dark Theme -->
        <button 
            @click="setTheme('dark')"
            class="w-full flex items-center px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
            :class="{ 'bg-gray-100 dark:bg-gray-700': theme === 'dark' }">
            <i class="fas fa-moon text-indigo-400 w-5"></i>
            <span class="ml-3 text-sm text-gray-700 dark:text-gray-300">Dark</span>
            <i x-show="theme === 'dark'" class="fas fa-check text-blue-600 dark:text-blue-400 ml-auto text-xs"></i>
        </button>

        <!-- Sepia Theme -->
        <button 
            @click="setTheme('sepia')"
            class="w-full flex items-center px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
            :class="{ 'bg-gray-100 dark:bg-gray-700': theme === 'sepia' }">
            <i class="fas fa-book text-amber-600 w-5"></i>
            <span class="ml-3 text-sm text-gray-700 dark:text-gray-300">Sepia</span>
            <i x-show="theme === 'sepia'" class="fas fa-check text-blue-600 dark:text-blue-400 ml-auto text-xs"></i>
        </button>

        <!-- Ocean Theme -->
        <button 
            @click="setTheme('ocean')"
            class="w-full flex items-center px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
            :class="{ 'bg-gray-100 dark:bg-gray-700': theme === 'ocean' }">
            <i class="fas fa-water text-cyan-500 w-5"></i>
            <span class="ml-3 text-sm text-gray-700 dark:text-gray-300">Ocean</span>
            <i x-show="theme === 'ocean'" class="fas fa-check text-blue-600 dark:text-blue-400 ml-auto text-xs"></i>
        </button>

        <!-- Forest Theme -->
        <button 
            @click="setTheme('forest')"
            class="w-full flex items-center px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
            :class="{ 'bg-gray-100 dark:bg-gray-700': theme === 'forest' }">
            <i class="fas fa-tree text-green-600 w-5"></i>
            <span class="ml-3 text-sm text-gray-700 dark:text-gray-300">Forest</span>
            <i x-show="theme === 'forest'" class="fas fa-check text-blue-600 dark:text-blue-400 ml-auto text-xs"></i>
        </button>
    </div>
</div>