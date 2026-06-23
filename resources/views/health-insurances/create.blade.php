@extends('layouts.saas')

@section('title', 'Novo Convênio')

@section('breadcrumb')
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-primary-600 dark:text-gray-400 dark:hover:text-white">
                    Dashboard
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                    <a href="{{ route('health-insurances.index') }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-primary-600 md:ml-2 dark:text-gray-400 dark:hover:text-white">Convênios</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2 dark:text-gray-400">Novo Convênio</span>
                </div>
            </li>
        </ol>
    </nav>
@endsection

@section('content')
    <form action="{{ route('health-insurances.store') }}" method="POST" class="space-y-6">
        @csrf

        <div class="bg-white dark:bg-gray-800 shadow-md rounded-2xl p-6">
            <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4 border-b pb-2">Dados do Convênio</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <x-input-label for="name" :value="__('Nome do Convênio (Ex: Unimed)')" />
                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="registration_number" :value="__('Registro ANS')" />
                    <x-text-input id="registration_number" name="registration_number" type="text" class="mt-1 block w-full" :value="old('registration_number')" />
                    <x-input-error :messages="$errors->get('registration_number')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="company_name" :value="__('Razão Social')" />
                    <x-text-input id="company_name" name="company_name" type="text" class="mt-1 block w-full" :value="old('company_name')" />
                    <x-input-error :messages="$errors->get('company_name')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="cnpj" :value="__('CNPJ')" />
                    <x-text-input id="cnpj" name="cnpj" type="text" class="mt-1 block w-full" :value="old('cnpj')" />
                    <x-input-error :messages="$errors->get('cnpj')" class="mt-2" />
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-md rounded-2xl p-6">
            <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4 border-b pb-2">Contato e Endereço</h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <x-input-label for="email" :value="__('E-mail')" />
                    <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email')" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="phone" :value="__('Telefone')" />
                    <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone')" />
                    <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="zip_code" :value="__('CEP')" />
                    <x-text-input id="zip_code" name="zip_code" type="text" class="mt-1 block w-full" :value="old('zip_code')" />
                </div>

                <div class="md:col-span-2">
                    <x-input-label for="address" :value="__('Logradouro')" />
                    <x-text-input id="address" name="address" type="text" class="mt-1 block w-full" :value="old('address')" />
                </div>

                <div>
                    <x-input-label for="address_number" :value="__('Número')" />
                    <x-text-input id="address_number" name="address_number" type="text" class="mt-1 block w-full" :value="old('address_number')" />
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-md rounded-2xl p-6">
            <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4 border-b pb-2">Configurações de Faturamento</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <x-input-label :value="__('Tipos de Guias Aceitas')" />
                    <div class="mt-2 space-y-2">
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="accepted_guide_types[]" value="consulta" class="rounded border-gray-300 text-primary-600 shadow-sm focus:ring-primary-500">
                            <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Consulta</span>
                        </label>
                        <br>
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="accepted_guide_types[]" value="sp_sadt" class="rounded border-gray-300 text-primary-600 shadow-sm focus:ring-primary-500">
                            <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">SP/SADT (Sessões/Procedimentos)</span>
                        </label>
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <x-input-label for="payment_method" :value="__('Forma de Pagamento')" />
                        <select id="payment_method" name="payment_method" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 dark:focus:border-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 rounded-xl shadow-sm">
                            <option value="">Selecione...</option>
                            <option value="transfer">Transferência Bancária</option>
                            <option value="check">Cheque</option>
                            <option value="deposit">Depósito</option>
                            <option value="other">Outro</option>
                        </select>
                    </div>

                    <div>
                        <x-input-label for="payment_terms_days" :value="__('Prazo de Recebimento (dias)')" />
                        <x-text-input id="payment_terms_days" name="payment_terms_days" type="number" class="mt-1 block w-full" :value="old('payment_terms_days', 30)" />
                    </div>

                    <div>
                        <label class="inline-flex items-center">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300 text-primary-600 shadow-sm focus:ring-primary-500" {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                            <span class="ml-2 text-sm font-bold text-gray-700 dark:text-gray-300">Convênio Ativo</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-4">
            <a href="{{ route('health-insurances.index') }}" class="px-4 py-2 bg-white border border-gray-300 rounded-xl font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                Cancelar
            </a>
            <x-primary-button>
                Salvar Convênio
            </x-primary-button>
        </div>
    </form>
@endsection
