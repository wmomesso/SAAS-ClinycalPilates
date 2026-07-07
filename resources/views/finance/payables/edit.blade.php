@extends('layouts.saas')

@section('title', 'Editar Conta a Pagar')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-2xl p-6">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-6">Editar Conta a Pagar</h2>
            <form method="POST" action="{{ route('payables.update', $payable) }}">
                @method('PUT')
                @include('finance.payables._form', ['submitLabel' => 'Atualizar Conta'])
            </form>
        </div>
    </div>
@endsection
