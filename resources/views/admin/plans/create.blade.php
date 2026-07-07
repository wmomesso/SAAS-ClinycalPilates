@extends('layouts.saas')

@section('title', 'Criar Novo Plano')

@section('content')
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Novo Plano SAAS</h1>
        <p class="text-gray-600 dark:text-gray-400">Configure os detalhes do novo plano de assinatura.</p>
    </div>

    <div class="max-w-4xl bg-white border border-gray-200 rounded-2xl shadow-sm dark:bg-gray-800 dark:border-gray-700 p-8">
        @if ($errors->any())
            <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700 dark:border-red-900/50 dark:bg-red-900/20 dark:text-red-300">
                <p class="font-semibold">Verifique os campos abaixo antes de salvar.</p>
                <ul class="mt-2 list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('plans.store') }}" method="POST">
            @csrf
            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nome do Plano</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" placeholder="Ex: Plano Premium" required>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="slug" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Identificador interno</label>
                        <input type="text" name="slug" id="slug" value="{{ old('slug') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" placeholder="Gerado pelo nome se ficar vazio">
                        @error('slug')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="description" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Descrição</label>
                    <textarea name="description" id="description" rows="4" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" placeholder="O que este plano oferece?">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="price" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Preço (R$)</label>
                        <input type="number" step="0.01" name="price" id="price" value="{{ old('price') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" placeholder="0.00" required>
                        @error('price')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="billing_period" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Período de Cobrança</label>
                        <select name="billing_period" id="billing_period" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <option value="monthly" {{ old('billing_period', 'monthly') == 'monthly' ? 'selected' : '' }}>Mensal</option>
                            <option value="yearly" {{ old('billing_period') == 'yearly' ? 'selected' : '' }}>Anual</option>
                        </select>
                        @error('billing_period')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="stripe_plan_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">ID do preço/plano no Stripe</label>
                    <input type="text" name="stripe_plan_id" id="stripe_plan_id" value="{{ old('stripe_plan_id') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" placeholder="Ex: price_..." required>
                    @error('stripe_plan_id')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="border-t border-gray-200 pt-6 dark:border-gray-700">
                    <h2 class="text-base font-semibold text-gray-900 dark:text-white">Limites do plano</h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Deixe vazio para permitir uso ilimitado.</p>

                    <div class="mt-4 grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label for="limit_professionals" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Profissionais</label>
                            <input type="number" min="0" name="limit_professionals" id="limit_professionals" value="{{ old('limit_professionals') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            @error('limit_professionals')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="limit_secretaries" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Secretárias</label>
                            <input type="number" min="0" name="limit_secretaries" id="limit_secretaries" value="{{ old('limit_secretaries') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            @error('limit_secretaries')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="limit_patients" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Pacientes</label>
                            <input type="number" min="0" name="limit_patients" id="limit_patients" value="{{ old('limit_patients') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            @error('limit_patients')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="limit_rooms" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Salas</label>
                            <input type="number" min="0" name="limit_rooms" id="limit_rooms" value="{{ old('limit_rooms') }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-xl focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            @error('limit_rooms')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" id="is_active" value="1" class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500" {{ old('is_active', true) ? 'checked' : '' }}>
                    <label for="is_active" class="text-sm font-medium text-gray-900 dark:text-white">Plano ativo para contratação</label>
                    @error('is_active')
                        <p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end gap-4 pt-4">
                    <a href="{{ route('plans.index') }}" class="px-5 py-2.5 text-sm font-medium text-gray-900 bg-white border border-gray-200 rounded-xl hover:bg-gray-100 hover:text-primary-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">Cancelar</a>
                    <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:ring-4 focus:outline-none focus:ring-primary-300 rounded-xl text-center dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">Criar Plano</button>
                </div>
            </div>
        </form>
    </div>
@endsection
