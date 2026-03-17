<?php

namespace Modules\Session\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

abstract class SessionException extends Exception
{
    /**
 * Provide a machine-readable error code that identifies this exception.
 *
 * @return string The error code that uniquely identifies the exception.
 */
abstract public function errorCode(): string;

    /**
     * Render the exception as a standardized JSON HTTP response.
     *
     * The response payload contains `message` (the exception message) and
     * `error_code` (value returned by {@see errorCode}), and uses the
     * exception code as the HTTP status.
     *
     * @return \Illuminate\Http\JsonResponse The JSON response with `message` and `error_code`.
     */
    public function render(): JsonResponse
    {
        return response()->json([
            'message' => $this->getMessage(),
            'error_code' => $this->errorCode(),
        ], $this->getCode());
    }
}
