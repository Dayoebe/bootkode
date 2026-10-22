<div class="min-h-screen bg-themed-primary transition-colors duration-300">
    <div
        class="bg-themed-secondary rounded-2xl shadow-lg p-6 mb-6 border border-themed-primary transition-colors duration-300">
        <h1 class="text-3xl font-bold text-themed-primary mb-2 transition-colors duration-300">Mentor Resources</h1>
        <p class="text-themed-secondary transition-colors duration-300">Tools and guides to help you be an effective
            mentor</p>
    </div>
    <p class="mb-6 text-sm text-content-muted">This catalogue lists planned resources. Published links will appear here when available.</p>
    <!-- Category Tabs -->
    <div
        class="bg-themed-secondary rounded-xl shadow-lg p-4 mb-6 border border-themed-primary transition-colors duration-300">
        <div class="flex flex-wrap gap-2">
            @foreach($resources as $key => $category)
                <button wire:click="setActiveCategory('{{ $key }}')"
                    class="min-h-11 px-4 py-2 rounded-lg transition-colors duration-300 {{ $activeCategory === $key ? 'bk-button bk-button-primary ' : 'bg-themed-tertiary text-themed-primary hover:bg-brand/20' }}">
                    <i class="{{ $category['icon'] }} mr-2"></i>{{ $category['title'] }}
                </button>
            @endforeach
        </div>
    </div>

    <!-- Resources Grid -->
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($resources[$activeCategory]['items'] as $item)
            <div
                class="bg-themed-secondary rounded-xl shadow-lg p-6 hover:shadow-xl transition-all duration-300 border border-themed-primary">
                <div class="flex items-start space-x-4">
                    <div
                        class="w-12 h-12 bg-brand/20 rounded-lg flex items-center justify-center flex-shrink-0 border border-brand/30 transition-colors duration-300">
                        <i
                            class="fas fa-{{ $item['type'] === 'document' ? 'file-alt' : ($item['type'] === 'video' ? 'play-circle' : 'link') }} text-brand transition-colors duration-300"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-semibold text-themed-primary mb-2 transition-colors duration-300">
                            {{ $item['title'] }}</h3>
                        <p class="text-sm text-themed-secondary mb-3 transition-colors duration-300">
                            {{ $item['description'] }}</p>
                        @if($item['url'] === '#')
                            <p class="text-sm text-content-muted">Resource not published yet.</p>
                        @else
                            <a href="{{ $item['url'] }}" class="inline-flex min-h-11 items-center text-brand underline">View resource</a>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>