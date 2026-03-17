<?php

namespace Modules\Auth\Actions;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Auth\DTOs\RegisterData;
use Modules\Auth\Enums\Plan;
use Modules\Auth\Models\User;

class RegisterAction
{
    public function execute(RegisterData $data): array
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => $data->name,
                'email' => $data->email,
                'password' => $data->password,
                'crp' => $data->crp,
                'phone' => $data->phone,
                'therapeutic_approach' => $data->therapeutic_approach->value,
                'session_duration' => $data->session_duration,
                'session_interval' => $data->session_interval,
                'session_price' => $data->session_price,
                'plan' => Plan::Free->value,
                'plan_expires_at' => now()->addDays(14),
                'slug' => Str::slug($data->name),
                'timezone' => 'America/Sao_Paulo',
            ]);

            foreach ($data->availabilities as $availability) {
                $user->availabilities()->create([
                    'day_of_week' => $availability->day_of_week,
                    'start_time' => $availability->start_time,
                    'end_time' => $availability->end_time,
                    'is_active' => true,
                ]);
            }

            $token = $user->createToken('api')->plainTextToken;

            return [
                'token' => $token,
                'user' => $user,
            ];
        });
    }
}
