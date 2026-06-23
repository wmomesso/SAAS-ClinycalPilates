@extends('layouts.saas')

@section('title', 'Minha Assinatura')

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
                    <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2 dark:text-gray-400">Assinatura</span>
                </div>
            </li>
        </ol>
    </nav>
@endsection

@section('content')
    <div class="max-w-6xl mx-auto">
        {{-- Status da Assinatura Atual --}}
        <div class="bg-white dark:bg-gray-800 shadow-md rounded-2xl p-6 mb-8 border-l-8 {{ $currentSubscription && $currentSubscription->valid() ? 'border-green-500' : 'border-amber-500' }}">
            <div class="flex flex-col md:flex-row justify-between items-center gap-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 dark:text-white">
                        @if($currentSubscription && $currentSubscription->valid())
                            Sua clínica está ativa!
                        @else
                            Assinatura pendente ou expirada
                        @endif
                    </h2>
                    <p class="text-gray-500 dark:text-gray-400 mt-1">
                        @if($currentSubscription)
                            Plano atual: <span class="font-bold text-primary-600">{{ $currentSubscription->type }}</span> •
                            @if($currentSubscription->onGracePeriod())
                                Expira em: {{ $currentSubscription->ends_at->format('d/m/Y') }}
                            @else
                                Próxima cobrança: {{ $currentSubscription->nextPayment() ? $currentSubscription->nextPayment()->date()->format('d/m/Y') : '-' }}
                            @endif
                        @else
                            Você ainda não possui um plano ativo. Escolha um abaixo para começar.
                        @endif
                    </p>
                </div>
                @if($currentSubscription)
                    <a href="{{ route('subscription.billing') }}" class="inline-flex items-center px-6 py-3 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-800 dark:text-white font-bold rounded-xl transition-all">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        Portal de Faturamento
                    </a>
                @endif
            </div>
        </div>

        {{-- Lista de Planos --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach ($plans as $plan)
                <div class="bg-white dark:bg-gray-800 shadow-xl rounded-3xl p-8 border-2 {{ $currentSubscription && $currentSubscription->hasPrice($plan->stripe_plan_id) ? 'border-primary-500' : 'border-transparent' }} flex flex-col relative overflow-hidden">
                    @if($currentSubscription && $currentSubscription->hasPrice($plan->stripe_plan_id))
                        <div class="absolute top-0 right-0 bg-primary-500 text-white text-[10px] font-black uppercase px-4 py-1 rounded-bl-xl tracking-widest">Atual</div>
                    @endif

                    <h3 class="text-xl font-black text-gray-800 dark:text-white mb-2">{{ $plan->name }}</h3>
                    <p class="text-gray-500 dark:text-gray-400 text-sm mb-6">{{ $plan->description }}</p>

                    <div class="mb-8">
                        <span class="text-4xl font-black text-gray-900 dark:text-white">R$ {{ number_format($plan->price, 2, ',', '.') }}</span>
                        <span class="text-gray-500">/mês</span>
                    </div>

                    <ul class="space-y-4 mb-8 flex-1">
                        <li class="flex items-center gap-3 text-sm text-gray-600 dark:text-gray-400">
                            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Até {{ $plan->max_patients ?? 'Ilimitados' }} pacientes
                        </li>
                        <li class="flex items-center gap-3 text-sm text-gray-600 dark:text-gray-400">
                            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Até {{ $plan->max_professionals ?? 'Ilimitados' }} profissionais
                        </li>
                    </ul>

                    @if(!$currentSubscription || !$currentSubscription->hasPrice($plan->stripe_plan_id))
                        <form action="{{ route('subscription.checkout') }}" method="POST">
                            @csrf
                            <input type="hidden" name="plan_id" value="{{ $plan->stripe_plan_id }}">
                            <button type="submit" class="w-full py-4 bg-primary-600 hover:bg-primary-700 text-white font-black rounded-2xl shadow-lg shadow-primary-500/30 transition-all">
                                Assinar Agora
                            </button>
                        </form>
                    @else
                        <button disabled class="w-full py-4 bg-gray-100 dark:bg-gray-700 text-gray-400 font-black rounded-2xl cursor-not-allowed">
                            Plano Atual
                        </button>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
@endsection
