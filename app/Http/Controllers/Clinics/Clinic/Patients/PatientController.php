<?php

namespace App\Http\Controllers\Clinics\Clinic\Patients;

use App\Http\Controllers\Controller;
use App\Http\Requests\Clinics\Clinic\Patient\StorePatientRequest;
use App\Models\Clinics\Clinic\Finance\BankAccount;
use App\Models\Clinics\Clinic\Finance\PatientPackage;
use App\Models\Clinics\Clinic\Finance\Receivable;
use App\Models\Clinics\Clinic\Finance\ServicePackage;
use App\Models\Clinics\Clinic\Patient\Patient;
use App\Models\SecurityAuditLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

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
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));
        $cpfSearch = preg_replace('/\D+/', '', $search);

        $patients = Patient::query()
            ->when($search !== '', function ($query) use ($search, $cpfSearch) {
                $query->where(function ($query) use ($search, $cpfSearch) {
                    $query->where('full_name', 'LIKE', "%{$search}%")
                        ->orWhere('document_cpf', 'LIKE', "%{$search}%")
                        ->orWhere('email', 'LIKE', "%{$search}%")
                        ->orWhere('phone', 'LIKE', "%{$search}%");

                    if ($cpfSearch !== '' && $cpfSearch !== $search) {
                        $query->orWhere('document_cpf', 'LIKE', "%{$cpfSearch}%");
                    }
                });
            })
            ->orderBy('full_name')
            ->paginate(15)
            ->withQueryString();

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
        $clinic = Auth::user()->clinic;

        if ($clinic && $clinic->hasReachedSubscriptionLimit('limit_patients', $clinic->patients()->count())) {
            return back()
                ->withErrors(['full_name' => 'O limite de pacientes do plano contratado foi atingido.'])
                ->withInput();
        }

        Patient::create($request->validated());

        return redirect()->route('patients.index')
            ->with('success', 'Paciente registrado com sucesso.');
    }

    /**
     * Exibe os detalhes do paciente (Prontuário).
     */
    public function show(Patient $patient)
    {
        SecurityAuditLog::record('viewed_medical_record', $patient);

        // Carrega relações para o prontuário
        $patient->load([
            'anamneses.professional',
            'appointments.professional',
            'appointments.room',
            'appointments.serviceType',
            'appointments.patientPackage.package.serviceType',
            'documents',
            'consents.recordedBy',
            'consents.revokedBy',
            'evolutions.professional',
            'receivables.bankAccount',
            'receivables.patientPackage.package',
            'packages.package.serviceType',
            'packages.bankAccount',
        ]);

        $servicePackages = ServicePackage::with('serviceType')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $bankAccounts = BankAccount::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('patients.show', compact('patient', 'servicePackages', 'bankAccounts'));
    }

    public function storePackage(Request $request, Patient $patient)
    {
        $this->authorize('update', $patient);

        $data = $request->validate([
            'service_package_id' => [
                'required',
                Rule::exists('service_packages', 'id')->where(fn ($query) => $query
                    ->where('clinic_id', auth()->user()->clinic_id)
                    ->where('is_active', true)),
            ],
            'start_date' => 'required|date',
            'price_paid' => 'nullable|numeric|min:0',
            'billing_type' => ['required', Rule::in(['single', 'monthly_recurring'])],
            'payment_method' => ['required', Rule::in(['cash', 'pix', 'credit_card', 'debit_card', 'bank_slip', 'bank_transfer', 'other'])],
            'bank_account_id' => [
                'nullable',
                Rule::exists('bank_accounts', 'id')->where(fn ($query) => $query->where('clinic_id', auth()->user()->clinic_id)),
            ],
            'billing_day' => 'required_if:billing_type,monthly_recurring|nullable|integer|min:1|max:31',
            'first_due_date' => 'required|date',
        ]);

        $servicePackage = ServicePackage::findOrFail($data['service_package_id']);
        $startDate = Carbon::parse($data['start_date']);
        $firstDueDate = Carbon::parse($data['first_due_date']);
        $pricePaid = $data['price_paid'] ?? $servicePackage->price;
        $bankAccount = isset($data['bank_account_id']) ? BankAccount::find($data['bank_account_id']) : null;

        if ($bankAccount && $data['payment_method'] === 'pix' && ! $bankAccount->has_pix) {
            return back()->withErrors(['bank_account_id' => 'A conta selecionada não possui Pix habilitado.'])->withInput();
        }

        if ($bankAccount && $data['payment_method'] === 'bank_slip' && ! $bankAccount->issues_bank_slips) {
            return back()->withErrors(['bank_account_id' => 'A conta selecionada não emite boletos.'])->withInput();
        }

        $nextBillingDate = null;
        if ($data['billing_type'] === 'monthly_recurring') {
            $nextBillingDate = $firstDueDate->copy()->addMonthNoOverflow();
            $billingDay = (int) $data['billing_day'];
            $nextBillingDate->day(min($billingDay, $nextBillingDate->daysInMonth));
        }

        DB::transaction(function () use ($patient, $servicePackage, $startDate, $firstDueDate, $nextBillingDate, $pricePaid, $data) {
            $patientPackage = PatientPackage::create([
                'patient_id' => $patient->id,
                'service_package_id' => $servicePackage->id,
                'bank_account_id' => $data['bank_account_id'] ?? null,
                'total_sessions' => $servicePackage->number_of_sessions,
                'used_sessions' => 0,
                'missed_sessions' => 0,
                'start_date' => $startDate->toDateString(),
                'end_date' => $servicePackage->validity_in_days ? $startDate->copy()->addDays($servicePackage->validity_in_days)->toDateString() : null,
                'status' => 'active',
                'price_paid' => $pricePaid,
                'billing_type' => $data['billing_type'],
                'payment_method' => $data['payment_method'],
                'billing_day' => $data['billing_type'] === 'monthly_recurring' ? $data['billing_day'] : null,
                'next_billing_date' => $nextBillingDate?->toDateString(),
            ]);

            Receivable::create([
                'patient_id' => $patient->id,
                'patient_package_id' => $patientPackage->id,
                'bank_account_id' => $data['bank_account_id'] ?? null,
                'description' => $data['billing_type'] === 'monthly_recurring'
                    ? "Mensalidade {$servicePackage->name} - {$patient->full_name}"
                    : "Pacote {$servicePackage->name} - {$patient->full_name}",
                'amount' => $pricePaid,
                'payment_method' => $data['payment_method'],
                'due_date' => $firstDueDate->toDateString(),
                'status' => 'pending',
            ]);
        });

        return redirect()->route('patients.show', $patient)
            ->with('success', 'Plano vinculado e cobrança criada com sucesso.');
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
