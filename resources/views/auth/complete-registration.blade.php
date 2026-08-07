<x-guest-layout>
    <div class="text-center mb-6">
        <h2 class="text-xl font-bold text-gray-800 dark:text-white">Finalizar cadastro</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Crie sua senha para iniciar os 7 dias de teste grátis</p>
    </div>

    <form method="POST" action="{{ url()->full() }}" class="space-y-4">
        @csrf

        <div>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21h18M5 21V7l8-4v18M19 21V11l-6-4M9 9h1m-1 4h1m-1 4h1m4-4h1m-1 4h1"/>
                    </svg>
                </div>
                <x-text-input id="clinic_name"
                              class="block w-full pl-9 pr-3 py-2.5 text-sm border-2 border-gray-200 dark:border-gray-700 rounded-xl focus:border-primary focus:ring-primary dark:focus:border-primary dark:focus:ring-primary bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder:text-gray-400 dark:placeholder:text-gray-500"
                              type="text"
                              name="clinic_name"
                              :value="old('clinic_name')"
                              required
                              autocomplete="organization"
                              placeholder="Nome da clínica" />
            </div>
            <x-input-error :messages="$errors->get('clinic_name')" class="mt-1.5 text-xs" />
        </div>

        <div>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <x-text-input id="name"
                              class="block w-full pl-9 pr-3 py-2.5 text-sm border-2 border-gray-200 dark:border-gray-700 rounded-xl focus:border-primary focus:ring-primary dark:focus:border-primary dark:focus:ring-primary bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder:text-gray-400 dark:placeholder:text-gray-500"
                              type="text"
                              name="name"
                              :value="old('name')"
                              required
                              autofocus
                              autocomplete="name"
                              placeholder="Seu nome completo" />
            </div>
            <x-input-error :messages="$errors->get('name')" class="mt-1.5 text-xs" />
        </div>

        <div>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <x-text-input id="email"
                              class="block w-full pl-9 pr-3 py-2.5 text-sm border-2 border-gray-200 dark:border-gray-700 rounded-xl bg-gray-50 dark:bg-gray-900 text-gray-500 dark:text-gray-400"
                              type="email"
                              :value="$email"
                              disabled />
            </div>
        </div>

        <div>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <x-text-input id="password"
                              class="block w-full pl-9 pr-3 py-2.5 text-sm border-2 border-gray-200 dark:border-gray-700 rounded-xl focus:border-primary focus:ring-primary dark:focus:border-primary dark:focus:ring-primary bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder:text-gray-400 dark:placeholder:text-gray-500"
                              type="password"
                              name="password"
                              required
                              autocomplete="new-password"
                              placeholder="Sua senha" />
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-1.5 text-xs" />
        </div>

        <div>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </div>
                <x-text-input id="password_confirmation"
                              class="block w-full pl-9 pr-3 py-2.5 text-sm border-2 border-gray-200 dark:border-gray-700 rounded-xl focus:border-primary focus:ring-primary dark:focus:border-primary dark:focus:ring-primary bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder:text-gray-400 dark:placeholder:text-gray-500"
                              type="password"
                              name="password_confirmation"
                              required
                              autocomplete="new-password"
                              placeholder="Confirmar senha" />
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5 text-xs" />
        </div>

        <button type="submit"
                class="w-full flex justify-center items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-primary-500 to-primary-600 hover:from-primary-600 hover:to-primary-700 text-white rounded-xl text-sm font-semibold transition-all duration-200 shadow-md shadow-primary-500/25 hover:shadow-lg hover:shadow-primary-500/35 transform hover:scale-[1.01] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:focus:ring-offset-gray-800">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            {{ __('Criar conta e iniciar teste') }}
        </button>
    </form>
</x-guest-layout>
