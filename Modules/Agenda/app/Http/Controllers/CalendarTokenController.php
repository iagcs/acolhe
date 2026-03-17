<?php

namespace Modules\Agenda\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Agenda\Actions\GenerateCalendarTokenAction;

class CalendarTokenController extends Controller
{
    /**
     * Returns the authenticated user's calendar token and a feed URL when available.
     *
     * @return JsonResponse JSON object with `token` (string|null) and `url` (string|null). `url` is `null` when `token` is `null`.
     */
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();
        $token = $user->calendar_token;

        return response()->json([
            'token' => $token,
            'url' => $token ? url("/api/calendar/feed/{$token}") : null,
        ]);
    }

    /**
     * Generate a new calendar token for the authenticated user and return its feed URL.
     *
     * @param Request $request The current HTTP request used to identify the authenticated user.
     * @param GenerateCalendarTokenAction $action Action that generates or updates the user's calendar token.
     * @return JsonResponse JSON containing `token` (the generated token) and `url` (the calendar feed URL); response status 201 Created.
     */
    public function store(Request $request, GenerateCalendarTokenAction $action): JsonResponse
    {
        $token = $action->execute($request->user());

        return response()->json([
            'token' => $token,
            'url' => url("/api/calendar/feed/{$token}"),
        ], 201);
    }
}
