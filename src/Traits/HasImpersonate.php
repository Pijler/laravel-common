<?php

namespace Common\Traits;

use Illuminate\Support\Facades\Session;

trait HasImpersonate
{
    /**
     * Determine if the user can impersonate another user.
     */
    public function canImpersonate(): bool
    {
        return $this->can('canImpersonate');
    }

    /**
     * Determine if the user can be impersonated.
     */
    public function canBeImpersonated(): bool
    {
        return $this->can('canBeImpersonated');
    }

    /**
     * Impersonate the user.
     */
    public function impersonate(): void
    {
        if ($this->canBeImpersonated()) {
            Session::put('session::user::impersonate', $this->id);
        }
    }

    /**
     * Determine if the user is impersonated.
     */
    public function isImpersonated(): bool
    {
        $id = Session::get('session::user::impersonate');

        return $id && $this->id === $id;
    }

    /**
     * Stop impersonating the user.
     */
    public function stopImpersonated(): void
    {
        if ($this->isImpersonated()) {
            Session::forget('session::super::user');

            Session::forget('session::user::impersonate');
        }
    }
}
