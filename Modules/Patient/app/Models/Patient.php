<?php

namespace Modules\Patient\Models;

use Database\Factories\PatientFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\AIScreening\Models\AiScreeningMessage;
use Modules\Auth\Models\User;
use Modules\Billing\Models\Payment;
use Modules\Document\Models\Document;
use Modules\RiskScore\Models\RiskScoreEvent;
use Modules\Session\Models\Session;
use Modules\WaitingList\Models\WaitingListEntry;

class Patient extends Model
{
    /** @use HasFactory<PatientFactory> */
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'psychologist_id',
        'name',
        'phone',
        'email',
        'birth_date',
        'notes',
        'is_active',
        'risk_score',
        'risk_level',
        'referred_by',
        'session_price_override',
        'billing_enabled',
        'ai_screening_status',
        'ai_screening_summary',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'is_active' => 'boolean',
            'billing_enabled' => 'boolean',
            'session_price_override' => 'decimal:2',
            'metadata' => 'array',
        ];
    }

    public function psychologist(): BelongsTo
    {
        return $this->belongsTo(User::class, 'psychologist_id');
    }

    public function referrer(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'referred_by');
    }

    public function referrals(): HasMany
    {
        return $this->hasMany(Patient::class, 'referred_by');
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(Session::class);
    }

    public function consents(): HasMany
    {
        return $this->hasMany(Consent::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function riskScoreEvents(): HasMany
    {
        return $this->hasMany(RiskScoreEvent::class);
    }

    public function aiScreeningMessages(): HasMany
    {
        return $this->hasMany(AiScreeningMessage::class);
    }

    public function waitingListEntries(): HasMany
    {
        return $this->hasMany(WaitingListEntry::class);
    }
}
