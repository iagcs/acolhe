<?php

namespace Modules\Agenda\Exceptions;

class CalendarTokenNotFoundException extends AgendaException
{
    /**
     * Initialize the exception with a default calendar token not found message and HTTP status 404.
     */
    public function __construct()
    {
        parent::__construct('Token de calendário não encontrado.', 404);
    }

    /**
     * Standardized error code for the calendar token not found condition.
     *
     * @return string The error code 'CALENDAR_TOKEN_NOT_FOUND'.
     */
    public function errorCode(): string
    {
        return 'CALENDAR_TOKEN_NOT_FOUND';
    }
}
