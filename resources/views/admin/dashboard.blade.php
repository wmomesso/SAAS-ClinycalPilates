@extends('layouts.saas')

@section('title', 'Admin Dashboard')

@section('content')
    <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Dashboard Administrativo</h1>
            <p class="text-gray-600 dark:text-gray-400">Visão geral do ecossistema Clinycal.</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="px-4 py-2 bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300 rounded-2xl text-sm font-semibold border border-primary-100 dark:border-primary-800">
                {{ now()->format('d/m/Y') }}
            </span>
        </div>
    </div>

    {{-- Principais Métricas --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        {{-- Card Total de Clínicas --}}
        <div class="p-6 bg-white border border-gray-100 rounded-3xl shadow-sm dark:bg-gray-800 dark:border-gray-700 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-2xl">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <span class="text-xs font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded-lg dark:bg-blue-900/30">Global</span>
            </div>
            <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total de Clínicas</h2>
            <p class="text-3xl font-black text-gray-900 dark:text-white mt-1">{{ $totalClinics }}</p>
        </div>

        {{-- Card Adimplentes --}}
        <div class="p-6 bg-white border border-gray-100 rounded-3xl shadow-sm dark:bg-gray-800 dark:border-gray-700 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-emerald-50 dark:bg-emerald-900/20 rounded-2xl">
                    <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-lg dark:bg-emerald-900/30">Ativos</span>
            </div>
            <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Adimplentes</h2>
            <p class="text-3xl font-black text-gray-900 dark:text-white mt-1">{{ $activeClinics }}</p>
        </div>

        {{-- Card Inadimplentes --}}
        <div class="p-6 bg-white border border-gray-100 rounded-3xl shadow-sm dark:bg-gray-800 dark:border-gray-700 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-rose-50 dark:bg-rose-900/20 rounded-2xl">
                    <svg class="w-6 h-6 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <span class="text-xs font-bold text-rose-600 bg-rose-50 px-2 py-1 rounded-lg dark:bg-rose-900/30">Risco</span>
            </div>
            <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Inadimplentes</h2>
            <p class="text-3xl font-black text-gray-900 dark:text-white mt-1">{{ $delinquentClinics }}</p>
        </div>

        {{-- Card Faturamento Estimado --}}
        <div class="p-6 bg-white border border-gray-100 rounded-3xl shadow-sm dark:bg-gray-800 dark:border-gray-700 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="p-3 bg-amber-50 dark:bg-amber-900/20 rounded-2xl">
                    <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <span class="text-xs font-bold text-amber-600 bg-amber-50 px-2 py-1 rounded-lg dark:bg-amber-900/30">Mês Atual</span>
            </div>
            <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Faturamento (Est.)</h2>
            <p class="text-3xl font-black text-gray-900 dark:text-white mt-1">R$ {{ number_format(end($revenueData['data']), 2, ',', '.') }}</p>
        </div>
    </div>

    {{-- Gráficos --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        {{-- Gráfico de Faturamento --}}
        <div class="bg-white p-6 border border-gray-100 rounded-3xl shadow-sm dark:bg-gray-800 dark:border-gray-700">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                <svg class="w-5 h-5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/></svg>
                Faturamento (Últimos 3 Meses)
            </h3>
            <div class="h-64">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        {{-- Gráfico de Assinaturas por Plano --}}
        <div class="bg-white p-6 border border-gray-100 rounded-3xl shadow-sm dark:bg-gray-800 dark:border-gray-700">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-2">
                <svg class="w-5 h-5 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/></svg>
                Assinaturas por Plano
            </h3>
            <div class="h-64">
                <canvas id="plansChart"></canvas>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {{-- Listagem rápida de Últimas Clínicas --}}
        <div class="bg-white border border-gray-100 rounded-3xl shadow-sm dark:bg-gray-800 dark:border-gray-700 overflow-hidden">
            <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Últimos Clientes</h3>
                <a href="{{ route('admin.clinics.index') }}" class="text-sm font-bold text-primary-600 hover:text-primary-700 dark:text-primary-400">Ver todas →</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Clínica</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Responsável</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Data</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($latestClinics as $clinic)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-primary-100 dark:bg-primary-900/40 rounded-lg flex items-center justify-center text-primary-600 font-bold text-xs">
                                        {{ substr($clinic->name, 0, 1) }}
                                    </div>
                                    <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $clinic->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                {{ $clinic->owner->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-500">
                                {{ $clinic->created_at->format('d/m/Y') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-6 py-8 text-center text-sm text-gray-500">Nenhuma clínica cadastrada.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Ações Rápidas e Atalhos --}}
        <div class="space-y-6">
            <div class="bg-white border border-gray-100 rounded-3xl shadow-sm dark:bg-gray-800 dark:border-gray-700 p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6">Ações Rápidas</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <a href="{{ route('plans.create') }}" class="flex items-center gap-4 p-4 border border-gray-100 dark:border-gray-700 rounded-2xl hover:bg-primary-50 dark:hover:bg-primary-900/20 hover:border-primary-200 transition-all group">
                        <div class="p-3 bg-primary-50 dark:bg-primary-900/40 rounded-xl text-primary-600 group-hover:scale-110 transition-transform">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        </div>
                        <div>
                            <p class="font-bold text-gray-900 dark:text-white text-sm">Novo Plano</p>
                            <p class="text-xs text-gray-500">Criar modalidade</p>
                        </div>
                    </a>
                    <a href="{{ route('admin.clinics.index') }}" class="flex items-center gap-4 p-4 border border-gray-100 dark:border-gray-700 rounded-2xl hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:border-blue-200 transition-all group">
                        <div class="p-3 bg-blue-50 dark:bg-blue-900/40 rounded-xl text-blue-600 group-hover:scale-110 transition-transform">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </div>
                        <div>
                            <p class="font-bold text-gray-900 dark:text-white text-sm">Ver Clínicas</p>
                            <p class="text-xs text-gray-500">Gerir assinantes</p>
                        </div>
                    </a>
                </div>
            </div>

            {{-- Widget de Planos --}}
            <div class="bg-primary-600 rounded-3xl p-6 text-white shadow-lg shadow-primary-200 dark:shadow-none">
                <div class="flex justify-between items-start mb-4">
                    <h3 class="text-lg font-bold">Resumo de Planos</h3>
                    <svg class="w-8 h-8 text-primary-400 opacity-50" fill="currentColor" viewBox="0 0 24 24"><path d="M13 2H6c-1.1 0-2 .9-2 2v16c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V9l-7-7zm0 1.5L18.5 9H13V3.5zM6 20V4h5v7h7v9H6z"/></svg>
                </div>
                <div class="space-y-3">
                    @foreach($plansStats as $stat)
                    <div class="flex justify-between items-center bg-white/10 p-3 rounded-xl border border-white/10">
                        <span class="text-sm font-medium">{{ $stat->name }}</span>
                        <span class="font-bold">{{ $stat->subscriptions_count }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Gráfico de Faturamento
            const revenueCtx = document.getElementById('revenueChart').getContext('2d');
            new Chart(revenueCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($revenueData['labels']) !!},
                    datasets: [{
                        label: 'Faturamento (R$)',
                        data: {!! json_encode($revenueData['data']) !!},
                        borderColor: '#0ea5e9',
                        backgroundColor: 'rgba(14, 165, 233, 0.1)',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 3,
                        pointRadius: 4,
                        pointBackgroundColor: '#0ea5e9'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { borderDash: [5, 5] }
                        },
                        x: {
                            grid: { display: false }
                        }
                    }
                }
            });

            // Gráfico de Planos
            const plansCtx = document.getElementById('plansChart').getContext('2d');
            new Chart(plansCtx, {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode($plansStats->pluck('name')) !!},
                    datasets: [{
                        data: {!! json_encode($plansStats->pluck('subscriptions_count')) !!},
                        backgroundColor: [
                            '#0ea5e9', '#8b5cf6', '#ec4899', '#f59e0b', '#10b981'
                        ],
                        borderWidth: 0,
                        hoverOffset: 10
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                usePointStyle: true
                            }
                        }
                    },
                    cutout: '70%'
                }
            });
        });
    </script>
    @endpush
@endsection
