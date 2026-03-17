<?php

namespace Modules\Session\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Agenda\Models\Recurrence;
use Modules\Auth\Models\User;
use Modules\Billing\Models\Payment;
use Modules\Patient\Models\Patient;
use Modules\Receipt\Models\Receipt;
use Modules\Reminder\Models\Reminder;

class Session extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'psychologist_id',
        'patient_id',
        'recurrence_id',
        'starts_at',
        'ends_at',
        'status',
        'type',
        'online_link',
        'private_notes',
        'price',
        'receipt_sent',
        'payment_status',
        'cancelled_at',
        'cancellation_reason',
        'reschedule_count',
        'confirmation_responded_at',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'confirmation_responded_at' => 'datetime',
            'price' => 'decimal:2',
            'receipt_sent' => 'boolean',
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

    public function recurrence(): BelongsTo
    {
        return $this->belongsTo(Recurrence::class);
    }

    public function reminders(): HasMany
    {
        return $this->hasMany(Reminder::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function receipts(): HasMany
    {
        return $this->hasMany(Receipt::class);
    }
}
