<?php

namespace App\Models\Clinics\Clinic\Finance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;
    protected $fillable = ['invoice_id', 'amount', 'payment_method', 'transaction_date', 'notes'];
    protected $casts = ['transaction_date' => 'datetime'];

    public function invoice(): BelongsTo { return $this->belongsTo(Invoice::class); }
}
