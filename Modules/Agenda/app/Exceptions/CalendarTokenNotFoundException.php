<?php

namespace Modules\Agenda\Exceptions;

class CalendarTokenNotFoundException extends AgendaException
{
    public function __construct()
    {
        parent::__construct('Token de calendário não encontrado.', 404);
    }

    public function errorCode(): string
    {
        return 'CALENDAR_TOKEN_NOT_FOUND';
    }
}
