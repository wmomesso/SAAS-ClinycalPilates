@csrf

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
        <x-input-label for="name" value="Nome" />
        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $paymentMethod->name ?? '')" required autofocus />
        <x-input-error class="mt-2" :messages="$errors->get('name')" />
    </div>

    <div>
        <x-input-label for="type" value="Tipo" />
        <select id="type" name="type" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 dark:focus:border-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 rounded-xl shadow-sm" required>
            @foreach(['cash' => 'Dinheiro', 'pix' => 'Pix', 'credit_card' => 'Cartão crédito', 'debit_card' => 'Cartão débito', 'bank_slip' => 'Boleto', 'bank_transfer' => 'Transferência', 'health_insurance' => 'Convênio', 'other' => 'Outro'] as $value => $label)
                <option value="{{ $value }}" {{ old('type', $paymentMethod->type ?? 'other') === $value ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('type')" />
    </div>
</div>

<div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
    <label class="flex items-center gap-3 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
        <input type="checkbox" name="requires_bank_account" value="1" class="rounded border-gray-300 text-primary-600 shadow-sm focus:ring-primary-500" {{ old('requires_bank_account', $paymentMethod->requires_bank_account ?? false) ? 'checked' : '' }}>
        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Exige conta bancária</span>
    </label>

    <label class="flex items-center gap-3 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
        <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300 text-primary-600 shadow-sm focus:ring-primary-500" {{ old('is_active', $paymentMethod->is_active ?? true) ? 'checked' : '' }}>
        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Ativa</span>
    </label>
</div>

<div class="mt-8 flex justify-end gap-3">
    <a href="{{ route('payment-methods.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 dark:bg-gray-700 border border-transparent rounded-xl font-semibold text-xs text-gray-700 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-200 dark:hover:bg-gray-600">Cancelar</a>
    <x-primary-button>{{ $submitLabel }}</x-primary-button>
</div>
