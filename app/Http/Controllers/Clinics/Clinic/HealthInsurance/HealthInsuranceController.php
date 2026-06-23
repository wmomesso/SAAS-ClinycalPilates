<?php

namespace App\Http\Controllers\Clinics\Clinic\HealthInsurance;

use App\Http\Controllers\Controller;
use App\Http\Requests\Clinics\Clinic\HealthInsurance\StoreHealthInsuranceRequest;
use App\Models\Clinics\Clinic\HealthInsurance\HealthInsurance;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class HealthInsuranceController extends Controller
{
    /**
     * Exibe a lista de convênios.
     */
    public function index(): View
    {
        $healthInsurances = HealthInsurance::orderBy('name')->paginate(15);

        return view('health-insurances.index', compact('healthInsurances'));
    }

    /**
     * Exibe o formulário de criação.
     */
    public function create(): View
    {
        return view('health-insurances.create');
    }

    /**
     * Armazena um novo convênio.
     */
    public function store(StoreHealthInsuranceRequest $request): RedirectResponse
    {
        HealthInsurance::create($request->validated());

        return redirect()->route('health-insurances.index')
            ->with('success', 'Convênio cadastrado com sucesso.');
    }

    /**
     * Exibe os detalhes do convênio.
     */
    public function show(HealthInsurance $healthInsurance): View
    {
        $healthInsurance->load('guides.patient');

        return view('health-insurances.show', compact('healthInsurance'));
    }

    /**
     * Exibe o formulário de edição.
     */
    public function edit(HealthInsurance $healthInsurance): View
    {
        return view('health-insurances.edit', compact('healthInsurance'));
    }

    /**
     * Atualiza os dados do convênio.
     */
    public function update(StoreHealthInsuranceRequest $request, HealthInsurance $healthInsurance): RedirectResponse
    {
        $healthInsurance->update($request->validated());

        return redirect()->route('health-insurances.index')
            ->with('success', 'Convênio atualizado com sucesso.');
    }

    /**
     * Remove o convênio (Soft Delete).
     */
    public function destroy(HealthInsurance $healthInsurance): RedirectResponse
    {
        $healthInsurance->delete();

        return redirect()->route('health-insurances.index')
            ->with('success', 'Convênio removido com sucesso.');
    }
}
