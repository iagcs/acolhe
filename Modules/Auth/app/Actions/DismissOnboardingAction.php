<?php

namespace Modules\Auth\Actions;

use Modules\Auth\Models\User;

class DismissOnboardingAction
{
    public function execute(User $user): void
    {
        $settings = $user->settings ?? [];
        $settings['onboarding_dismissed'] = true;
        $user->settings = $settings;
        $user->save();
    }
}
