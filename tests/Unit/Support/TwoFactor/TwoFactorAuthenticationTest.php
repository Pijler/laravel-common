<?php

use Common\Support\TwoFactor\TwoFactorAuthentication;
use PragmaRX\Google2FA\Google2FA;
use Workbench\App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->twoFactor()->create();
});

test('it should generates a new secret key', function () {
    $secret = app(TwoFactorAuthentication::class)->generateSecretKey();

    expect($secret)->toBeString();
    expect(decrypt($secret))->toHaveLength(32);
});

test('it should generates a QR code URL', function () {
    $code = app(TwoFactorAuthentication::class)->qrCodeUrl('Company Name', 'Company Email', 'Secret');

    expect($code)->toBeString();
    expect($code)->toContain('Secret');
    expect($code)->toContain('Company%20Name');
    expect($code)->toContain('Company%20Email');
});

test('it should verifies the two factor code', function () {
    $code = app(Google2FA::class)->getCurrentOtp(decrypt($this->user->two_factor_secret));

    $result = app(TwoFactorAuthentication::class)->verify($this->user, $code);
    expect($result)->toBeTrue();

    $result = app(TwoFactorAuthentication::class)->verify($this->user, '123456');
    expect($result)->toBeFalse();

    $result = app(TwoFactorAuthentication::class)->verify($this->user, $code);
    expect($result)->toBeFalse();
});
