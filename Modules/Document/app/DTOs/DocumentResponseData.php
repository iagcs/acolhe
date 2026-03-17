<?php

namespace Modules\Document\DTOs;

use Modules\Document\Models\Document;
use Spatie\LaravelData\Data;

class DocumentResponseData extends Data
{
    public function __construct(
        public readonly string $id,
        public readonly string $patient_id,
        public readonly string $name,
        public readonly string $type,
        public readonly ?string $category,
        public readonly int $size_bytes,
        public readonly string $created_at,
        public readonly string $download_url,
    ) {}

    public static function fromModel(Document $document): self
    {
        return new self(
            id: $document->id,
            patient_id: $document->patient_id,
            name: $document->name,
            type: $document->type,
            category: $document->category,
            size_bytes: $document->size_bytes,
            created_at: $document->created_at->toIso8601String(),
            download_url: route('api.document.show', $document->id),
        );
    }
}
