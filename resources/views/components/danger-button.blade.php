<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center gap-2 rounded-xl border border-transparent bg-red-600 px-4 py-2 text-xs font-semibold uppercase tracking-wider text-white shadow-sm shadow-red-500/20 transition hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900']) }}>
    {{ $slot }}
</button>
