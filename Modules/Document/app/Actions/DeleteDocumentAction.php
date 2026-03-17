<?php

namespace Modules\Document\Actions;

use Illuminate\Support\Facades\Storage;
use Modules\Auth\Models\User;
use Modules\Document\Exceptions\DocumentNotFoundException;
use Modules\Document\Models\Document;

class DeleteDocumentAction
{
    public function execute(User $user, string $id): Document
    {
        $document = Document::where('id', $id)
            ->where('psychologist_id', $user->id)
            ->first();

        if (! $document) {
            throw new DocumentNotFoundException;
        }

        Storage::disk('local')->delete($document->path);

        $document->delete();

        return $document;
    }
}
