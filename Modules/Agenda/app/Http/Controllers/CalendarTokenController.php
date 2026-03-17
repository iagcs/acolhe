<?php

namespace Modules\Agenda\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Agenda\Actions\GenerateCalendarTokenAction;

class CalendarTokenController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();
        $token = $user->calendar_token;

        return response()->json([
            'token' => $token,
            'url' => $token ? url("/api/calendar/feed/{$token}") : null,
        ]);
    }

    public function store(Request $request, GenerateCalendarTokenAction $action): JsonResponse
    {
        $token = $action->execute($request->user());

        return response()->json([
            'token' => $token,
            'url' => url("/api/calendar/feed/{$token}"),
        ], 201);
    }
}
