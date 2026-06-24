<?php

namespace App\Http\Controllers\Clinics\Clinic\Patients;

use App\Http\Controllers\Controller;
use App\Http\Requests\Clinics\Clinic\Patient\StorePatientRequest;
use App\Models\Clinics\Clinic\Patient\Patient;

class PatientController extends Controller
{
    public function __construct()
    {
        // Aplica a Policy automaticamente para os métodos do resource
        $this->authorizeResource(Patient::class, 'patient');
    }

    /**
     * Lista os pacientes da clínica do utilizador logado.
     */
    public function index()
    {
        $patients = Patient::orderBy('full_name')
            ->paginate(15);

        return view('patients.index', compact('patients'));
    }

    /**
     * Busca pacientes para autocomplete.
     */
    public function search(\Illuminate\Http\Request $request)
    {
        $search = $request->get('q');

        $patients = Patient::query()
            ->where('full_name', 'LIKE', "%{$search}%")
            ->orderBy('full_name')
            ->limit(10)
            ->get(['id', 'full_name']);

        return response()->json($patients);
    }

    /**
     * Exibe o formulário de criação.
     */
    public function create()
    {
        return view('patients.create');
    }

    /**
     * Armazena um novo paciente.
     */
    public function store(StorePatientRequest $request)
    {
        Patient::create($request->validated());

        return redirect()->route('patients.index')
            ->with('success', 'Paciente registrado com sucesso.');
    }

    /**
     * Exibe os detalhes do paciente (Prontuário).
     */
    public function show(Patient $patient)
    {
        // Carrega relações para o prontuário
        $patient->load(['anamneses.professional', 'evolutions.professional', 'documents']);

        return view('patients.show', compact('patient'));
    }

    /**
     * Exibe o formulário de edição.
     */
    public function edit(Patient $patient)
    {
        return view('patients.edit', compact('patient'));
    }

    /**
     * Atualiza os dados do paciente.
     */
    public function update(StorePatientRequest $request, Patient $patient)
    {
        $patient->update($request->validated());

        return redirect()->route('patients.show', $patient)
            ->with('success', 'Dados do paciente atualizados.');
    }

    /**
     * Remove o paciente (Soft Delete).
     */
    public function destroy(Patient $patient)
    {
        $patient->delete();

        return redirect()->route('patients.index')
            ->with('success', 'Paciente removido do sistema.');
    }
}
