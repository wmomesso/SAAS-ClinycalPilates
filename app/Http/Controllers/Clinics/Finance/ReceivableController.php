<?php

namespace App\Http\Controllers\Clinics\Finance;

use App\Http\Controllers\Controller;
use App\Http\Requests\Clinics\Finance\StoreReceivableRequest;
use App\Models\Clinics\Clinic\Finance\BankAccount;
use App\Models\Clinics\Clinic\Finance\PaymentMethod;
use App\Models\Clinics\Clinic\Finance\Receivable;
use App\Models\Clinics\Clinic\HealthInsurance\HealthInsurance;
use App\Models\Clinics\Clinic\Patient\Patient;
use Illuminate\Http\Request;

class ReceivableController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Receivable::class, 'receivable');
    }

    public function index()
    {
        $receivables = Receivable::with(['bankAccount', 'patient', 'paymentMethod', 'paymentSource'])->get();

        return view('finance.receivables.index', compact('receivables'));
    }

    public function create()
    {
        $bankAccounts = BankAccount::where('is_active', true)->get();
        $patients = Patient::query()->get();
        $healthInsurances = HealthInsurance::where('is_active', true)->orderBy('name')->get();
        $paymentMethods = PaymentMethod::where('is_active', true)->orderBy('name')->get();

        return view('finance.receivables.create', compact('bankAccounts', 'patients', 'healthInsurances', 'paymentMethods'));
    }

    public function store(StoreReceivableRequest $request)
    {
        Receivable::create($request->validated());

        return redirect()->route('receivables.index')->with('success', 'Conta a receber criada com sucesso.');
    }

    public function edit(Receivable $receivable)
    {
        $bankAccounts = BankAccount::where('is_active', true)->get();
        $patients = Patient::query()->get();
        $healthInsurances = HealthInsurance::where('is_active', true)->orderBy('name')->get();
        $paymentMethods = PaymentMethod::where('is_active', true)->orderBy('name')->get();

        return view('finance.receivables.edit', compact('receivable', 'bankAccounts', 'patients', 'healthInsurances', 'paymentMethods'));
    }

    public function update(StoreReceivableRequest $request, Receivable $receivable)
    {
        $receivable->update($request->validated());

        return redirect()->route('receivables.index')->with('success', 'Conta a receber atualizada com sucesso.');
    }

    public function destroy(Receivable $receivable)
    {
        $receivable->delete();

        return redirect()->route('receivables.index')->with('success', 'Conta a receber excluída com sucesso.');
    }
}
