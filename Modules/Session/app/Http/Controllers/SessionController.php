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
    /**
     * Retrieve sessions filtered by query parameters.
     *
     * Accepts optional query parameters: `from`, `to`, `patient_id`, `status`, and `per_page`.
     *
     * @param \Illuminate\Http\Request $request The HTTP request containing optional filter and pagination query parameters.
     * @param \Modules\Session\Actions\ListSessionsAction $action Action that performs session listing with the provided filters.
     * @return \Illuminate\Http\JsonResponse JSON response containing the session collection or paginated result matching the provided filters.
     */
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

    /**
     * Create a new session and return it as JSON.
     *
     * @param StoreSessionRequest $request The validated request; session attributes available via {@see getData()}.
     * @param StoreSessionAction $action The action that persists the session.
     * @return JsonResponse The created session under the `session` key with HTTP status 201.
     */
    public function store(StoreSessionRequest $request, StoreSessionAction $action): JsonResponse
    {
        $session = $action->execute($request->user(), $request->getData());

        return response()->json(['session' => $session], 201);
    }

    /**
     * Retrieve a single session resource and return it as JSON.
     *
     * The response body contains the session under the `session` key.
     *
     * @param string $id The session identifier.
     * @return \Illuminate\Http\JsonResponse A JSON response containing the session resource under the `session` key.
     */
    public function show(Request $request, string $id, ShowSessionAction $action): JsonResponse
    {
        $session = $action->execute($request->user(), $id);

        return response()->json(['session' => $session]);
    }

    /**
     * Update an existing session and return the updated resource.
     *
     * @param UpdateSessionRequest $request The validated request containing update data (accessible via getData()).
     * @param string $id The identifier of the session to update.
     * @return \Illuminate\Http\JsonResponse JSON response with a `session` key containing the updated session data.
     */
    public function update(UpdateSessionRequest $request, string $id, UpdateSessionAction $action): JsonResponse
    {
        $session = $action->execute($request->user(), $id, $request->getData());

        return response()->json(['session' => $session]);
    }

    /**
     * Cancel a session and return the resulting session data.
     *
     * @param string $id The session identifier.
     * @return JsonResponse The JSON response containing the canceled session under the `session` key.
     */
    public function destroy(Request $request, string $id, CancelSessionAction $action): JsonResponse
    {
        $session = $action->execute($request->user(), $id);

        return response()->json(['session' => $session]);
    }
}
