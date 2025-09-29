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
                    <span class="ms-1 text-sm font-medium text-gray-500 md:ms-2 dark:text-gray-400">Listar</span>
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
                        Cadastrar Novo Usuário
                    </h2>
                    <p class="mt-1 text-sm font-normal text-gray-500 dark:text-gray-400">
                        Preencha os dados abaixo para adicionar um novo usuário ao sistema.
                    </p>
                </div>
                <a href="{{ route('clinic-users.index') }}"
                   class="text-sm font-medium text-blue-600 dark:text-blue-500 hover:underline">
                    &larr; Voltar para a lista
                </a>
            </div>

            {{-- Formulário de Criação --}}
            <form action="{{ route('clinic-users.store') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Campo Nome --}}
                    <div>
                        <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nome
                            Completo</label>
                        <input type="text" name="name" id="name"
                               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                               placeholder="Ex: João da Silva" value="{{ old('name') }}" required>
                        @error('name')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Campo Email --}}
                    <div>
                        <label for="email"
                               class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">E-mail</label>
                        <input type="email" name="email" id="email"
                               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                               placeholder="usuario@email.com" value="{{ old('email') }}" required>
                        @error('email')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Campo Senha --}}
                    <div>
                        <label for="password"
                               class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Senha</label>
                        <input type="password" name="password" id="password"
                               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                               placeholder="••••••••" required>
                        @error('password')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Campo Perfil (Role) --}}
                    <div>
                        <label for="role" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Perfil de
                            Acesso</label>
                        <select id="role" name="role"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                required>
                            <option selected disabled value="">Selecione um perfil</option>
                            {{-- O loop de roles virá aqui. Exemplo: --}}
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                        @error('role')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Botão de Submissão --}}
                <div class="mt-6 flex justify-end">
                    <button type="submit"
                            class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                        Salvar Usuário
                    </button>
                </div>
            </form>
        </div>
    </div>

@endsection
