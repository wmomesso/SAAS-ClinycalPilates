@extends('layouts.saas')

@section('title', 'Usuários')

@section('content')
    {{-- Cabeçalho da Página --}}
    <h3 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">
        Gestão da clinica
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
                    <a href="{{ route('admin.clinics.index') }}"
                       class="ms-1 text-sm font-medium text-gray-700 hover:text-blue-600 md:ms-2 dark:text-gray-400 dark:hover:text-white">Clinicas</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="rtl:rotate-180 w-3 h-3 text-gray-400 mx-1" aria-hidden="true"
                         xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="m1 9 4-4-4-4"/>
                    </svg>
                    <span
                        class="ms-1 text-sm font-medium text-gray-500 md:ms-2 dark:text-gray-400">{{ $clinic->name }}</span>
                </div>
            </li>
        </ol>
    </nav>
    {{-- Seus cards, gráficos e tabelas do dashboard entram aqui --}}
    <div class="p-4 bg-white dark:bg-gray-800 flex justify-between items-center rounded-t-lg">
        {{-- Título e Subtítulo à Esquerda --}}
        <div>
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                Dados da Clínica
            </h2>
            <p class="mt-1 text-sm font-normal text-gray-500 dark:text-gray-400">
                Controle e configuração da clinica {{ $clinic->name }}
            </p>
        </div>

        {{-- Botão de Ação à Direita --}}
        <div>
            <a href="{{ route('clinic-users.create') }}"
               class="inline-flex items-center justify-center px-3 py-2 text-sm font-medium text-center text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"
                     aria-hidden="true">
                    <path clip-rule="evenodd" fill-rule="evenodd"
                          d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"></path>
                </svg>
                Novo Usuário
            </a>
        </div>
    </div>

    <div class="p-4 mt-4 bg-white dark:bg-gray-800 flex flex-col rounded-t-lg">
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg w-full mb-8">
            <form action="{{ route('clinic.update', $clinic) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT') {{-- ou PATCH --}}

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                    {{-- Coluna de Informações (Esquerda) --}}
                    <div class="lg:col-span-2 space-y-6">
                        {{-- Campo Nome da Clínica --}}
                        <div>
                            <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nome
                                da Clínica</label>
                            <input type="text" name="name" id="name"
                                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                                   placeholder="Fisio & Saúde" value="{{ old('name', $clinic->name) }}" required>
                            @error('name')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Campo Subdomínio --}}
                        <div>
                            <label for="subdomain" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Subdomínio</label>
                            <div class="flex">
                        <span
                            class="inline-flex items-center px-3 text-sm text-gray-900 bg-gray-200 border rounded-e-0 border-gray-300 rounded-s-md dark:bg-gray-600 dark:text-gray-400 dark:border-gray-600">
                           https://
                        </span>
                                <input type="text" name="subdomain" id="subdomain"
                                       class="rounded-none rounded-e-lg bg-gray-50 border text-gray-900 focus:ring-blue-500 focus:border-blue-500 block flex-1 min-w-0 w-full text-sm border-gray-300 p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                                       placeholder="minha-clinica" value="{{ old('subdomain', $clinic->subdomain) }}"
                                       required>
                                <span
                                    class="inline-flex items-center px-3 text-sm text-gray-900 bg-gray-200 border rounded-s-0 border-gray-300 rounded-e-md dark:bg-gray-600 dark:text-gray-400 dark:border-gray-600">
                           .fisiogestor.com
                        </span>
                            </div>
                            @error('subdomain')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Campo Documento (CNPJ/CPF) --}}
                        <div>
                            <label for="document" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Documento (CNPJ/CPF)</label>
                            <input type="text" name="document" id="document"
                                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                                   placeholder="00.000.000/0000-00" value="{{ old('document', $clinic->document) }}" data-mask="cpf_cnpj">
                            @error('document')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Coluna do Logo (Direita) --}}
                    <div class="lg:col-span-1">
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Logo da
                            Clínica</label>
                        <div class="flex flex-col items-center space-y-4">
                            {{-- Preview da Imagem Atual --}}
                            <img id="logo-preview" class="h-32 w-32 rounded-full object-cover shadow-sm"
                                 src="{{ $clinic->logo_path ? asset('storage/' . $clinic->logo_path) : 'https://ui-avatars.com/api/?name=' . urlencode($clinic->name) . '&size=128&background=random' }}"
                                 alt="Logo atual">

                            {{-- Botão de Upload --}}
                            <label for="logo"
                                   class="cursor-pointer bg-white dark:bg-gray-700 text-gray-900 dark:text-white py-2 px-4 rounded-lg border border-gray-300 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-600 text-sm font-medium">
                                <svg class="w-4 h-4 mr-2 inline-block" fill="none" stroke="currentColor"
                                     viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                </svg>
                                <span>Alterar Logo</span>
                                <input id="logo" name="logo" type="file" class="hidden"
                                       accept="image/png, image/jpeg, image/gif">
                            </label>
                            <p id="file-name" class="text-xs text-gray-500 dark:text-gray-400 mt-1"></p>
                            @error('logo')
                            <p class="text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Botão de Submissão --}}
                <div class="mt-8 mb-4 pt-6 border-t border-gray-200 dark:border-gray-700 flex justify-end">
                    <button type="submit"
                            class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                        Salvar Alterações
                    </button>
                </div>
            </form>
        </div>

        {{-- Seção de Papéis e Permissões Padrão --}}
        <div class="mt-10 border-t border-gray-200 dark:border-gray-700 pt-8">
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Estrutura de Papéis e Acessos Padrão
                </h3>
                <p class="mt-1 text-sm font-normal text-gray-500 dark:text-gray-400">
                    Confira o que cada papel de usuário pode acessar por padrão no sistema.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
                {{-- Admin da Clínica --}}
                @php
                    $roleAdmin = $roles->where('name', 'admin-clinica')->first();
                @endphp
                <div onclick="openPermissionsModal({{ $roleAdmin->id }})" class="cursor-pointer p-4 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 hover:border-blue-500 dark:hover:border-blue-400 transition-colors">
                    <div class="flex items-center mb-3">
                        <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg mr-3">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                        </div>
                        <h4 class="font-bold text-gray-900 dark:text-white">Admin da Clínica</h4>
                    </div>
                    <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                        <li class="flex items-start">
                            <svg class="w-4 h-4 text-green-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Gestão total da clínica
                        </li>
                        <li class="flex items-start">
                            <svg class="w-4 h-4 text-green-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Financeiro e Fluxo de Caixa
                        </li>
                        <li class="flex items-start">
                            <svg class="w-4 h-4 text-green-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Gestão de Salas e Agenda
                        </li>
                        <li class="flex items-start">
                            <svg class="w-4 h-4 text-green-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Controle de Assinatura
                        </li>
                    </ul>
                </div>

                {{-- Profissional --}}
                @php
                    $roleProfissional = $roles->where('name', 'profissional')->first();
                @endphp
                <div onclick="openPermissionsModal({{ $roleProfissional->id }})" class="cursor-pointer p-4 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 hover:border-blue-500 dark:hover:border-blue-400 transition-colors">
                    <div class="flex items-center mb-3">
                        <div class="p-2 bg-green-100 dark:bg-green-900 rounded-lg mr-3">
                            <svg class="w-5 h-5 text-green-600 dark:text-green-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        </div>
                        <h4 class="font-bold text-gray-900 dark:text-white">Profissional</h4>
                    </div>
                    <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                        <li class="flex items-start">
                            <svg class="w-4 h-4 text-green-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Ver agenda de profissionais
                        </li>
                        <li class="flex items-start">
                            <svg class="w-4 h-4 text-green-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Gerenciar agendamentos próprios
                        </li>
                        <li class="flex items-start">
                            <svg class="w-4 h-4 text-green-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Registrar evolução do paciente
                        </li>
                        <li class="flex items-start">
                            <svg class="w-4 h-4 text-green-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Visualizar pacientes
                        </li>
                    </ul>
                </div>

                {{-- Recepcionista --}}
                @php
                    $roleRecepcionista = $roles->where('name', 'recepcionista')->first();
                @endphp
                <div onclick="openPermissionsModal({{ $roleRecepcionista->id }})" class="cursor-pointer p-4 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 hover:border-blue-500 dark:hover:border-blue-400 transition-colors">
                    <div class="flex items-center mb-3">
                        <div class="p-2 bg-yellow-100 dark:bg-yellow-900 rounded-lg mr-3">
                            <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                        <h4 class="font-bold text-gray-900 dark:text-white">Recepcionista</h4>
                    </div>
                    <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                        <li class="flex items-start">
                            <svg class="w-4 h-4 text-green-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Gerenciar agenda e salas
                        </li>
                        <li class="flex items-start">
                            <svg class="w-4 h-4 text-green-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Gestão de pacientes
                        </li>
                        <li class="flex items-start">
                            <svg class="w-4 h-4 text-green-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Controle de pagamentos
                        </li>
                        <li class="flex items-start">
                            <svg class="w-4 h-4 text-green-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Visualizar dados básicos
                        </li>
                    </ul>
                </div>

                {{-- Paciente --}}
                @php
                    $rolePaciente = $roles->where('name', 'paciente')->first();
                @endphp
                <div onclick="openPermissionsModal({{ $rolePaciente->id }})" class="cursor-pointer p-4 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 hover:border-blue-500 dark:hover:border-blue-400 transition-colors">
                    <div class="flex items-center mb-3">
                        <div class="p-2 bg-purple-100 dark:bg-purple-900 rounded-lg mr-3">
                            <svg class="w-5 h-5 text-purple-600 dark:text-purple-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                        </div>
                        <h4 class="font-bold text-gray-900 dark:text-white">Paciente</h4>
                    </div>
                    <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                        <li class="flex items-start">
                            <svg class="w-4 h-4 text-blue-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Acesso via portal (em breve)
                        </li>
                        <li class="flex items-start">
                            <svg class="w-4 h-4 text-blue-500 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Visualizar próprios agendamentos
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal de Permissões --}}
    <div id="permissions-modal-backdrop" class="fixed inset-0 bg-gray-900 bg-opacity-50 dark:bg-opacity-80 z-40 hidden"></div>
    <div id="permissions-modal" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-2xl max-h-full">
            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                        Gerenciar Permissões: <span id="modal-role-name"></span>
                    </h3>
                    <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" onclick="closePermissionsModal()">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                        </svg>
                        <span class="sr-only">Fechar modal</span>
                    </button>
                </div>
                <form id="permissions-form" onsubmit="savePermissions(event)">
                    <div class="p-4 md:p-5 space-y-4 max-h-[60vh] overflow-y-auto">
                        <div id="permissions-container" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {{-- Preenchido via JS --}}
                        </div>
                    </div>
                    <div class="flex items-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
                        <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Salvar</button>
                        <button type="button" class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900 focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700" onclick="closePermissionsModal()">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('js-scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Máscara dinâmica CPF/CNPJ
                const documentEl = document.getElementById('document');
                if (documentEl) {
                    IMask(documentEl, {
                        mask: [
                            { mask: '000.000.000-00', type: 'cpf' },
                            { mask: '00.000.000/0000-00', type: 'cnpj' }
                        ],
                        dispatch: function (appended, dynamicMasked) {
                            const number = (dynamicMasked.value + appended).replace(/\D/g, '');
                            if (number.length <= 11) {
                                return dynamicMasked.compiledMasks[0];
                            }
                            return dynamicMasked.compiledMasks[1];
                        }
                    });
                }
            });

            document.getElementById('logo').addEventListener('change', function (event) {
                const fileInput = event.target;
                const fileNameDisplay = document.getElementById('file-name');
                const logoPreview = document.getElementById('logo-preview');

                if (fileInput.files && fileInput.files[0]) {
                    const file = fileInput.files[0];
                    fileNameDisplay.textContent = file.name;

                    // Opcional: Mostrar preview da nova imagem
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        logoPreview.src = e.target.result;
                    }
                    reader.readAsDataURL(file);
                } else {
                    fileNameDisplay.textContent = '';
                }
            });

            let currentRoleId = null;

            async function openPermissionsModal(roleId) {
                currentRoleId = roleId;
                const modal = document.getElementById('permissions-modal');
                const backdrop = document.getElementById('permissions-modal-backdrop');
                const container = document.getElementById('permissions-container');
                const roleNameSpan = document.getElementById('modal-role-name');

                container.innerHTML = '<p class="text-gray-500 dark:text-gray-400">Carregando...</p>';
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                backdrop.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');

                try {
                    const response = await fetch(`/roles/${roleId}/permissions`);
                    const data = await response.json();

                    roleNameSpan.textContent = data.role.name === 'admin-clinica' ? 'Admin da Clínica' :
                                               data.role.name.charAt(0).toUpperCase() + data.role.name.slice(1);

                    container.innerHTML = '';

                    for (const [group, permissions] of Object.entries(data.allPermissions)) {
                        const groupDiv = document.createElement('div');
                        groupDiv.className = 'col-span-1 md:col-span-2 mt-4';
                        groupDiv.innerHTML = `<h4 class="font-semibold text-gray-700 dark:text-gray-300 border-b pb-1 mb-2 capitalize">${group}</h4>`;
                        container.appendChild(groupDiv);

                        permissions.forEach(permission => {
                            const isChecked = data.rolePermissions.includes(permission.name);
                            const div = document.createElement('div');
                            div.className = 'flex items-center mb-2';
                            div.innerHTML = `
                                <input id="perm-${permission.id}" type="checkbox" name="permissions[]" value="${permission.name}" ${isChecked ? 'checked' : ''}
                                       class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                <label for="perm-${permission.id}" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300 capitalize">${permission.name.replace(/-/g, ' ')}</label>
                            `;
                            container.appendChild(div);
                        });
                    }
                } catch (error) {
                    console.error('Erro ao carregar permissões:', error);
                    container.innerHTML = '<p class="text-red-500">Erro ao carregar permissões.</p>';
                }
            }

            function closePermissionsModal() {
                const modal = document.getElementById('permissions-modal');
                const backdrop = document.getElementById('permissions-modal-backdrop');
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                backdrop.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
                currentRoleId = null;
            }

            async function savePermissions(event) {
                event.preventDefault();
                const form = event.target;
                const formData = new FormData(form);
                const permissions = formData.getAll('permissions[]');

                try {
                    const response = await fetch(`/roles/${currentRoleId}/permissions`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ permissions })
                    });

                    if (response.ok) {
                        Swal.fire({
                            title: 'Sucesso!',
                            text: 'Permissões atualizadas com sucesso!',
                            icon: 'success',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                        closePermissionsModal();
                    } else {
                        const data = await response.json();
                        Swal.fire({
                            title: 'Erro!',
                            text: (data.message || 'Erro ao salvar.'),
                            icon: 'error'
                        });
                    }
                } catch (error) {
                    console.error('Erro ao salvar permissões:', error);
                    Swal.fire({
                        title: 'Erro!',
                        text: 'Erro ao processar a requisição.',
                        icon: 'error'
                    });
                }
            }
        </script>
    @endpush
@endsection
