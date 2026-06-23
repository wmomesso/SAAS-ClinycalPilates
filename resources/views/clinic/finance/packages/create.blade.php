@extends('layouts.saas')

@section('title', 'Novo Pacote')

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
                    <a href="{{ route('service-packages.index') }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-primary-600 md:ml-2 dark:text-gray-400 dark:hover:text-white">Pacotes</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2 dark:text-gray-400">Novo Pacote</span>
                </div>
            </li>
        </ol>
    </nav>
@endsection

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-2xl p-6">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Cadastrar Novo Pacote</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Defina as regras e valores para o novo pacote de serviços.</p>
            </div>

            <form action="{{ route('service-packages.store') }}" method="POST" class="space-y-6">
                @csrf

                <div>
                    <x-input-label for="name" :value="__('Nome do Pacote')" />
                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required autofocus placeholder="Ex: Pacote Pilates 10 Sessões" />
                    <x-input-error class="mt-2" :messages="$errors->get('name')" />
                </div>

                <div>
                    <x-input-label for="service_type_id" :value="__('Tipo de Serviço')" />
                    <select id="service_type_id" name="service_type_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 rounded-xl shadow-sm">
                        <option value="">Selecione o serviço</option>
                        @foreach($serviceTypes as $service)
                            <option value="{{ $service->id }}">{{ $service->name }}</option>
                        @endforeach
                    </select>
                    <x-input-error class="mt-2" :messages="$errors->get('service_type_id')" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <x-input-label for="total_sessions" :value="__('Total de Sessões')" />
                        <x-text-input id="total_sessions" name="total_sessions" type="number" min="1" class="mt-1 block w-full" :value="old('total_sessions', 10)" required />
                        <x-input-error class="mt-2" :messages="$errors->get('total_sessions')" />
                    </div>

                    <div>
                        <x-input-label for="price" :value="__('Preço do Pacote (R$)')" />
                        <x-text-input id="price" name="price" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('price')" required placeholder="0,00" />
                        <x-input-error class="mt-2" :messages="$errors->get('price')" />
                    </div>
                </div>

                <div>
                    <x-input-label for="validity_days" :value="__('Validade em Dias')" />
                    <x-text-input id="validity_days" name="validity_days" type="number" min="1" class="mt-1 block w-full" :value="old('validity_days', 30)" required />
                    <p class="text-xs text-gray-500 mt-1">Após este período, as sessões não utilizadas expiram.</p>
                    <x-input-error class="mt-2" :messages="$errors->get('validity_days')" />
                </div>

                <div class="flex items-center justify-end mt-8 gap-4">
                    <a href="{{ route('service-packages.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 underline">
                        Cancelar
                    </a>
                    <x-primary-button class="bg-primary-600 hover:bg-primary-700 shadow-lg shadow-primary-500/30">
                        Salvar Pacote
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
@endsection
