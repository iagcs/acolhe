<?php

namespace Modules\Session\Models;

use Database\Factories\SessionFactory;
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
use Modules\Session\Enums\SessionStatus;

class Session extends Model
{
    use HasFactory, HasUuids;

    /**
     * Provide a model factory instance for the Session model.
     *
     * @return SessionFactory A new factory for creating Session model instances.
     */
    protected static function newFactory(): SessionFactory
    {
        return SessionFactory::new();
    }

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

    /**
     * Define the model's attribute casting rules.
     *
     * Each array key is an attribute name and each value is the cast type or class used to convert
     * the attribute when accessing or persisting the model (e.g., 'datetime', 'decimal:2',
     * boolean, integer, enum class, or 'encrypted').
     *
     * @return array<string,string> Mapping of attribute names to their cast definitions.
     */
    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'confirmation_responded_at' => 'datetime',
            'price' => 'decimal:2',
            'receipt_sent' => 'boolean',
            'reschedule_count' => 'integer',
            'status' => SessionStatus::class,
            'private_notes' => 'encrypted',
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
