@csrf

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
        <x-input-label for="name" value="Nome da conta" />
        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $bankAccount->name ?? '')" required autofocus />
        <x-input-error class="mt-2" :messages="$errors->get('name')" />
    </div>

    <div>
        <x-input-label for="bank_name" value="Banco" />
        <x-text-input id="bank_name" name="bank_name" type="text" class="mt-1 block w-full" :value="old('bank_name', $bankAccount->bank_name ?? '')" />
        <x-input-error class="mt-2" :messages="$errors->get('bank_name')" />
    </div>

    <div>
        <x-input-label for="agency" value="Agência" />
        <x-text-input id="agency" name="agency" type="text" class="mt-1 block w-full" :value="old('agency', $bankAccount->agency ?? '')" />
        <x-input-error class="mt-2" :messages="$errors->get('agency')" />
    </div>

    <div>
        <x-input-label for="account_number" value="Conta" />
        <x-text-input id="account_number" name="account_number" type="text" class="mt-1 block w-full" :value="old('account_number', $bankAccount->account_number ?? '')" />
        <x-input-error class="mt-2" :messages="$errors->get('account_number')" />
    </div>

    <div>
        <x-input-label for="initial_balance" value="Saldo inicial" />
        <x-text-input id="initial_balance" name="initial_balance" type="number" step="0.01" class="mt-1 block w-full" :value="old('initial_balance', $bankAccount->initial_balance ?? '0.00')" required />
        <x-input-error class="mt-2" :messages="$errors->get('initial_balance')" />
    </div>

    <div>
        <x-input-label for="pix_key_type" value="Tipo de chave Pix" />
        <select id="pix_key_type" name="pix_key_type" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-primary-500 dark:focus:border-primary-600 focus:ring-primary-500 dark:focus:ring-primary-600 rounded-xl shadow-sm">
            <option value="">Selecione...</option>
            @foreach(['cpf' => 'CPF', 'cnpj' => 'CNPJ', 'email' => 'E-mail', 'phone' => 'Telefone', 'random' => 'Chave aleatória'] as $value => $label)
                <option value="{{ $value }}" {{ old('pix_key_type', $bankAccount->pix_key_type ?? '') === $value ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('pix_key_type')" />
    </div>

    <div class="md:col-span-2">
        <x-input-label for="pix_key" value="Chave Pix" />
        <x-text-input id="pix_key" name="pix_key" type="text" class="mt-1 block w-full" :value="old('pix_key', $bankAccount->pix_key ?? '')" />
        <x-input-error class="mt-2" :messages="$errors->get('pix_key')" />
    </div>
</div>

<div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
    <label class="flex items-center gap-3 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
        <input type="checkbox" name="has_pix" value="1" class="rounded border-gray-300 text-primary-600 shadow-sm focus:ring-primary-500" {{ old('has_pix', $bankAccount->has_pix ?? false) ? 'checked' : '' }}>
        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Possui Pix</span>
    </label>

    <label class="flex items-center gap-3 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
        <input type="checkbox" name="issues_bank_slips" value="1" class="rounded border-gray-300 text-primary-600 shadow-sm focus:ring-primary-500" {{ old('issues_bank_slips', $bankAccount->issues_bank_slips ?? false) ? 'checked' : '' }}>
        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Emite boletos</span>
    </label>

    <label class="flex items-center gap-3 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
        <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300 text-primary-600 shadow-sm focus:ring-primary-500" {{ old('is_active', $bankAccount->is_active ?? true) ? 'checked' : '' }}>
        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Conta ativa</span>
    </label>
</div>

<div class="mt-8 flex justify-end gap-3">
    <a href="{{ route('bank-accounts.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 dark:bg-gray-700 border border-transparent rounded-xl font-semibold text-xs text-gray-700 dark:text-gray-200 uppercase tracking-widest hover:bg-gray-200 dark:hover:bg-gray-600">
        Cancelar
    </a>
    <x-primary-button>{{ $submitLabel }}</x-primary-button>
</div>
