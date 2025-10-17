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
    public function render(Response $response, Throwable $exception, Request $request): Response
    {
        if (! $this->checkAlertException($request, $exception)) {
            return $response;
        }

        return $this->redirectWithAlert($exception);
    }

    /**
     * Check if exception is an alert exception.
     */
    private function checkAlertException(Request $request, Throwable $exception): bool
    {
        return ! config('app.debug')
            && ! $request->wantsJson()
            && alert_check_exception($exception);
    }

    /**
     * Redirect with an alert.
     */
    private function redirectWithAlert(Throwable $exception): RedirectResponse
    {
        $action = match (class_basename($exception)) {
            'InfoException' => Alert::INFO,
            'ErrorException' => Alert::ERROR,
            'WarningException' => Alert::WARNING,
        };

        return back()->message($exception->getMessage(), $action);
    }
}
