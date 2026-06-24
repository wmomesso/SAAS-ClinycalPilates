@extends('layouts.saas')

@section('title', 'Meu Dashboard')

@section('content')
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Cabeçalho -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
                        <span>Dashboard</span>
                        <span class="text-sm font-normal text-gray-400 dark:text-gray-500 bg-gray-100 dark:bg-gray-700 px-3 py-1 rounded-full">
                            {{ now()->translatedFormat('d/m/Y') }}
                        </span>
                    </h1>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Bem-vindo(a) de volta, <span class="font-semibold text-emerald-600 dark:text-emerald-400">{{ Auth::user()->name }}</span>!
                        Veja o resumo da sua clínica para hoje.
                    </p>
                </div>
                <div class="mt-4 md:mt-0 flex items-center gap-3">
                    <span class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white text-sm font-medium rounded-xl shadow-md shadow-emerald-500/25">
                        <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        {{ now()->translatedFormat('d \d\e F \d\e Y') }}
                    </span>
                </div>
            </div>

            <!-- Cards Principais -->
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
                <!-- Atendimentos Agendados -->
                <div class="group bg-white dark:bg-gray-800 overflow-hidden shadow-lg hover:shadow-xl rounded-2xl border border-gray-100 dark:border-gray-700 transition-all duration-300 hover:scale-[1.02]">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-3 shadow-md shadow-blue-500/25">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Agendados Hoje</dt>
                                    <dd class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalAppointmentsToday }}</dd>
                                </dl>
                            </div>
                        </div>
                        <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-700">
                            <span class="text-xs text-gray-400">Última atualização: agora</span>
                        </div>
                    </div>
                </div>

                <!-- Ocupação das Salas -->
                <div class="group bg-white dark:bg-gray-800 overflow-hidden shadow-lg hover:shadow-xl rounded-2xl border border-gray-100 dark:border-gray-700 transition-all duration-300 hover:scale-[1.02]">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl p-3 shadow-md shadow-emerald-500/25">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Ocupação das Salas</dt>
                                    <dd class="flex items-baseline">
                                        <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ $roomUsagePercentage }}%</span>
                                        <span class="ml-2 text-sm text-gray-500">
                                            {{ $roomUsagePercentage >= 70 ? '🟢 Alto' : ($roomUsagePercentage >= 40 ? '🟡 Médio' : '🔵 Baixo') }}
                                        </span>
                                    </dd>
                                </dl>
                                <div class="mt-1 w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 overflow-hidden">
                                    <div class="bg-gradient-to-r from-emerald-500 to-emerald-400 h-2 rounded-full transition-all duration-500" style="width: {{ $roomUsagePercentage }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Particulares -->
                <div class="group bg-white dark:bg-gray-800 overflow-hidden shadow-lg hover:shadow-xl rounded-2xl border border-gray-100 dark:border-gray-700 transition-all duration-300 hover:scale-[1.02]">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl p-3 shadow-md shadow-amber-500/25">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Atend. Particulares</dt>
                                    <dd class="text-2xl font-bold text-gray-900 dark:text-white">{{ $appointmentsPrivate }}</dd>
                                </dl>
                            </div>
                        </div>
                        <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-700">
                            <span class="text-xs text-gray-400">{{ $totalAppointmentsToday > 0 ? round(($appointmentsPrivate / $totalAppointmentsToday) * 100) : 0 }}% do total</span>
                        </div>
                    </div>
                </div>

                <!-- Convênios -->
                <div class="group bg-white dark:bg-gray-800 overflow-hidden shadow-lg hover:shadow-xl rounded-2xl border border-gray-100 dark:border-gray-700 transition-all duration-300 hover:scale-[1.02]">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-3 shadow-md shadow-purple-500/25">
                                <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Atend. Convênio</dt>
                                    <dd class="text-2xl font-bold text-gray-900 dark:text-white">{{ $appointmentsByInsurance }}</dd>
                                </dl>
                            </div>
                        </div>
                        <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-700">
                            <span class="text-xs text-gray-400">{{ $totalAppointmentsToday > 0 ? round(($appointmentsByInsurance / $totalAppointmentsToday) * 100) : 0 }}% do total</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Tipos de Atendimento -->
                <div class="bg-white dark:bg-gray-800 shadow-lg rounded-2xl p-6 border border-gray-100 dark:border-gray-700 hover:shadow-xl transition-shadow duration-200">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                            <span class="w-1 h-6 bg-indigo-500 rounded-full"></span>
                            Tipos de Atendimento
                        </h3>
                        <span class="text-xs text-gray-400">Hoje</span>
                    </div>
                    <div class="space-y-4">
                        @forelse($appointmentsByType as $type => $count)
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-gray-600 dark:text-gray-400">{{ $type }}</span>
                                    <span class="font-semibold text-gray-900 dark:text-white">{{ $count }}</span>
                                </div>
                                <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-2 overflow-hidden">
                                    @php $percent = $totalAppointmentsToday > 0 ? ($count / $totalAppointmentsToday) * 100 : 0; @endphp
                                    <div class="bg-gradient-to-r from-indigo-500 to-indigo-400 h-2 rounded-full transition-all duration-500" style="width: {{ $percent }}%"></div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-6">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Sem atendimentos hoje.</p>
                            </div>
                        @endforelse
                    </div>

                    <div class="mt-8">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2 mb-4">
                            <span class="w-1 h-6 bg-purple-500 rounded-full"></span>
                            Por Convênio
                        </h3>
                        <div class="space-y-3">
                            @forelse($insuranceStats as $insurance => $count)
                                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $insurance }}</span>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300">
                                        {{ $count }}
                                    </span>
                                </div>
                            @empty
                                <p class="text-sm text-gray-500 dark:text-gray-400 italic text-center py-4">Nenhum convênio hoje.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Agenda do Dia Agrupada por Salas -->
                <div class="lg:col-span-1 bg-white dark:bg-gray-800 shadow-lg rounded-2xl p-6 border border-gray-100 dark:border-gray-700 hover:shadow-xl transition-shadow duration-200">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                            <span class="w-1 h-6 bg-emerald-500 rounded-full"></span>
                            Próximos Atendimentos
                        </h3>
                        <span class="text-xs text-gray-400">{{ $totalAppointmentsToday }} total hoje</span>
                    </div>

                    <div class="space-y-6">
                        @forelse($appointmentsByRoom as $roomName => $appointments)
                            <div class="bg-gray-50 dark:bg-gray-700/30 rounded-2xl p-4 border border-gray-100 dark:border-gray-700/50">
                                <h4 class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-3 flex items-center gap-2">
                                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                    {{ $roomName }}
                                    <span class="ml-auto bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 text-[10px] px-2 py-0.5 rounded-full">
                                        {{ $appointments->count() }}
                                    </span>
                                </h4>
                                <ul role="list" class="space-y-3">
                                    @foreach($appointments as $appointment)
                                        <li class="group bg-white dark:bg-gray-800 p-3 rounded-xl border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-md transition-all duration-200">
                                            <div class="flex items-center space-x-3">
                                                <div class="flex-shrink-0">
                                                    <span class="inline-flex items-center justify-center h-9 w-9 rounded-lg bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 font-bold text-[10px]">
                                                        {{ $appointment->start_time->format('H:i') }}
                                                    </span>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                                                        {{ $appointment->patient->full_name }}
                                                    </p>
                                                    <p class="text-[10px] text-gray-500 dark:text-gray-400 truncate">
                                                        {{ $appointment->serviceType->name }}
                                                    </p>
                                                </div>
                                                <div>
                                                    @if($appointment->status === 'confirmed')
                                                        <span class="h-2 w-2 rounded-full bg-emerald-500 block" title="Confirmado"></span>
                                                    @elseif($appointment->status === 'completed')
                                                        <span class="h-2 w-2 rounded-full bg-blue-500 block" title="Concluído"></span>
                                                    @elseif($appointment->status === 'cancelled')
                                                        <span class="h-2 w-2 rounded-full bg-red-500 block" title="Cancelado"></span>
                                                    @else
                                                        <span class="h-2 w-2 rounded-full bg-yellow-500 block" title="Pendente"></span>
                                                    @endif
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @empty
                            <div class="text-center py-12">
                                <div class="flex flex-col items-center">
                                    <div class="bg-gray-100 dark:bg-gray-700/50 p-4 rounded-full mb-4">
                                        <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">Nada agendado para hoje.</p>
                                </div>
                            </div>
                        @endforelse
                    </div>
                    @if($appointmentsToday->count() > 10)
                        <div class="mt-4 text-center">
                            <a href="{{ route('appointments.index') }}" class="text-xs text-emerald-600 dark:text-emerald-400 hover:underline font-medium">
                                Ver todos os atendimentos →
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Aniversariantes -->
                <div class="bg-white dark:bg-gray-800 shadow-lg rounded-2xl p-6 border border-gray-100 dark:border-gray-700 hover:shadow-xl transition-shadow duration-200">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center gap-2">
                            <span class="w-1 h-6 bg-pink-500 rounded-full"></span>
                            Aniversariantes do Dia
                        </h3>
                        <svg class="h-6 w-6 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 15.546c-.523 0-1.046.151-1.5.454a2.704 2.704 0 01-3 0 2.703 2.703 0 00-3 0 2.704 2.704 0 01-3 0 2.703 2.703 0 00-3 0 2.704 2.704 0 01-3 0 2.701 2.701 0 00-1.5-.454M9 16v2m3-6v6m3-3v3M9 12h6M4 21h16a1 1 0 001-1V10a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1z"/>
                        </svg>
                    </div>
                    <div class="space-y-3">
                        @forelse($birthdaysToday as $patient)
                            <div class="flex items-center p-3 border border-pink-100 dark:border-pink-900/30 bg-gradient-to-r from-pink-50 to-white dark:from-pink-900/10 dark:to-gray-800 rounded-xl hover:shadow-md transition-shadow duration-200">
                                <div class="bg-gradient-to-br from-pink-200 to-pink-300 dark:from-pink-800 dark:to-pink-700 rounded-full h-10 w-10 flex items-center justify-center mr-3 shadow-sm">
                                    <span class="text-pink-700 dark:text-pink-200 text-base">🎂</span>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $patient->full_name }}</p>
                                    <p class="text-xs text-pink-600 dark:text-pink-400">Completando {{ $patient->birth_date->age }} anos!</p>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 15.546c-.523 0-1.046.151-1.5.454a2.704 2.704 0 01-3 0 2.703 2.703 0 00-3 0 2.704 2.704 0 01-3 0 2.703 2.703 0 00-3 0 2.704 2.704 0 01-3 0 2.701 2.701 0 00-1.5-.454M9 16v2m3-6v6m3-3v3M9 12h6M4 21h16a1 1 0 001-1V10a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1z"/>
                                </svg>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Nenhum aniversariante hoje.</p>
                            </div>
                        @endforelse
                    </div>

                    <!-- Atalhos Rápidos -->
                    <div class="mt-8 pt-6 border-t border-gray-100 dark:border-gray-700">
                        <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-4 flex items-center gap-2">
                            <span class="w-1 h-5 bg-emerald-500 rounded-full"></span>
                            Ações Rápidas
                        </h3>
                        <div class="grid grid-cols-2 gap-3">
                            <a href="{{ route('appointments.create') }}"
                               class="flex flex-col items-center p-4 bg-gradient-to-br from-emerald-50 to-emerald-100 dark:from-emerald-900/20 dark:to-emerald-800/20 rounded-xl hover:from-emerald-100 hover:to-emerald-200 dark:hover:from-emerald-900/30 dark:hover:to-emerald-800/30 transition-all duration-200 border border-emerald-100 dark:border-emerald-800/30 group">
                                <span class="text-2xl mb-2 group-hover:scale-110 transition-transform">📅</span>
                                <span class="text-xs font-medium text-emerald-700 dark:text-emerald-300">Novo Agendamento</span>
                            </a>
                            <a href="{{ route('patients.create') }}"
                               class="flex flex-col items-center p-4 bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-xl hover:from-blue-100 hover:to-blue-200 dark:hover:from-blue-900/30 dark:hover:to-blue-800/30 transition-all duration-200 border border-blue-100 dark:border-blue-800/30 group">
                                <span class="text-2xl mb-2 group-hover:scale-110 transition-transform">👤</span>
                                <span class="text-xs font-medium text-blue-700 dark:text-blue-300">Novo Paciente</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
