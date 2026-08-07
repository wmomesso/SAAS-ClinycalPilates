<?php

namespace App\Http\Controllers\Clinics\Clinic\Finance;

use App\Http\Controllers\Controller;
use App\Http\Requests\Clinics\Clinic\Finance\StoreInvoiceRequest;
use App\Models\Clinics\Clinic\Finance\Invoice;
use App\Models\Clinics\Clinic\Finance\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InvoiceController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Invoice::class, 'invoice');
    }

    /**
     * Lista as faturas da clínica.
     */
    public function index()
    {
        $invoices = Invoice::with('patient')
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
        $items = $data['items'];
        unset($data['items']);

        $data['invoice_number'] = 'INV-'.strtoupper(uniqid());
        $data['total_amount'] = collect($items)->sum(fn ($item) => $item['quantity'] * $item['unit_price']);

        DB::transaction(function () use ($data, $items) {
            $invoice = Invoice::create($data);

            foreach ($items as $item) {
                $invoice->items()->create([
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total' => $item['quantity'] * $item['unit_price'],
                    'itemable_id' => $item['itemable_id'] ?? null,
                    'itemable_type' => $item['itemable_type'] ?? null,
                ]);
            }
        });

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
            'notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $invoice) {
            $invoice = Invoice::query()->lockForUpdate()->findOrFail($invoice->id);
            $outstanding = max(0, (float) $invoice->total_amount - (float) $invoice->amount_paid);

            if ((float) $request->amount > $outstanding) {
                throw ValidationException::withMessages([
                    'amount' => 'O pagamento não pode exceder o saldo em aberto de R$ '.number_format($outstanding, 2, ',', '.'),
                ]);
            }

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
            $invoice->refresh();

            // Se o total foi atingido, marca como paga
            if ($invoice->amount_paid >= $invoice->total_amount) {
                $invoice->update([
                    'status' => 'paid',
                    'paid_at' => now(),
                ]);
            }
        });

        return back()->with('success', 'Pagamento registado com sucesso.');
    }
}
