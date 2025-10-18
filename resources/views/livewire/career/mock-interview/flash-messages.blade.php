@if (session('message'))
    <div class="container mx-auto px-6 pt-4">
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg relative animate-fade-in">
            <span class="block sm:inline">{{ session('message') }}</span>
        </div>
    </div>
@endif

@if (session('error'))
    <div class="container mx-auto px-6 pt-4">
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg relative animate-fade-in">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    </div>
@endif