<button {{ $attributes->merge(['type' => 'submit', 'class' => 'bk-button bk-button-danger']) }}>
    {{ $slot }}
</button>
