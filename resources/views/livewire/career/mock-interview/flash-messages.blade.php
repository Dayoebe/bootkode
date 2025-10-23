@if (session('message'))
    <div class="container mx-auto px-6 pt-4">
        <div class="bg-themed-secondary border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 px-4 py-3 rounded-lg relative animate-fade-in">
            <span class="block sm:inline">{{ session('message') }}</span>
        </div>
    </div>
@endif

@if (session('error'))
    <div class="container mx-auto px-6 pt-4">
        <div class="bg-themed-secondary border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 px-4 py-3 rounded-lg relative animate-fade-in">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    </div>
@endif