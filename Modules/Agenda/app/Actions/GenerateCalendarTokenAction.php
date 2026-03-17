<?php

namespace Modules\Agenda\Actions;

use Illuminate\Support\Str;
use Modules\Auth\Models\User;

class GenerateCalendarTokenAction
{
    public function execute(User $user): string
    {
        $token = Str::random(64);

        $user->update(['calendar_token' => $token]);

        return $token;
    }
}
