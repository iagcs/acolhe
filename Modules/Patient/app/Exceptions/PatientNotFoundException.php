<?php

namespace Modules\Patient\Exceptions;

class PatientNotFoundException extends PatientException
{
    public function __construct()
    {
        parent::__construct('Paciente não encontrado.', 404);
    }

    public function errorCode(): string
    {
        return 'PATIENT_NOT_FOUND';
    }
}
