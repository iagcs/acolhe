<?php

namespace Modules\RiskScore\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Patient\Models\Patient;
use Modules\Session\Models\Session;

class RiskScoreEvent extends Model
{
    use HasUuids;

    protected $fillable = [
        'patient_id',
        'session_id',
        'event_type',
        'score_change',
        'score_after',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(Session::class);
    }
}
