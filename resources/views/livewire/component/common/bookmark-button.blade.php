<button 
    wire:click="toggleBookmark"
    class="flex items-center gap-2 {{ $isBookmarked ? 'text-orange-500' : 'text-gray-400 hover:text-orange-500' }} transition-colors"
    title="{{ $isBookmarked ? 'Remove from saved' : 'Save for later' }}"
>
    @if($size === 'sm')
        <i class="fas fa-bookmark text-sm {{ $isBookmarked ? 'fas' : 'far' }}"></i>
    @elseif($size === 'lg')
        <i class="fas fa-bookmark text-xl {{ $isBookmarked ? 'fas' : 'far' }}"></i>
    @else
        <i class="fas fa-bookmark {{ $isBookmarked ? 'fas' : 'far' }}"></i>
    @endif
    
    @if($showText)
        <span class="text-sm whitespace-nowrap">
            {{ $isBookmarked ? 'Saved' : 'Save' }}
        </span>
    @endif
</button>