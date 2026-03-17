<?php

namespace Modules\Reminder\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Session\Models\Session;

class Reminder extends Model
{
    use HasUuids;

    protected $fillable = [
        'session_id',
        'type',
        'confirmation_type',
        'status',
        'scheduled_at',
        'sent_at',
        'delivered_at',
        'read_at',
        'whatsapp_message_id',
        'error_message',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'sent_at' => 'datetime',
            'delivered_at' => 'datetime',
            'read_at' => 'datetime',
        ];
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(Session::class);
    }
}
