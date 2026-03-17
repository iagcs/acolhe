<?php

namespace Modules\Patient\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Patient\Actions\DeletePatientAction;
use Modules\Patient\Actions\ListPatientsAction;
use Modules\Patient\Actions\ShowPatientAction;
use Modules\Patient\Actions\StorePatientAction;
use Modules\Patient\Actions\UpdatePatientAction;
use Modules\Patient\Http\Requests\StorePatientRequest;
use Modules\Patient\Http\Requests\UpdatePatientRequest;

class PatientController extends Controller
{
    public function index(Request $request, ListPatientsAction $action): JsonResponse
    {
        $patients = $action->execute(
            user: $request->user(),
            search: $request->query('search'),
            isActive: $request->has('is_active') ? filter_var($request->query('is_active'), FILTER_VALIDATE_BOOLEAN) : null,
            perPage: (int) $request->query('per_page', 15),
        );

        return response()->json($patients);
    }

    public function store(StorePatientRequest $request, StorePatientAction $action): JsonResponse
    {
        $patient = $action->execute($request->user(), $request->getData());

        return response()->json(['patient' => $patient], 201);
    }

    public function show(Request $request, string $id, ShowPatientAction $action): JsonResponse
    {
        $patient = $action->execute($request->user(), $id);

        return response()->json(['patient' => $patient]);
    }

    public function update(UpdatePatientRequest $request, string $id, UpdatePatientAction $action): JsonResponse
    {
        $patient = $action->execute($request->user(), $id, $request->getData());

        return response()->json(['patient' => $patient]);
    }

    public function destroy(Request $request, string $id, DeletePatientAction $action): JsonResponse
    {
        $action->execute($request->user(), $id);

        return response()->json(null, 204);
    }
}
