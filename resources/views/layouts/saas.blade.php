<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ config('app.name', 'Laravel') }} - @yield('title', 'Dashboard')</title>

    <script>
        window.userName = @json(Auth::user()->name ?? 'Usuário');
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <script>
        // Configuração inicial (apenas para evitar FOUC)
        (function() {
            // Dark mode
            if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }

            // Primary color
            const savedColor = localStorage.getItem('primary-color') || 'blue';
            document.documentElement.setAttribute('data-primary-color', savedColor);
        })();
    </script>

    <style>
        /* Color themes definitions - CSS Variables */
        :root {
            --color-primary: #3b82f6;
            --color-primary-50: #eff6ff;
            --color-primary-100: #dbeafe;
            --color-primary-200: #bfdbfe;
            --color-primary-300: #93c5fd;
            --color-primary-400: #60a5fa;
            --color-primary-500: #3b82f6;
            --color-primary-600: #2563eb;
            --color-primary-700: #1d4ed8;
            --color-primary-800: #1e40af;
            --color-primary-900: #1e3a8a;
        }

        @supports (color: color-mix(in srgb, black, white)) {
            :root {
                --color-primary: #3b82f6;
            }
        }

        [data-primary-color="blue"] {
            --color-primary: #3b82f6;
            --color-primary-50: #eff6ff;
            --color-primary-100: #dbeafe;
            --color-primary-200: #bfdbfe;
            --color-primary-300: #93c5fd;
            --color-primary-400: #60a5fa;
            --color-primary-500: #3b82f6;
            --color-primary-600: #2563eb;
            --color-primary-700: #1d4ed8;
            --color-primary-800: #1e40af;
            --color-primary-900: #1e3a8a;
        }

        [data-primary-color="emerald"] {
            --color-primary: #10b981;
            --color-primary-50: #ecfdf5;
            --color-primary-100: #d1fae5;
            --color-primary-200: #a7f3d0;
            --color-primary-300: #6ee7b7;
            --color-primary-400: #34d399;
            --color-primary-500: #10b981;
            --color-primary-600: #059669;
            --color-primary-700: #047857;
            --color-primary-800: #065f46;
            --color-primary-900: #064e3b;
        }

        [data-primary-color="purple"] {
            --color-primary: #a855f7;
            --color-primary-50: #faf5ff;
            --color-primary-100: #f3e8ff;
            --color-primary-200: #e9d5ff;
            --color-primary-300: #d8b4fe;
            --color-primary-400: #c084fc;
            --color-primary-500: #a855f7;
            --color-primary-600: #9333ea;
            --color-primary-700: #7e22ce;
            --color-primary-800: #6b21a5;
            --color-primary-900: #581c87;
        }

        [data-primary-color="rose"] {
            --color-primary: #f43f5e;
            --color-primary-50: #fff1f2;
            --color-primary-100: #ffe4e6;
            --color-primary-200: #fecdd3;
            --color-primary-300: #fda4af;
            --color-primary-400: #fb7185;
            --color-primary-500: #f43f5e;
            --color-primary-600: #e11d48;
            --color-primary-700: #be123c;
            --color-primary-800: #9f1239;
            --color-primary-900: #881337;
        }

        [data-primary-color="amber"] {
            --color-primary: #f59e0b;
            --color-primary-50: #fffbeb;
            --color-primary-100: #fef3c7;
            --color-primary-200: #fde68a;
            --color-primary-300: #fcd34d;
            --color-primary-400: #fbbf24;
            --color-primary-500: #f59e0b;
            --color-primary-600: #d97706;
            --color-primary-700: #b45309;
            --color-primary-800: #92400e;
            --color-primary-900: #78350f;
        }

        /* Utility classes for dynamic colors */
        .bg-primary-50 { background-color: var(--color-primary-50, #eff6ff); }
        .bg-primary-100 { background-color: var(--color-primary-100, #dbeafe); }
        .bg-primary-200 { background-color: var(--color-primary-200, #bfdbfe); }
        .bg-primary-300 { background-color: var(--color-primary-300, #93c5fd); }
        .bg-primary-400 { background-color: var(--color-primary-400, #60a5fa); }
        .bg-primary-500 { background-color: var(--color-primary-500, #3b82f6); }
        .bg-primary-600 { background-color: var(--color-primary-600, #2563eb); }
        .bg-primary-700 { background-color: var(--color-primary-700, #1d4ed8); }
        .bg-primary-800 { background-color: var(--color-primary-800, #1e40af); }
        .bg-primary-900 { background-color: var(--color-primary-900, #1e3a8a); }

        .text-primary-50 { color: var(--color-primary-50, #eff6ff); }
        .text-primary-100 { color: var(--color-primary-100, #dbeafe); }
        .text-primary-200 { color: var(--color-primary-200, #bfdbfe); }
        .text-primary-300 { color: var(--color-primary-300, #93c5fd); }
        .text-primary-400 { color: var(--color-primary-400, #60a5fa); }
        .text-primary-500 { color: var(--color-primary-500, #3b82f6); }
        .text-primary-600 { color: var(--color-primary-600, #2563eb); }
        .text-primary-700 { color: var(--color-primary-700, #1d4ed8); }
        .text-primary-800 { color: var(--color-primary-800, #1e40af); }
        .text-primary-900 { color: var(--color-primary-900, #1e3a8a); }

        .border-primary-500 { border-color: var(--color-primary-500, #3b82f6); }
        .ring-primary-500 { --tw-ring-color: var(--color-primary-500, #3b82f6); }
        .focus\:ring-primary-500:focus { --tw-ring-color: var(--color-primary-500, #3b82f6); }

        .from-primary-500 { --tw-gradient-from: var(--color-primary-500, #3b82f6); }
        .to-primary-600 { --tw-gradient-to: var(--color-primary-600, #2563eb); }
        .from-primary-600 { --tw-gradient-from: var(--color-primary-600, #2563eb); }
        .to-primary-400 { --tw-gradient-to: var(--color-primary-400, #60a5fa); }

        .hover\:bg-primary-50:hover { background-color: var(--color-primary-50, #eff6ff); }
        .hover\:text-primary-600:hover { color: var(--color-primary-600, #2563eb); }
        .group-hover\:text-primary-500:hover { color: var(--color-primary-500, #3b82f6); }

        .dark .dark\:bg-primary-900\/20 { background-color: rgba(var(--color-primary-900, 30, 58, 138), 0.2); }
        .dark .dark\:text-primary-400 { color: var(--color-primary-400, #60a5fa); }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        .dark ::-webkit-scrollbar-track {
            background: #1f2937;
        }

        .dark ::-webkit-scrollbar-thumb {
            background: #4b5563;
        }

        .dark ::-webkit-scrollbar-thumb:hover {
            background: #6b7280;
        }

        /* Smooth transitions */
        * {
            transition: background-color 0.3s cubic-bezier(0.4, 0, 0.2, 1),
                        border-color 0.3s cubic-bezier(0.4, 0, 0.2, 1),
                        color 0.3s cubic-bezier(0.4, 0, 0.2, 1),
                        box-shadow 0.3s cubic-bezier(0.4, 0, 0.2, 1),
                        transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Fluid layout adjustments */
        .glass-panel {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .dark .glass-panel {
            background: rgba(17, 24, 39, 0.7);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* Modern scrollbar */
        ::-webkit-scrollbar {
            width: 5px;
            height: 5px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: rgba(156, 163, 175, 0.3);
            border-radius: 20px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: rgba(156, 163, 175, 0.5);
        }

        /* Animation */
        @keyframes fade-in-up {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in-up {
            animation: fade-in-up 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        /* Hover effects */
        .hover-lift {
            transition: transform 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .hover-lift:hover {
            transform: translateY(-2px);
        }

        @keyframes pulse-ring {
            0% {
                transform: scale(0.8);
                opacity: 0.5;
            }
            100% {
                transform: scale(1.2);
                opacity: 0;
            }
        }

        .color-theme-option.active {
            animation: pulse-ring 0.5s ease-out;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-950 font-sans antialiased">

{{-- Barra de Navegação Superior --}}
<nav class="fixed top-0 z-50 w-full glass-panel shadow-sm">
    <div class="px-4 py-3 lg:px-6 lg:pl-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                {{-- Botão menu mobile --}}
                <button
                    data-drawer-target="logo-sidebar"
                    data-drawer-toggle="logo-sidebar"
                    type="button"
                    class="inline-flex items-center justify-center p-2 rounded-xl text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 transition-all sm:hidden"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>

                {{-- Logo --}}
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2 group hover-lift">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-primary-500 to-primary-700 flex items-center justify-center shadow-lg shadow-primary-500/20 group-hover:shadow-primary-500/40 transition-all duration-300">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0"/>
                        </svg>
                    </div>
                    <span class="text-xl font-extrabold tracking-tight bg-gradient-to-r from-primary-600 to-primary-400 dark:from-primary-400 dark:to-primary-200 bg-clip-text text-transparent">
                        FisioGestor
                    </span>
                </a>
            </div>

            <div class="flex items-center gap-2">
                {{-- Color Theme Switcher --}}
                <div class="relative">
                    <button
                        id="color-theme-toggle"
                        class="p-2.5 text-gray-600 dark:text-gray-400 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                        </svg>
                    </button>

                    <div id="color-theme-dropdown"
                         class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 hidden z-50">
                        <div class="p-3 space-y-2">
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">Cores do Tema</p>

                            <button data-color="blue" class="color-theme-option w-full flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                <div class="w-5 h-5 rounded-full bg-gradient-to-r from-blue-500 to-blue-600 shadow-md"></div>
                                <span class="text-sm text-gray-700 dark:text-gray-300 capitalize">Azul</span>
                            </button>

                            <button data-color="emerald" class="color-theme-option w-full flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                <div class="w-5 h-5 rounded-full bg-gradient-to-r from-emerald-500 to-emerald-600 shadow-md"></div>
                                <span class="text-sm text-gray-700 dark:text-gray-300 capitalize">Esmeralda</span>
                            </button>

                            <button data-color="purple" class="color-theme-option w-full flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                <div class="w-5 h-5 rounded-full bg-gradient-to-r from-purple-500 to-purple-600 shadow-md"></div>
                                <span class="text-sm text-gray-700 dark:text-gray-300 capitalize">Roxo</span>
                            </button>

                            <button data-color="rose" class="color-theme-option w-full flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                <div class="w-5 h-5 rounded-full bg-gradient-to-r from-rose-500 to-rose-600 shadow-md"></div>
                                <span class="text-sm text-gray-700 dark:text-gray-300 capitalize">Rosa</span>
                            </button>

                            <button data-color="amber" class="color-theme-option w-full flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                <div class="w-5 h-5 rounded-full bg-gradient-to-r from-amber-500 to-amber-600 shadow-md"></div>
                                <span class="text-sm text-gray-700 dark:text-gray-300 capitalize">Âmbar</span>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Dark Mode Toggle --}}
                <button id="theme-toggle"
                        class="p-2.5 text-gray-600 dark:text-gray-400 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                    <svg id="theme-toggle-dark-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"/>
                    </svg>
                    <svg id="theme-toggle-light-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" fill-rule="evenodd" clip-rule="evenodd"/>
                    </svg>
                </button>

                {{-- User Menu --}}
                <div class="relative ml-2">
                    <button id="user-menu-button"
                            class="flex items-center gap-2 p-1.5 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors focus:outline-none">
                        @if(Auth::user()->avatar_path)
                            <img id="user-avatar"
                                 class="w-8 h-8 rounded-xl object-cover ring-2 ring-primary-500/20"
                                 src="{{ Auth::user()->avatar_url }}"
                                 alt="user avatar"
                                 data-custom-avatar="true">
                        @else
                            <div id="user-avatar-placeholder" class="w-8 h-8 rounded-xl bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center ring-2 ring-primary-500/20">
                                <span class="text-xs font-bold text-primary-700 dark:text-primary-400">
                                    {{ Auth::user()->initials }}
                                </span>
                            </div>
                        @endif
                        <div class="hidden md:block text-left">
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                {{ Auth::user()->name }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-500">
                                {{ Auth::user()->email }}
                            </p>
                        </div>
                        <svg class="hidden md:block w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div id="user-dropdown"
                         class="absolute right-0 mt-2 w-64 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 hidden z-50">
                        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                {{ Auth::user()->name }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                {{ Auth::user()->email }}
                            </p>
                        </div>
                        <div class="p-2">
                            <a href="{{ route('profile.edit') }}"
                               class="flex items-center gap-3 px-3 py-2 text-sm text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                Meu Perfil
                            </a>
                            <a href="{{ route('clinic.settings') }}"
                               class="flex items-center gap-3 px-3 py-2 text-sm text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                Configurações
                            </a>
                        </div>
                        <div class="p-2 border-t border-gray-200 dark:border-gray-700">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                        class="w-full flex items-center gap-3 px-3 py-2 text-sm text-red-600 dark:text-red-400 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                    </svg>
                                    Sair
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>

{{-- Menu Lateral (Sidebar) --}}
<aside id="logo-sidebar" class="fixed top-0 left-0 z-40 w-64 h-screen pt-16 transition-transform -translate-x-full glass-panel sm:translate-x-0 shadow-xl" aria-label="Sidebar">
    <div class="h-full px-3 pb-4 overflow-y-auto">
        <div class="py-4">
            <div class="mb-6 px-3">
                <div class="relative group">
                    <input type="text" placeholder="Buscar..." class="w-full pl-10 pr-4 py-2.5 text-sm bg-gray-100/50 dark:bg-gray-800/50 border-0 rounded-2xl focus:ring-2 focus:ring-primary-500 dark:focus:ring-primary-400 transition-all text-gray-700 dark:text-gray-300 placeholder:text-gray-400 dark:placeholder:text-gray-500">
                    <svg class="absolute left-3 top-3 w-4 h-4 text-gray-400 dark:text-gray-500 group-focus-within:text-primary-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </div>

            <ul class="space-y-1.5">
                {{-- Dashboard - Item ativo (exemplo) --}}
                @role('super-admin')
                <li>
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 text-gray-700 dark:text-gray-300 rounded-2xl hover:bg-primary-50 dark:hover:bg-primary-900/30 hover:text-primary-600 dark:hover:text-primary-400 transition-all duration-300 group {{ request()->routeIs('admin.dashboard') ? 'bg-primary-50/80 dark:bg-primary-900/40 text-primary-600 dark:text-primary-400 shadow-sm ring-1 ring-primary-500/10' : '' }}">
                        <div class="flex items-center justify-center transition-transform group-hover:scale-110">
                            <svg class="w-5 h-5 transition-colors duration-200 {{ request()->routeIs('admin.dashboard') ? 'text-primary-500' : 'text-gray-400 dark:text-gray-500 group-hover:text-primary-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                        <span class="font-semibold text-sm">Admin SAAS</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.clinics.index') }}" class="flex items-center gap-3 px-4 py-3 text-gray-700 dark:text-gray-300 rounded-2xl hover:bg-primary-50 dark:hover:bg-primary-900/30 hover:text-primary-600 dark:hover:text-primary-400 transition-all duration-300 group {{ request()->routeIs('admin.clinics.*') ? 'bg-primary-50/80 dark:bg-primary-900/40 text-primary-600 dark:text-primary-400 shadow-sm ring-1 ring-primary-500/10' : '' }}">
                        <div class="flex items-center justify-center transition-transform group-hover:scale-110">
                            <svg class="w-5 h-5 transition-colors duration-200 {{ request()->routeIs('admin.clinics.*') ? 'text-primary-500' : 'text-gray-400 dark:text-gray-500 group-hover:text-primary-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <span class="font-semibold text-sm">Gerenciar Clínicas</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('plans.index') }}" class="flex items-center gap-3 px-4 py-3 text-gray-700 dark:text-gray-300 rounded-2xl hover:bg-primary-50 dark:hover:bg-primary-900/30 hover:text-primary-600 dark:hover:text-primary-400 transition-all duration-300 group {{ request()->routeIs('plans.*') ? 'bg-primary-50/80 dark:bg-primary-900/40 text-primary-600 dark:text-primary-400 shadow-sm ring-1 ring-primary-500/10' : '' }}">
                        <div class="flex items-center justify-center transition-transform group-hover:scale-110">
                            <svg class="w-5 h-5 transition-colors duration-200 {{ request()->routeIs('plans.*') ? 'text-primary-500' : 'text-gray-400 dark:text-gray-500 group-hover:text-primary-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                            </svg>
                        </div>
                        <span class="font-semibold text-sm">Planos SAAS</span>
                    </a>
                </li>
                @endrole

                @unless(auth()->user()->hasRole('super-admin'))
                <li>
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 text-gray-700 dark:text-gray-300 rounded-2xl hover:bg-primary-50 dark:hover:bg-primary-900/30 hover:text-primary-600 dark:hover:text-primary-400 transition-all duration-300 group {{ request()->routeIs('dashboard') ? 'bg-primary-50/80 dark:bg-primary-900/40 text-primary-600 dark:text-primary-400 shadow-sm ring-1 ring-primary-500/10' : '' }}">
                        <div class="flex items-center justify-center transition-transform group-hover:scale-110">
                            <svg class="w-5 h-5 transition-colors duration-200 {{ request()->routeIs('dashboard') ? 'text-primary-500' : 'text-gray-400 dark:text-gray-500 group-hover:text-primary-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                        </div>
                        <span class="font-semibold text-sm">Dashboard</span>
                    </a>
                </li>

                {{-- Agenda --}}
                <li>
                    <a href="{{ route('appointments.index') }}" class="flex items-center gap-3 px-4 py-3 text-gray-700 dark:text-gray-300 rounded-2xl hover:bg-primary-50 dark:hover:bg-primary-900/30 hover:text-primary-600 dark:hover:text-primary-400 transition-all duration-300 group {{ request()->routeIs('appointments.*') ? 'bg-primary-50/80 dark:bg-primary-900/40 text-primary-600 dark:text-primary-400 shadow-sm ring-1 ring-primary-500/10' : '' }}">
                        <div class="flex items-center justify-center transition-transform group-hover:scale-110">
                            <svg class="w-5 h-5 transition-colors duration-200 {{ request()->routeIs('appointments.*') ? 'text-primary-500' : 'text-gray-400 dark:text-gray-500 group-hover:text-primary-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <span class="font-semibold text-sm">Agenda</span>
                    </a>
                </li>

                {{-- Pacientes --}}
                <li>
                    <a href="{{ route('patients.index') }}" class="flex items-center gap-3 px-4 py-3 text-gray-700 dark:text-gray-300 rounded-2xl hover:bg-primary-50 dark:hover:bg-primary-900/30 hover:text-primary-600 dark:hover:text-primary-400 transition-all duration-300 group {{ request()->routeIs('patients.*') ? 'bg-primary-50/80 dark:bg-primary-900/40 text-primary-600 dark:text-primary-400 shadow-sm ring-1 ring-primary-500/10' : '' }}">
                        <div class="flex items-center justify-center transition-transform group-hover:scale-110">
                            <svg class="w-5 h-5 transition-colors duration-200 {{ request()->routeIs('patients.*') ? 'text-primary-500' : 'text-gray-400 dark:text-gray-500 group-hover:text-primary-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                        </div>
                        <span class="font-semibold text-sm">Pacientes</span>
                    </a>
                </li>

                {{-- Salas --}}
                <li>
                    <a href="{{ route('rooms.index') }}" class="flex items-center gap-3 px-4 py-3 text-gray-700 dark:text-gray-300 rounded-2xl hover:bg-primary-50 dark:hover:bg-primary-900/30 hover:text-primary-600 dark:hover:text-primary-400 transition-all duration-300 group {{ request()->routeIs('rooms.*') ? 'bg-primary-50/80 dark:bg-primary-900/40 text-primary-600 dark:text-primary-400 shadow-sm ring-1 ring-primary-500/10' : '' }}">
                        <div class="flex items-center justify-center transition-transform group-hover:scale-110">
                            <svg class="w-5 h-5 transition-colors duration-200 {{ request()->routeIs('rooms.*') ? 'text-primary-500' : 'text-gray-400 dark:text-gray-500 group-hover:text-primary-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <span class="font-semibold text-sm">Salas</span>
                    </a>
                </li>

                {{-- Financeiro --}}
                <li>
                    <button type="button" class="flex items-center w-full gap-3 px-4 py-3 text-gray-700 dark:text-gray-300 rounded-2xl hover:bg-primary-50 dark:hover:bg-primary-900/30 hover:text-primary-600 dark:hover:text-primary-400 transition-all duration-300 group {{ request()->routeIs('invoices.*') || request()->routeIs('service-packages.*') || request()->routeIs('service-types.*') ? 'bg-primary-50/80 dark:bg-primary-900/40 text-primary-600 dark:text-primary-400' : '' }}" data-collapse-toggle="dropdown-financeiro">
                        <div class="flex items-center justify-center transition-transform group-hover:scale-110">
                            <svg class="w-5 h-5 transition-colors duration-200 {{ request()->routeIs('invoices.*') || request()->routeIs('service-packages.*') || request()->routeIs('service-types.*') ? 'text-primary-500' : 'text-gray-400 dark:text-gray-500 group-hover:text-primary-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <span class="font-semibold text-sm text-left flex-1">Financeiro</span>
                        <svg class="w-3 h-3 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <ul id="dropdown-financeiro" class="hidden py-2 space-y-1 ml-9">
                        <li>
                            <a href="{{ route('invoices.index') }}" class="flex items-center p-2 text-sm font-medium text-gray-600 dark:text-gray-400 rounded-xl hover:text-primary-600 dark:hover:text-primary-400 transition-colors {{ request()->routeIs('invoices.*') ? 'text-primary-600 font-bold' : '' }}">Faturamento</a>
                        </li>
                        <li>
                            <a href="{{ route('service-packages.index') }}" class="flex items-center p-2 text-sm font-medium text-gray-600 dark:text-gray-400 rounded-xl hover:text-primary-600 dark:hover:text-primary-400 transition-colors {{ request()->routeIs('service-packages.*') ? 'text-primary-600 font-bold' : '' }}">Pacotes</a>
                        </li>
                        <li>
                            <a href="{{ route('service-types.index') }}" class="flex items-center p-2 text-sm font-medium text-gray-600 dark:text-gray-400 rounded-xl hover:text-primary-600 dark:hover:text-primary-400 transition-colors {{ request()->routeIs('service-types.*') ? 'text-primary-600 font-bold' : '' }}">Tipos de Atendimento</a>
                        </li>
                    </ul>
                </li>
                @endunless

                @unless(auth()->user()->hasRole('super-admin'))
                {{-- Assinatura --}}
                <li>
                    <a href="{{ route('subscription.index') }}" class="flex items-center gap-3 px-4 py-3 text-gray-700 dark:text-gray-300 rounded-2xl hover:bg-primary-50 dark:hover:bg-primary-900/30 hover:text-primary-600 dark:hover:text-primary-400 transition-all duration-300 group {{ request()->routeIs('subscription.index') ? 'bg-primary-50/80 dark:bg-primary-900/40 text-primary-600 dark:text-primary-400 shadow-sm ring-1 ring-primary-500/10' : '' }}">
                        <div class="flex items-center justify-center transition-transform group-hover:scale-110">
                            <svg class="w-5 h-5 transition-colors duration-200 {{ request()->routeIs('subscription.index') ? 'text-primary-500' : 'text-gray-400 dark:text-gray-500 group-hover:text-primary-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                        </div>
                        <span class="font-semibold text-sm">Assinatura</span>
                    </a>
                </li>
                @endunless

                @unless(auth()->user()->hasRole('super-admin'))
                {{-- Usuários --}}
                <li>
                    <a href="{{ route('clinic-users.index') }}" class="flex items-center gap-3 px-4 py-3 text-gray-700 dark:text-gray-300 rounded-2xl hover:bg-primary-50 dark:hover:bg-primary-900/30 hover:text-primary-600 dark:hover:text-primary-400 transition-all duration-300 group {{ request()->routeIs('clinic-users.*') ? 'bg-primary-50/80 dark:bg-primary-900/40 text-primary-600 dark:text-primary-400 shadow-sm ring-1 ring-primary-500/10' : '' }}">
                        <div class="flex items-center justify-center transition-transform group-hover:scale-110">
                            <svg class="w-5 h-5 transition-colors duration-200 {{ request()->routeIs('clinic-users.*') ? 'text-primary-500' : 'text-gray-400 dark:text-gray-500 group-hover:text-primary-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                        </div>
                        <span class="font-semibold text-sm">Usuários</span>
                    </a>
                </li>
                @endunless

                {{-- Divisor --}}
                <li class="py-2">
                    <div class="mx-4 border-t border-gray-200/50 dark:border-gray-700/50"></div>
                </li>

                @unless(auth()->user()->hasRole('super-admin'))
                {{-- Configurações --}}
                <li>
                    <a href="{{ route('clinic.settings') }}" class="flex items-center gap-3 px-4 py-3 text-gray-700 dark:text-gray-300 rounded-2xl hover:bg-primary-50 dark:hover:bg-primary-900/30 hover:text-primary-600 dark:hover:text-primary-400 transition-all duration-300 group {{ request()->routeIs('clinic.settings') ? 'bg-primary-50/80 dark:bg-primary-900/40 text-primary-600 dark:text-primary-400 shadow-sm ring-1 ring-primary-500/10' : '' }}">
                        <div class="flex items-center justify-center transition-transform group-hover:scale-110">
                            <svg class="w-5 h-5 transition-colors duration-200 {{ request()->routeIs('clinic.settings') ? 'text-primary-500' : 'text-gray-400 dark:text-gray-500 group-hover:text-primary-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <span class="font-semibold text-sm">Configurações</span>
                    </a>
                </li>
                @endunless
            </ul>

            {{-- Rodapé da sidebar (opcional) --}}
            <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                <div class="px-3 py-2">
                    <div class="text-xs text-gray-500 dark:text-gray-500">
                        <span class="block">Versão 1.0.0</span>
                        <span class="block mt-1">© FisioGestor</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</aside>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleBtn = document.querySelector('[data-collapse-toggle="dropdown-financeiro"]');
        const dropdown = document.getElementById('dropdown-financeiro');

        if (toggleBtn && dropdown) {
            toggleBtn.addEventListener('click', function() {
                dropdown.classList.toggle('hidden');
                const icon = toggleBtn.querySelector('svg:last-child');
                if (icon) {
                    icon.classList.toggle('rotate-180');
                }
            });

            @if(request()->routeIs('invoices.*') || request()->routeIs('service-packages.*') || request()->routeIs('service-types.*'))
                dropdown.classList.remove('hidden');
                const icon = toggleBtn.querySelector('svg:last-child');
                if (icon) icon.classList.add('rotate-180');
            @endif
        }
    });
</script>

{{-- Área de Conteúdo Principal --}}
<div class="p-4 sm:ml-64 pt-20">
    {{-- Alertas e Notificações --}}
    @if (session('success') && !Session::has('alert.config'))
        <div id="alert-success" class="flex items-center p-4 mb-6 text-green-800 bg-green-50/80 backdrop-blur-sm border border-green-200 rounded-xl dark:bg-green-900/20 dark:text-green-400 dark:border-green-800/30 shadow-sm" role="alert">
            <div class="flex-shrink-0">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ms-3 text-sm font-medium flex-1">
                {{ session('success') }}
            </div>
            <button type="button" class="ms-auto -mx-1.5 -my-1.5 bg-green-50 text-green-500 rounded-lg focus:ring-2 focus:ring-green-400 p-1.5 hover:bg-green-200 inline-flex items-center justify-center h-8 w-8 dark:bg-green-900/20 dark:text-green-400 dark:hover:bg-green-800/30" data-dismiss-target="#alert-success" aria-label="Close">
                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                </svg>
            </button>
        </div>
    @endif

    @if ($errors->any())
        <div id="alert-error" class="flex items-center p-4 mb-6 text-red-800 bg-red-50/80 backdrop-blur-sm border border-red-200 rounded-xl dark:bg-red-900/20 dark:text-red-400 dark:border-red-800/30 shadow-sm" role="alert">
            <div class="flex-shrink-0">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ms-3 text-sm font-medium flex-1">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <button type="button" class="ms-auto -mx-1.5 -my-1.5 bg-red-50 text-red-500 rounded-lg focus:ring-2 focus:ring-red-400 p-1.5 hover:bg-red-200 inline-flex items-center justify-center h-8 w-8 dark:bg-red-900/20 dark:text-red-400 dark:hover:bg-red-800/30" data-dismiss-target="#alert-error" aria-label="Close">
                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                </svg>
            </button>
        </div>
    @endif

    {{-- Breadcrumb (opcional) --}}
    @hasSection('breadcrumb')
        <div class="mb-6">
            @yield('breadcrumb')
        </div>
    @endif

    {{-- O conteúdo da sua página será injetado aqui --}}
    <main class="animate-fade-in-up">
        @yield('content')
    </main>
</div>

@include('sweetalert::alert')

<script src="{{ asset('vendor/sweetalert/sweetalert.all.js') }}"></script>

<script>
    function confirmDelete(form, message = 'Tem certeza que deseja excluir este item?') {
        Swal.fire({
            title: 'Confirmação',
            text: message,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sim, excluir!',
            cancelButtonText: 'Cancelar',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
        return false;
    }
</script>

<script src="https://unpkg.com/imask"></script>
@stack('scripts')
@stack('modals')
</body>
</html>
