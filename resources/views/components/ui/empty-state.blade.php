@props(['title', 'description' => null])

<div {{ $attributes->class(['bk-panel flex flex-col items-start gap-3 border-dashed p-6 sm:p-8']) }}>
    <h2 class="text-lg font-bold text-content">{{ $title }}</h2>
    @if($description)<p class="max-w-xl text-sm leading-6 text-content-muted">{{ $description }}</p>@endif
    {{ $slot }}
</div>
