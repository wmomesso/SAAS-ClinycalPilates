@csrf

@php
    $sourceKind = old('payment_source_kind');
    if (! $sourceKind && isset($receivable)) {
        $sourceKind = $receivable->payment_source_type === \App\Models\Clinics\Clinic\HealthInsurance\HealthInsurance::class ? 'health_insurance' : ($receivable->patient_id ? 'patient' : '');
    }
@endphp

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
        <x-input-label for="payment_source_kind" value="Receber de" />
        <select id="payment_source_kind" name="payment_source_kind" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 dark:focus:border-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 rounded-xl shadow-sm">
            <option value="">Não definido</option>
            <option value="patient" {{ $sourceKind === 'patient' ? 'selected' : '' }}>Paciente</option>
            <option value="health_insurance" {{ $sourceKind === 'health_insurance' ? 'selected' : '' }}>Convênio</option>
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('payment_source_kind')" />
    </div>

    <div>
        <x-input-label for="patient_id" value="Paciente" />
        <select id="patient_id" name="patient_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 dark:focus:border-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 rounded-xl shadow-sm">
            <option value="">Sem paciente</option>
            @foreach($patients as $patient)
                <option value="{{ $patient->id }}" {{ old('patient_id', $receivable->patient_id ?? '') == $patient->id ? 'selected' : '' }}>
                    {{ $patient->full_name }}
                </option>
            @endforeach
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('patient_id')" />
    </div>

    <div>
        <x-input-label for="health_insurance_id" value="Convênio" />
        <select id="health_insurance_id" name="health_insurance_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 dark:focus:border-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 rounded-xl shadow-sm">
            <option value="">Sem convênio</option>
            @foreach($healthInsurances as $healthInsurance)
                <option value="{{ $healthInsurance->id }}" {{ old('health_insurance_id', isset($receivable) && $receivable->payment_source_type === \App\Models\Clinics\Clinic\HealthInsurance\HealthInsurance::class ? $receivable->payment_source_id : '') == $healthInsurance->id ? 'selected' : '' }}>
                    {{ $healthInsurance->name }}
                </option>
            @endforeach
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('health_insurance_id')" />
    </div>

    <div>
        <x-input-label for="bank_account_id" value="Conta bancária" />
        <select id="bank_account_id" name="bank_account_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 dark:focus:border-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 rounded-xl shadow-sm">
            <option value="">Não definida</option>
            @foreach($bankAccounts as $bankAccount)
                <option value="{{ $bankAccount->id }}" {{ old('bank_account_id', $receivable->bank_account_id ?? '') == $bankAccount->id ? 'selected' : '' }}>
                    {{ $bankAccount->name }}
                </option>
            @endforeach
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('bank_account_id')" />
    </div>

    <div class="md:col-span-2">
        <x-input-label for="description" value="Descrição" />
        <x-text-input id="description" name="description" type="text" class="mt-1 block w-full" :value="old('description', $receivable->description ?? '')" required />
        <x-input-error class="mt-2" :messages="$errors->get('description')" />
    </div>

    <div>
        <x-input-label for="amount" value="Valor" />
        <x-text-input id="amount" name="amount" type="number" min="0" step="0.01" class="mt-1 block w-full" :value="old('amount', $receivable->amount ?? '')" required />
        <x-input-error class="mt-2" :messages="$errors->get('amount')" />
    </div>

    <div>
        <x-input-label for="payment_method_id" value="Forma de pagamento cadastrada" />
        <select id="payment_method_id" name="payment_method_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 dark:focus:border-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 rounded-xl shadow-sm">
            <option value="">Não definida</option>
            @foreach($paymentMethods as $paymentMethod)
                <option value="{{ $paymentMethod->id }}" {{ old('payment_method_id', $receivable->payment_method_id ?? '') == $paymentMethod->id ? 'selected' : '' }}>{{ $paymentMethod->name }}</option>
            @endforeach
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('payment_method_id')" />
    </div>

    <div>
        <x-input-label for="payment_method" value="Observação da forma de pagamento" />
        <select id="payment_method" name="payment_method" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 dark:focus:border-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 rounded-xl shadow-sm">
            <option value="">Não definida</option>
            @foreach(['cash' => 'Dinheiro', 'pix' => 'Pix', 'credit_card' => 'Cartão crédito', 'debit_card' => 'Cartão débito', 'bank_slip' => 'Boleto', 'bank_transfer' => 'Transferência', 'other' => 'Outro'] as $value => $label)
                <option value="{{ $value }}" {{ old('payment_method', $receivable->payment_method ?? '') === $value ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('payment_method')" />
    </div>

    <div>
        <x-input-label for="due_date" value="Vencimento" />
        <x-text-input id="due_date" name="due_date" type="date" class="mt-1 block w-full" :value="old('due_date', isset($receivable) && $receivable->due_date ? $receivable->due_date->format('Y-m-d') : now()->toDateString())" required />
        <x-input-error class="mt-2" :messages="$errors->get('due_date')" />
    </div>

    <div>
        <x-input-label for="status" value="Status" />
        <select id="status" name="status" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 dark:focus:border-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 rounded-xl shadow-sm" required>
            @foreach(['pending' => 'Pendente', 'received' => 'Recebido', 'partially_received' => 'Recebido parcial', 'canceled' => 'Cancelado'] as $value => $label)
                <option value="{{ $value }}" {{ old('status', $receivable->status ?? 'pending') === $value ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('status')" />
    </div>

    <div>
        <x-input-label for="receipt_date" value="Data de recebimento" />
        <x-text-input id="receipt_date" name="receipt_date" type="date" class="mt-1 block w-full" :value="old('receipt_date', isset($receivable) && $receivable->receipt_date ? $receivable->receipt_date->format('Y-m-d') : '')" />
        <x-input-error class="mt-2" :messages="$errors->get('receipt_date')" />
    </div>

    <div>
        <x-input-label for="amount_received" value="Valor recebido" />
        <x-text-input id="amount_received" name="amount_received" type="number" min="0" step="0.01" class="mt-1 block w-full" :value="old('amount_received', $receivable->amount_received ?? '0.00')" />
        <x-input-error class="mt-2" :messages="$errors->get('amount_received')" />
    </div>

    <div class="md:col-span-2">
        <x-input-label for="notes" value="Observações" />
        <textarea id="notes" name="notes" rows="4" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 dark:focus:border-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 rounded-xl shadow-sm">{{ old('notes', $receivable->notes ?? '') }}</textarea>
        <x-input-error class="mt-2" :messages="$errors->get('notes')" />
    </div>
</div>

<div class="mt-8 flex justify-end gap-3">
    <a href="{{ route('receivables.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 dark:bg-gray-700 border border-transparent rounded-xl font-semibold text-xs text-gray-700 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-200 dark:hover:bg-gray-600">
        Cancelar
    </a>
    <x-primary-button>{{ $submitLabel }}</x-primary-button>
</div>
