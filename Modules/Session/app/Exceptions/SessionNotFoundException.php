<?php

namespace Modules\Session\Exceptions;

class SessionNotFoundException extends SessionException
{
    public function __construct()
    {
        parent::__construct('Sessão não encontrada.', 404);
    }

    public function errorCode(): string
    {
        return 'SESSION_NOT_FOUND';
    }
}
