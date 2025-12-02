<?php

namespace Common\Exceptions;

use Common\Enum\Alert;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class Handler
{
    /**
     * Render an exception into an HTTP response.
     */
    public static function render(Response $response, Throwable $exception, Request $request): Response
    {
        if (! self::checkAlertException($request, $exception)) {
            return $response;
        }

        return self::redirectWithAlert($exception);
    }

    /**
     * Redirect with an alert.
     */
    private static function redirectWithAlert(Throwable $exception): RedirectResponse
    {
        $action = exception_type($exception);

        return back()->message($exception->getMessage(), $action);
    }

    /**
     * Check if exception is an alert exception.
     */
    private static function checkAlertException(Request $request, Throwable $exception): bool
    {
        return ! config('app.debug')
            && ! $request->wantsJson()
            && check_exception($exception);
    }
}
