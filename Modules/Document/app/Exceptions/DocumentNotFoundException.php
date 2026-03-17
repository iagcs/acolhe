<?php

namespace Modules\Document\Exceptions;

class DocumentNotFoundException extends DocumentException
{
    public function __construct()
    {
        parent::__construct('Documento não encontrado.', 404);
    }

    public function errorCode(): string
    {
        return 'DOCUMENT_NOT_FOUND';
    }
}
