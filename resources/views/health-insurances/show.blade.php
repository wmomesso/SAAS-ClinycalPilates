@extends('layouts.saas')

@section('title', $healthInsurance->name)

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
                    <a href="{{ route('health-insurances.index') }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-primary-600 md:ml-2 dark:text-gray-400 dark:hover:text-white">Convênios</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2 dark:text-gray-400">{{ $healthInsurance->name }}</span>
                </div>
            </li>
        </ol>
    </nav>
@endsection

@section('content')
    <div class="space-y-6">
        {{-- Cabeçalho e Ações --}}
        <div class="flex justify-between items-start">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-primary-100 dark:bg-primary-900/30 rounded-2xl">
                    <svg class="w-8 h-8 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 dark:text-white">{{ $healthInsurance->name }}</h1>
                    <div class="flex items-center gap-2 mt-1">
                        @if($healthInsurance->is_active)
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                Ativo
                            </span>
                        @else
                            <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">
                                Inativo
                            </span>
                        @endif
                        @if($healthInsurance->registration_number)
                            <span class="text-sm text-gray-500 dark:text-gray-400">ANS: {{ $healthInsurance->registration_number }}</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="flex gap-2">
                <a href="{{ route('health-insurances.edit', $healthInsurance) }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-xl font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Editar
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Detalhes da Empresa --}}
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white dark:bg-gray-800 shadow-md rounded-2xl p-6">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4 border-b pb-2 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        Dados da Operadora
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Razão Social</p>
                            <p class="text-gray-700 dark:text-gray-300">{{ $healthInsurance->company_name ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">CNPJ</p>
                            <p class="text-gray-700 dark:text-gray-300">{{ $healthInsurance->cnpj ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">E-mail</p>
                            <p class="text-gray-700 dark:text-gray-300">{{ $healthInsurance->email ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Telefone</p>
                            <p class="text-gray-700 dark:text-gray-300">{{ $healthInsurance->phone ?? '-' }}</p>
                        </div>
                    </div>

                    <div class="mt-6">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Endereço</p>
                        <p class="text-gray-700 dark:text-gray-300">
                            @if($healthInsurance->address)
                                {{ $healthInsurance->address }}, {{ $healthInsurance->address_number }}
                                @if($healthInsurance->complement) - {{ $healthInsurance->complement }} @endif<br>
                                {{ $healthInsurance->neighborhood }} - {{ $healthInsurance->city }}/{{ $healthInsurance->state }}<br>
                                CEP: {{ $healthInsurance->zip_code }}
                            @else
                                Não informado
                            @endif
                        </p>
                    </div>
                </div>

                {{-- Guias de Autorização (Resumo) --}}
                <div class="bg-white dark:bg-gray-800 shadow-md rounded-2xl p-6">
                    <div class="flex justify-between items-center mb-4 border-b pb-2">
                        <h3 class="text-lg font-bold text-gray-800 dark:text-white flex items-center">
                            <svg class="w-5 h-5 mr-2 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            Guias Autorizadas
                        </h3>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th class="px-4 py-3 text-center">Código</th>
                                    <th class="px-4 py-3">Paciente</th>
                                    <th class="px-4 py-3 text-center">Sessões</th>
                                    <th class="px-4 py-3 text-right">Valor</th>
                                    <th class="px-4 py-3 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($healthInsurance->guides as $guide)
                                    <tr class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                        <td class="px-4 py-3 text-center font-medium">{{ $guide->auth_code }}</td>
                                        <td class="px-4 py-3">{{ $guide->patient->name }}</td>
                                        <td class="px-4 py-3 text-center">{{ $guide->total_sessions }}</td>
                                        <td class="px-4 py-3 text-right">R$ {{ number_format($guide->total_value, 2, ',', '.') }}</td>
                                        <td class="px-4 py-3 text-center">
                                            @php
                                                $statusClasses = [
                                                    'pending' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400',
                                                    'billed' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400',
                                                    'paid' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400',
                                                    'denied' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400',
                                                ];
                                            @endphp
                                            <span class="px-2 py-1 rounded-lg text-[10px] font-bold uppercase {{ $statusClasses[$guide->status] ?? '' }}">
                                                {{ $guide->status }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-8 text-center text-gray-500 italic">
                                            Nenhuma guia vinculada a este convênio.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Configurações de Faturamento --}}
            <div class="space-y-6">
                <div class="bg-white dark:bg-gray-800 shadow-md rounded-2xl p-6">
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4 border-b pb-2 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Faturamento
                    </h3>
                    <div class="space-y-4">
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Forma de Pagamento</p>
                            <p class="text-gray-700 dark:text-gray-300">
                                @php
                                    $methods = [
                                        'transfer' => 'Transferência Bancária',
                                        'check' => 'Cheque',
                                        'deposit' => 'Depósito',
                                        'other' => 'Outro',
                                    ];
                                @endphp
                                {{ $methods[$healthInsurance->payment_method] ?? 'Não informada' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Prazo de Recebimento</p>
                            <p class="text-gray-700 dark:text-gray-300">{{ $healthInsurance->payment_terms_days }} dias</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Guias Aceitas</p>
                            <div class="flex flex-wrap gap-2">
                                @forelse($healthInsurance->accepted_guide_types ?? [] as $type)
                                    <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded-lg text-xs text-gray-600 dark:text-gray-400">
                                        {{ $type == 'consulta' ? 'Consulta' : 'SP/SADT' }}
                                    </span>
                                @empty
                                    <span class="text-gray-500 italic text-sm">Nenhuma selecionada</span>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-primary-50 dark:bg-primary-900/10 rounded-2xl p-6 border border-primary-100 dark:border-primary-800/50">
                    <h4 class="text-primary-800 dark:text-primary-300 font-bold mb-2">Resumo Financeiro</h4>
                    <p class="text-sm text-primary-600 dark:text-primary-400 mb-4">Total a receber deste convênio.</p>
                    <div class="text-2xl font-bold text-primary-700 dark:text-primary-300">
                        R$ {{ number_format($healthInsurance->guides()->whereIn('status', ['pending', 'billed'])->sum('total_value'), 2, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
