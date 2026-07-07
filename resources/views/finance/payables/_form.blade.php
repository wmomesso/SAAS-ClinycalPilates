@csrf

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div class="md:col-span-2">
        <x-input-label for="description" value="Descrição" />
        <x-text-input id="description" name="description" type="text" class="mt-1 block w-full" :value="old('description', $payable->description ?? '')" required autofocus />
        <x-input-error class="mt-2" :messages="$errors->get('description')" />
    </div>

    <div>
        <x-input-label for="provider" value="Fornecedor" />
        <x-text-input id="provider" name="provider" type="text" class="mt-1 block w-full" :value="old('provider', $payable->provider ?? '')" />
        <x-input-error class="mt-2" :messages="$errors->get('provider')" />
    </div>

    <div>
        <x-input-label for="bank_account_id" value="Conta bancária" />
        <select id="bank_account_id" name="bank_account_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 dark:focus:border-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 rounded-xl shadow-sm">
            <option value="">Não definida</option>
            @foreach($bankAccounts as $bankAccount)
                <option value="{{ $bankAccount->id }}" {{ old('bank_account_id', $payable->bank_account_id ?? '') == $bankAccount->id ? 'selected' : '' }}>{{ $bankAccount->name }}</option>
            @endforeach
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('bank_account_id')" />
    </div>

    <div>
        <x-input-label for="payment_method_id" value="Forma de pagamento" />
        <select id="payment_method_id" name="payment_method_id" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 dark:focus:border-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 rounded-xl shadow-sm">
            <option value="">Não definida</option>
            @foreach($paymentMethods as $paymentMethod)
                <option value="{{ $paymentMethod->id }}" {{ old('payment_method_id', $payable->payment_method_id ?? '') == $paymentMethod->id ? 'selected' : '' }}>{{ $paymentMethod->name }}</option>
            @endforeach
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('payment_method_id')" />
    </div>

    <div>
        <x-input-label for="amount" value="Valor" />
        <x-text-input id="amount" name="amount" type="number" min="0" step="0.01" class="mt-1 block w-full" :value="old('amount', $payable->amount ?? '')" required />
        <x-input-error class="mt-2" :messages="$errors->get('amount')" />
    </div>

    <div>
        <x-input-label for="due_date" value="Vencimento" />
        <x-text-input id="due_date" name="due_date" type="date" class="mt-1 block w-full" :value="old('due_date', isset($payable) && $payable->due_date ? $payable->due_date->format('Y-m-d') : now()->toDateString())" required />
        <x-input-error class="mt-2" :messages="$errors->get('due_date')" />
    </div>

    <div>
        <x-input-label for="status" value="Status" />
        <select id="status" name="status" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 dark:focus:border-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 rounded-xl shadow-sm" required>
            @foreach(['pending' => 'Pendente', 'paid' => 'Pago', 'partially_paid' => 'Pago parcial', 'canceled' => 'Cancelado'] as $value => $label)
                <option value="{{ $value }}" {{ old('status', $payable->status ?? 'pending') === $value ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('status')" />
    </div>

    <div>
        <x-input-label for="payment_date" value="Data de pagamento" />
        <x-text-input id="payment_date" name="payment_date" type="date" class="mt-1 block w-full" :value="old('payment_date', isset($payable) && $payable->payment_date ? $payable->payment_date->format('Y-m-d') : '')" />
        <x-input-error class="mt-2" :messages="$errors->get('payment_date')" />
    </div>

    <div>
        <x-input-label for="amount_paid" value="Valor pago" />
        <x-text-input id="amount_paid" name="amount_paid" type="number" min="0" step="0.01" class="mt-1 block w-full" :value="old('amount_paid', $payable->amount_paid ?? '0.00')" />
        <x-input-error class="mt-2" :messages="$errors->get('amount_paid')" />
    </div>

    <div class="md:col-span-2">
        <x-input-label for="notes" value="Observações" />
        <textarea id="notes" name="notes" rows="4" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 dark:focus:border-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 rounded-xl shadow-sm">{{ old('notes', $payable->notes ?? '') }}</textarea>
        <x-input-error class="mt-2" :messages="$errors->get('notes')" />
    </div>
</div>

<div class="mt-8 flex justify-end gap-3">
    <a href="{{ route('payables.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 dark:bg-gray-700 border border-transparent rounded-xl font-semibold text-xs text-gray-700 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-200 dark:hover:bg-gray-600">Cancelar</a>
    <x-primary-button>{{ $submitLabel }}</x-primary-button>
</div>
