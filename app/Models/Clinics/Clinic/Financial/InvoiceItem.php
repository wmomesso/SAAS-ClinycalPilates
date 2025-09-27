<?php

namespace App\Models\Clinics\Clinic\Financial;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceItem extends Model
{
    use HasFactory;
    protected $fillable = ['invoice_id', 'itemable_id', 'itemable_type', 'description', 'quantity', 'unit_price', 'total'];

    public function invoice(): BelongsTo { return $this->belongsTo(Invoice::class); }
    public function itemable() { return $this->morphTo(); }
}
