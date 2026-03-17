<?php

namespace Modules\Session\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Session\Actions\CancelSessionAction;
use Modules\Session\Actions\ListSessionsAction;
use Modules\Session\Actions\ShowSessionAction;
use Modules\Session\Actions\StoreSessionAction;
use Modules\Session\Actions\UpdateSessionAction;
use Modules\Session\Http\Requests\StoreSessionRequest;
use Modules\Session\Http\Requests\UpdateSessionRequest;

class SessionController extends Controller
{
    public function index(Request $request, ListSessionsAction $action): JsonResponse
    {
        $sessions = $action->execute(
            user: $request->user(),
            from: $request->query('from'),
            to: $request->query('to'),
            patientId: $request->query('patient_id'),
            status: $request->query('status'),
            perPage: (int) $request->query('per_page', 15),
        );

        return response()->json($sessions);
    }

    public function store(StoreSessionRequest $request, StoreSessionAction $action): JsonResponse
    {
        $session = $action->execute($request->user(), $request->getData());

        return response()->json(['session' => $session], 201);
    }

    public function show(Request $request, string $id, ShowSessionAction $action): JsonResponse
    {
        $session = $action->execute($request->user(), $id);

        return response()->json(['session' => $session]);
    }

    public function update(UpdateSessionRequest $request, string $id, UpdateSessionAction $action): JsonResponse
    {
        $session = $action->execute($request->user(), $id, $request->getData());

        return response()->json(['session' => $session]);
    }

    public function destroy(Request $request, string $id, CancelSessionAction $action): JsonResponse
    {
        $session = $action->execute($request->user(), $id);

        return response()->json(['session' => $session]);
    }
}
