@extends('layouts.saas')

@section('title', 'Editar Conta Bancária')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-2xl p-6">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Editar Conta Bancária</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $bankAccount->name }}</p>
            </div>

            <form method="POST" action="{{ route('bank-accounts.update', $bankAccount) }}">
                @method('PUT')
                @include('finance.bank_accounts._form', ['submitLabel' => 'Atualizar Conta'])
            </form>
        </div>
    </div>
@endsection
