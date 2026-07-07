<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <script>
        (function() {
            if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            }

            document.documentElement.setAttribute('data-primary-color', localStorage.getItem('primary-color') || 'blue');
        })();
    </script>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-gray-900 antialiased">
<div class="min-h-screen flex flex-col sm:justify-center items-center px-4 pt-6 sm:pt-0 bg-[radial-gradient(circle_at_top_left,_rgb(var(--color-primary-100)/0.9),_transparent_34%),linear-gradient(135deg,_#f9fafb,_#ffffff_45%,_rgb(var(--color-primary-50)/0.8))] dark:bg-[radial-gradient(circle_at_top_left,_rgb(var(--color-primary-900)/0.28),_transparent_34%),linear-gradient(135deg,_#030712,_#111827_55%,_#030712)]">
    <!-- Logo e Título -->
    <div class="mb-8 text-center">
        <div class="flex justify-center mb-4">
            <a href="/" class="block">
                <div class="w-20 h-20 bg-primary-gradient rounded-2xl flex items-center justify-center shadow-primary">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </a>
        </div>
        <h1 class="text-2xl font-bold text-primary-gradient">
            {{ config('app.name', 'Laravel') }}
        </h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
            {{ __('Clínica de Fisioterapia & Estúdio de Pilates') }}
        </p>
    </div>

    <!-- Card -->
    <div class="w-full sm:max-w-md px-6 py-6 bg-white/95 dark:bg-gray-900/90 backdrop-blur-sm shadow-2xl shadow-gray-900/10 rounded-2xl border border-gray-100 dark:border-gray-800 transition-all duration-300">
        {{ $slot }}
    </div>

    <!-- Footer -->
    <div class="mt-6 text-center text-xs text-gray-400 dark:text-gray-500">
        {{ config('app.name', 'Laravel') }} &copy; {{ date('Y') }} - {{ __('Todos os direitos reservados') }}
    </div>
</div>

@include('sweetalert::alert')
</body>
</html>
