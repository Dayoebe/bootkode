<div class="min-h-screen bg-gray-50 dark:bg-gray-900 p-6">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 mb-6">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Mentor Resources</h1>
        <p class="text-gray-600 dark:text-gray-400">Tools and guides to help you be an effective mentor</p>
    </div>

    <!-- Category Tabs -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-4 mb-6">
        <div class="flex flex-wrap gap-2">
            @foreach($resources as $key => $category)
                <button wire:click="setActiveCategory('{{ $key }}')"
                    class="px-4 py-2 rounded-lg {{ $activeCategory === $key ? 'bg-blue-600 dark:bg-blue-500 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }}">
                    <i class="{{ $category['icon'] }} mr-2"></i>{{ $category['title'] }}
                </button>
            @endforeach
        </div>
    </div>

    <!-- Resources Grid -->
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($resources[$activeCategory]['items'] as $item)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 hover:shadow-xl transition-shadow">
                <div class="flex items-start space-x-4">
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                        <i
                            class="fas fa-{{ $item['type'] === 'document' ? 'file-alt' : ($item['type'] === 'video' ? 'play-circle' : 'link') }} text-blue-600 dark:text-blue-400"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-gray-900 dark:text-white mb-2">{{ $item['title'] }}</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">{{ $item['description'] }}</p>
                        <a href="{{ $item['url'] }}"
                            class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 text-sm font-medium">
                            View Resource <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>