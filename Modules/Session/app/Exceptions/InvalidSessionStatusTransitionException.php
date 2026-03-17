<?php

namespace Modules\Session\Exceptions;

class InvalidSessionStatusTransitionException extends SessionException
{
    /**
     * Initialize the exception with a default error message and HTTP status code 422.
     *
     * The exception message is "Transição de status inválida." and the HTTP status code is 422.
     */
    public function __construct()
    {
        parent::__construct('Transição de status inválida.', 422);
    }

    /**
     * Get the canonical error code identifying this exception.
     *
     * @return string The error code `INVALID_STATUS_TRANSITION`.
     */
    public function errorCode(): string
    {
        return 'INVALID_STATUS_TRANSITION';
    }
}
