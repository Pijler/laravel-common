<?php

namespace Common\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class HandleUserImpersonate
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->canImpersonate()) {
            $this->impersonate($request);
        }

        return $next($request);
    }

    /**
     * Impersonate the user.
     */
    private function impersonate(Request $request): void
    {
        $admin = $request->user();

        if ($id = $request->session()->get('session::user::impersonate')) {
            Auth::onceUsingId($id);

            Session::put('session::super::user', $admin->id);
        }
    }
}
