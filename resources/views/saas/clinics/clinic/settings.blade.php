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
                    <a href="{{ route('clinics.index') }}"
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

    <div class="p-4 mt-4 bg-white dark:bg-gray-800 flex justify-between items-center rounded-t-lg">
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg w-full">
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
                            <label for="document" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Documento
                                (CNPJ)</label>
                            <input type="text" name="document" id="document"
                                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                                   placeholder="00.000.000/0001-00" value="{{ old('document', $clinic->document) }}">
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
    </div>
    @push('js-scripts')
        <script>
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
        </script>
    @endpush
@endsection
