@extends('layouts.saas')

@section('title', 'Faturamento')

@section('breadcrumb')
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-primary-600 dark:text-gray-400 dark:hover:text-white">
                    Dashboard
                </a>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2 dark:text-gray-400">Faturamento</span>
                </div>
            </li>
        </ol>
    </nav>
@endsection

@section('content')
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-2xl p-6">
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Faturamento e Cobranças</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Acompanhe as faturas e pagamentos dos pacientes.</p>
            </div>
            <div class="flex gap-2">
                <button class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-xl font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 transition ease-in-out duration-150 shadow-lg shadow-primary-500/30">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Nova Fatura
                </button>
            </div>
        </div>

        {{-- Tabela de Faturas --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700/50 dark:text-gray-400">
                    <tr>
                        <th class="px-6 py-4 font-bold">Nº Fatura / Paciente</th>
                        <th class="px-6 py-4 font-bold">Vencimento</th>
                        <th class="px-6 py-4 font-bold">Valor Total</th>
                        <th class="px-6 py-4 font-bold text-center">Status</th>
                        <th class="px-6 py-4 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($invoices as $invoice)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-gray-900 dark:text-white">{{ $invoice->invoice_number }}</div>
                                <div class="text-xs text-gray-500">{{ $invoice->patient->full_name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm {{ $invoice->due_date->isPast() && $invoice->status === 'pending' ? 'text-red-600 font-bold' : '' }}">
                                {{ $invoice->due_date->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap font-bold text-gray-900 dark:text-white">
                                R$ {{ number_format($invoice->total_amount, 2, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2.5 py-1 rounded-lg text-xs font-bold uppercase tracking-wider
                                    @if($invoice->status === 'paid') bg-green-100 text-green-800 @endif
                                    @if($invoice->status === 'pending') bg-amber-100 text-amber-800 @endif
                                    @if($invoice->status === 'canceled') bg-red-100 text-red-800 @endif
                                ">
                                    {{ $invoice->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('invoices.show', $invoice) }}" class="p-2 text-primary-600 hover:bg-primary-50 dark:hover:bg-primary-900/20 rounded-lg transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-gray-500">
                                Nenhuma fatura encontrada.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $invoices->links() }}
        </div>
    </div>
@endsection
