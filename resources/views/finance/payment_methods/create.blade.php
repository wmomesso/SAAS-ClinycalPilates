@extends('layouts.saas')

@section('title', 'Nova Forma de Pagamento')

@section('content')
    <div class="max-w-3xl mx-auto bg-white dark:bg-gray-800 shadow-md rounded-2xl p-6">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-6">Nova Forma de Pagamento</h2>
        <form method="POST" action="{{ route('payment-methods.store') }}">
            @include('finance.payment_methods._form', ['paymentMethod' => null, 'submitLabel' => 'Salvar Forma'])
        </form>
    </div>
@endsection
