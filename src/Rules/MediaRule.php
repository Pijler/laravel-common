<?php

namespace Common\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Arr;

class MediaRule implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! Arr::has($value, ['id', 'name', 'size', 'url'])) {
            $fail(trans('validation.image'));
        }
    }
}
