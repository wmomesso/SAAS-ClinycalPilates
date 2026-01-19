<?php

namespace App\Http\Controllers\Clinics\Clinic\Subscriptions;

use App\Http\Controllers\Controller;
use App\Models\SAAS\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    /**
     * Exibe os planos disponíveis para a clínica.
     */
    public function index()
    {
        $clinic = Auth::user()->clinic;
        $plans = SubscriptionPlan::where('is_active', true)->get();
        $currentSubscription = $clinic->subscription('default');

        return view('clinic.subscriptions.index', compact('plans', 'currentSubscription', 'clinic'));
    }

    /**
     * Inicia o checkout do Stripe para um plano.
     */
    public function checkout(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:subscription_plans,stripe_plan_id',
        ]);

        return Auth::user()->clinic
            ->newSubscription('default', $request->plan_id)
            ->checkout([
                'success_url' => route('subscription.index') . '?success=true',
                'cancel_url' => route('subscription.index') . '?canceled=true',
            ]);
    }

    /**
     * Redireciona para o Portal de Faturamento do Stripe (gerenciar cartões/cancelar).
     */
    public function billingPortal(Request $request)
    {
        return $request->user()->clinic->redirectToBillingPortal(
            route('subscription.index')
        );
    }
}
