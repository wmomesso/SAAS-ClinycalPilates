@extends('layouts.saas')

@section('title', 'Usuários')

@section('content')
    {{-- Cabeçalho da Página --}}
    <h3 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">
        Gestão de usuários
    </h3>

    <nav class="flex mb-4" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-2 rtl:space-x-reverse">
            <li class="inline-flex items-center">
                <a href="{{ route('dashboard') }}" aria-current="page"
                   class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white">
                    <svg class="w-3 h-3 pr-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                         viewBox="0 0 20 20">
                        <path
                            d="m19.707 9.293-2-2-7-7a1 1 0 0 0-1.414 0l-7 7-2 2a1 1 0 0 0 1.414 1.414L2 10.414V18a2 2 0 0 0 2 2h3a1 1 0 0 0 1-1v-4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v4a1 1 0 0 0 1 1h3a2 2 0 0 0 2-2v-7.586l.293.293a1 1 0 0 0 1.414-1.414Z"/>
                    </svg>
                    <span class="ml-1">Dashboard</span>
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" aria-hidden="true"
                         xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="m1 9 4-4-4-4"/>
                    </svg>
                    <a href="{{ route('clinic-users.index') }}"
                       class="ms-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ms-2 dark:text-gray-400 dark:hover:text-white">Usuários</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" aria-hidden="true"
                         xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="m1 9 4-4-4-4"/>
                    </svg>
                    <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2 dark:text-gray-400">{{ $user->name }}</span>
                </div>
            </li>
        </ol>
    </nav>
    {{-- Seus cards, gráficos e tabelas do dashboard entram aqui --}}

    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                        Dados do Usuário
                    </h2>
                    <p class="mt-1 text-sm font-normal text-gray-500 dark:text-gray-400">
                        Edite os dados abaixo para atualizar as informações do usuário.
                    </p>
                </div>
                <a href="{{ route('clinic-users.index') }}"
                   class="text-sm font-medium text-blue-600 dark:text-blue-500 hover:underline">
                    &larr; Voltar para a lista
                </a>
            </div>

            {{-- Formulário de Criação --}}


            <div class="w-full p-4 text-center bg-white border border-gray-200 rounded-lg shadow-sm sm:p-8 dark:bg-gray-800 dark:border-gray-700">
                <h5 class="mb-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $user->name }}</h5>
                <p class="mb-5 text-base text-gray-500 sm:text-lg dark:text-gray-400">{{ $user->getRoleNames()->first() }}</p>
                <div class="items-center justify-center space-y-4 sm:flex sm:space-y-0 sm:space-x-4 rtl:space-x-reverse">
                    ----
                </div>
            </div>

        </div>
    </div>

@endsection
