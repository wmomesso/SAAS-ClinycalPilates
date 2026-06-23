<?php

namespace App\Http\Controllers\SAAS;

use App\Http\Controllers\Controller;
use App\Http\Requests\SAAS\Plan\StorePlanRequest;
use App\Models\SAAS\SubscriptionPlan;

class PlanController extends Controller
{
    public function __construct()
    {
        // Apenas Super Admin pode gerenciar planos globais
        $this->authorizeResource(SubscriptionPlan::class, 'plan');
    }

    public function index()
    {
        $plans = SubscriptionPlan::all();

        return view('admin.plans.index', compact('plans'));
    }

    public function create()
    {
        return view('admin.plans.create');
    }

    public function store(StorePlanRequest $request)
    {
        SubscriptionPlan::create($request->validated());

        return redirect()->route('plans.index')
            ->with('success', 'Plano SAAS criado com sucesso.');
    }

    public function edit(SubscriptionPlan $plan)
    {
        return view('admin.plans.edit', compact('plan'));
    }

    public function update(StorePlanRequest $request, SubscriptionPlan $plan)
    {
        $plan->update($request->validated());

        return redirect()->route('plans.index')
            ->with('success', 'Plano atualizado.');
    }

    public function destroy(SubscriptionPlan $plan)
    {
        $plan->delete();

        return redirect()->route('plans.index')
            ->with('success', 'Plano removido.');
    }
}
