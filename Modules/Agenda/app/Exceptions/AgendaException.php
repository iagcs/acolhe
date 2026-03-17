<?php

namespace Modules\Agenda\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

abstract class AgendaException extends Exception
{
    /**
 * Provide a short machine-readable error code that uniquely identifies the specific agenda error.
 *
 * @return string A string error code identifying the concrete exception.
 */
abstract public function errorCode(): string;

    /**
     * Builds a JSON HTTP response containing the exception message and error code.
     *
     * The response payload includes:
     * - `message`: the exception message
     * - `error_code`: the string provided by {@see errorCode()}
     *
     * The HTTP status code for the response is taken from the exception's code.
     *
     * @return JsonResponse JSON response with keys `message` and `error_code`; HTTP status set from the exception code.
     */
    public function render(): JsonResponse
    {
        return response()->json([
            'message' => $this->getMessage(),
            'error_code' => $this->errorCode(),
        ], $this->getCode());
    }
}
