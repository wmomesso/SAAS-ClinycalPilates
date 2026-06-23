@extends('layouts.saas')

@section('title', 'Planos de Assinatura')

@section('content')
    <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Planos SAAS</h1>
            <p class="text-gray-600 dark:text-gray-400">Gerencie os planos de assinatura disponíveis para as clínicas.</p>
        </div>
        <a href="{{ route('plans.create') }}" class="px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-xl transition-colors flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            Novo Plano
        </a>
    </div>

    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm dark:bg-gray-800 dark:border-gray-700 overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-gray-50 dark:bg-gray-700/50">
                <tr>
                    <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</th>
                    <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Preço</th>
                    <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Frequência</th>
                    <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($plans as $plan)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                        <td class="px-6 py-4">
                            <div class="text-sm font-bold text-gray-900 dark:text-white">{{ $plan->name }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ Str::limit($plan->description, 50) }}</div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                            R$ {{ number_format($plan->price, 2, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ $plan->billing_period }}
                        </td>
                        <td class="px-6 py-4 text-sm font-medium">
                            <div class="flex items-center gap-3">
                                <a href="{{ route('plans.edit', $plan) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">Editar</a>
                                <form action="{{ route('plans.destroy', $plan) }}" method="POST" onsubmit="return confirmDelete(this, 'Tem certeza que deseja remover este plano?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">Excluir</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                            Nenhum plano cadastrado.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
