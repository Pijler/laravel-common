<?php

use Common\Exceptions\Alert\ErrorException;
use Common\Exceptions\Alert\InfoException;
use Common\Exceptions\Alert\WarningException;

if (! function_exists('alert_throw_exception')) {
    /**
     * Throw if the exception is an alert exception.
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
     * Check if the exception is an alert exception.
     */
    function alert_check_exception(Throwable $exception): bool
    {
        return $exception instanceof InfoException
            || $exception instanceof ErrorException
            || $exception instanceof WarningException;
    }
}
