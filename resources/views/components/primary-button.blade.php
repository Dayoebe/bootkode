<button {{ $attributes->merge(['type' => 'submit', 'class' => 'bk-button bk-button-primary']) }}>
    {{ $slot }}
</button>
