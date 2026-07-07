@extends('layouts.saas')

@section('title', 'Formas de Pagamento')

@section('content')
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-2xl overflow-hidden">
        <div class="p-6 flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-gray-100 dark:border-gray-700">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Formas de Pagamento</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Cadastre as formas aceitas pela clínica.</p>
            </div>
            <a href="{{ route('payment-methods.create') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 rounded-xl text-xs font-bold uppercase tracking-wider text-white hover:bg-primary-700">
                Nova Forma
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th class="px-6 py-3">Nome</th>
                        <th class="px-6 py-3">Tipo</th>
                        <th class="px-6 py-3">Conta bancária</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-right">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($paymentMethods as $method)
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $method->name }}</td>
                            <td class="px-6 py-4">{{ ['cash' => 'Dinheiro', 'pix' => 'Pix', 'credit_card' => 'Cartão crédito', 'debit_card' => 'Cartão débito', 'bank_slip' => 'Boleto', 'bank_transfer' => 'Transferência', 'health_insurance' => 'Convênio', 'other' => 'Outro'][$method->type] ?? $method->type }}</td>
                            <td class="px-6 py-4">{{ $method->requires_bank_account ? 'Obrigatória' : 'Opcional' }}</td>
                            <td class="px-6 py-4">{{ $method->is_active ? 'Ativa' : 'Inativa' }}</td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('payment-methods.edit', $method) }}" class="font-medium text-primary-600 dark:text-primary-400 hover:underline">Editar</a>
                                <form method="POST" action="{{ route('payment-methods.destroy', $method) }}" class="inline ml-3" onsubmit="return confirmDelete(this, 'Desativar esta forma de pagamento?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="font-medium text-red-600 hover:underline">Desativar</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
