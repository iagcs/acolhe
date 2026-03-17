<?php

namespace Modules\Session\Exceptions;

class SessionNotFoundException extends SessionException
{
    /**
     * Create a SessionNotFoundException with the message "Sessão não encontrada." and HTTP status code 404.
     */
    public function __construct()
    {
        parent::__construct('Sessão não encontrada.', 404);
    }

    /**
     * Gets the machine-readable error code for this exception.
     *
     * @return string The error code 'SESSION_NOT_FOUND'.
     */
    public function errorCode(): string
    {
        return 'SESSION_NOT_FOUND';
    }
}
