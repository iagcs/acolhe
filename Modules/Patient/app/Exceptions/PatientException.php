<?php

namespace Modules\Patient\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

abstract class PatientException extends Exception
{
    abstract public function errorCode(): string;

    public function render(): JsonResponse
    {
        return response()->json([
            'message' => $this->getMessage(),
            'error_code' => $this->errorCode(),
        ], $this->getCode());
    }
}
