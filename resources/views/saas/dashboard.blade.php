@extends('layouts.saas')

@section('title', 'Meu Dashboard')

@section('content')
    {{-- Cabeçalho da Página --}}
    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white mb-4">
        Bem-vindo(a) de volta, {{ Auth::user()->name }}!
    </h1>

    {{-- Seus cards, gráficos e tabelas do dashboard entram aqui --}}
    <div class="p-6 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
        <p class="text-gray-700 dark:text-gray-400">
            Este é o conteúdo principal da sua página de dashboard.
        </p>
    </div>
@endsection
