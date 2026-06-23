@extends('layouts.saas')

@section('title', 'Salas')

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
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2 dark:text-gray-400">Salas</span>
                </div>
            </li>
        </ol>
    </nav>
@endsection

@section('content')
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-2xl p-6">
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Gestão de Salas</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Configure as salas e espaços de atendimento da sua clínica.</p>
            </div>
            <a href="{{ route('rooms.create') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-xl font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-700 transition ease-in-out duration-150 shadow-lg shadow-primary-500/30">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nova Sala
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($rooms as $room)
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-2xl p-5 border border-gray-100 dark:border-gray-700 hover:shadow-lg transition-shadow">
                    <div class="flex justify-between items-start mb-4">
                        <div class="w-12 h-12 rounded-xl bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center text-primary-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <div class="flex gap-1">
                            <a href="{{ route('rooms.edit', $room) }}" class="p-2 text-gray-400 hover:text-amber-600 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            <form action="{{ route('rooms.destroy', $room) }}" method="POST" onsubmit="return confirmDelete(this, 'Excluir esta sala?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-gray-400 hover:text-red-600 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-1">{{ $room->name }}</h3>
                    <div class="flex items-center gap-2 text-sm text-gray-500 mb-4">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        Capacidade: {{ $room->capacity }} pacientes
                    </div>

                    @if($room->resources)
                        <div class="flex flex-wrap gap-1 mt-auto">
                            @foreach($room->resources as $resource)
                                <span class="px-2 py-0.5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded text-[10px] text-gray-600 dark:text-gray-400 font-medium">{{ $resource }}</span>
                            @endforeach
                        </div>
                    @endif

                    <div class="mt-4 flex items-center justify-between">
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $room->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $room->is_active ? 'Ativa' : 'Inativa' }}
                        </span>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-12 text-center bg-gray-50 dark:bg-gray-700/30 rounded-2xl border-2 border-dashed border-gray-200 dark:border-gray-700">
                    <p class="text-gray-500">Nenhuma sala cadastrada.</p>
                    <a href="{{ route('rooms.create') }}" class="text-primary-600 font-bold hover:underline mt-2 inline-block">Criar minha primeira sala</a>
                </div>
            @endforelse
        </div>
    </div>
@endsection
