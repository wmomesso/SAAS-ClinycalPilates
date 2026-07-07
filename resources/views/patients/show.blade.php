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
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Hábitos de vida</p>
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $patient->lifestyle_habits ?? 'Não informado' }}</p>
                    </div>
                    <div class="pt-2">
                        <a href="{{ route('patients.edit', $patient) }}" class="text-xs font-bold text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 uppercase tracking-wider">
                            Editar dados de saúde
                        </a>
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
                            <a href="javascript:void(0)" @click="activeTab = 'packages'" :class="activeTab === 'packages' ? 'text-primary-600 border-primary-600 dark:text-primary-500 dark:border-primary-500' : 'border-transparent hover:text-gray-600 hover:border-gray-300 dark:hover:text-gray-300'" class="inline-flex items-center justify-center p-4 border-b-2 rounded-t-lg group">
                                <svg class="w-4 h-4 mr-2" :class="activeTab === 'packages' ? 'text-primary-600 dark:text-primary-500' : 'text-gray-400 group-hover:text-gray-500 dark:text-gray-500 dark:group-hover:text-gray-300'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7H4m16 0v10a2 2 0 01-2 2H6a2 2 0 01-2-2V7m16 0l-2-4H6L4 7m5 4h6"/></svg>
                                Pacotes / Sessões
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
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white">Atendimentos e Evoluções</h3>
                            <button @click="$dispatch('open-modal', 'new-evolution')" class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-xl font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 transition ease-in-out duration-150">
                                Nova Evolução
                            </button>
                        </div>

                        @php
                            $appointmentStatusLabels = [
                                'scheduled' => 'Agendado',
                                'confirmed' => 'Confirmado',
                                'completed' => 'Realizado',
                                'canceled' => 'Cancelado',
                                'no_show' => 'Falta',
                            ];
                            $appointmentStatusClasses = [
                                'scheduled' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
                                'confirmed' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300',
                                'completed' => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
                                'canceled' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
                                'no_show' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300',
                            ];
                        @endphp

                        <div class="mb-8">
                            <h4 class="text-sm font-bold text-gray-800 dark:text-white uppercase tracking-wider mb-4">Atendimentos Agendados</h4>
                            <div class="space-y-4">
                                @forelse ($patient->appointments as $appointment)
                                    <div class="rounded-2xl border border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50 p-4">
                                        <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-3">
                                            <div>
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <p class="text-sm font-bold text-gray-900 dark:text-white">
                                                        {{ $appointment->start_time->format('d/m/Y') }}
                                                        <span class="font-medium text-gray-500 dark:text-gray-400">
                                                            {{ $appointment->start_time->format('H:i') }} - {{ $appointment->end_time->format('H:i') }}
                                                        </span>
                                                    </p>
                                                    <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded-full {{ $appointmentStatusClasses[$appointment->status] ?? 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300' }}">
                                                        {{ $appointmentStatusLabels[$appointment->status] ?? ucfirst($appointment->status) }}
                                                    </span>
                                                    <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded-full {{ $appointment->patient_package_id ? 'bg-primary-100 text-primary-700 dark:bg-primary-900/30 dark:text-primary-300' : 'bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-300' }}">
                                                        {{ $appointment->patient_package_id ? 'Pacote' : 'Avulso' }}
                                                    </span>
                                                </div>
                                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                                                    {{ $appointment->serviceType->name ?? 'Serviço não informado' }}
                                                </p>
                                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                                    Profissional: {{ $appointment->professional->name ?? 'Não informado' }}
                                                    @if($appointment->room)
                                                        · Sala: {{ $appointment->room->name }}
                                                    @endif
                                                </p>
                                            </div>
                                            @if($appointment->patientPackage)
                                                <div class="text-xs text-gray-500 dark:text-gray-400 lg:text-right">
                                                    <p class="font-semibold text-gray-700 dark:text-gray-300">{{ $appointment->patientPackage->package->name ?? 'Pacote removido' }}</p>
                                                    <p>{{ $appointment->patientPackage->remaining_sessions }} sessões restantes</p>
                                                </div>
                                            @endif
                                        </div>
                                        @if($appointment->notes)
                                            <p class="mt-3 text-sm text-gray-600 dark:text-gray-300 whitespace-pre-line">{{ $appointment->notes }}</p>
                                        @endif
                                        @if(in_array($appointment->status, ['scheduled', 'confirmed'], true))
                                            <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-600 flex flex-wrap gap-2">
                                                <form method="POST" action="{{ route('appointments.status', $appointment) }}" onsubmit="return confirmAppointmentStatus(this, 'Confirmar sessão realizada?', 'Esta ação marca presença e consome uma sessão realizada do pacote quando houver pacote ativo.');">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="completed">
                                                    <button type="submit" class="inline-flex items-center px-3 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold uppercase tracking-wider transition">
                                                        Confirmar sessão
                                                    </button>
                                                </form>

                                                <form method="POST" action="{{ route('appointments.status', $appointment) }}" onsubmit="return confirmAppointmentStatus(this, 'Registrar falta?', 'Esta ação consome uma sessão perdida do pacote quando houver pacote ativo.');">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="no_show">
                                                    <button type="submit" class="inline-flex items-center px-3 py-2 rounded-xl bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold uppercase tracking-wider transition">
                                                        Falta
                                                    </button>
                                                </form>

                                                <form method="POST" action="{{ route('appointments.status', $appointment) }}" onsubmit="return confirmAppointmentStatus(this, 'Cancelar antecipadamente?', 'O atendimento ficará cancelado e a sessão não será consumida. O horário poderá ser reagendado.');">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="status" value="canceled">
                                                    <input type="hidden" name="notes" value="Cancelamento antecipado pelo prontuário do paciente.">
                                                    <button type="submit" class="inline-flex items-center px-3 py-2 rounded-xl bg-gray-700 hover:bg-gray-800 text-white text-xs font-bold uppercase tracking-wider transition">
                                                        Cancelamento antecipado
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    </div>
                                @empty
                                    <div class="text-center py-8 rounded-2xl border border-dashed border-gray-200 dark:border-gray-700">
                                        <p class="text-gray-500 dark:text-gray-400">Nenhum atendimento agendado para este paciente.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        <h4 class="text-sm font-bold text-gray-800 dark:text-white uppercase tracking-wider mb-4">Evoluções Clínicas</h4>
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
                                    <button type="button" @click="$dispatch('open-modal', 'new-evolution')" class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-400 hover:text-primary-600 hover:bg-primary-50 dark:hover:bg-primary-900/30 transition" aria-label="Registrar nova evolução clínica">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                    </button>
                                    <p class="text-gray-500 dark:text-gray-400">Nenhuma evolução registrada para este paciente.</p>
                                </div>
                            @endforelse
                        </div>

                        <div class="mt-8">
                            <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4">Cobranças do Paciente</h3>
                            <div class="space-y-3">
                                @forelse($patient->receivables as $receivable)
                                    @php
                                        $receivableStatusClasses = [
                                            'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-200',
                                            'received' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-200',
                                            'partially_received' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-200',
                                            'canceled' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-200',
                                        ];
                                        $receivableStatusLabels = [
                                            'pending' => 'Pendente',
                                            'received' => 'Recebido',
                                            'partially_received' => 'Parcial',
                                            'canceled' => 'Cancelado',
                                        ];
                                        $paymentLabels = [
                                            'cash' => 'Dinheiro',
                                            'pix' => 'Pix',
                                            'credit_card' => 'Cartão crédito',
                                            'debit_card' => 'Cartão débito',
                                            'bank_slip' => 'Boleto',
                                            'bank_transfer' => 'Transferência',
                                            'other' => 'Outro',
                                        ];
                                    @endphp
                                    <div class="p-4 rounded-2xl border border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm">
                                        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                                            <div>
                                                <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $receivable->description }}</p>
                                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                                    Vencimento {{ $receivable->due_date->format('d/m/Y') }}
                                                    @if($receivable->payment_method)
                                                        · {{ $paymentLabels[$receivable->payment_method] ?? $receivable->payment_method }}
                                                    @endif
                                                    @if($receivable->bankAccount)
                                                        · {{ $receivable->bankAccount->name }}
                                                    @endif
                                                </p>
                                            </div>
                                            <div class="flex flex-wrap lg:justify-end items-center gap-3">
                                                <span class="text-sm font-bold text-gray-900 dark:text-white">R$ {{ number_format($receivable->amount, 2, ',', '.') }}</span>
                                                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ $receivableStatusClasses[$receivable->status] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200' }}">
                                                    {{ $receivableStatusLabels[$receivable->status] ?? $receivable->status }}
                                                </span>
                                                <a href="{{ route('receivables.edit', $receivable) }}" class="text-xs font-bold uppercase tracking-wider text-primary-600 hover:text-primary-700 dark:text-primary-400">
                                                    Gerenciar pagamento
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-8 rounded-2xl border border-dashed border-gray-200 dark:border-gray-700">
                                        <p class="text-gray-500 dark:text-gray-400">Nenhuma cobrança registrada para este paciente.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    {{-- Tab: Pacotes --}}
                    <div x-show="activeTab === 'packages'" style="display: none;">
                        @php
                            $packageTotals = [
                                'total' => $patient->packages->sum('total_sessions'),
                                'used' => $patient->packages->sum('used_sessions'),
                                'missed' => $patient->packages->sum('missed_sessions'),
                                'remaining' => $patient->packages->sum(fn ($package) => $package->remaining_sessions),
                            ];
                        @endphp

                        <div class="flex flex-col xl:flex-row xl:items-start justify-between gap-4 mb-6">
                            <div>
                                <h3 class="text-lg font-bold text-gray-800 dark:text-white">Pacotes e Controle de Sessões</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">A presença consome sessão feita; a falta sem cancelamento consome sessão perdida.</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
                            <div class="p-4 rounded-2xl bg-gray-50 dark:bg-gray-700/50 border border-gray-100 dark:border-gray-700">
                                <p class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">Compradas</p>
                                <p class="mt-1 text-2xl font-black text-gray-900 dark:text-white">{{ $packageTotals['total'] }}</p>
                            </div>
                            <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-800">
                                <p class="text-xs font-semibold uppercase text-emerald-700 dark:text-emerald-300">Feitas</p>
                                <p class="mt-1 text-2xl font-black text-emerald-700 dark:text-emerald-300">{{ $packageTotals['used'] }}</p>
                            </div>
                            <div class="p-4 rounded-2xl bg-red-50 dark:bg-red-900/20 border border-red-100 dark:border-red-800">
                                <p class="text-xs font-semibold uppercase text-red-700 dark:text-red-300">Perdidas</p>
                                <p class="mt-1 text-2xl font-black text-red-700 dark:text-red-300">{{ $packageTotals['missed'] }}</p>
                            </div>
                            <div class="p-4 rounded-2xl bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800">
                                <p class="text-xs font-semibold uppercase text-blue-700 dark:text-blue-300">Restantes</p>
                                <p class="mt-1 text-2xl font-black text-blue-700 dark:text-blue-300">{{ $packageTotals['remaining'] }}</p>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('patients.packages.store', $patient) }}" class="mb-6 p-4 rounded-2xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/40">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                                <div class="md:col-span-2">
                                    <x-input-label for="service_package_id" value="Pacote comprado" />
                                    <select id="service_package_id" name="service_package_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 dark:focus:border-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 rounded-md shadow-sm" required>
                                        <option value="">Selecione...</option>
                                        @foreach($servicePackages as $servicePackage)
                                            <option value="{{ $servicePackage->id }}">
                                                {{ $servicePackage->name }} · {{ $servicePackage->number_of_sessions }} sessões · R$ {{ number_format($servicePackage->price, 2, ',', '.') }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error class="mt-2" :messages="$errors->get('service_package_id')" />
                                </div>
                                <div>
                                    <x-input-label for="package_start_date" value="Início" />
                                    <x-text-input id="package_start_date" name="start_date" type="date" class="mt-1 block w-full" :value="old('start_date', now()->toDateString())" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('start_date')" />
                                </div>
                                <div>
                                    <x-input-label for="price_paid" value="Valor pago" />
                                    <x-text-input id="price_paid" name="price_paid" type="number" min="0" step="0.01" class="mt-1 block w-full" :value="old('price_paid')" placeholder="Valor do pacote" />
                                    <x-input-error class="mt-2" :messages="$errors->get('price_paid')" />
                                </div>
                            </div>
                            <div class="mt-4 grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                                <div>
                                    <x-input-label for="billing_type" value="Tipo de cobrança" />
                                    <select id="billing_type" name="billing_type" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 dark:focus:border-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 rounded-md shadow-sm" required>
                                        <option value="single" {{ old('billing_type', 'monthly_recurring') === 'single' ? 'selected' : '' }}>Avulsa</option>
                                        <option value="monthly_recurring" {{ old('billing_type', 'monthly_recurring') === 'monthly_recurring' ? 'selected' : '' }}>Mensal recorrente</option>
                                    </select>
                                    <x-input-error class="mt-2" :messages="$errors->get('billing_type')" />
                                </div>
                                <div>
                                    <x-input-label for="payment_method" value="Forma de pagamento" />
                                    <select id="payment_method" name="payment_method" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 dark:focus:border-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 rounded-md shadow-sm" required>
                                        @foreach(['pix' => 'Pix', 'bank_slip' => 'Boleto', 'credit_card' => 'Cartão crédito', 'debit_card' => 'Cartão débito', 'bank_transfer' => 'Transferência', 'cash' => 'Dinheiro', 'other' => 'Outro'] as $value => $label)
                                            <option value="{{ $value }}" {{ old('payment_method') === $value ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    <x-input-error class="mt-2" :messages="$errors->get('payment_method')" />
                                </div>
                                <div>
                                    <x-input-label for="bank_account_id" value="Conta para conciliação" />
                                    <select id="bank_account_id" name="bank_account_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 dark:focus:border-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 rounded-md shadow-sm">
                                        <option value="">Não definida</option>
                                        @foreach($bankAccounts as $bankAccount)
                                            <option value="{{ $bankAccount->id }}" {{ old('bank_account_id') == $bankAccount->id ? 'selected' : '' }}>
                                                {{ $bankAccount->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error class="mt-2" :messages="$errors->get('bank_account_id')" />
                                </div>
                                <div>
                                    <x-input-label for="first_due_date" value="Primeiro vencimento" />
                                    <x-text-input id="first_due_date" name="first_due_date" type="date" class="mt-1 block w-full" :value="old('first_due_date', now()->toDateString())" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('first_due_date')" />
                                </div>
                                <div>
                                    <x-input-label for="billing_day" value="Dia mensal" />
                                    <x-text-input id="billing_day" name="billing_day" type="number" min="1" max="31" class="mt-1 block w-full" :value="old('billing_day', now()->day)" />
                                    <x-input-error class="mt-2" :messages="$errors->get('billing_day')" />
                                </div>
                            </div>
                            <div class="mt-4 flex justify-end">
                                <x-primary-button>Vincular Plano e Gerar Cobrança</x-primary-button>
                            </div>
                        </form>

                        <div class="space-y-4">
                            @forelse($patient->packages as $patientPackage)
                                @php
                                    $progress = $patientPackage->total_sessions > 0 ? round(($patientPackage->consumed_sessions / $patientPackage->total_sessions) * 100) : 0;
                                @endphp
                                <div class="p-4 rounded-2xl border border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm">
                                    <div class="flex flex-col lg:flex-row lg:items-start justify-between gap-4">
                                        <div>
                                            <div class="flex flex-wrap items-center gap-2">
                                                <h4 class="text-sm font-bold text-gray-900 dark:text-white">{{ $patientPackage->package->name ?? 'Pacote removido' }}</h4>
                                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $patientPackage->status === 'active' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300' }}">
                                                    {{ $patientPackage->status === 'active' ? 'Ativo' : 'Concluído' }}
                                                </span>
                                            </div>
                                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                                {{ $patientPackage->package?->serviceType?->name ?? 'Serviço não informado' }} ·
                                                {{ $patientPackage->start_date->format('d/m/Y') }}
                                                @if($patientPackage->end_date)
                                                    até {{ $patientPackage->end_date->format('d/m/Y') }}
                                                @endif
                                            </p>
                                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                                {{ $patientPackage->billing_type === 'monthly_recurring' ? 'Mensal recorrente' : 'Cobrança avulsa' }}
                                                @if($patientPackage->payment_method)
                                                    · {{ ['cash' => 'Dinheiro', 'pix' => 'Pix', 'credit_card' => 'Cartão crédito', 'debit_card' => 'Cartão débito', 'bank_slip' => 'Boleto', 'bank_transfer' => 'Transferência', 'other' => 'Outro'][$patientPackage->payment_method] ?? $patientPackage->payment_method }}
                                                @endif
                                                @if($patientPackage->bankAccount)
                                                    · {{ $patientPackage->bankAccount->name }}
                                                @endif
                                                @if($patientPackage->next_billing_date)
                                                    · próxima cobrança em {{ $patientPackage->next_billing_date->format('d/m/Y') }}
                                                @endif
                                            </p>
                                        </div>
                                        <div class="grid grid-cols-4 gap-3 text-center">
                                            <div>
                                                <p class="text-xs text-gray-500">Total</p>
                                                <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $patientPackage->total_sessions }}</p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-500">Feitas</p>
                                                <p class="text-sm font-bold text-emerald-600">{{ $patientPackage->used_sessions }}</p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-500">Perdidas</p>
                                                <p class="text-sm font-bold text-red-600">{{ $patientPackage->missed_sessions }}</p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-500">Restantes</p>
                                                <p class="text-sm font-bold text-blue-600">{{ $patientPackage->remaining_sessions }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-4 h-2 rounded-full bg-gray-100 dark:bg-gray-700 overflow-hidden">
                                        <div class="h-full bg-primary-500 rounded-full" style="width: {{ min(100, $progress) }}%"></div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-12 rounded-2xl border border-dashed border-gray-200 dark:border-gray-700">
                                    <p class="text-gray-500 dark:text-gray-400">Nenhum pacote vinculado a este paciente.</p>
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

@push('scripts')
    <script>
        function confirmAppointmentStatus(form, title, text) {
            Swal.fire({
                title: title,
                text: text,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#059669',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Confirmar',
                cancelButtonText: 'Voltar',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });

            return false;
        }
    </script>
@endpush
