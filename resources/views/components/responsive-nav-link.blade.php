@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2.5 border-l-4 border-primary-500 dark:border-primary-400 text-start text-base font-semibold text-primary-700 dark:text-primary-300 bg-primary-50 dark:bg-primary-900/30 focus:outline-none focus:text-primary-800 dark:focus:text-primary-200 focus:bg-primary-100 dark:focus:bg-primary-900/40 focus:border-primary-600 dark:focus:border-primary-300 transition duration-150 ease-in-out'
            : 'block w-full ps-3 pe-4 py-2.5 border-l-4 border-transparent text-start text-base font-medium text-gray-600 dark:text-gray-400 hover:text-primary-700 dark:hover:text-primary-300 hover:bg-primary-50/70 dark:hover:bg-primary-900/20 hover:border-primary-200 dark:hover:border-primary-800 focus:outline-none focus:text-primary-700 dark:focus:text-primary-300 focus:bg-primary-50 dark:focus:bg-primary-900/20 focus:border-primary-300 dark:focus:border-primary-700 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
