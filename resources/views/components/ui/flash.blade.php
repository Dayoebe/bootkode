@props(['messages' => []])

<div class="space-y-3" aria-live="polite" aria-atomic="true">
    @foreach(['message' => 'Success', 'success' => 'Success', 'error' => 'Unable to complete', 'warning' => 'Please check', 'info' => 'Information'] as $key => $label)
        @php($notice = $messages[$key] ?? session($key))
        @if(filled($notice))
            <div @class(['bk-feedback mb-4', 'bk-feedback-error' => $key === 'error']) role="{{ $key === 'error' ? 'alert' : 'status' }}">
                <span class="font-bold">{{ $label }}:</span> {{ $notice }}
            </div>
        @endif
    @endforeach
</div>
