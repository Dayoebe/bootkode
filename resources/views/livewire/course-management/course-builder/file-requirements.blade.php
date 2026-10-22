<div class="space-y-4 rounded-lg border border-line p-4">
    <h4 class="font-semibold text-content">File requirements</h4>
    <p class="text-sm text-content-muted">The current server accepts up to {{ number_format($serverLimitKb / 1024, 2) }} MiB per file. Each new attempt uses the smaller of this limit and your configured limit. Files are private and submitted work requires manual review.</p>
    <div class="grid gap-4 sm:grid-cols-3">
        <div><x-input-label for="{{ $prefix }}-file-size" value="Maximum file size (MiB)" /><x-text-input id="{{ $prefix }}-file-size" type="number" min="1" max="100" wire:model="maxFileSize" class="w-full" required aria-describedby="{{ $prefix }}-file-size-error" :aria-invalid="$errors->has('maxFileSize') ? 'true' : 'false'" /><x-input-error id="{{ $prefix }}-file-size-error" :messages="$errors->get('maxFileSize')" /></div>
        @if($project)
            <div><x-input-label for="{{ $prefix }}-min-files" value="Minimum files" /><x-text-input id="{{ $prefix }}-min-files" type="number" min="0" max="20" wire:model="minFiles" class="w-full" required aria-describedby="{{ $prefix }}-min-error" :aria-invalid="$errors->has('minFiles') ? 'true' : 'false'" /><x-input-error id="{{ $prefix }}-min-error" :messages="$errors->get('minFiles')" /></div>
            <div><x-input-label for="{{ $prefix }}-max-files" value="Maximum files" /><x-text-input id="{{ $prefix }}-max-files" type="number" min="1" max="20" wire:model="maxFiles" class="w-full" required aria-describedby="{{ $prefix }}-max-error" :aria-invalid="$errors->has('maxFiles') ? 'true' : 'false'" /><x-input-error id="{{ $prefix }}-max-error" :messages="$errors->get('maxFiles')" /></div>
        @else<p class="text-sm text-content-muted sm:col-span-2">An assignment response can include one optional file. Required questions need a written response or a permitted file.</p>@endif
    </div>
    <p class="break-words text-sm text-content-muted">Leave the type list empty to allow the supported types: {{ implode(', ', $supportedExtensions) }}.</p>
    @foreach($fileTypes as $index => $extension)
        <div wire:key="{{ $prefix }}-extension-{{ $index }}" class="flex flex-wrap items-end gap-3">
            <div class="min-w-0 flex-1"><x-input-label for="{{ $prefix }}-file-type-{{ $index }}" :value="'Allowed file type '.($index + 1)" /><x-text-input id="{{ $prefix }}-file-type-{{ $index }}" wire:model="fileTypes.{{ $index }}" class="w-full" placeholder="pdf" aria-describedby="{{ $prefix }}-type-error-{{ $index }}" :aria-invalid="$errors->has('fileTypes.'.$index) ? 'true' : 'false'" /><x-input-error id="{{ $prefix }}-type-error-{{ $index }}" :messages="$errors->get('fileTypes.'.$index)" /></div>
            <x-secondary-button wire:click="removeFileType({{ $index }})" :aria-label="'Remove allowed file type '.($index + 1)">Remove type</x-secondary-button>
        </div>
    @endforeach
    <x-input-error :messages="$errors->get('fileTypes')" />
    <x-secondary-button wire:click="addFileType">Add allowed file type</x-secondary-button>
</div>
