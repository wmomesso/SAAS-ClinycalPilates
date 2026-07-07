@extends('layouts.saas')

@section('title', 'Conciliação Bancária')

@section('content')
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-2xl p-6 mb-6">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Conciliação Bancária</h2>
        <form method="GET" action="{{ route('bank-reconciliation.index') }}" class="mt-4 grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div>
                <x-input-label for="bank_account_id" value="Conta bancária" />
                <select id="bank_account_id" name="bank_account_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 dark:focus:border-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 rounded-xl shadow-sm">
                    @foreach($bankAccounts as $bankAccount)
                        <option value="{{ $bankAccount->id }}" {{ (int) $bankAccountId === $bankAccount->id ? 'selected' : '' }}>{{ $bankAccount->name }}</option>
                    @endforeach
                </select>
            </div>
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

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-2xl overflow-hidden">
            <div class="p-5 border-b border-gray-100 dark:border-gray-700">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white">Entradas</h3>
            </div>
            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($receivables as $receivable)
                    <div class="p-4">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $receivable->description }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $receivable->due_date->format('d/m/Y') }} · {{ $receivable->patient?->full_name ?? 'Sem paciente' }}</p>
                                @if($receivable->reconciled_date)
                                    <p class="mt-1 text-xs text-emerald-600">Conciliado em {{ $receivable->reconciled_date->format('d/m/Y') }} por {{ $receivable->reconciledBy?->name ?? '-' }}</p>
                                @endif
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-bold text-emerald-600">+ R$ {{ number_format($receivable->amount, 2, ',', '.') }}</p>
                                @unless($receivable->reconciled_date)
                                    <form method="POST" action="{{ route('bank-reconciliation.receivables.reconcile', $receivable) }}" class="mt-2 flex gap-2 justify-end">
                                        @csrf
                                        @method('PATCH')
                                        <input type="date" name="reconciled_date" value="{{ now()->toDateString() }}" class="w-36 rounded-lg border-gray-300 text-xs dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300">
                                        <button type="submit" class="px-3 py-2 rounded-lg bg-emerald-600 text-white text-xs font-bold uppercase">Conciliar</button>
                                    </form>
                                @endunless
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="p-6 text-center text-gray-500">Nenhuma entrada no período.</p>
                @endforelse
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-md rounded-2xl overflow-hidden">
            <div class="p-5 border-b border-gray-100 dark:border-gray-700">
                <h3 class="text-lg font-bold text-gray-800 dark:text-white">Saídas</h3>
            </div>
            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($payables as $payable)
                    <div class="p-4">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $payable->description }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $payable->due_date->format('d/m/Y') }} · {{ $payable->provider ?? 'Sem fornecedor' }}</p>
                                @if($payable->reconciled_date)
                                    <p class="mt-1 text-xs text-emerald-600">Conciliado em {{ $payable->reconciled_date->format('d/m/Y') }} por {{ $payable->reconciledBy?->name ?? '-' }}</p>
                                @endif
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-bold text-red-600">- R$ {{ number_format($payable->amount, 2, ',', '.') }}</p>
                                @unless($payable->reconciled_date)
                                    <form method="POST" action="{{ route('bank-reconciliation.payables.reconcile', $payable) }}" class="mt-2 flex gap-2 justify-end">
                                        @csrf
                                        @method('PATCH')
                                        <input type="date" name="reconciled_date" value="{{ now()->toDateString() }}" class="w-36 rounded-lg border-gray-300 text-xs dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300">
                                        <button type="submit" class="px-3 py-2 rounded-lg bg-emerald-600 text-white text-xs font-bold uppercase">Conciliar</button>
                                    </form>
                                @endunless
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="p-6 text-center text-gray-500">Nenhuma saída no período.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection
