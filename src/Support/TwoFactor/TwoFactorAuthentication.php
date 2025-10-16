<?php

namespace Common\Support\TwoFactor;

use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Cache;
use PragmaRX\Google2FA\Google2FA;

class TwoFactorAuthentication
{
    /**
     * The Google2FA engine.
     */
    protected Google2FA $engine;

    /**
     * Create a new instance of the TwoFactorAuthentication.
     */
    public function __construct(Google2FA $engine)
    {
        $this->engine = $engine;
    }

    /**
     * Generate a new secret key.
     */
    public function generateSecretKey(): string
    {
        return encrypt($this->engine->generateSecretKey());
    }

    /**
     * Get the QR code URL.
     */
    public function qrCodeUrl(string $companyName, string $companyEmail, string $secret): string
    {
        return $this->engine->getQRCodeUrl($companyName, $companyEmail, $secret);
    }

    /**
     * Verify the two factor code.
     */
    public function verify(User $user, string $code): bool
    {
        $secret = decrypt($user->two_factor_secret);

        $cache = Cache::get($key = "cache.two-factor.code.{$code}");

        $timestamp = $this->engine->verifyKeyNewer($secret, $code, $cache);

        if ($timestamp !== false) {
            if ($timestamp === true) {
                $timestamp = $this->engine->getTimestamp();
            }

            Cache::put($key, $timestamp, ($this->engine->getWindow() ?: 1) * 60);

            return true;
        }

        return false;
    }
}
