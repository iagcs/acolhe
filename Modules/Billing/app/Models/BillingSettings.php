<?php

namespace Modules\Billing\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Auth\Models\User;

class BillingSettings extends Model
{
    use HasUuids;

    protected $fillable = [
        'psychologist_id',
        'asaas_account_id',
        'asaas_api_key',
        'billing_mode',
        'default_method',
        'auto_receipt',
        'reminder_overdue',
        'pix_key',
    ];

    protected function casts(): array
    {
        return [
            'auto_receipt' => 'boolean',
            'reminder_overdue' => 'boolean',
        ];
    }

    public function psychologist(): BelongsTo
    {
        return $this->belongsTo(User::class, 'psychologist_id');
    }
}
