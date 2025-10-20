<?php

use Common\Support\TwoFactor\RecoveryCode;

test('it should generate a new recovery code', function () {
    $recoveryCode = RecoveryCode::generate();

    expect($recoveryCode)->toHaveLength(21);
});

test('it should generate many recovery codes', function () {
    $recoveryCodes = RecoveryCode::generateMany();

    expect(json_decode(decrypt($recoveryCodes)))->toHaveLength(8);
});
