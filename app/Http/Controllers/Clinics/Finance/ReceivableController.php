<?php

namespace App\Http\Controllers\Clinics\Finance;

use App\Http\Controllers\Controller;
use App\Http\Requests\Clinics\Finance\StoreReceivableRequest;
use App\Models\Clinics\Clinic\Finance\BankAccount;
use App\Models\Clinics\Clinic\Finance\Receivable;
use App\Models\Clinics\Clinic\Patient\Patient;
use Illuminate\Http\Request;

class ReceivableController extends Controller
{
    public function index()
    {
        $receivables = Receivable::with(['bankAccount', 'patient'])->get();

        return view('finance.receivables.index', compact('receivables'));
    }

    public function create()
    {
        $bankAccounts = BankAccount::where('is_active', true)->get();
        $patients = Patient::query()->get();

        return view('finance.receivables.create', compact('bankAccounts', 'patients'));
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

        return view('finance.receivables.edit', compact('receivable', 'bankAccounts', 'patients'));
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
