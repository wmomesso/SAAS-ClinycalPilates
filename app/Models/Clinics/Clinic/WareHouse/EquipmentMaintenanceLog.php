<?php

namespace App\Models\Clinics\Clinic\WareHouse;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EquipmentMaintenanceLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_item_id', 'performed_by', 'performed_at', 'next_due_at',
        'cost', 'provider', 'description',
    ];

    protected function casts(): array
    {
        return [
            'performed_at' => 'date',
            'next_due_at' => 'date',
            'cost' => 'decimal:2',
        ];
    }

    public function stockItem(): BelongsTo
    {
        return $this->belongsTo(StockItem::class);
    }

    public function performedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
}
