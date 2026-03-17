<?php

namespace Modules\Session\Exceptions;

class InvalidSessionStatusTransitionException extends SessionException
{
    public function __construct()
    {
        parent::__construct('Transição de status inválida.', 422);
    }

    public function errorCode(): string
    {
        return 'INVALID_STATUS_TRANSITION';
    }
}
