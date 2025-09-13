
{{-- resources/views/livewire/marketplace/partial/saved-drafts.blade.php --}}
<div class="space-y-6">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Saved Drafts</h2>
                <p class="text-gray-600">Items you're working on</p>
            </div>
            <a href="{{ route('marketplace.seller.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                <i class="fas fa-plus mr-2"></i>
                Create New
            </a>
        </div>
        
        @if($drafts->count() > 0)
            <div class="space-y-4">
                @foreach($drafts as $draft)
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="font-medium text-gray-900">{{ $draft->title }}</h3>
                                <p class="text-sm text-gray-500">Last modified {{ $draft->updated_at->format('M d, Y') }}</p>
                            </div>
                            <div class="flex space-x-2">
                                <a href="#" class="text-blue-600 hover:text-blue-700 text-sm">Edit</a>
                                <button class="text-red-600 hover:text-red-700 text-sm">Delete</button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8">
                <i class="fas fa-file-alt text-gray-300 text-4xl mb-4"></i>
                <p class="text-gray-500">No drafts saved yet</p>
            </div>
        @endif
    </div>
</div>
