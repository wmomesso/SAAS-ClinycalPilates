<button {{ $attributes->merge(['type' => 'button', 'class' => 'ui-button-secondary text-xs uppercase tracking-wider']) }}>
    {{ $slot }}
</button>
