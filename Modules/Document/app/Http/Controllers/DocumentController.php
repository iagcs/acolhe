<?php

namespace Modules\Document\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Modules\Document\Actions\DeleteDocumentAction;
use Modules\Document\Actions\ListDocumentsAction;
use Modules\Document\Actions\StoreDocumentAction;
use Modules\Document\DTOs\DocumentResponseData;
use Modules\Document\Exceptions\DocumentNotFoundException;
use Modules\Document\Http\Requests\StoreDocumentRequest;
use Modules\Document\Models\Document;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentController extends Controller
{
    public function index(Request $request, ListDocumentsAction $action): JsonResponse
    {
        $request->validate([
            'patient_id' => ['required', 'uuid'],
        ]);

        $documents = $action->execute(
            user: $request->user(),
            patientId: $request->query('patient_id'),
        );

        $data = $documents->map(fn (Document $doc) => DocumentResponseData::fromModel($doc));

        return response()->json([
            'documents' => $data,
            'total' => $documents->count(),
        ]);
    }

    public function store(StoreDocumentRequest $request, StoreDocumentAction $action): JsonResponse
    {
        $document = $action->execute(
            user: $request->user(),
            patientId: $request->input('patient_id'),
            file: $request->file('file'),
            name: $request->input('name'),
            category: $request->input('category'),
        );

        return response()->json([
            'document' => DocumentResponseData::fromModel($document),
        ], 201);
    }

    public function show(Request $request, string $id): StreamedResponse
    {
        $document = Document::where('id', $id)
            ->where('psychologist_id', $request->user()->id)
            ->first();

        if (! $document) {
            throw new DocumentNotFoundException;
        }

        return Storage::disk('local')->download($document->path, $document->name);
    }

    public function destroy(Request $request, string $id, DeleteDocumentAction $action): JsonResponse
    {
        $document = $action->execute($request->user(), $id);

        return response()->json([
            'document' => DocumentResponseData::fromModel($document),
        ]);
    }
}
