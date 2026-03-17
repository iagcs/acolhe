<?php

namespace Modules\Billing\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Modules\Auth\Models\User;
use Modules\Patient\Models\Patient;
use Modules\Receipt\Models\Receipt;
use Modules\Session\Models\Session;

class Payment extends Model
{
    use HasUuids;

    protected $fillable = [
        'psychologist_id',
        'patient_id',
        'session_id',
        'asaas_payment_id',
        'asaas_invoice_url',
        'amount',
        'net_amount',
        'fee',
        'method',
        'status',
        'billing_mode',
        'due_date',
        'paid_at',
        'refunded_at',
        'whatsapp_sent',
        'whatsapp_reminder_count',
        'external_note',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'net_amount' => 'decimal:2',
            'fee' => 'decimal:2',
            'due_date' => 'date',
            'paid_at' => 'datetime',
            'refunded_at' => 'datetime',
            'whatsapp_sent' => 'boolean',
        ];
    }

    public function psychologist(): BelongsTo
    {
        return $this->belongsTo(User::class, 'psychologist_id');
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(Session::class);
    }

    public function receipt(): HasOne
    {
        return $this->hasOne(Receipt::class);
    }
}
