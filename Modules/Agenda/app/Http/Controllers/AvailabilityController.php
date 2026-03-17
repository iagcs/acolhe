<?php

namespace Modules\Agenda\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Agenda\Actions\ListAvailabilitiesAction;
use Modules\Agenda\DTOs\AvailabilityData;

class AvailabilityController extends Controller
{
    public function index(Request $request, ListAvailabilitiesAction $action): JsonResponse
    {
        $availabilities = $action->execute($request->user());

        return response()->json([
            'availabilities' => AvailabilityData::collect($availabilities),
        ]);
    }
}
