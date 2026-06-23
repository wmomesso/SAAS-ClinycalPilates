@extends('layouts.saas')

@section('title', 'Nova Sala')

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
                    <a href="{{ route('rooms.index') }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-primary-600 md:ml-2 dark:text-gray-400 dark:hover:text-white">Salas</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2 dark:text-gray-400">Nova Sala</span>
                </div>
            </li>
        </ol>
    </nav>
@endsection

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-2xl p-6">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Cadastrar Nova Sala</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Defina o nome e a capacidade da nova sala de atendimento.</p>
            </div>

            <form action="{{ route('rooms.store') }}" method="POST" class="space-y-6">
                @csrf

                <div>
                    <x-input-label for="name" :value="__('Nome da Sala')" />
                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required autofocus placeholder="Ex: Sala 01, Pilates A, etc." />
                    <x-input-error class="mt-2" :messages="$errors->get('name')" />
                </div>

                <div>
                    <x-input-label for="capacity" :value="__('Capacidade Máxima de Pacientes')" />
                    <x-text-input id="capacity" name="capacity" type="number" min="1" class="mt-1 block w-full" :value="old('capacity', 1)" required />
                    <x-input-error class="mt-2" :messages="$errors->get('capacity')" />
                </div>

                <div>
                    <x-input-label for="resources" :value="__('Recursos Disponíveis (Opcional)')" />
                    <p class="text-xs text-gray-500 mb-2">Liste equipamentos ou recursos separados por vírgula.</p>
                    <x-text-input id="resources_input" name="resources_input" type="text" class="mt-1 block w-full" placeholder="Ex: Reformer, Cadillac, Espaldar" />
                    <x-input-error class="mt-2" :messages="$errors->get('resources')" />
                </div>

                <div class="flex items-center justify-end mt-8 gap-4">
                    <a href="{{ route('rooms.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 underline">
                        Cancelar
                    </a>
                    <x-primary-button class="bg-primary-600 hover:bg-primary-700 shadow-lg shadow-primary-500/30">
                        Salvar Sala
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
@endsection
