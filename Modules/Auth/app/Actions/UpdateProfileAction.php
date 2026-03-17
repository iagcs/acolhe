<?php

namespace Modules\Auth\Actions;

use Illuminate\Support\Facades\Storage;
use Modules\Auth\Http\Requests\UpdateProfileRequest;
use Modules\Auth\Models\User;

class UpdateProfileAction
{
    public function execute(User $user, UpdateProfileRequest $request): User
    {
        if ($request->hasFile('photo')) {
            $path = Storage::disk('public')->putFile('photos', $request->file('photo'));
            $user->photo = $path;
        }

        if ($request->has('bio')) {
            $user->bio = $request->input('bio');
        }

        $user->save();

        return $user;
    }
}
