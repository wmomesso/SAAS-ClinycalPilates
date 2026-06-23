<?php

namespace App\Http\Controllers\Clinics\Finance;

use App\Http\Controllers\Controller;
use App\Http\Requests\Clinics\Finance\StoreBankAccountRequest;
use App\Models\Clinics\Clinic\Finance\BankAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BankAccountController extends Controller
{
    public function index()
    {
        $bankAccounts = BankAccount::query()->get();

        return view('finance.bank_accounts.index', compact('bankAccounts'));
    }

    public function create()
    {
        return view('finance.bank_accounts.create');
    }

    public function store(StoreBankAccountRequest $request)
    {
        BankAccount::create($request->validated());

        return redirect()->route('bank-accounts.index')->with('success', 'Conta bancária criada com sucesso.');
    }

    public function edit(BankAccount $bankAccount)
    {
        return view('finance.bank_accounts.edit', compact('bankAccount'));
    }

    public function update(StoreBankAccountRequest $request, BankAccount $bankAccount)
    {
        $bankAccount->update($request->validated());

        return redirect()->route('bank-accounts.index')->with('success', 'Conta bancária atualizada com sucesso.');
    }

    public function destroy(BankAccount $bankAccount)
    {
        $bankAccount->delete();

        return redirect()->route('bank-accounts.index')->with('success', 'Conta bancária excluída com sucesso.');
    }
}
