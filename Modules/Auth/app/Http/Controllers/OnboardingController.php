<?php

namespace Modules\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Auth\Actions\DismissOnboardingAction;
use Modules\Auth\Actions\GetOnboardingStatusAction;

class OnboardingController extends Controller
{
    public function status(Request $request, GetOnboardingStatusAction $action): JsonResponse
    {
        return response()->json($action->execute($request->user()));
    }

    public function dismiss(Request $request, DismissOnboardingAction $action): JsonResponse
    {
        $action->execute($request->user());

        return response()->json(['dismissed' => true]);
    }
}
