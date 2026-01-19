<?php

namespace App\Models\Clinics\Clinic\Room;

use App\Models\Clinics\Clinic\Clinic;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Novo Modelo para Room (Salas)
 * Arquivo: app/Models/Room.php
 */
class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'clinic_id',
        'name',
        'capacity',
        'resources',
        'is_active',
    ];

    protected $casts = [
        'resources' => 'array',
        'is_active' => 'boolean',
    ];

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }
}
