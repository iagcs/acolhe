<?php

namespace Modules\Document\Actions;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\Auth\Models\User;
use Modules\Document\Models\Document;

class StoreDocumentAction
{
    public function execute(User $user, string $patientId, UploadedFile $file, ?string $name, ?string $category): Document
    {
        $path = Storage::disk('local')->putFile(
            "documents/{$user->id}/{$patientId}",
            $file
        );

        $type = $this->detectType($file->getMimeType());

        return Document::create([
            'patient_id' => $patientId,
            'psychologist_id' => $user->id,
            'name' => $name ?? $file->getClientOriginalName(),
            'path' => $path,
            'type' => $type,
            'category' => $category,
            'size_bytes' => $file->getSize(),
            'uploaded_by' => 'psychologist',
        ]);
    }

    private function detectType(string $mimeType): string
    {
        if (str_starts_with($mimeType, 'image/')) {
            return 'image';
        }

        if ($mimeType === 'application/pdf') {
            return 'pdf';
        }

        return 'doc';
    }
}
