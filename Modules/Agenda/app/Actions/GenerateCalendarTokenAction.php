<?php

namespace Modules\Agenda\Actions;

use Illuminate\Support\Str;
use Modules\Auth\Models\User;

class GenerateCalendarTokenAction
{
    /**
     * Generates a 64-character calendar token and stores it on the given user.
     *
     * @param User $user The user whose `calendar_token` field will be updated with the generated token.
     * @return string The generated 64-character token.
     */
    public function execute(User $user): string
    {
        $token = Str::random(64);

        $user->update(['calendar_token' => $token]);

        return $token;
    }
}
