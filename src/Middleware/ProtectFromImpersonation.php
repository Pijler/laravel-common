<?php

namespace Common\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProtectFromImpersonation
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): RedirectResponse|Response
    {
        if ($request->user()->isImpersonated()) {
            return back();
        }

        return $next($request);
    }
}
