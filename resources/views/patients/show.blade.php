@extends('layouts.saas')

@section('title', 'Prontuário: ' . $patient->full_name)

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
                    <a href="{{ route('patients.index') }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-primary-600 md:ml-2 dark:text-gray-400 dark:hover:text-white">Pacientes</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2 dark:text-gray-400">Prontuário</span>
                </div>
            </li>
        </ol>
    </nav>
@endsection

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Lado Esquerdo: Info do Paciente --}}
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white dark:bg-gray-800 shadow-md rounded-2xl p-6">
                <div class="flex flex-col items-center text-center">
                    <img class="h-24 w-24 rounded-2xl object-cover ring-4 ring-primary-500/10 mb-4" src="https://ui-avatars.com/api/?name={{ urlencode($patient->full_name) }}&background=3b82f6&color=fff&size=128&bold=true" alt="">
                    <h2 class="text-xl font-bold text-gray-800 dark:text-white">{{ $patient->full_name }}</h2>
                    <span class="mt-1 px-3 py-1 text-xs font-bold {{ $patient->is_active ? 'text-green-700 bg-green-100 dark:bg-green-900/30 dark:text-green-400' : 'text-red-700 bg-red-100 dark:bg-red-900/30 dark:text-red-400' }} rounded-full">
                        {{ $patient->is_active ? 'Ativo' : 'Inativo' }}
                    </span>
                </div>

                <div class="mt-8 space-y-4">
                    <div class="flex items-center gap-3 text-sm">
                        <div class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-xs text-gray-500 dark:text-gray-400">E-mail</p>
                            <p class="font-medium text-gray-800 dark:text-gray-200 truncate">{{ $patient->email ?? 'Não informado' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 text-sm">
                        <div class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-xs text-gray-500 dark:text-gray-400">Telefone</p>
                            <p class="font-medium text-gray-800 dark:text-gray-200">{{ $patient->phone ?? 'Não informado' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 text-sm">
                        <div class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-xs text-gray-500 dark:text-gray-400">Nascimento</p>
                            <p class="font-medium text-gray-800 dark:text-gray-200">{{ $patient->birth_date ? $patient->birth_date->format('d/m/Y') : 'Não informado' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 text-sm">
                        <div class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 014 0"/></svg>
                        </div>
                        <div class="flex-1">
                            <p class="text-xs text-gray-500 dark:text-gray-400">CPF</p>
                            <p class="font-medium text-gray-800 dark:text-gray-200">{{ $patient->document_cpf ?? 'Não informado' }}</p>
                        </div>
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t border-gray-100 dark:border-gray-700">
                    <a href="{{ route('patients.edit', $patient) }}" class="w-full inline-flex justify-center items-center px-4 py-2 bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-xl font-semibold text-xs text-gray-700 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-100 dark:hover:bg-gray-600 transition ease-in-out duration-150">
                        Editar Cadastro
                    </a>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow-md rounded-2xl p-6">
                <h3 class="text-sm font-bold text-gray-800 dark:text-white uppercase tracking-wider mb-4">Informações de Saúde</h3>
                <div class="space-y-4">
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Tipo Sanguíneo</p>
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $patient->blood_type ?? 'Não informado' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Medicamentos em uso</p>
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $patient->medications ?? 'Nenhum' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Alergias</p>
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $patient->allergies ?? 'Nenhuma' }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow-md rounded-2xl p-6">
                <h3 class="text-sm font-bold text-gray-800 dark:text-white uppercase tracking-wider mb-4">Contato de Emergência</h3>
                <div class="space-y-3">
                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $patient->emergency_contact_name ?? 'Não informado' }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $patient->emergency_contact_phone ?? '-' }}</p>
                </div>
            </div>
        </div>

        {{-- Lado Direito: Prontuário, Evoluções, Documentos --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Tabs --}}
            <div x-data="{ activeTab: 'evolutions' }" class="bg-white dark:bg-gray-800 shadow-md rounded-2xl overflow-hidden">
                <div class="border-b border-gray-200 dark:border-gray-700">
                    <ul class="flex flex-wrap -mb-px text-sm font-medium text-center text-gray-500 dark:text-gray-400">
                        <li class="mr-2">
                            <a href="javascript:void(0)" @click="activeTab = 'evolutions'" :class="activeTab === 'evolutions' ? 'text-primary-600 border-primary-600 dark:text-primary-500 dark:border-primary-500' : 'border-transparent hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300'" class="inline-flex items-center justify-center p-4 border-b-2 rounded-t-lg group">
                                <svg class="w-4 h-4 mr-2" :class="activeTab === 'evolutions' ? 'text-primary-600 dark:text-primary-500' : 'text-gray-400 group-hover:text-gray-500 dark:text-gray-500 dark:group-hover:text-gray-300'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                Evoluções / Atendimentos
                            </a>
                        </li>
                        <li class="mr-2">
                            <a href="javascript:void(0)" @click="activeTab = 'anamneses'" :class="activeTab === 'anamneses' ? 'text-primary-600 border-primary-600 dark:text-primary-500 dark:border-primary-500' : 'border-transparent hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300'" class="inline-flex items-center justify-center p-4 border-b-2 rounded-t-lg group">
                                <svg class="w-4 h-4 mr-2" :class="activeTab === 'anamneses' ? 'text-primary-600 dark:text-primary-500' : 'text-gray-400 group-hover:text-gray-500 dark:text-gray-500 dark:group-hover:text-gray-300'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                                Anamneses
                            </a>
                        </li>
                        <li class="mr-2">
                            <a href="javascript:void(0)" @click="activeTab = 'documents'" :class="activeTab === 'documents' ? 'text-primary-600 border-primary-600 dark:text-primary-500 dark:border-primary-500' : 'border-transparent hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300'" class="inline-flex items-center justify-center p-4 border-b-2 rounded-t-lg group">
                                <svg class="w-4 h-4 mr-2" :class="activeTab === 'documents' ? 'text-primary-600 dark:text-primary-500' : 'text-gray-400 group-hover:text-gray-500 dark:text-gray-500 dark:group-hover:text-gray-300'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                Documentos / Exames
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="p-6">
                    {{-- Tab: Evoluções --}}
                    <div x-show="activeTab === 'evolutions'">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white">Histórico de Evoluções</h3>
                            <button @click="$dispatch('open-modal', 'new-evolution')" class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-xl font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 transition ease-in-out duration-150">
                                Nova Evolução
                            </button>
                        </div>

                        <div class="space-y-6">
                            @forelse ($patient->evolutions as $evolution)
                                <div class="relative pl-8 border-l-2 border-primary-200 dark:border-primary-900/50 pb-6 last:pb-0 last:border-l-0">
                                    <div class="absolute -left-2.5 top-0 w-5 h-5 rounded-full bg-primary-500 border-4 border-white dark:border-gray-800"></div>
                                    <div class="bg-gray-50 dark:bg-gray-700/50 rounded-2xl p-4">
                                        <div class="flex justify-between items-start mb-2">
                                            <div>
                                                <div class="flex items-center gap-2">
                                                    <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $evolution->professional->name }}</span>
                                                    @if($evolution->type)
                                                        <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded-full {{ $evolution->type === 'evaluation' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700' }}">
                                                            {{ $evolution->type }}
                                                        </span>
                                                    @endif
                                                </div>
                                                <span class="text-xs text-gray-500 dark:text-gray-400">{{ $evolution->created_at->format('d/m/Y H:i') }}</span>
                                            </div>
                                            <div class="flex gap-2">
                                                @if($evolution->title)
                                                    <span class="text-xs font-medium text-gray-500">{{ $evolution->title }}</span>
                                                @endif
                                                <form action="{{ route('evolutions.destroy', $evolution) }}" method="POST" onsubmit="return confirmDelete(this, 'Tem certeza que deseja remover esta evolução?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-gray-400 hover:text-red-500 transition-colors">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                        <p class="text-sm text-gray-600 dark:text-gray-300 whitespace-pre-line">
                                            {{ $evolution->description ?? $evolution->notes }}
                                        </p>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-12">
                                    <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-400">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                    </div>
                                    <p class="text-gray-500 dark:text-gray-400">Nenhuma evolução registrada para este paciente.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    {{-- Tab: Anamneses --}}
                    <div x-show="activeTab === 'anamneses'" style="display: none;">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white">Histórico de Anamneses</h3>
                            <div class="flex gap-2">
                                <button @click="$dispatch('open-modal', 'compare-anamnesis')" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-xl font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 transition ease-in-out duration-150">
                                    Comparar
                                </button>
                                <button @click="$dispatch('open-modal', 'new-anamnesis')" class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-xl font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 transition ease-in-out duration-150">
                                    Nova Anamnese
                                </button>
                            </div>
                        </div>

                        <div class="space-y-4">
                            @forelse ($patient->anamneses as $anamnesis)
                                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-2xl p-4 border border-gray-100 dark:border-gray-600">
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <p class="text-sm font-bold text-gray-900 dark:text-white">Anamnese em {{ $anamnesis->created_at->format('d/m/Y') }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">Responsável: {{ $anamnesis->professional->name }}</p>
                                        </div>
                                        <button class="text-primary-600 hover:text-primary-700 text-sm font-bold">Ver Detalhes</button>
                                    </div>
                                </div>
                            @empty
                                <p class="text-center py-12 text-gray-500 dark:text-gray-400">Nenhuma anamnese registrada.</p>
                            @endforelse
                        </div>
                    </div>

                    {{-- Tab: Documentos --}}
                    <div x-show="activeTab === 'documents'" style="display: none;">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white">Documentos e Exames</h3>
                            <button @click="$dispatch('open-modal', 'upload-document')" class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-xl font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 transition ease-in-out duration-150">
                                Upload de Arquivo
                            </button>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @forelse ($patient->documents as $document)
                                <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-2xl border border-gray-100 dark:border-gray-600">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-lg bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center text-primary-600">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-gray-900 dark:text-white truncate max-w-[150px]">{{ $document->name }}</p>
                                            <p class="text-xs text-gray-500">{{ number_format($document->size / 1024, 2) }} KB</p>
                                        </div>
                                    </div>
                                    <div class="flex gap-2">
                                        <a href="{{ Storage::url($document->file_path) }}" target="_blank" class="text-gray-400 hover:text-primary-500">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </a>
                                        <form action="{{ route('documents.destroy', $document) }}" method="POST" onsubmit="return confirmDelete(this, 'Excluir este documento?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-gray-400 hover:text-red-500">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <p class="col-span-2 text-center py-12 text-gray-500 dark:text-gray-400">Nenhum documento anexado.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            {{-- Histórico Médico / Observações --}}
            <div class="bg-white dark:bg-gray-800 shadow-md rounded-2xl p-6">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4">Histórico Médico / Observações</h3>
                <div class="bg-amber-50 dark:bg-amber-900/20 border-l-4 border-amber-400 p-4 rounded-r-xl">
                    <p class="text-sm text-amber-800 dark:text-amber-200">
                        {{ $patient->medical_history ?? 'Nenhum histórico médico informado.' }}
                    </p>
                </div>
                @if($patient->lifestyle_habits)
                    <div class="mt-4">
                        <h4 class="text-sm font-bold text-gray-800 dark:text-white mb-2">Hábitos de Vida</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $patient->lifestyle_habits }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Modais --}}
    <x-modal name="new-evolution" focusable>
        <form method="post" action="{{ route('patients.evolutions.store', $patient) }}" class="p-6">
            @csrf
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Registrar Nova Evolução</h2>
            <div class="mt-6 space-y-4">
                <div>
                    <x-input-label for="title" value="Título (Opcional)" />
                    <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" />
                </div>
                <div>
                    <x-input-label for="type" value="Tipo de Evolução" />
                    <select id="type" name="type" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 dark:focus:border-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 rounded-md shadow-sm">
                        <option value="routine">Rotina / Atendimento</option>
                        <option value="evaluation">Avaliação</option>
                        <option value="emergency">Urgência</option>
                        <option value="other">Outro</option>
                    </select>
                </div>
                <div>
                    <x-input-label for="description" value="Descrição detalhada" />
                    <textarea id="description" name="description" rows="5" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 dark:focus:border-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 rounded-md shadow-sm" required></textarea>
                </div>
            </div>
            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">Cancelar</x-secondary-button>
                <x-primary-button class="ml-3">Salvar Evolução</x-primary-button>
            </div>
        </form>
    </x-modal>

    <x-modal name="upload-document" focusable>
        <form method="post" action="{{ route('patients.documents.store', $patient) }}" enctype="multipart/form-data" class="p-6">
            @csrf
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Upload de Documento/Exame</h2>
            <div class="mt-6 space-y-4">
                <div>
                    <x-input-label for="doc_name" value="Nome do Documento" />
                    <x-text-input id="doc_name" name="name" type="text" class="mt-1 block w-full" required />
                </div>
                <div>
                    <x-input-label for="file" value="Arquivo" />
                    <input id="file" name="file" type="file" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100" required />
                </div>
            </div>
            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">Cancelar</x-secondary-button>
                <x-primary-button class="ml-3">Enviar Arquivo</x-primary-button>
            </div>
        </form>
    </x-modal>

    <x-modal name="new-anamnesis" focusable>
        <form method="post" action="{{ route('patients.anamneses.store', $patient) }}" class="p-6">
            @csrf
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Nova Anamnese</h2>
            <div class="mt-6 space-y-4">
                <div>
                    <x-input-label for="main_complaint" value="Queixa Principal" />
                    <textarea id="main_complaint" name="main_complaint" rows="3" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 dark:focus:border-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 rounded-md shadow-sm" required></textarea>
                </div>
                <div>
                    <x-input-label for="history_of_current_illness" value="Histórico da Doença Atual (HDA)" />
                    <textarea id="history_of_current_illness" name="history_of_current_illness" rows="5" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 dark:focus:border-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 rounded-md shadow-sm" required></textarea>
                </div>
            </div>
            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">Cancelar</x-secondary-button>
                <x-primary-button class="ml-3">Salvar Anamnese</x-primary-button>
            </div>
        </form>
    </x-modal>

    <x-modal name="compare-anamnesis" focusable>
        <form method="get" action="{{ route('patients.anamneses.compare', $patient) }}" class="p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Comparar Anamneses</h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Selecione exatamente duas anamneses para comparar a evolução do quadro.</p>
            <div class="mt-6 space-y-2">
                @foreach ($patient->anamneses as $anamnesis)
                    <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer">
                        <input type="checkbox" name="ids[]" value="{{ $anamnesis->id }}" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-primary-600 shadow-sm focus:ring-primary-500 dark:focus:ring-primary-600">
                        <div>
                            <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $anamnesis->created_at->format('d/m/Y') }}</p>
                            <p class="text-xs text-gray-500">Por: {{ $anamnesis->professional->name }}</p>
                        </div>
                    </label>
                @endforeach
            </div>
            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">Cancelar</x-secondary-button>
                <x-primary-button class="ml-3">Comparar Selecionadas</x-primary-button>
            </div>
        </form>
    </x-modal>
@endsection
