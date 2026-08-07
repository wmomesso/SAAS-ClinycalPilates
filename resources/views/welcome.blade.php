<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Clinycal Pilates centraliza a gestao de clinicas, estudios de pilates, pacientes, agenda, financeiro e assinaturas em um unico sistema.">

        <title>Clinycal Pilates | Sistema para clinicas e estudios de pilates</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="bg-slate-50 text-slate-950 antialiased">
        <div class="min-h-screen overflow-hidden">
            <header class="border-b border-slate-200/80 bg-white/90 backdrop-blur">
                <div class="mx-auto flex max-w-7xl items-center justify-between px-5 py-4 sm:px-6 lg:px-8">
                    <a href="/" class="flex items-center gap-3" aria-label="Clinycal Pilates">
                        <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-600 text-sm font-bold text-white shadow-sm">CP</span>
                        <span class="text-lg font-semibold text-slate-950">Clinycal Pilates</span>
                    </a>

                    <nav class="hidden items-center gap-8 text-sm font-medium text-slate-600 md:flex" aria-label="Navegacao principal">
                        <a href="#recursos" class="transition hover:text-emerald-700">Recursos</a>
                        <a href="#operacao" class="transition hover:text-emerald-700">Operacao</a>
                        <a href="#financeiro" class="transition hover:text-emerald-700">Financeiro</a>
                        <a href="#implantacao" class="transition hover:text-emerald-700">Implantacao</a>
                    </nav>

                    <div class="flex items-center gap-3">
                        @auth
                            <a href="{{ route('dashboard') }}" class="hidden rounded-md border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-emerald-200 hover:bg-emerald-50 hover:text-emerald-700 sm:inline-flex">Dashboard</a>
                        @else
                            @if (Route::has('login'))
                                <a href="{{ route('login') }}" class="hidden rounded-md px-4 py-2 text-sm font-semibold text-slate-700 transition hover:text-emerald-700 sm:inline-flex">Entrar</a>
                            @endif

                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="inline-flex rounded-md bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm shadow-emerald-500/20 transition hover:bg-emerald-700">Criar conta</a>
                            @endif
                        @endauth
                    </div>
                </div>
            </header>

            <main>
                <section class="relative bg-white">
                    <div class="mx-auto grid max-w-7xl gap-12 px-5 py-14 sm:px-6 lg:grid-cols-[0.95fr_1.05fr] lg:items-center lg:px-8 lg:py-20">
                        <div class="max-w-2xl">
                            <p class="mb-5 inline-flex rounded-md border border-emerald-200 bg-emerald-50 px-3 py-1 text-sm font-semibold text-emerald-800">Gestao completa para clinicas e estudios</p>
                            <h1 class="text-4xl font-bold leading-tight text-slate-950 sm:text-5xl lg:text-6xl">
                                Sistema para gerenciar pilates com agenda, pacientes e financeiro no mesmo lugar
                            </h1>
                            <p class="mt-6 max-w-xl text-lg leading-8 text-slate-600">
                                O Clinycal Pilates organiza a rotina da sua equipe, reduz controles manuais e entrega uma visao clara de aulas, salas, pacotes, recebimentos, convênios e evolucao dos pacientes.
                            </p>

                            <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                                @auth
                                    <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center gap-2 rounded-md bg-emerald-600 px-5 py-3 text-sm font-semibold text-white shadow-sm shadow-emerald-500/20 transition hover:bg-emerald-700">
                                        Acessar dashboard
                                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path fill-rule="evenodd" d="M3 10a.75.75 0 0 1 .75-.75h10.64l-3.22-3.22a.75.75 0 1 1 1.06-1.06l4.5 4.5a.75.75 0 0 1 0 1.06l-4.5 4.5a.75.75 0 1 1-1.06-1.06l3.22-3.22H3.75A.75.75 0 0 1 3 10Z" clip-rule="evenodd" />
                                        </svg>
                                    </a>
                                @else
                                    @if (Route::has('register'))
                                        <a href="{{ route('register') }}" class="inline-flex items-center justify-center gap-2 rounded-md bg-emerald-600 px-5 py-3 text-sm font-semibold text-white shadow-sm shadow-emerald-500/20 transition hover:bg-emerald-700">
                                            Comecar agora
                                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path fill-rule="evenodd" d="M3 10a.75.75 0 0 1 .75-.75h10.64l-3.22-3.22a.75.75 0 1 1 1.06-1.06l4.5 4.5a.75.75 0 0 1 0 1.06l-4.5 4.5a.75.75 0 1 1-1.06-1.06l3.22-3.22H3.75A.75.75 0 0 1 3 10Z" clip-rule="evenodd" />
                                            </svg>
                                        </a>
                                    @endif
                                @endauth

                                <a href="#recursos" class="inline-flex items-center justify-center rounded-md border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-800 shadow-sm transition hover:border-sky-200 hover:bg-sky-50 hover:text-sky-800">
                                    Ver recursos
                                </a>
                            </div>

                            <dl class="mt-10 grid grid-cols-3 gap-4 border-t border-slate-200 pt-6">
                                <div>
                                    <dt class="text-sm text-slate-500">Modulos</dt>
                                    <dd class="mt-1 text-2xl font-bold text-slate-950">10+</dd>
                                </div>
                                <div>
                                    <dt class="text-sm text-slate-500">Agenda</dt>
                                    <dd class="mt-1 text-2xl font-bold text-slate-950">Visual</dd>
                                </div>
                                <div>
                                    <dt class="text-sm text-slate-500">Controle</dt>
                                    <dd class="mt-1 text-2xl font-bold text-slate-950">SaaS</dd>
                                </div>
                            </dl>
                        </div>

                        <div class="relative">
                            <div class="absolute -left-8 top-10 hidden h-28 w-28 rounded-lg bg-sky-100 lg:block"></div>
                            <div class="absolute -bottom-8 right-6 hidden h-32 w-32 rounded-lg bg-amber-100 lg:block"></div>

                            <div class="relative overflow-hidden rounded-lg border border-slate-200 bg-white shadow-2xl shadow-slate-200">
                                <div class="flex items-center justify-between border-b border-slate-200 bg-slate-900 px-4 py-3 text-white">
                                    <div>
                                        <p class="text-xs font-semibold uppercase text-emerald-300">Painel da clinica</p>
                                        <p class="text-sm font-semibold">Hoje, segunda-feira</p>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="h-2.5 w-2.5 rounded-full bg-emerald-400"></span>
                                        <span class="text-xs text-slate-300">Sincronizado</span>
                                    </div>
                                </div>

                                <div class="grid gap-0 lg:grid-cols-[14rem_1fr]">
                                    <aside class="hidden border-r border-slate-200 bg-slate-50 p-4 lg:block">
                                        <div class="space-y-2 text-sm">
                                            <span class="block rounded-md bg-emerald-600 px-3 py-2 font-semibold text-white">Agenda</span>
                                            <span class="block rounded-md px-3 py-2 text-slate-600">Pacientes</span>
                                            <span class="block rounded-md px-3 py-2 text-slate-600">Salas</span>
                                            <span class="block rounded-md px-3 py-2 text-slate-600">Financeiro</span>
                                        </div>
                                    </aside>

                                    <div class="p-4 sm:p-6">
                                        <div class="grid gap-4 sm:grid-cols-3">
                                            <div class="rounded-lg border border-slate-200 p-4">
                                                <p class="text-xs font-medium text-slate-500">Aulas hoje</p>
                                                <p class="mt-2 text-2xl font-bold text-slate-950">28</p>
                                                <p class="mt-1 text-xs text-emerald-700">6 salas ativas</p>
                                            </div>
                                            <div class="rounded-lg border border-slate-200 p-4">
                                                <p class="text-xs font-medium text-slate-500">Receber</p>
                                                <p class="mt-2 text-2xl font-bold text-slate-950">R$ 4,8k</p>
                                                <p class="mt-1 text-xs text-sky-700">Pacotes e avulsos</p>
                                            </div>
                                            <div class="rounded-lg border border-slate-200 p-4">
                                                <p class="text-xs font-medium text-slate-500">Ocupacao</p>
                                                <p class="mt-2 text-2xl font-bold text-slate-950">82%</p>
                                                <p class="mt-1 text-xs text-amber-700">Pico as 18h</p>
                                            </div>
                                        </div>

                                        <div class="mt-5 rounded-lg border border-slate-200">
                                            <div class="grid grid-cols-[4.5rem_1fr_1fr] border-b border-slate-200 bg-slate-50 px-4 py-3 text-xs font-semibold text-slate-500 sm:grid-cols-[5rem_1fr_1fr_1fr]">
                                                <span>Hora</span>
                                                <span>Paciente</span>
                                                <span>Servico</span>
                                                <span class="hidden sm:block">Status</span>
                                            </div>
                                            <div class="divide-y divide-slate-100 text-sm">
                                                <div class="grid grid-cols-[4.5rem_1fr_1fr] items-center px-4 py-3 sm:grid-cols-[5rem_1fr_1fr_1fr]">
                                                    <span class="font-semibold text-slate-900">08:00</span>
                                                    <span class="text-slate-700">Marina Lopes</span>
                                                    <span class="text-slate-600">Pilates solo</span>
                                                    <span class="hidden text-emerald-700 sm:block">Confirmado</span>
                                                </div>
                                                <div class="grid grid-cols-[4.5rem_1fr_1fr] items-center px-4 py-3 sm:grid-cols-[5rem_1fr_1fr_1fr]">
                                                    <span class="font-semibold text-slate-900">09:30</span>
                                                    <span class="text-slate-700">Rafael Costa</span>
                                                    <span class="text-slate-600">Reformer</span>
                                                    <span class="hidden text-sky-700 sm:block">Em sala</span>
                                                </div>
                                                <div class="grid grid-cols-[4.5rem_1fr_1fr] items-center px-4 py-3 sm:grid-cols-[5rem_1fr_1fr_1fr]">
                                                    <span class="font-semibold text-slate-900">11:00</span>
                                                    <span class="text-slate-700">Ana Paula</span>
                                                    <span class="text-slate-600">Avaliacao</span>
                                                    <span class="hidden text-amber-700 sm:block">Pendente</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section id="recursos" class="border-y border-slate-200 bg-slate-50 py-16 sm:py-20">
                    <div class="mx-auto max-w-7xl px-5 sm:px-6 lg:px-8">
                        <div class="max-w-3xl">
                            <p class="text-sm font-semibold uppercase text-emerald-700">Recursos</p>
                            <h2 class="mt-3 text-3xl font-bold text-slate-950 sm:text-4xl">Tudo que a clinica precisa para operar sem planilhas paralelas</h2>
                            <p class="mt-4 text-lg leading-8 text-slate-600">Da primeira avaliacao ao recebimento, cada area do estudio trabalha com dados conectados e processos padronizados.</p>
                        </div>

                        <div class="mt-10 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                            <article class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
                                <div class="mb-5 flex h-11 w-11 items-center justify-center rounded-md bg-emerald-100 text-emerald-700">
                                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M5.75 3a.75.75 0 0 1 .75.75V5h7V3.75a.75.75 0 0 1 1.5 0V5h.25A2.75 2.75 0 0 1 18 7.75v6.5A2.75 2.75 0 0 1 15.25 17H4.75A2.75 2.75 0 0 1 2 14.25v-6.5A2.75 2.75 0 0 1 4.75 5H5V3.75A.75.75 0 0 1 5.75 3ZM3.5 9v5.25c0 .69.56 1.25 1.25 1.25h10.5c.69 0 1.25-.56 1.25-1.25V9h-13Z"/></svg>
                                </div>
                                <h3 class="text-lg font-semibold text-slate-950">Agenda inteligente</h3>
                                <p class="mt-3 text-sm leading-6 text-slate-600">Controle aulas, atendimentos, confirmacoes, status e encaixes com visualizacao diaria, semanal e por profissional.</p>
                            </article>

                            <article class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
                                <div class="mb-5 flex h-11 w-11 items-center justify-center rounded-md bg-sky-100 text-sky-700">
                                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M10 9a3 3 0 1 0 0-6 3 3 0 0 0 0 6ZM3.5 17a6.5 6.5 0 0 1 13 0 .75.75 0 0 1-.75.75H4.25A.75.75 0 0 1 3.5 17Z"/></svg>
                                </div>
                                <h3 class="text-lg font-semibold text-slate-950">Prontuario e evolucao</h3>
                                <p class="mt-3 text-sm leading-6 text-slate-600">Cadastre pacientes, anamneses, documentos, pacotes contratados e evolucoes para acompanhar o progresso com historico completo.</p>
                            </article>

                            <article class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
                                <div class="mb-5 flex h-11 w-11 items-center justify-center rounded-md bg-amber-100 text-amber-700">
                                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M4 3.5A1.5 1.5 0 0 1 5.5 2h9A1.5 1.5 0 0 1 16 3.5v13.75a.75.75 0 0 1-1.2.6L10 14.25l-4.8 3.6a.75.75 0 0 1-1.2-.6V3.5Zm3.25 3a.75.75 0 0 0 0 1.5h5.5a.75.75 0 0 0 0-1.5h-5.5Zm0 3a.75.75 0 0 0 0 1.5h5.5a.75.75 0 0 0 0-1.5h-5.5Z" clip-rule="evenodd"/></svg>
                                </div>
                                <h3 class="text-lg font-semibold text-slate-950">Planos e pacotes</h3>
                                <p class="mt-3 text-sm leading-6 text-slate-600">Gerencie pacotes de sessoes, servicos, contratos recorrentes e controle de utilizacao sem depender de controles externos.</p>
                            </article>

                            <article class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
                                <div class="mb-5 flex h-11 w-11 items-center justify-center rounded-md bg-rose-100 text-rose-700">
                                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M3.5 4.75A2.75 2.75 0 0 1 6.25 2h7.5a2.75 2.75 0 0 1 2.75 2.75v10.5A2.75 2.75 0 0 1 13.75 18h-7.5a2.75 2.75 0 0 1-2.75-2.75V4.75Zm2.75-1.25c-.69 0-1.25.56-1.25 1.25v10.5c0 .69.56 1.25 1.25 1.25h7.5c.69 0 1.25-.56 1.25-1.25V4.75c0-.69-.56-1.25-1.25-1.25h-7.5Zm1 3.25A.75.75 0 0 1 8 6h4a.75.75 0 0 1 0 1.5H8a.75.75 0 0 1-.75-.75Zm0 3A.75.75 0 0 1 8 9h4a.75.75 0 0 1 0 1.5H8a.75.75 0 0 1-.75-.75Z"/></svg>
                                </div>
                                <h3 class="text-lg font-semibold text-slate-950">Salas e recursos</h3>
                                <p class="mt-3 text-sm leading-6 text-slate-600">Evite conflitos de horario com controle de salas, equipamentos, profissionais e capacidade operacional do estudio.</p>
                            </article>

                            <article id="financeiro" class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
                                <div class="mb-5 flex h-11 w-11 items-center justify-center rounded-md bg-indigo-100 text-indigo-700">
                                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M2.5 6.75A2.75 2.75 0 0 1 5.25 4h9.5a2.75 2.75 0 0 1 2.75 2.75v6.5A2.75 2.75 0 0 1 14.75 16h-9.5a2.75 2.75 0 0 1-2.75-2.75v-6.5Zm1.5 1V8.5h12v-.75c0-.69-.56-1.25-1.25-1.25h-9.5C4.56 6.5 4 7.06 4 7.75Zm12 2.25H4v3.25c0 .69.56 1.25 1.25 1.25h9.5c.69 0 1.25-.56 1.25-1.25V10Z"/></svg>
                                </div>
                                <h3 class="text-lg font-semibold text-slate-950">Financeiro integrado</h3>
                                <p class="mt-3 text-sm leading-6 text-slate-600">Acompanhe contas a pagar, receber, pagamentos, conciliacao bancaria e relatorios financeiros em tempo real.</p>
                            </article>

                            <article class="rounded-lg border border-slate-200 bg-white p-6 shadow-sm">
                                <div class="mb-5 flex h-11 w-11 items-center justify-center rounded-md bg-cyan-100 text-cyan-700">
                                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 2.5 4 5.1v4.35c0 3.84 2.43 7.24 6 8.05 3.57-.81 6-4.21 6-8.05V5.1l-6-2.6Zm2.78 5.72a.75.75 0 0 1 0 1.06l-3.25 3.25a.75.75 0 0 1-1.06 0L7.22 11.28a.75.75 0 1 1 1.06-1.06L9 10.94l2.72-2.72a.75.75 0 0 1 1.06 0Z" clip-rule="evenodd"/></svg>
                                </div>
                                <h3 class="text-lg font-semibold text-slate-950">Convênios e permissoes</h3>
                                <p class="mt-3 text-sm leading-6 text-slate-600">Organize guias, convênios, acessos de usuarios e regras por funcao para manter a operacao controlada.</p>
                            </article>
                        </div>
                    </div>
                </section>

                <section id="operacao" class="bg-white py-16 sm:py-20">
                    <div class="mx-auto grid max-w-7xl gap-10 px-5 sm:px-6 lg:grid-cols-[0.9fr_1.1fr] lg:items-start lg:px-8">
                        <div>
                            <p class="text-sm font-semibold uppercase text-sky-700">Operacao diaria</p>
                            <h2 class="mt-3 text-3xl font-bold text-slate-950 sm:text-4xl">Visao operacional para coordenar equipe, agenda e atendimento</h2>
                            <p class="mt-4 text-lg leading-8 text-slate-600">A tela de trabalho foi pensada para quem precisa tomar decisoes rapidas: confirmar presencas, ajustar horarios, consultar historico e acompanhar indicadores sem trocar de ferramenta.</p>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="rounded-lg border border-slate-200 bg-slate-50 p-5">
                                <p class="text-sm font-semibold text-slate-950">Check-in de pacientes</p>
                                <p class="mt-2 text-sm leading-6 text-slate-600">Status de atendimento, confirmacao e faltas ficam visiveis para recepcao e instrutores.</p>
                            </div>
                            <div class="rounded-lg border border-slate-200 bg-slate-50 p-5">
                                <p class="text-sm font-semibold text-slate-950">Historico centralizado</p>
                                <p class="mt-2 text-sm leading-6 text-slate-600">Anamneses, documentos, evolucoes e pacotes acompanham o cadastro do paciente.</p>
                            </div>
                            <div class="rounded-lg border border-slate-200 bg-slate-50 p-5">
                                <p class="text-sm font-semibold text-slate-950">Indicadores de ocupacao</p>
                                <p class="mt-2 text-sm leading-6 text-slate-600">Veja horarios de pico, salas mais usadas e capacidade disponivel para novas vendas.</p>
                            </div>
                            <div class="rounded-lg border border-slate-200 bg-slate-50 p-5">
                                <p class="text-sm font-semibold text-slate-950">Perfis e permissoes</p>
                                <p class="mt-2 text-sm leading-6 text-slate-600">Separe acessos de administradores, recepcao, financeiro e profissionais de atendimento.</p>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="bg-slate-900 py-16 text-white sm:py-20">
                    <div class="mx-auto max-w-7xl px-5 sm:px-6 lg:px-8">
                        <div class="grid gap-10 lg:grid-cols-[0.9fr_1.1fr] lg:items-center">
                            <div>
                                <p class="text-sm font-semibold uppercase text-emerald-300">Financeiro e crescimento</p>
                                <h2 class="mt-3 text-3xl font-bold sm:text-4xl">Controle de caixa conectado ao atendimento</h2>
                                <p class="mt-4 text-lg leading-8 text-slate-300">Recebimentos, pacotes, notas de servico, cobrancas e conciliacao passam a conversar com a agenda. A gestao enxerga o que vendeu, o que foi entregue e o que ainda precisa ser cobrado.</p>
                            </div>

                            <div class="grid gap-4 sm:grid-cols-3">
                                <div class="rounded-lg border border-white/10 bg-white/5 p-5">
                                    <p class="text-sm text-slate-300">Recebiveis</p>
                                    <p class="mt-3 text-3xl font-bold">+ clareza</p>
                                </div>
                                <div class="rounded-lg border border-white/10 bg-white/5 p-5">
                                    <p class="text-sm text-slate-300">Pacotes</p>
                                    <p class="mt-3 text-3xl font-bold">+ controle</p>
                                </div>
                                <div class="rounded-lg border border-white/10 bg-white/5 p-5">
                                    <p class="text-sm text-slate-300">Relatorios</p>
                                    <p class="mt-3 text-3xl font-bold">+ decisao</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section id="implantacao" class="bg-white py-16 sm:py-20">
                    <div class="mx-auto max-w-7xl px-5 sm:px-6 lg:px-8">
                        <div class="grid gap-10 rounded-lg border border-slate-200 bg-slate-50 p-6 sm:p-8 lg:grid-cols-[1fr_auto] lg:items-center">
                            <div>
                                <p class="text-sm font-semibold uppercase text-emerald-700">Comece com estrutura</p>
                                <h2 class="mt-3 text-3xl font-bold text-slate-950">Organize sua clinica em uma plataforma pronta para crescer</h2>
                                <p class="mt-4 max-w-3xl text-lg leading-8 text-slate-600">Cadastre usuarios, planos, salas, pacientes e servicos. O Clinycal Pilates oferece a base para transformar a rotina administrativa em um processo mensuravel e escalavel.</p>
                            </div>

                            <div class="flex flex-col gap-3 sm:flex-row lg:flex-col">
                                @auth
                                    <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center rounded-md bg-emerald-600 px-5 py-3 text-sm font-semibold text-white shadow-sm shadow-emerald-500/20 transition hover:bg-emerald-700">Abrir sistema</a>
                                @else
                                    @if (Route::has('register'))
                                        <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-md bg-emerald-600 px-5 py-3 text-sm font-semibold text-white shadow-sm shadow-emerald-500/20 transition hover:bg-emerald-700">Criar conta</a>
                                    @endif

                                    @if (Route::has('login'))
                                        <a href="{{ route('login') }}" class="inline-flex items-center justify-center rounded-md border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-800 shadow-sm transition hover:border-sky-200 hover:bg-sky-50 hover:text-sky-800">Entrar</a>
                                    @endif
                                @endauth
                            </div>
                        </div>
                    </div>
                </section>
            </main>

            <footer class="border-t border-slate-200 bg-white">
                <div class="mx-auto flex max-w-7xl flex-col gap-4 px-5 py-6 text-sm text-slate-500 sm:flex-row sm:items-center sm:justify-between sm:px-6 lg:px-8">
                    <p>&copy; {{ date('Y') }} Clinycal Pilates. Sistema para gestao de clinicas e estudios de pilates.</p>
                    <div class="flex gap-5">
                        <a href="#recursos" class="transition hover:text-emerald-700">Recursos</a>
                        <a href="#operacao" class="transition hover:text-emerald-700">Operacao</a>
                        <a href="#financeiro" class="transition hover:text-emerald-700">Financeiro</a>
                    </div>
                </div>
            </footer>
        </div>
    </body>
</html>
