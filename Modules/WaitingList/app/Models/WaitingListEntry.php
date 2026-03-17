<?php

namespace Modules\WaitingList\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Auth\Models\User;
use Modules\Patient\Models\Patient;
use Modules\Session\Models\Session;

class WaitingListEntry extends Model
{
    use HasUuids;

    protected $fillable = [
        'psychologist_id',
        'patient_id',
        'preferred_days',
        'preferred_period',
        'status',
        'offered_session_id',
        'offered_at',
        'responded_at',
        'position',
    ];

    protected function casts(): array
    {
        return [
            'preferred_days' => 'array',
            'offered_at' => 'datetime',
            'responded_at' => 'datetime',
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

    public function offeredSession(): BelongsTo
    {
        return $this->belongsTo(Session::class, 'offered_session_id');
    }
}
