@extends('layouts.saas')

@section('title', 'Relatórios Financeiros')

@section('content')
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-2xl p-6 mb-6">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Relatórios Financeiros</h2>
        <form method="GET" action="{{ route('financial-reports.index') }}" class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            <div>
                <x-input-label for="date_from" value="De" />
                <x-text-input id="date_from" name="date_from" type="date" class="mt-1 block w-full" :value="$dateFrom" />
            </div>
            <div>
                <x-input-label for="date_to" value="Até" />
                <x-text-input id="date_to" name="date_to" type="date" class="mt-1 block w-full" :value="$dateTo" />
            </div>
            <x-primary-button>Filtrar</x-primary-button>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
        @foreach([
            ['label' => 'A receber', 'value' => $summary['receivable_total']],
            ['label' => 'Recebido', 'value' => $summary['received_total']],
            ['label' => 'A pagar', 'value' => $summary['payable_total']],
            ['label' => 'Pago', 'value' => $summary['paid_total']],
            ['label' => 'Saldo projetado', 'value' => $summary['projected_balance']],
            ['label' => 'Saldo realizado', 'value' => $summary['realized_balance']],
        ] as $card)
            <div class="bg-white dark:bg-gray-800 shadow-md rounded-2xl p-5">
                <p class="text-xs font-bold uppercase text-gray-500 dark:text-gray-400">{{ $card['label'] }}</p>
                <p class="mt-2 text-2xl font-black text-gray-900 dark:text-white">R$ {{ number_format($card['value'], 2, ',', '.') }}</p>
            </div>
        @endforeach
    </div>

    <div class="mt-6 bg-white dark:bg-gray-800 shadow-md rounded-2xl p-6">
        <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4">Pendências</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600 dark:text-gray-300">
            <p>Contas a receber pendentes: <span class="font-bold">{{ $summary['open_receivables'] }}</span></p>
            <p>Contas a pagar pendentes: <span class="font-bold">{{ $summary['open_payables'] }}</span></p>
            <p>Recebíveis não conciliados: <span class="font-bold">{{ $summary['unreconciled_receivables'] }}</span></p>
            <p>Pagáveis não conciliados: <span class="font-bold">{{ $summary['unreconciled_payables'] }}</span></p>
        </div>
    </div>
@endsection
