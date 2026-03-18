<?php

namespace Modules\WhatsApp\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Auth\Models\User;
use Modules\Patient\Models\Patient;

class WhatsAppConversation extends Model
{
    use HasUuids;

    protected $table = 'whatsapp_conversations';

    protected $fillable = [
        'phone',
        'psychologist_id',
        'patient_id',
        'state',
        'context',
        'last_message_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'context' => 'array',
            'last_message_at' => 'datetime',
            'expires_at' => 'datetime',
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

    public function messages(): HasMany
    {
        return $this->hasMany(WhatsAppMessage::class, 'conversation_id');
    }

    /**
     * Merge a partial context patch into the existing context array.
     */
    public function patchContext(array $patch): void
    {
        $this->context = array_merge($this->context ?? [], $patch);
    }

    /**
     * Retrieve a single key from the conversation context.
     */
    public function getContext(string $key, mixed $default = null): mixed
    {
        return ($this->context ?? [])[$key] ?? $default;
    }
}
