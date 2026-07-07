@extends('layouts.saas')

@section('title', 'Editar Conta a Receber')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-2xl p-6">
            <div class="mb-6">
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Gerenciar Pagamento</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $receivable->description }}</p>
            </div>

            <form method="POST" action="{{ route('receivables.update', $receivable) }}">
                @method('PUT')
                @include('finance.receivables._form', ['submitLabel' => 'Atualizar Cobrança'])
            </form>
        </div>
    </div>
@endsection
