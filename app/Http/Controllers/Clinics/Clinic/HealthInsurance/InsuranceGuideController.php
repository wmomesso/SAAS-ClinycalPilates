<?php

namespace App\Http\Controllers\Clinics\Clinic\HealthInsurance;

use App\Http\Controllers\Controller;
use App\Models\Clinics\Clinic\HealthInsurance\HealthInsurance;
use App\Models\Clinics\Clinic\HealthInsurance\InsuranceGuide;
use App\Models\Clinics\Clinic\Patient\Patient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class InsuranceGuideController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(InsuranceGuide::class, 'insurance_guide');
    }

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
        $patients = Patient::orderBy('full_name')->get();
        $healthInsurances = HealthInsurance::where('is_active', true)->orderBy('name')->get();

        return view('insurance-guides.create', compact('patients', 'healthInsurances'));
    }

    /**
     * Armazena uma nova guia.
     */
    public function store(Request $request): RedirectResponse
    {
        $clinicId = $request->user()->clinic_id;

        $validated = $request->validate([
            'patient_id' => [
                'required',
                Rule::exists('patients', 'id')->where(fn ($query) => $query->where('clinic_id', $clinicId)),
            ],
            'health_insurance_id' => [
                'required',
                Rule::exists('health_insurances', 'id')->where(fn ($query) => $query->where('clinic_id', $clinicId)),
            ],
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
        $patients = Patient::orderBy('full_name')->get();
        $healthInsurances = HealthInsurance::where('is_active', true)->orderBy('name')->get();

        return view('insurance-guides.edit', compact('insuranceGuide', 'patients', 'healthInsurances'));
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
