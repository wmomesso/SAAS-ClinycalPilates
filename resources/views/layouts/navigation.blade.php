<nav class="bg-white/80 dark:bg-gray-900/80 backdrop-blur-md border-b border-gray-100 dark:border-gray-800 sticky top-0 z-50">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="transition-transform hover:scale-105">
                        <x-application-logo class="block h-10 w-auto fill-current text-primary-600 dark:text-primary-400" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    @unless(auth()->user()->hasRole('super-admin'))
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" class="text-sm font-semibold">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    <x-nav-link :href="route('patients.index')" :active="request()->routeIs('patients.*')" class="text-sm font-semibold">
                        {{ __('Pacientes') }}
                    </x-nav-link>

                    <x-nav-link :href="route('appointments.index')" :active="request()->routeIs('appointments.*')" class="text-sm font-semibold">
                        {{ __('Agenda') }}
                    </x-nav-link>

                    <x-nav-link :href="route('health-insurances.index')" :active="request()->routeIs('health-insurances.*')" class="text-sm font-semibold">
                        {{ __('Convênios') }}
                    </x-nav-link>

                    <div class="hidden sm:flex sm:items-center sm:ms-2">
                        <x-dropdown align="left" width="48">
                            <x-slot name="trigger">
                                <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-semibold rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-900 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                                    <div>Financeiro</div>
                                    <div class="ms-1">
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </button>
                            </x-slot>
                            <x-slot name="content">
                                <x-dropdown-link :href="route('bank-accounts.index')">Contas Bancárias</x-dropdown-link>
                                <x-dropdown-link :href="route('payables.index')">Contas a Pagar</x-dropdown-link>
                                <x-dropdown-link :href="route('receivables.index')">Contas a Receber</x-dropdown-link>
                                <x-dropdown-link :href="route('invoices.index')">Faturamento</x-dropdown-link>
                            </x-slot>
                        </x-dropdown>
                    </div>
                    @endunless
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <div class="relative">
                    <button id="user-menu-button-nav" class="inline-flex items-center px-4 py-2 border border-transparent text-sm leading-4 font-bold rounded-xl text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-gray-800/50 hover:text-gray-900 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-800 focus:outline-none transition-all duration-200">
                        <div class="mr-2">
                            @if(Auth::user()->avatar_path)
                                <img id="user-avatar-nav" class="h-6 w-6 rounded-lg object-cover ring-2 ring-primary-500/20" src="{{ Auth::user()->avatar_url }}" alt="" data-custom-avatar="true">
                            @else
                                <div id="user-avatar-nav-placeholder" class="h-6 w-6 rounded-lg bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center ring-2 ring-primary-500/20">
                                    <span class="text-[10px] font-bold text-primary-700 dark:text-primary-400">
                                        {{ Auth::user()->initials }}
                                    </span>
                                </div>
                            @endif
                        </div>
                        <div>{{ Auth::user()->name }}</div>

                        <div class="ms-1">
                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </button>

                    <div id="user-dropdown-nav" class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 hidden z-50">
                        <div class="py-1">
                            <x-dropdown-link :href="route('profile.edit')" class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                {{ __('Perfil') }}
                            </x-dropdown-link>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault();
                                                    this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button id="mobile-menu-button" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path id="hamburger-icon" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path id="close-icon" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div id="responsive-menu" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            @unless(auth()->user()->hasRole('super-admin'))
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('patients.index')" :active="request()->routeIs('patients.*')">
                {{ __('Pacientes') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('appointments.index')" :active="request()->routeIs('appointments.*')">
                {{ __('Agenda') }}
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('health-insurances.index')" :active="request()->routeIs('health-insurances.*')">
                {{ __('Convênios') }}
            </x-responsive-nav-link>

            <div class="border-t border-gray-200 dark:border-gray-600 pt-2 pb-1">
                <div class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Financeiro</div>
                <x-responsive-nav-link :href="route('bank-accounts.index')" :active="request()->routeIs('bank-accounts.*')">Contas Bancárias</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('payables.index')" :active="request()->routeIs('payables.*')">Contas a Pagar</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('receivables.index')" :active="request()->routeIs('receivables.*')">Contas a Receber</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('invoices.index')" :active="request()->routeIs('invoices.*')">Faturamento</x-responsive-nav-link>
            </div>
            @endunless
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="flex items-center px-4">
                <div class="flex-shrink-0 mr-3">
                    @if(Auth::user()->avatar_path)
                        <img id="user-avatar-responsive" class="h-10 w-10 rounded-xl object-cover ring-2 ring-primary-500/20" src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->name }}" data-custom-avatar="true">
                    @else
                        <div id="user-avatar-responsive-placeholder" class="h-10 w-10 rounded-xl bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center ring-2 ring-primary-500/20">
                            <span class="text-sm font-bold text-primary-700 dark:text-primary-400">
                                {{ Auth::user()->initials }}
                            </span>
                        </div>
                    @endif
                </div>
                <div>
                    <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
