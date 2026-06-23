@extends('layouts.saas')

@section('title', 'Fatura: ' . $invoice->invoice_number)

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
                    <a href="{{ route('invoices.index') }}" class="ml-1 text-sm font-medium text-gray-700 hover:text-primary-600 md:ml-2 dark:text-gray-400 dark:hover:text-white">Faturamento</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2 dark:text-gray-400">Detalhes</span>
                </div>
            </li>
        </ol>
    </nav>
@endsection

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-2xl overflow-hidden mb-6">
            <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50 dark:bg-gray-700/30">
                <div>
                    <h2 class="text-xl font-bold text-gray-800 dark:text-white">Fatura {{ $invoice->invoice_number }}</h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Gerada em {{ $invoice->created_at->format('d/m/Y') }}</p>
                </div>
                <span class="px-4 py-1.5 rounded-xl text-xs font-bold uppercase tracking-widest
                    @if($invoice->status === 'paid') bg-green-100 text-green-800 @endif
                    @if($invoice->status === 'pending') bg-amber-100 text-amber-800 @endif
                    @if($invoice->status === 'canceled') bg-red-100 text-red-800 @endif
                ">
                    {{ $invoice->status }}
                </span>
            </div>

            <div class="p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-12 mb-12">
                    <div>
                        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Paciente</h3>
                        <div class="flex items-center">
                            <div class="w-12 h-12 rounded-xl bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center text-primary-600 mr-4">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </div>
                            <div>
                                <p class="font-bold text-gray-800 dark:text-white">{{ $invoice->patient->full_name }}</p>
                                <p class="text-sm text-gray-500">{{ $invoice->patient->document_cpf }}</p>
                                <p class="text-sm text-gray-500">{{ $invoice->patient->phone }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="md:text-right">
                        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Pagamento</h3>
                        <p class="text-sm text-gray-500">Vencimento: <span class="font-bold text-gray-800 dark:text-white">{{ $invoice->due_date->format('d/m/Y') }}</span></p>
                        @if($invoice->paid_at)
                            <p class="text-sm text-gray-500">Pago em: <span class="font-bold text-green-600">{{ $invoice->paid_at->format('d/m/Y') }}</span></p>
                        @endif
                    </div>
                </div>

                {{-- Itens da Fatura --}}
                <div class="mb-12">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Itens da Fatura</h3>
                    <table class="w-full text-sm">
                        <thead class="border-b border-gray-100 dark:border-gray-700">
                            <tr>
                                <th class="py-3 text-left font-bold text-gray-700 dark:text-gray-300">Descrição</th>
                                <th class="py-3 text-right font-bold text-gray-700 dark:text-gray-300">Quantidade</th>
                                <th class="py-3 text-right font-bold text-gray-700 dark:text-gray-300">Preço Unit.</th>
                                <th class="py-3 text-right font-bold text-gray-700 dark:text-gray-300">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                            @foreach($invoice->items as $item)
                                <tr>
                                    <td class="py-4 text-gray-800 dark:text-gray-300">{{ $item->description }}</td>
                                    <td class="py-4 text-right text-gray-600 dark:text-gray-400">{{ $item->quantity }}</td>
                                    <td class="py-4 text-right text-gray-600 dark:text-gray-400">R$ {{ number_format($item->unit_price, 2, ',', '.') }}</td>
                                    <td class="py-4 text-right font-bold text-gray-800 dark:text-white">R$ {{ number_format($item->total_price, 2, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="border-t border-gray-200 dark:border-gray-700">
                                <td colspan="3" class="py-6 text-right font-bold text-gray-500 uppercase tracking-widest">Total da Fatura</td>
                                <td class="py-6 text-right text-2xl font-black text-primary-600 dark:text-primary-400">R$ {{ number_format($invoice->total_amount, 2, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                {{-- Histórico de Pagamentos --}}
                @if($invoice->transactions->count() > 0)
                    <div class="mb-12">
                        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Histórico de Pagamentos</h3>
                        <div class="space-y-3">
                            @foreach($invoice->transactions as $transaction)
                                <div class="flex justify-between items-center p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-100 dark:border-gray-700">
                                    <div class="flex items-center">
                                        <div class="w-2 h-2 rounded-full bg-green-500 mr-3"></div>
                                        <div>
                                            <p class="text-sm font-bold text-gray-800 dark:text-white">R$ {{ number_format($transaction->amount, 2, ',', '.') }}</p>
                                            <p class="text-xs text-gray-500">{{ $transaction->payment_method }} • {{ $transaction->transaction_date->format('d/m/Y H:i') }}</p>
                                        </div>
                                    </div>
                                    @if($transaction->notes)
                                        <p class="text-xs text-gray-500 italic">{{ $transaction->notes }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Registrar Pagamento (se não estiver paga) --}}
                @if($invoice->status !== 'paid')
                    <div class="bg-primary-50 dark:bg-primary-900/20 rounded-2xl p-6 border border-primary-100 dark:border-primary-800/30">
                        <h3 class="text-lg font-bold text-primary-800 dark:text-primary-300 mb-4">Registrar Pagamento</h3>
                        <form action="{{ route('invoices.payment', $invoice) }}" method="POST">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                                <div>
                                    <x-input-label for="amount" :value="__('Valor')" />
                                    <x-text-input id="amount" name="amount" type="number" step="0.01" class="mt-1 block w-full" :value="old('amount', $invoice->total_amount - $invoice->amount_paid)" required />
                                </div>
                                <div>
                                    <x-input-label for="payment_method" :value="__('Forma de Pagamento')" />
                                    <select id="payment_method" name="payment_method" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 rounded-xl shadow-sm text-sm">
                                        <option value="Dinheiro">Dinheiro</option>
                                        <option value="Cartão de Crédito">Cartão de Crédito</option>
                                        <option value="Cartão de Débito">Cartão de Débito</option>
                                        <option value="PIX">PIX</option>
                                        <option value="Transferência">Transferência</option>
                                    </select>
                                </div>
                                <x-primary-button class="bg-primary-600 hover:bg-primary-700 h-10 flex justify-center shadow-lg shadow-primary-500/30">
                                    Confirmar Pagamento
                                </x-primary-button>
                            </div>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
