<div class="mt-4 space-y-4">
    @foreach($responses as $response)
        <section class="min-w-0 space-y-2 rounded-lg border border-line p-4" wire:key="response-result-{{ $response['id'] }}">
            <h3 class="break-words font-semibold text-content">{{ $response['question'] }}</h3>
            <p class="whitespace-pre-wrap break-words text-content">{{ $response['response'] }}</p>
            <p class="text-sm text-content-muted">{{ $response['pending_review'] ? 'Awaiting instructor review' : 'Recorded mark: '.$response['points'].' / '.($response['max_points'] ?? 'unavailable').' points' }}</p>
            @if($response['feedback'])<p class="whitespace-pre-wrap break-words text-content-muted">Instructor feedback: {{ $response['feedback'] }}</p>@endif
        </section>
    @endforeach
</div>
