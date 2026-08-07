@extends('layouts.saas')

@section('title', 'Estoque e Insumos')

@section('content')
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Estoque e Equipamentos</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Controle insumos, aparelhos de Pilates, reposição, indisponibilidade e manutenção preventiva.</p>
        </div>

        @can('create', App\Models\Clinics\Clinic\WareHouse\StockItem::class)
            <details class="rounded-2xl bg-white p-6 shadow dark:bg-gray-800" @if($errors->any()) open @endif>
                <summary class="cursor-pointer font-semibold text-primary-600">Cadastrar novo item</summary>
                <form method="POST" action="{{ route('stock-items.store') }}" class="mt-5 grid grid-cols-1 gap-4 md:grid-cols-3">
                    @csrf
                    <div class="md:col-span-2"><x-input-label for="name" value="Nome" /><x-text-input id="name" name="name" class="mt-1 block w-full" value="{{ old('name') }}" required /></div>
                    <div><x-input-label for="sku" value="Código/SKU" /><x-text-input id="sku" name="sku" class="mt-1 block w-full" value="{{ old('sku') }}" /></div>
                    <div><x-input-label for="category" value="Categoria" /><select id="category" name="category" class="mt-1 block w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" required><option value="consumable">Insumo</option><option value="equipment">Equipamento</option></select></div>
                    <div><x-input-label for="serial_number" value="Número de série" /><x-text-input id="serial_number" name="serial_number" class="mt-1 block w-full" value="{{ old('serial_number') }}" /></div>
                    <div><x-input-label for="acquired_at" value="Data de aquisição" /><x-text-input id="acquired_at" name="acquired_at" type="date" class="mt-1 block w-full" value="{{ old('acquired_at') }}" /></div>
                    <div><x-input-label for="unit" value="Unidade" /><x-text-input id="unit" name="unit" class="mt-1 block w-full" value="{{ old('unit', 'un') }}" required /></div>
                    <div><x-input-label for="quantity" value="Saldo inicial" /><x-text-input id="quantity" name="quantity" type="number" min="0" class="mt-1 block w-full" value="{{ old('quantity', 0) }}" required /></div>
                    <div><x-input-label for="min_stock_level" value="Estoque mínimo" /><x-text-input id="min_stock_level" name="min_stock_level" type="number" min="0" class="mt-1 block w-full" value="{{ old('min_stock_level', 0) }}" required /></div>
                    <div><x-input-label for="next_maintenance_at" value="Próxima manutenção" /><x-text-input id="next_maintenance_at" name="next_maintenance_at" type="date" class="mt-1 block w-full" value="{{ old('next_maintenance_at') }}" /></div>
                    <input type="hidden" name="equipment_status" value="operational">
                    <div class="md:col-span-3"><x-input-label for="description" value="Descrição" /><textarea id="description" name="description" rows="2" class="mt-1 block w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">{{ old('description') }}</textarea></div>
                    <div class="md:col-span-3">@foreach($errors->all() as $error)<p class="text-sm text-red-600">{{ $error }}</p>@endforeach</div>
                    <div class="md:col-span-3 flex justify-end"><x-primary-button>Cadastrar item</x-primary-button></div>
                </form>
            </details>
        @endcan

        <div class="space-y-4">
            @forelse($items as $item)
                <div class="rounded-2xl border {{ $item->quantity <= $item->min_stock_level ? 'border-amber-300 bg-amber-50/60 dark:border-amber-700 dark:bg-amber-900/10' : 'border-gray-100 bg-white dark:border-gray-700 dark:bg-gray-800' }} p-5 shadow-sm">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <div class="flex items-center gap-2">
                                <h2 class="font-bold text-gray-900 dark:text-white">{{ $item->name }}</h2>
                                @if($item->quantity <= $item->min_stock_level)<span class="rounded-full bg-amber-100 px-2 py-0.5 text-xs font-semibold text-amber-800">Repor</span>@endif
                                @if($item->category === 'equipment' && $item->next_maintenance_at && $item->next_maintenance_at->isPast())<span class="rounded-full bg-red-100 px-2 py-0.5 text-xs font-semibold text-red-800">Manutenção vencida</span>@endif
                            </div>
                            <p class="text-sm text-gray-500">{{ $item->category === 'equipment' ? 'Equipamento' : 'Insumo' }} · {{ $item->sku ?: 'Sem SKU' }} · mínimo {{ $item->min_stock_level }} {{ $item->unit }}</p>
                            @if($item->category === 'equipment')<p class="text-xs text-gray-500">Série: {{ $item->serial_number ?: '—' }} · próxima manutenção: {{ $item->next_maintenance_at?->format('d/m/Y') ?? 'não agendada' }} · status: {{ $item->equipment_status }}</p>@endif
                        </div>
                        <div class="text-left lg:text-right"><span class="text-3xl font-bold text-gray-900 dark:text-white">{{ $item->quantity }}</span> <span class="text-sm text-gray-500">{{ $item->unit }}</span></div>
                    </div>

                    @can('update', $item)
                        <form method="POST" action="{{ route('stock-items.movements.store', $item) }}" class="mt-4 grid grid-cols-1 gap-3 rounded-xl bg-gray-50 p-3 dark:bg-gray-900/40 md:grid-cols-4">
                            @csrf
                            <select name="type" class="rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" required><option value="entrada">Entrada</option><option value="saida">Saída</option><option value="ajuste">Ajuste (+/-)</option></select>
                            <x-text-input name="quantity" type="number" placeholder="Quantidade" required />
                            <x-text-input name="reason" placeholder="Motivo / fornecedor" required />
                            <x-primary-button class="justify-center">Registrar</x-primary-button>
                        </form>
                    @endcan

                    @if($item->category === 'equipment')
                        @can('update', $item)
                            <details class="mt-4 rounded-xl border border-gray-100 p-3 dark:border-gray-700">
                                <summary class="cursor-pointer text-sm font-medium text-primary-600">Registrar manutenção/inspeção</summary>
                                <form method="POST" action="{{ route('stock-items.maintenance.store', $item) }}" class="mt-3 grid grid-cols-1 gap-3 md:grid-cols-3">
                                    @csrf
                                    <x-text-input name="performed_at" type="date" value="{{ now()->toDateString() }}" required />
                                    <x-text-input name="next_due_at" type="date" aria-label="Próximo vencimento" />
                                    <x-text-input name="provider" placeholder="Fornecedor/técnico" />
                                    <x-text-input name="cost" type="number" step="0.01" min="0" placeholder="Custo" />
                                    <select name="equipment_status" class="rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300" required><option value="operational">Operacional</option><option value="maintenance">Em manutenção</option><option value="out_of_service">Fora de uso</option></select>
                                    <x-text-input name="description" placeholder="Serviço realizado / resultado da inspeção" required />
                                    <div class="md:col-span-3 flex justify-end"><x-primary-button>Salvar manutenção</x-primary-button></div>
                                </form>
                            </details>
                        @endcan
                    @endif

                    <details class="mt-4">
                        <summary class="cursor-pointer text-sm font-medium text-primary-600">Últimas movimentações</summary>
                        <div class="mt-2 divide-y divide-gray-100 text-sm dark:divide-gray-700">
                            @forelse($item->movements as $movement)
                                <div class="flex justify-between gap-3 py-2"><span>{{ $movement->created_at?->format('d/m/Y H:i') }} · {{ $movement->reason }} · {{ $movement->user?->name ?? 'Sistema' }}</span><span class="font-semibold {{ $movement->quantity_change >= 0 ? 'text-green-600' : 'text-red-600' }}">{{ $movement->quantity_change > 0 ? '+' : '' }}{{ $movement->quantity_change }}</span></div>
                            @empty
                                <p class="py-2 text-gray-500">Sem movimentações.</p>
                            @endforelse
                        </div>
                    </details>
                    @if($item->category === 'equipment' && $item->maintenanceLogs->isNotEmpty())
                        <details class="mt-2">
                            <summary class="cursor-pointer text-sm font-medium text-primary-600">Histórico de manutenção</summary>
                            @foreach($item->maintenanceLogs as $maintenance)
                                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">{{ $maintenance->performed_at->format('d/m/Y') }} · {{ $maintenance->description }} · {{ $maintenance->provider ?: 'equipe interna' }} · próxima: {{ $maintenance->next_due_at?->format('d/m/Y') ?? '—' }}</p>
                            @endforeach
                        </details>
                    @endif
                </div>
            @empty
                <div class="rounded-2xl bg-white p-10 text-center text-gray-500 shadow dark:bg-gray-800">Nenhum item cadastrado.</div>
            @endforelse
        </div>

        {{ $items->links() }}
    </div>
@endsection
