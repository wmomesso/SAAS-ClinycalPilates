<?php

namespace App\Http\Controllers\Clinics\Clinic\HealthInsurance;

use App\Http\Controllers\Controller;
use App\Models\Clinics\Clinic\HealthInsurance\InsuranceGuide;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InsuranceGuideController extends Controller
{
    /**
     * Lista as guias de convênio.
     */
    public function index(): View
    {
        $guides = InsuranceGuide::with(['patient', 'healthInsurance'])
            ->latest()
            ->paginate(15);

        return view('insurance-guides.index', compact('guides'));
    }

    /**
     * Exibe o formulário de criação de guia.
     */
    public function create(): View
    {
        // Precisaríamos carregar pacientes e convênios para o formulário
        return view('insurance-guides.create');
    }

    /**
     * Armazena uma nova guia.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'health_insurance_id' => 'required|exists:health_insurances,id',
            'guide_type' => 'required|string',
            'auth_code' => 'required|string|max:255',
            'total_value' => 'required|numeric|min:0',
            'total_sessions' => 'required|integer|min:1',
            'issue_date' => 'nullable|date',
            'expiration_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        InsuranceGuide::create($validated);

        return redirect()->route('insurance-guides.index')
            ->with('success', 'Guia cadastrada com sucesso.');
    }

    /**
     * Exibe os detalhes da guia.
     */
    public function show(InsuranceGuide $insuranceGuide): View
    {
        $insuranceGuide->load(['patient', 'healthInsurance', 'appointments']);

        return view('insurance-guides.show', compact('insuranceGuide'));
    }

    /**
     * Exibe formulário de edição.
     */
    public function edit(InsuranceGuide $insuranceGuide): View
    {
        return view('insurance-guides.edit', compact('insuranceGuide'));
    }

    /**
     * Atualiza a guia.
     */
    public function update(Request $request, InsuranceGuide $insuranceGuide): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,billed,paid,denied',
            'paid_value' => 'nullable|numeric|min:0',
            'payment_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $insuranceGuide->update($validated);

        return redirect()->route('insurance-guides.index')
            ->with('success', 'Guia atualizada com sucesso.');
    }

    /**
     * Remove a guia.
     */
    public function destroy(InsuranceGuide $insuranceGuide): RedirectResponse
    {
        $insuranceGuide->delete();

        return redirect()->route('insurance-guides.index')
            ->with('success', 'Guia removida com sucesso.');
    }
}
