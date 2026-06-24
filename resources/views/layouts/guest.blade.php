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

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .bg-primary-gradient {
            background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%);
        }
        .bg-primary-gradient-light {
            background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%);
        }
        .text-primary-gradient {
            background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .hover\:bg-primary-gradient:hover {
            background: linear-gradient(135deg, #0f766e 0%, #0d9488 100%);
        }
        .focus\:ring-primary-gradient:focus {
            --tw-ring-color: #0d9488;
        }
        .shadow-primary {
            box-shadow: 0 10px 25px -5px rgba(13, 148, 136, 0.3);
        }
        .shadow-primary-hover:hover {
            box-shadow: 0 20px 40px -5px rgba(13, 148, 136, 0.4);
        }
        .border-primary {
            border-color: #0d9488;
        }
        .dark .bg-dark-card {
            background-color: #1f2937;
        }
        .dark .border-dark-border {
            border-color: #374151;
        }
    </style>
</head>
<body class="font-sans text-gray-900 antialiased">
<div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-teal-50 via-white to-cyan-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
    <!-- Logo e Título -->
    <div class="mb-8 text-center">
        <div class="flex justify-center mb-4">
            <a href="/" class="block">
                <div class="w-20 h-20 bg-primary-gradient rounded-2xl flex items-center justify-center shadow-primary shadow-teal-500/30">
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
    <div class="w-full sm:max-w-md px-6 py-6 bg-white/95 dark:bg-gray-800/95 backdrop-blur-sm shadow-2xl rounded-2xl border border-gray-100 dark:border-gray-700 transition-all duration-300 hover:shadow-primary">
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
