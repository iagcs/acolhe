<?php

namespace Modules\Auth\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Modules\Agenda\Models\Availability;
use Modules\Agenda\Models\Recurrence;
use Modules\Billing\Models\BillingSettings;
use Modules\Patient\Models\Patient;
use Modules\Session\Models\Session;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, HasUuids, Notifiable, SoftDeletes;

    protected static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }

    protected $table = 'psychologists';

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'crp',
        'timezone',
        'session_duration',
        'session_interval',
        'session_price',
        'therapeutic_approach',
        'plan',
        'plan_expires_at',
        'fiscal_data',
        'photo',
        'bio',
        'ai_settings',
        'settings',
        'slug',
        'calendar_token',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'plan_expires_at' => 'datetime',
            'fiscal_data' => 'array',
            'ai_settings' => 'array',
            'settings' => 'array',
            'session_price' => 'decimal:2',
        ];
    }

    public function patients(): HasMany
    {
        return $this->hasMany(Patient::class, 'psychologist_id');
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(Session::class, 'psychologist_id');
    }

    public function availabilities(): HasMany
    {
        return $this->hasMany(Availability::class, 'psychologist_id');
    }

    public function recurrences(): HasMany
    {
        return $this->hasMany(Recurrence::class, 'psychologist_id');
    }

    public function billingSettings(): HasOne
    {
        return $this->hasOne(BillingSettings::class, 'psychologist_id');
    }
}
