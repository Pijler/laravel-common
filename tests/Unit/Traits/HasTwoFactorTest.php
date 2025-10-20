<?php

use Illuminate\Support\Arr;
use Workbench\App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->twoFactor()->create();
});

test('it should return true if the user has enabled two factor authentication', function () {
    expect($this->user->hasTwoFactor())->toBeTrue();

    $this->user->forceFill([
        'two_factor_secret' => null,
        'two_factor_confirmed_at' => null,
        'two_factor_recovery_codes' => null,
    ])->save();

    $this->user->refresh();

    expect($this->user->hasTwoFactor())->toBeFalse();
});

test('it should return the recovery codes', function () {
    $recoveryCodes = $this->user->recoveryCodes();

    expect($recoveryCodes)->toBeArray();
    expect($recoveryCodes)->toHaveCount(8);

    $recoveryCodes = Arr::shuffle($recoveryCodes);

    $code = array_shift($recoveryCodes);

    $this->user->replaceRecoveryCode($code);

    $newRecoveryCodes = $this->user->recoveryCodes();

    expect($newRecoveryCodes)->toBeArray();
    expect($newRecoveryCodes)->toHaveCount(8);

    expect($newRecoveryCodes)->not->toContain($code);
    expect($newRecoveryCodes)->toContain(...$recoveryCodes);
});

test('it should return the two factor authentication QR code SVG', function () {
    $svg = $this->user->twoFactorQrCodeSvg();

    expect($svg)->toBeString();
    expect($svg)->toContain('<svg', '</svg>');
});

test('it should return the two factor authentication QR code URL', function () {
    $url = $this->user->twoFactorQrCodeUrl();

    $secret = decrypt($this->user->two_factor_secret);

    expect($url)->toBeString();
    expect($url)->toContain($secret);
});
