<?php

namespace App\Models\Clinics\Clinic\Finance;

use App\Models\Traits\BelongsToClinic;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicePackage extends Model
{
    protected $table = 'service_packages';

    use BelongsToClinic, HasFactory;

    protected $fillable = ['clinic_id', 'service_type_id', 'name', 'description', 'number_of_sessions', 'price', 'validity_in_days', 'is_active'];
}
