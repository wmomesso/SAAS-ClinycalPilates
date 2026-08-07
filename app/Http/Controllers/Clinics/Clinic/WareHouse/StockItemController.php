<?php

namespace App\Http\Controllers\Clinics\Clinic\WareHouse;

use App\Http\Controllers\Controller;
use App\Models\Clinics\Clinic\WareHouse\StockItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class StockItemController extends Controller
{
    public function index(): View
    {
        $this->authorize('viewAny', StockItem::class);

        $items = StockItem::query()
            ->with([
                'movements' => fn ($query) => $query->with('user')->latest()->limit(5),
                'maintenanceLogs' => fn ($query) => $query->with('performedBy')->latest('performed_at')->limit(5),
            ])
            ->orderByRaw('quantity <= min_stock_level DESC')
            ->orderBy('name')
            ->paginate(20);

        return view('clinic.stock.index', compact('items'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', StockItem::class);
        $clinicId = $request->user()->clinic_id;

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'category' => ['required', Rule::in(['consumable', 'equipment'])],
            'sku' => ['nullable', 'string', 'max:100', Rule::unique('stock_items', 'sku')->where(fn ($query) => $query->where('clinic_id', $clinicId))],
            'serial_number' => ['nullable', 'string', 'max:150'],
            'unit' => ['required', 'string', 'max:20'],
            'quantity' => ['required', 'integer', 'min:0'],
            'min_stock_level' => ['required', 'integer', 'min:0'],
            'acquired_at' => ['nullable', 'date', 'before_or_equal:today'],
            'next_maintenance_at' => ['nullable', 'date'],
            'equipment_status' => ['required', Rule::in(['operational', 'maintenance', 'out_of_service'])],
        ]);

        DB::transaction(function () use ($data, $request): void {
            $initialQuantity = (int) $data['quantity'];
            $item = StockItem::create([...$data, 'quantity' => 0]);

            if ($initialQuantity > 0) {
                $item->movements()->create([
                    'user_id' => $request->user()->id,
                    'type' => 'ajuste',
                    'quantity_change' => $initialQuantity,
                    'reason' => 'Saldo inicial',
                ]);
                $item->update(['quantity' => $initialQuantity]);
            }
        });

        return back()->with('success', 'Item cadastrado no estoque.');
    }

    public function update(Request $request, StockItem $stockItem): RedirectResponse
    {
        $this->authorize('update', $stockItem);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'category' => ['required', Rule::in(['consumable', 'equipment'])],
            'sku' => ['nullable', 'string', 'max:100', Rule::unique('stock_items', 'sku')->where(fn ($query) => $query->where('clinic_id', $request->user()->clinic_id))->ignore($stockItem)],
            'serial_number' => ['nullable', 'string', 'max:150'],
            'unit' => ['required', 'string', 'max:20'],
            'min_stock_level' => ['required', 'integer', 'min:0'],
            'acquired_at' => ['nullable', 'date', 'before_or_equal:today'],
            'next_maintenance_at' => ['nullable', 'date'],
            'equipment_status' => ['required', Rule::in(['operational', 'maintenance', 'out_of_service'])],
        ]);

        $stockItem->update($data);

        return back()->with('success', 'Item atualizado.');
    }

    public function movement(Request $request, StockItem $stockItem): RedirectResponse
    {
        $this->authorize('update', $stockItem);

        $data = $request->validate([
            'type' => ['required', Rule::in(['entrada', 'saida', 'ajuste'])],
            'quantity' => ['required', 'integer', 'not_in:0'],
            'reason' => ['required', 'string', 'max:500'],
        ]);

        if ($data['type'] !== 'ajuste' && $data['quantity'] < 1) {
            return back()->withErrors(['quantity' => 'Entradas e saídas devem usar quantidade positiva.']);
        }

        DB::transaction(function () use ($stockItem, $data, $request): void {
            $item = StockItem::query()->lockForUpdate()->findOrFail($stockItem->id);
            $delta = match ($data['type']) {
                'entrada' => abs((int) $data['quantity']),
                'saida' => -abs((int) $data['quantity']),
                default => (int) $data['quantity'],
            };

            abort_if($item->quantity + $delta < 0, 422, 'A saída deixaria o estoque negativo.');

            $item->movements()->create([
                'user_id' => $request->user()->id,
                'type' => $data['type'],
                'quantity_change' => $delta,
                'reason' => $data['reason'],
            ]);
            $item->increment('quantity', $delta);
        });

        return back()->with('success', 'Movimentação registrada.');
    }

    public function destroy(StockItem $stockItem): RedirectResponse
    {
        $this->authorize('delete', $stockItem);
        $stockItem->delete();

        return back()->with('success', 'Item removido do estoque.');
    }

    public function maintenance(Request $request, StockItem $stockItem): RedirectResponse
    {
        $this->authorize('update', $stockItem);
        abort_unless($stockItem->category === 'equipment', 404);

        $data = $request->validate([
            'performed_at' => ['required', 'date', 'before_or_equal:today'],
            'next_due_at' => ['nullable', 'date', 'after:performed_at'],
            'cost' => ['nullable', 'numeric', 'min:0'],
            'provider' => ['nullable', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:2000'],
            'equipment_status' => ['required', Rule::in(['operational', 'maintenance', 'out_of_service'])],
        ]);

        DB::transaction(function () use ($stockItem, $data, $request): void {
            $stockItem->maintenanceLogs()->create([
                ...$data,
                'performed_by' => $request->user()->id,
            ]);
            $stockItem->update([
                'next_maintenance_at' => $data['next_due_at'] ?? null,
                'equipment_status' => $data['equipment_status'],
            ]);
        });

        return back()->with('success', 'Manutenção do equipamento registrada.');
    }
}
