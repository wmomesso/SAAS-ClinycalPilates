<?php

namespace App\Models\Clinics\Clinic\Services;

use App\Models\Clinics\Clinic\Clinic;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceType extends Model
{
    use HasFactory;

    protected $fillable = [
        'clinic_id', 'name', 'duration_in_minutes', 'price', 'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }
}
