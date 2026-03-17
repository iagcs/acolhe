<?php

namespace Modules\Auth\Actions;

use Modules\Auth\Models\User;

class GetOnboardingStatusAction
{
    public function execute(User $user): array
    {
        return [
            'has_photo' => ! empty($user->photo),
            'has_bio' => ! empty($user->bio),
            'patient_count' => $user->patients()->count(),
            'session_count' => $user->sessions()->count(),
            'dismissed' => $user->settings['onboarding_dismissed'] ?? false,
        ];
    }
}
