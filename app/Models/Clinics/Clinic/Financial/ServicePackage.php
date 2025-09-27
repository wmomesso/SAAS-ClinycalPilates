<?php

namespace App\Models\Clinics\Clinic\Financial;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicePackage extends Model
{
    protected $table = 'service_packages';

    use HasFactory;
    protected $fillable = ['clinic_id', 'service_type_id', 'name', 'description', 'number_of_sessions', 'price', 'validity_in_days', 'is_active'];
}
