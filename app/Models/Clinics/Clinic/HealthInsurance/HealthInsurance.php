<?php

namespace App\Models\Clinics\Clinic\HealthInsurance;

use App\Models\Clinics\Clinic\Clinic;
use App\Models\Traits\BelongsToClinic;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class HealthInsurance extends Model
{
    use BelongsToClinic, HasFactory, SoftDeletes;

    protected $fillable = [
        'clinic_id',
        'name',
        'company_name',
        'cnpj',
        'registration_number',
        'email',
        'phone',
        'zip_code',
        'address',
        'address_number',
        'complement',
        'neighborhood',
        'city',
        'state',
        'is_active',
        'accepted_guide_types',
        'payment_method',
        'payment_terms_days',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'accepted_guide_types' => 'array',
            'payment_terms_days' => 'integer',
        ];
    }

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    public function guides(): HasMany
    {
        return $this->hasMany(InsuranceGuide::class);
    }
}
