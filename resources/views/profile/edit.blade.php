@extends(request()->routeIs('profile.*') && !request()->has('from_app') ? 'layouts.saas' : 'layouts.app')

@section('content')
    <div class="py-12 animate-fade-in-up">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            {{-- Título da Página --}}
            <div class="mb-8">
                <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">
                    {{ __('Configurações de Perfil') }}
                </h1>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    Gerencie suas informações pessoais, segurança e conta em um só lugar.
                </p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {{-- Coluna da Esquerda: Informações de Perfil --}}
                <div class="lg:col-span-2 space-y-8">
                    <div class="bg-white dark:bg-gray-800 shadow-xl shadow-gray-200/50 dark:shadow-none border border-gray-100 dark:border-gray-700 rounded-3xl overflow-hidden transition-all duration-300 hover:shadow-2xl hover:shadow-primary-500/10">
                        <div class="p-6 sm:p-10">
                            @include('profile.partials.update-profile-information-form')
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 shadow-xl shadow-gray-200/50 dark:shadow-none border border-gray-100 dark:border-gray-700 rounded-3xl overflow-hidden transition-all duration-300 hover:shadow-2xl hover:shadow-primary-500/10">
                        <div class="p-6 sm:p-10">
                            @include('profile.partials.update-password-form')
                        </div>
                    </div>
                </div>

                {{-- Coluna da Direita: Perigo / Outras Ações --}}
                <div class="lg:col-span-1">
                    <div class="bg-red-50/50 dark:bg-red-900/10 border border-red-100 dark:border-red-900/30 rounded-3xl overflow-hidden sticky top-24">
                        <div class="p-6 sm:p-8">
                            <div class="flex items-center gap-3 mb-6 text-red-600 dark:text-red-400">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                                <h3 class="text-lg font-bold">Zona de Perigo</h3>
                            </div>
                            @include('profile.partials.delete-user-form')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
