<?php

namespace App\Http\Controllers\Clinics\Clinic\Subscriptions;

use App\Http\Controllers\Controller;
use App\Models\SAAS\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class SubscriptionController extends Controller
{
    /**
     * Exibe os planos disponíveis para a clínica.
     */
    public function index()
    {
        if (Auth::user()->hasRole('super-admin')) {
            return redirect()->route('admin.dashboard');
        }

        $clinic = Auth::user()->clinic;
        abort_unless($clinic, 403);

        $plans = SubscriptionPlan::where('is_active', true)->get();
        $currentSubscription = $clinic->subscription('default');
        $canManageSubscription = Auth::user()->can('gerenciar-assinatura-saas');
        $onTrial = $clinic->onGenericTrial();
        $trialEndsAt = $clinic->trialEndsAt();

        return view('clinic.subscriptions.index', compact('plans', 'currentSubscription', 'clinic', 'canManageSubscription', 'onTrial', 'trialEndsAt'));
    }

    /**
     * Inicia o checkout do Stripe para um plano.
     */
    public function checkout(Request $request)
    {
        if (Auth::user()->hasRole('super-admin')) {
            return redirect()->route('admin.dashboard');
        }

        if (! $request->user()->can('gerenciar-assinatura-saas')) {
            return redirect()
                ->route('subscription.index')
                ->with('warning', 'Você não tem permissão para contratar planos. Informe o gestor da clínica que o plano está expirado.');
        }

        $validated = $request->validate([
            'plan_id' => [
                'required',
                Rule::exists('subscription_plans', 'stripe_plan_id')->where('is_active', true),
            ],
        ]);

        $clinic = Auth::user()->clinic;
        abort_unless($clinic, 403);

        if ($clinic->subscribed('default')) {
            return $clinic->redirectToBillingPortal(route('subscription.index'));
        }

        $subscription = $clinic->newSubscription('default', $validated['plan_id']);

        if ($clinic->onGenericTrial()) {
            $subscription->trialUntil($clinic->trialEndsAt());
        }

        return $subscription->checkout([
            'success_url' => route('subscription.index').'?success=true',
            'cancel_url' => route('subscription.index').'?canceled=true',
        ]);
    }

    /**
     * Redireciona para o Portal de Faturamento do Stripe (gerenciar cartões/cancelar).
     */
    public function billingPortal(Request $request)
    {
        if ($request->user()->hasRole('super-admin')) {
            return redirect()->route('admin.dashboard');
        }

        if (! $request->user()->can('gerenciar-assinatura-saas')) {
            return redirect()
                ->route('subscription.index')
                ->with('warning', 'Você não tem permissão para gerenciar a assinatura. Informe o gestor da clínica.');
        }

        $clinic = $request->user()->clinic;
        abort_unless($clinic, 403);

        return $clinic->redirectToBillingPortal(
            route('subscription.index')
        );
    }
}
