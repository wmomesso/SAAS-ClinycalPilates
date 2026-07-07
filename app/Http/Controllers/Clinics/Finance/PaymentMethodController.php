<?php

namespace App\Http\Controllers\Clinics\Finance;

use App\Http\Controllers\Controller;
use App\Models\Clinics\Clinic\Finance\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PaymentMethodController extends Controller
{
    public function index()
    {
        $this->authorizeFinance();

        $paymentMethods = PaymentMethod::orderBy('name')->get();

        return view('finance.payment_methods.index', compact('paymentMethods'));
    }

    public function create()
    {
        $this->authorizeFinance(true);

        return view('finance.payment_methods.create');
    }

    public function store(Request $request)
    {
        $this->authorizeFinance(true);

        PaymentMethod::create($this->validatedData($request));

        return redirect()->route('payment-methods.index')
            ->with('success', 'Forma de pagamento criada com sucesso.');
    }

    public function edit(PaymentMethod $paymentMethod)
    {
        $this->authorizePaymentMethod($paymentMethod);

        return view('finance.payment_methods.edit', compact('paymentMethod'));
    }

    public function update(Request $request, PaymentMethod $paymentMethod)
    {
        $this->authorizePaymentMethod($paymentMethod, true);

        $paymentMethod->update($this->validatedData($request));

        return redirect()->route('payment-methods.index')
            ->with('success', 'Forma de pagamento atualizada com sucesso.');
    }

    public function destroy(PaymentMethod $paymentMethod)
    {
        $this->authorizePaymentMethod($paymentMethod, true);

        $paymentMethod->update(['is_active' => false]);

        return redirect()->route('payment-methods.index')
            ->with('success', 'Forma de pagamento desativada com sucesso.');
    }

    private function validatedData(Request $request): array
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'type' => ['required', Rule::in(['cash', 'pix', 'credit_card', 'debit_card', 'bank_slip', 'bank_transfer', 'health_insurance', 'other'])],
            'requires_bank_account' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $data['requires_bank_account'] = $request->boolean('requires_bank_account');
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }

    private function authorizeFinance(bool $manage = false): void
    {
        abort_unless(
            auth()->user()->hasRole('admin-clinica')
            || auth()->user()->can($manage ? 'gerenciar-financeiro' : 'visualizar-financeiro'),
            403
        );
    }

    private function authorizePaymentMethod(PaymentMethod $paymentMethod, bool $manage = false): void
    {
        abort_unless($paymentMethod->clinic_id === auth()->user()->clinic_id, 403);
        $this->authorizeFinance($manage);
    }
}
