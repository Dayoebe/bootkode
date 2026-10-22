@php($files = $attachmentLists[$question['id']] ?? [])
<div class="mt-4 space-y-3 border-t border-line pt-4" x-data="{ uploading: false, progress: 0 }" x-on:livewire-upload-start="uploading = true; progress = 0" x-on:livewire-upload-finish="uploading = false" x-on:livewire-upload-error="uploading = false" x-on:livewire-upload-progress="progress = $event.detail.progress">
    <h3 class="font-semibold text-content">Submission files</h3>
    <p id="file-help-{{ $question['id'] }}" class="break-words text-sm text-content-muted">Attach files one at a time. {{ $question['submission']['min_files'] }}–{{ $question['submission']['max_files'] }} files; up to {{ number_format($question['submission']['max_kb'] / 1024, 2) }} MiB per file under the current server limit. Allowed: {{ implode(', ', $question['submission']['extensions']) }}. Draft files are private to you; submitted files are also available to authorized course reviewers.</p>
    <x-input-label for="submission-file-{{ $question['id'] }}" value="Choose a submission file" />
    <input id="submission-file-{{ $question['id'] }}" wire:key="upload-{{ $examSessionId }}-{{ $question['id'] }}-{{ count($files) }}" type="file" wire:model="uploads.{{ $question['id'] }}" accept="{{ implode(',', array_map(fn ($extension) => '.'.$extension, $question['submission']['extensions'])) }}" class="block min-h-11 w-full min-w-0 text-sm text-content file:mr-3 file:rounded-lg file:border-line file:px-3 file:py-3 focus:ring-2 focus:ring-action" aria-describedby="file-help-{{ $question['id'] }} file-error-{{ $question['id'] }}" aria-invalid="{{ $errors->has('uploads.'.$question['id']) ? 'true' : 'false' }}" />
    <p x-show="uploading" x-cloak role="status" class="text-sm text-content-muted">Uploading temporary file… <span x-text="progress"></span>%</p>
    <x-input-error id="file-error-{{ $question['id'] }}" :messages="$errors->get('uploads.'.$question['id'])" />
    <x-secondary-button wire:click="attachFile({{ $question['id'] }})" x-bind:disabled="uploading" wire:loading.attr="disabled" wire:target="attachFile,removeFile">Attach file to draft</x-secondary-button>
    <ul class="space-y-3">
        @foreach($files as $file)
            <li wire:key="draft-file-{{ $file['id'] }}" class="flex flex-wrap items-center justify-between gap-3 rounded-lg border border-line p-3">
                <div class="min-w-0">@if($file['available'])<a href="{{ $file['url'] }}" class="break-all font-medium text-action underline">{{ $file['name'] }}</a>@else<p class="break-all text-content">{{ $file['name'] }} · File unavailable</p>@endif<p class="text-sm text-content-muted">{{ $file['size'] < 1024 ? $file['size'].' bytes' : number_format($file['size'] / 1024, 1).' KiB' }} · Attached to this draft</p></div>
                <x-secondary-button wire:click="removeFile({{ $question['id'] }}, '{{ $file['id'] }}')" wire:loading.attr="disabled" wire:target="attachFile,removeFile" :aria-label="'Remove '.$file['name'].' from draft'">Remove file</x-secondary-button>
            </li>
        @endforeach
    </ul>
</div>
