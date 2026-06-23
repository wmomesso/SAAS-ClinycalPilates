@extends('layouts.saas')

@section('title', 'Gestão de Clínicas')

@section('content')
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Clínicas Clientes</h1>
        <p class="text-gray-600 dark:text-gray-400">Visualize e gerencie todas as clínicas cadastradas no sistema.</p>
    </div>

    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm dark:bg-gray-800 dark:border-gray-700 overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-gray-50 dark:bg-gray-700/50">
                <tr>
                    <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Clínica</th>
                    <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Dono</th>
                    <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Status Assinatura</th>
                    <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($clinics as $clinic)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                        <td class="px-6 py-4">
                            <div class="text-sm font-bold text-gray-900 dark:text-white">{{ $clinic->name }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $clinic->subdomain }}.{{ config('app.domain') }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900 dark:text-white">{{ $clinic->owner->name ?? 'N/A' }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $clinic->owner->email ?? '' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $isSubscribed = false;
                                try {
                                    $isSubscribed = $clinic->subscribed();
                                } catch (\Exception $e) {}
                            @endphp
                            @if($isSubscribed)
                                <span class="px-2.5 py-0.5 text-xs font-medium bg-green-100 text-green-800 rounded-full dark:bg-green-900/30 dark:text-green-400">
                                    Ativa
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 text-xs font-medium bg-red-100 text-red-800 rounded-full dark:bg-red-900/30 dark:text-red-400">
                                    Inativa / Inadimplente
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm font-medium">
                            <a href="#" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">Detalhes</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                            Nenhuma clínica cadastrada.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
