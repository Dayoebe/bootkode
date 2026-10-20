@props(['title' => null])

<section {{ $attributes->class(['bk-panel p-5 sm:p-6']) }}>
    @if($title)<h2 class="mb-4 text-lg font-bold text-content">{{ $title }}</h2>@endif
    {{ $slot }}
</section>
