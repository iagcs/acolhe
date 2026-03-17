<?php

namespace Modules\Document\Models;

use Database\Factories\DocumentFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Auth\Models\User;
use Modules\Patient\Models\Patient;

class Document extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected static function newFactory(): DocumentFactory
    {
        return DocumentFactory::new();
    }

    protected $fillable = [
        'patient_id',
        'psychologist_id',
        'name',
        'path',
        'type',
        'category',
        'size_bytes',
        'uploaded_by',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function psychologist(): BelongsTo
    {
        return $this->belongsTo(User::class, 'psychologist_id');
    }
}
