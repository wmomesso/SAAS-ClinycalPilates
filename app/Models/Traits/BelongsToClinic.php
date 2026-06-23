<?php

namespace App\Models\Traits;

use App\Models\Scopes\ClinicScope;
use Illuminate\Support\Facades\Auth;

trait BelongsToClinic
{
    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new ClinicScope);

        static::creating(function ($model) {
            if (Auth::hasUser() && Auth::user()->clinic_id && ! $model->clinic_id) {
                $model->clinic_id = Auth::user()->clinic_id;
            }
        });
    }
}
