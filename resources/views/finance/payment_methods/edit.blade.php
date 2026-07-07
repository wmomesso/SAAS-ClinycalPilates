@extends('layouts.saas')

@section('title', 'Editar Forma de Pagamento')

@section('content')
    <div class="max-w-3xl mx-auto bg-white dark:bg-gray-800 shadow-md rounded-2xl p-6">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-6">Editar Forma de Pagamento</h2>
        <form method="POST" action="{{ route('payment-methods.update', $paymentMethod) }}">
            @method('PUT')
            @include('finance.payment_methods._form', ['submitLabel' => 'Atualizar Forma'])
        </form>
    </div>
@endsection
