<?php

namespace App\Http\Controllers\Clinics\Finance;

use App\Http\Controllers\Controller;
use App\Http\Requests\Clinics\Finance\StorePayableRequest;
use App\Models\Clinics\Clinic\Finance\BankAccount;
use App\Models\Clinics\Clinic\Finance\Payable;
use Illuminate\Http\Request;

class PayableController extends Controller
{
    public function index()
    {
        $payables = Payable::with('bankAccount')->get();

        return view('finance.payables.index', compact('payables'));
    }

    public function create()
    {
        $bankAccounts = BankAccount::where('is_active', true)->get();

        return view('finance.payables.create', compact('bankAccounts'));
    }

    public function store(StorePayableRequest $request)
    {
        Payable::create($request->validated());

        return redirect()->route('payables.index')->with('success', 'Conta a pagar criada com sucesso.');
    }

    public function edit(Payable $payable)
    {
        $bankAccounts = BankAccount::where('is_active', true)->get();

        return view('finance.payables.edit', compact('payable', 'bankAccounts'));
    }

    public function update(StorePayableRequest $request, Payable $payable)
    {
        $payable->update($request->validated());

        return redirect()->route('payables.index')->with('success', 'Conta a pagar atualizada com sucesso.');
    }

    public function destroy(Payable $payable)
    {
        $payable->delete();

        return redirect()->route('payables.index')->with('success', 'Conta a pagar excluída com sucesso.');
    }
}
