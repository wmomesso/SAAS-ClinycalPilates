@extends('layouts.saas')

@section('title', 'Automação pelo WhatsApp')

@section('content')
    <div class="py-2">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
                <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-green-800 dark:border-green-800 dark:bg-green-900/30 dark:text-green-200">
                    {{ session('success') }}
                </div>
            @endif

            <section class="bg-white dark:bg-gray-800 shadow-sm rounded-2xl p-6">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Seu telefone</h3>
                        @if ($binding)
                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                                Vinculado em {{ $binding->bound_at->format('d/m/Y H:i') }} — {{ $maskedPhone }}
                            </p>
                        @else
                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">Nenhum telefone vinculado.</p>
                        @endif
                    </div>
                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $binding ? 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300' }}">
                        {{ $binding ? 'Ativo' : 'Inativo' }}
                    </span>
                </div>

                @if ($binding)
                    <form method="POST" action="{{ route('whatsapp-automation.destroy') }}" class="mt-5">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Desvincular este telefone?')" class="inline-flex items-center rounded-lg bg-red-600 px-4 py-2 text-xs font-semibold uppercase tracking-wider text-white hover:bg-red-500">
                            Desvincular telefone
                        </button>
                    </form>
                @endif
            </section>

            <section class="bg-white dark:bg-gray-800 shadow-sm rounded-2xl p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Como ativar</h3>
                <ol class="mt-4 space-y-3 text-sm text-gray-700 dark:text-gray-300 list-decimal list-inside">
                    <li>Gere um código temporário abaixo.</li>
                    <li>Do telefone que deseja vincular, envie somente o código para <strong>{{ $publicNumber ?: 'o número do SaaS informado pelo suporte' }}</strong>.</li>
                    <li>Você receberá a confirmação pelo WhatsApp.</li>
                </ol>

                @if ($activationCode)
                    <div class="mt-6 rounded-xl border-2 border-dashed border-primary-300 bg-primary-50 p-5 text-center dark:border-primary-700 dark:bg-primary-900/20">
                        <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Código descartável</p>
                        <p class="mt-2 font-mono text-3xl font-bold tracking-wider text-primary-700 dark:text-primary-300">{{ $activationCode }}</p>
                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Válido por {{ config('whatsapp.activation_code_ttl_minutes', 10) }} minutos e apenas uma utilização.</p>
                    </div>
                @endif

                <form method="POST" action="{{ route('whatsapp-automation.store') }}" class="mt-6">
                    @csrf
                    <button type="submit" class="inline-flex items-center rounded-lg bg-primary-600 px-4 py-2 text-xs font-semibold uppercase tracking-wider text-white hover:bg-primary-500">
                        {{ $activationCode ? 'Gerar outro código' : 'Gerar código de ativação' }}
                    </button>
                </form>
            </section>
        </div>
    </div>
@endsection
