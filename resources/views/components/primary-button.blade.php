<button {{ $attributes->merge(['type' => 'submit', 'class' => 'ui-button-primary text-xs uppercase tracking-wider']) }}>
    {{ $slot }}
</button>
