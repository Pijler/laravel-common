<?php

namespace Common\Support\TwoFactor;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class RecoveryCode
{
    /**
     * Generate a new recovery code.
     */
    public static function generate(): string
    {
        return Str::random(10).'-'.Str::random(10);
    }

    /**
     * Generate many recovery codes.
     */
    public static function generateMany(): string
    {
        $codes = Collection::times(8, fn () => self::generate())->all();

        return encrypt(json_encode($codes));
    }
}
