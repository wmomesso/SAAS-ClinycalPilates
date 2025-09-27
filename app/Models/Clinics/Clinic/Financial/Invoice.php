<?php

namespace App\Models\Clinics\Clinic\Financial;

use App\Models\Clinics\Clinic\Patient\Patient;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use HasFactory;
    protected $fillable = ['clinic_id', 'patient_id', 'invoice_number', 'status', 'total_amount', 'amount_paid', 'due_date', 'paid_at', 'notes'];
    protected $casts = ['due_date' => 'date', 'paid_at' => 'datetime'];

    public function patient(): BelongsTo { return $this->belongsTo(Patient::class); }
    public function items(): HasMany { return $this->hasMany(InvoiceItem::class); }
    public function transactions(): HasMany { return $this->hasMany(Transaction::class); }
}
