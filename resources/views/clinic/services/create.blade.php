@extends('layouts.saas')

@section('title', 'Novo Tipo de Atendimento')

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
                    <a href="{{ route('service-types.index') }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-primary-600 md:ml-2 dark:text-gray-400 dark:hover:text-white">Tipos de Atendimento</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2 dark:text-gray-400">Novo Tipo</span>
                </div>
            </li>
        </ol>
    </nav>
@endsection

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-2xl p-6">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Cadastrar Novo Tipo de Atendimento</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Defina o nome, duração e preço base para o novo serviço ou aula.</p>
            </div>

            <form action="{{ route('service-types.store') }}" method="POST" class="space-y-6">
                @csrf

                <div>
                    <x-input-label for="name" :value="__('Nome do Serviço / Aula')" />
                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required autofocus placeholder="Ex: Pilates Individual, Avaliação, Aula Experimental" />
                    <x-input-error class="mt-2" :messages="$errors->get('name')" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <x-input-label for="duration_in_minutes" :value="__('Duração (minutos)')" />
                        <x-text-input id="duration_in_minutes" name="duration_in_minutes" type="number" min="5" max="480" class="mt-1 block w-full" :value="old('duration_in_minutes', 50)" required />
                        <x-input-error class="mt-2" :messages="$errors->get('duration_in_minutes')" />
                    </div>

                    <div>
                        <x-input-label for="price" :value="__('Preço Base (Opcional)')" />
                        <x-text-input id="price" name="price" type="number" step="0.01" min="0" class="mt-1 block w-full" :value="old('price')" placeholder="0.00" />
                        <x-input-error class="mt-2" :messages="$errors->get('price')" />
                    </div>
                </div>

                <div class="block">
                    <label for="is_active" class="inline-flex items-center">
                        <input id="is_active" type="checkbox" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-primary-600 shadow-sm focus:ring-primary-500 dark:focus:ring-primary-600 dark:focus:ring-offset-gray-800" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                        <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Ativo') }}</span>
                    </label>
                    <x-input-error class="mt-2" :messages="$errors->get('is_active')" />
                </div>

                <div class="flex items-center justify-end mt-8 gap-4">
                    <a href="{{ route('service-types.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 underline">
                        Cancelar
                    </a>
                    <x-primary-button class="bg-primary-600 hover:bg-primary-700 shadow-lg shadow-primary-500/30">
                        Salvar Tipo de Atendimento
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
@endsection
