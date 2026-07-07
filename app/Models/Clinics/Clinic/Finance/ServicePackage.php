<?php

namespace App\Models\Clinics\Clinic\Finance;

use App\Models\Clinics\Clinic\Services\ServiceType;
use App\Models\Traits\BelongsToClinic;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServicePackage extends Model
{
    protected $table = 'service_packages';

    use BelongsToClinic, HasFactory;

    protected $fillable = ['clinic_id', 'service_type_id', 'name', 'description', 'number_of_sessions', 'price', 'validity_in_days', 'is_active'];

    public function serviceType(): BelongsTo
    {
        return $this->belongsTo(ServiceType::class);
    }
}
