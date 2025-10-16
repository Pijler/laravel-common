<?php

namespace Common\Traits;

use BaconQrCode\Renderer\Color\Rgb;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\Fill;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Support\Str;
use Common\Support\TwoFactor\RecoveryCode;
use Common\Support\TwoFactor\TwoFactorAuthentication;

trait HasTwoFactor
{
    /**
     * Determine if the user has enabled two factor authentication.
     */
    public function hasTwoFactor(): bool
    {
        return $this->two_factor_secret && $this->two_factor_confirmed_at;
    }

    /**
     * Get the two factor authentication recovery codes.
     */
    public function recoveryCodes(): array
    {
        $codes = decrypt($this->two_factor_recovery_codes);

        return json_decode($codes, true);
    }

    /**
     * Replace the given recovery code with a new one.
     */
    public function replaceRecoveryCode(string $code): void
    {
        $codes = decrypt($this->two_factor_recovery_codes);

        $newCodes = Str::replace($code, RecoveryCode::generate(), $codes);

        $this->update(['two_factor_recovery_codes' => encrypt($newCodes)]);
    }

    /**
     * Get the two factor authentication QR code SVG.
     */
    public function twoFactorQrCodeSvg(): string
    {
        $colors = Fill::uniformColor(new Rgb(255, 255, 255), new Rgb(45, 55, 72));

        $image = new ImageRenderer(new RendererStyle(160, 0, null, null, $colors), new SvgImageBackEnd);

        $svg = (new Writer($image))->writeString($this->twoFactorQrCodeUrl());

        return trim(substr($svg, strpos($svg, "\n") + 1));
    }

    /**
     * Get the two factor secret QR code URL.
     */
    public function twoFactorQrCodeUrl(): string
    {
        $secret = decrypt($this->two_factor_secret);

        return app(TwoFactorAuthentication::class)->qrCodeUrl(config('app.name'), $this->email, $secret);
    }
}
