<?php

namespace App\Models\Clinics\Clinic\WareHouse;

use App\Models\Clinics\Clinic\Clinic;
use App\Models\Traits\BelongsToClinic;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockItem extends Model
{
    use BelongsToClinic, HasFactory;

    protected $fillable = [
        'clinic_id', 'name', 'description', 'category', 'sku', 'serial_number', 'unit',
        'quantity', 'min_stock_level', 'acquired_at', 'next_maintenance_at', 'equipment_status',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'min_stock_level' => 'integer',
        'acquired_at' => 'date',
        'next_maintenance_at' => 'date',
    ];

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    public function movements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function maintenanceLogs(): HasMany
    {
        return $this->hasMany(EquipmentMaintenanceLog::class)->latest('performed_at');
    }
}
