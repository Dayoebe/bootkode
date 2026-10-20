<div class="space-y-3" aria-live="polite" aria-atomic="true">
    @foreach(['success' => 'Success', 'error' => 'Unable to complete', 'warning' => 'Please check', 'info' => 'Information'] as $key => $label)
        @if(session()->has($key))
            <div @class(['bk-feedback mb-4', 'bk-feedback-error' => $key === 'error']) role="{{ $key === 'error' ? 'alert' : 'status' }}">
                <span class="font-bold">{{ $label }}:</span> {{ session($key) }}
            </div>
        @endif
    @endforeach
</div>
