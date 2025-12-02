<?php

use Common\Enum\Alert;
use Common\Exceptions\Alert\ErrorException;
use Common\Exceptions\Alert\InfoException;
use Common\Exceptions\Alert\WarningException;
use Illuminate\Validation\ValidationException;

if (! function_exists('alert_throw_exception')) {
    /**
     * Throw if the exception is an alert or validation exception.
     */
    function alert_throw_exception(Throwable $exception): void
    {
        if (alert_check_exception($exception)) {
            throw $exception;
        }
    }
}

if (! function_exists('alert_check_exception')) {
    /**
     * Check if the exception is an alert or validation exception.
     */
    function alert_check_exception(Throwable $exception): bool
    {
        return $exception instanceof InfoException
            || $exception instanceof ErrorException
            || $exception instanceof WarningException
            || $exception instanceof ValidationException;
    }
}

if (! function_exists('alert_exception_type')) {
    /**
     * Get the alert exception type.
     */
    function alert_exception_type(Throwable $exception): Alert
    {
        return match (class_basename($exception)) {
            'InfoException' => Alert::INFO,
            'ErrorException' => Alert::ERROR,
            'WarningException' => Alert::WARNING,
        };
    }
}
