@extends('layouts.saas')

@section('title', 'Criar Novo Plano')

@section('content')
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Novo Plano SAAS</h1>
        <p class="text-gray-600 dark:text-gray-400">Configure os detalhes do novo plano de assinatura.</p>
    </div>

    <div class="max-w-2xl bg-white border border-gray-200 rounded-2xl shadow-sm dark:bg-gray-800 dark:border-gray-700 p-8">
        <form action="{{ route('plans.store') }}" method="POST">
            @csrf
            <div class="space-y-6">
                <div>
                    <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nome do Plano</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" placeholder="Ex: Plano Premium" required>
                </div>

                <div>
                    <label for="description" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Descrição</label>
                    <textarea name="description" id="description" rows="4" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" placeholder="O que este plano oferece?">{{ old('description') }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="price" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Preço (R$)</label>
                        <input type="number" step="0.01" name="price" id="price" value="{{ old('price') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" placeholder="0.00" required>
                    </div>
                    <div>
                        <label for="billing_period" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Período de Cobrança</label>
                        <select name="billing_period" id="billing_period" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="monthly" {{ old('billing_period') == 'monthly' ? 'selected' : '' }}>Mensal</option>
                            <option value="yearly" {{ old('billing_period') == 'yearly' ? 'selected' : '' }}>Anual</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end gap-4 pt-4">
                    <a href="{{ route('plans.index') }}" class="px-5 py-2.5 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-xl hover:bg-gray-100 hover:text-primary-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">Cancelar</a>
                    <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:ring-4 focus:outline-none focus:ring-primary-300 rounded-xl text-center dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">Criar Plano</button>
                </div>
            </div>
        </form>
    </div>
@endsection
