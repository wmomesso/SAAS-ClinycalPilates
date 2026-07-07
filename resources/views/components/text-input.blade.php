@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'rounded-xl border-gray-200 bg-white text-gray-900 shadow-sm placeholder:text-gray-400 focus:border-primary-500 focus:ring-primary-500 disabled:cursor-not-allowed disabled:bg-gray-100 disabled:text-gray-500 dark:border-gray-700 dark:bg-gray-900/80 dark:text-gray-100 dark:placeholder:text-gray-500 dark:disabled:bg-gray-800']) }}>
