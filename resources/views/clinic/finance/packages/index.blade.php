@extends('layouts.saas')

@section('title', 'Pacotes de Serviço')

@section('breadcrumb')
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-primary-600 dark:text-gray-400 dark:hover:text-white">
                    Dashboard
                </a>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2 dark:text-gray-400">Pacotes de Serviço</span>
                </div>
            </li>
        </ol>
    </nav>
@endsection

@section('content')
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-2xl p-6">
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Pacotes de Serviço</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Gerencie os planos e pacotes de sessões oferecidos aos pacientes.</p>
            </div>
            <a href="{{ route('service-packages.create') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-xl font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 transition ease-in-out duration-150 shadow-lg shadow-primary-500/30">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Novo Pacote
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($packages as $package)
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-2xl p-6 border border-gray-100 dark:border-gray-700 hover:shadow-lg transition-shadow relative overflow-hidden group">
                    <div class="absolute top-0 right-0 p-4">
                        <div class="flex gap-1">
                            <a href="{{ route('service-packages.edit', $package) }}" class="p-2 text-gray-400 hover:text-amber-600 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                        </div>
                    </div>

                    <div class="mb-4">
                        <span class="px-2 py-1 bg-primary-100 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300 text-[10px] font-bold uppercase rounded-lg">
                            {{ $package->serviceType->name }}
                        </span>
                    </div>

                    <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-2">{{ $package->name }}</h3>

                    <div class="flex items-baseline gap-1 mb-4">
                        <span class="text-2xl font-black text-primary-600 dark:text-primary-400">R$ {{ number_format($package->price, 2, ',', '.') }}</span>
                    </div>

                    <div class="space-y-3 mb-6">
                        <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>{{ $package->total_sessions }} Sessões</span>
                        </div>
                        <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Validade: {{ $package->validity_days }} dias</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-4 border-t border-gray-100 dark:border-gray-600">
                         <span class="text-xs text-gray-500">Preço por sessão: R$ {{ number_format($package->price / $package->total_sessions, 2, ',', '.') }}</span>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-12 text-center bg-gray-50 dark:bg-gray-700/30 rounded-2xl border-2 border-dashed border-gray-200 dark:border-gray-700">
                    <p class="text-gray-500">Nenhum pacote de serviço cadastrado.</p>
                    <a href="{{ route('service-packages.create') }}" class="text-primary-600 font-bold hover:underline mt-2 inline-block">Criar meu primeiro pacote</a>
                </div>
            @endforelse
        </div>
    </div>
@endsection
