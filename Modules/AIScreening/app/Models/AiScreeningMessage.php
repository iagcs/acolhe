<?php

namespace Modules\AIScreening\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Patient\Models\Patient;

class AiScreeningMessage extends Model
{
    use HasUuids;

    protected $fillable = [
        'patient_id',
        'role',
        'content',
        'sequence',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }
}
