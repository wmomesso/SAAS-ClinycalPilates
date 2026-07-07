<?php

namespace App\Http\Controllers\Clinics\Finance;

use App\Http\Controllers\Controller;
use App\Models\Clinics\Clinic\Finance\BankAccount;
use App\Models\Clinics\Clinic\Finance\Payable;
use App\Models\Clinics\Clinic\Finance\Receivable;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BankReconciliationController extends Controller
{
    public function index(Request $request)
    {
        $this->authorizeFinance();

        $bankAccounts = BankAccount::where('is_active', true)->orderBy('name')->get();
        $bankAccountId = $request->integer('bank_account_id') ?: $bankAccounts->first()?->id;

        $bankAccount = $bankAccountId
            ? BankAccount::whereKey($bankAccountId)->first()
            : null;

        abort_if($bankAccount && $bankAccount->clinic_id !== auth()->user()->clinic_id, 403);

        $dateFrom = $request->input('date_from', now()->startOfMonth()->toDateString());
        $dateTo = $request->input('date_to', now()->endOfMonth()->toDateString());

        $receivables = Receivable::with(['patient', 'bankAccount', 'paymentMethod', 'reconciledBy'])
            ->when($bankAccountId, fn ($query) => $query->where('bank_account_id', $bankAccountId))
            ->whereBetween('due_date', [$dateFrom, $dateTo])
            ->where('status', '!=', 'canceled')
            ->orderBy('due_date')
            ->get();

        $payables = Payable::with(['bankAccount', 'paymentMethod', 'reconciledBy'])
            ->when($bankAccountId, fn ($query) => $query->where('bank_account_id', $bankAccountId))
            ->whereBetween('due_date', [$dateFrom, $dateTo])
            ->where('status', '!=', 'canceled')
            ->orderBy('due_date')
            ->get();

        return view('finance.bank_reconciliation.index', compact(
            'bankAccounts',
            'bankAccountId',
            'dateFrom',
            'dateTo',
            'receivables',
            'payables'
        ));
    }

    public function reconcileReceivable(Request $request, Receivable $receivable)
    {
        $this->authorizeReconciliation($receivable->clinic_id);

        $data = $this->validatedData($request, 'received');

        $receivable->update([
            'status' => $receivable->status === 'pending' ? 'received' : $receivable->status,
            'receipt_date' => $receivable->receipt_date ?: $data['reconciled_date'],
            'amount_received' => $receivable->amount_received > 0 ? $receivable->amount_received : $receivable->amount,
            'reconciled_date' => $data['reconciled_date'],
            'reconciled_by' => auth()->id(),
        ]);

        return back()->with('success', 'Conta a receber conciliada com sucesso.');
    }

    public function reconcilePayable(Request $request, Payable $payable)
    {
        $this->authorizeReconciliation($payable->clinic_id);

        $data = $this->validatedData($request, 'paid');

        $payable->update([
            'status' => $payable->status === 'pending' ? 'paid' : $payable->status,
            'payment_date' => $payable->payment_date ?: $data['reconciled_date'],
            'amount_paid' => $payable->amount_paid > 0 ? $payable->amount_paid : $payable->amount,
            'reconciled_date' => $data['reconciled_date'],
            'reconciled_by' => auth()->id(),
        ]);

        return back()->with('success', 'Conta a pagar conciliada com sucesso.');
    }

    private function validatedData(Request $request, string $targetStatus): array
    {
        return $request->validate([
            'reconciled_date' => 'required|date',
            'target_status' => ['nullable', Rule::in([$targetStatus])],
        ]);
    }

    private function authorizeFinance(): void
    {
        abort_unless(
            auth()->user()->hasRole('admin-clinica')
            || auth()->user()->can('visualizar-financeiro')
            || auth()->user()->can('gerenciar-financeiro'),
            403
        );
    }

    private function authorizeReconciliation(int $clinicId): void
    {
        abort_unless($clinicId === auth()->user()->clinic_id, 403);
        abort_unless(
            auth()->user()->hasRole('admin-clinica') || auth()->user()->can('gerenciar-financeiro'),
            403
        );
    }
}
