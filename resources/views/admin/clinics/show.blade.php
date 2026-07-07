@extends('layouts.saas')

@section('title', 'Detalhes da Clínica')

@section('content')
    @php
        $domain = config('app.domain');
        $clinicUrl = $clinic->subdomain && $domain ? $clinic->subdomain.'.'.$domain : null;
        $statusLabel = $subscription?->stripe_status ?? 'Sem assinatura';
        $periodLabel = ['monthly' => 'Mensal', 'yearly' => 'Anual'][$subscriptionPlan?->billing_period] ?? null;
    @endphp

    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $clinic->name }}</h1>
            <p class="text-gray-600 dark:text-gray-400">Detalhes cadastrais, assinatura e uso da clínica.</p>
        </div>
        <a href="{{ route('admin.clinics.index') }}" class="inline-flex items-center justify-center rounded-xl border border-gray-200 px-4 py-2.5 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
            Voltar
        </a>
    </div>

    <div class="grid gap-4 md:grid-cols-3">
        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Usuários</p>
            <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $clinic->users_count }}</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Pacientes</p>
            <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $clinic->patients_count }}</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Salas</p>
            <p class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">{{ $clinic->rooms_count }}</p>
        </div>
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-2">
        <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <h2 class="text-lg font-bold text-gray-900 dark:text-white">Dados da Clínica</h2>

            <dl class="mt-6 space-y-4">
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Nome</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $clinic->name }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Subdomínio</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $clinic->subdomain ?? 'Não informado' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">URL</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $clinicUrl ?? 'Não informado' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Documento</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $clinic->document ?? 'Não informado' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Criada em</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $clinic->created_at?->format('d/m/Y H:i') ?? 'Não informado' }}</dd>
                </div>
            </dl>
        </section>

        <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <h2 class="text-lg font-bold text-gray-900 dark:text-white">Responsável</h2>

            <dl class="mt-6 space-y-4">
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Nome</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $clinic->owner->name ?? 'Não informado' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">E-mail</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $clinic->owner->email ?? 'Não informado' }}</dd>
                </div>
            </dl>
        </section>
    </div>

    <section class="mt-6 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-lg font-bold text-gray-900 dark:text-white">Assinatura</h2>
            @if($isSubscribed)
                <span class="w-fit rounded-full bg-green-100 px-2.5 py-1 text-xs font-medium text-green-800 dark:bg-green-900/30 dark:text-green-400">Ativa</span>
            @else
                <span class="w-fit rounded-full bg-red-100 px-2.5 py-1 text-xs font-medium text-red-800 dark:bg-red-900/30 dark:text-red-400">Inativa / Inadimplente</span>
            @endif
        </div>

        <div class="mt-6 grid gap-4 md:grid-cols-2 lg:grid-cols-4">
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Plano</p>
                <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $subscriptionPlan->name ?? 'Não identificado' }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Status Stripe</p>
                <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $statusLabel }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Período</p>
                <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $periodLabel ?? 'Não informado' }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Fim da assinatura</p>
                <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ $subscription?->ends_at?->format('d/m/Y') ?? 'Não informado' }}</p>
            </div>
        </div>

        @if($subscriptionPlan)
            <div class="mt-6 border-t border-gray-200 pt-6 dark:border-gray-700">
                <h3 class="text-sm font-bold text-gray-900 dark:text-white">Limites do plano</h3>
                <div class="mt-4 grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                    <div class="text-sm text-gray-600 dark:text-gray-400">Profissionais: {{ $subscriptionPlan->limit_professionals ?? 'Ilimitado' }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Secretárias: {{ $subscriptionPlan->limit_secretaries ?? 'Ilimitado' }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Pacientes: {{ $subscriptionPlan->limit_patients ?? 'Ilimitado' }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Salas: {{ $subscriptionPlan->limit_rooms ?? 'Ilimitado' }}</div>
                </div>
            </div>
        @endif
    </section>
@endsection
