<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureClinicHasActiveSubscription
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || $user->hasRole('super-admin')) {
            return $next($request);
        }

        $clinic = $user->clinic;
        abort_unless($clinic, 403);

        if ($clinic->subscribed('default')) {
            return $next($request);
        }

        if ($user->can('gerenciar-assinatura-saas')) {
            return redirect()
                ->route('subscription.index')
                ->with('warning', 'Sua clínica não possui um plano ativo. Escolha um plano para continuar.');
        }

        return redirect()
            ->route('subscription.index')
            ->with('warning', 'O plano da clínica está expirado. Informe o gestor da clínica para regularizar a assinatura.');
    }
}
