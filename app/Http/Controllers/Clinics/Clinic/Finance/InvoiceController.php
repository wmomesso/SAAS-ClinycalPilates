<?php

namespace App\Http\Controllers\Clinics\Clinic\Finance;

use App\Http\Controllers\Controller;
use App\Http\Requests\Clinics\Clinic\Finance\StoreInvoiceRequest;
use App\Models\Clinics\Clinic\Finance\Invoice;
use App\Models\Clinics\Clinic\Finance\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class InvoiceController extends Controller
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->authorizeResource(Invoice::class, 'invoice');
    }

    /**
     * Lista as faturas da clínica.
     */
    public function index()
    {
        $invoices = Invoice::where('clinic_id', Auth::user()->clinic_id)
            ->with('patient')
            ->orderBy('due_date', 'desc')
            ->paginate(15);

        return view('clinic.finance.invoices.index', compact('invoices'));
    }

    /**
     * Armazena uma nova fatura.
     */
    public function store(StoreInvoiceRequest $request)
    {
        $data = $request->validated();
        $data['clinic_id'] = Auth::user()->clinic_id;
        $data['invoice_number'] = 'INV-' . strtoupper(uniqid());

        Invoice::create($data);

        return redirect()->route('invoices.index')
            ->with('success', 'Fatura gerada com sucesso.');
    }

    /**
     * Exibe os detalhes da fatura e histórico de pagamentos.
     */
    public function show(Invoice $invoice)
    {
        $invoice->load(['patient', 'items', 'transactions']);
        return view('clinic.finance.invoices.show', compact('invoice'));
    }

    /**
     * Regista um pagamento para a fatura.
     */
    public function addPayment(Request $request, Invoice $invoice)
    {
        $this->authorize('update', $invoice);

        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string',
            'notes' => 'nullable|string'
        ]);

        DB::transaction(function () use ($request, $invoice) {
            // Regista a transação
            Transaction::create([
                'invoice_id' => $invoice->id,
                'amount' => $request->amount,
                'payment_method' => $request->payment_method,
                'transaction_date' => now(),
                'notes' => $request->notes,
            ]);

            // Atualiza o montante pago na fatura
            $invoice->increment('amount_paid', $request->amount);

            // Se o total foi atingido, marca como paga
            if ($invoice->amount_paid >= $invoice->total_amount) {
                $invoice->update([
                    'status' => 'paid',
                    'paid_at' => now()
                ]);
            }
        });

        return back()->with('success', 'Pagamento registado com sucesso.');
    }
}
