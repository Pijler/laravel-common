<?php

use Illuminate\Validation\ValidationException;
use Pijler\LaravelCommon\Enum\Alert;
use Pijler\LaravelCommon\Exceptions\Alert\ErrorException;
use Pijler\LaravelCommon\Exceptions\Alert\InfoException;
use Pijler\LaravelCommon\Exceptions\Alert\WarningException;

if (! function_exists('throw_exception')) {
    /**
     * Throw if the exception is an alert or validation exception.
     */
    function throw_exception(Throwable $exception): void
    {
        if (check_exception($exception) || $exception instanceof ValidationException) {
            throw $exception;
        }
    }
}

if (! function_exists('check_exception')) {
    /**
     * Check if the exception is an alert or validation exception.
     */
    function check_exception(Throwable $exception): bool
    {
        return $exception instanceof InfoException
            || $exception instanceof ErrorException
            || $exception instanceof WarningException;
    }
}

if (! function_exists('exception_type')) {
    /**
     * Get the alert exception type.
     */
    function exception_type(Throwable $exception): Alert
    {
        return match (class_basename($exception)) {
            'InfoException' => Alert::INFO,
            'ErrorException' => Alert::ERROR,
            'WarningException' => Alert::WARNING,
            default => Alert::ERROR,
        };
    }
}
