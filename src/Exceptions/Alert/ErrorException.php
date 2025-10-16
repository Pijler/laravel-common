<?php

namespace Common\Exceptions\Alert;

use Exception;

class ErrorException extends Exception
{
    /**
     * Create a new exception instance.
     */
    public static function make(string $message): self
    {
        return new static($message, 500);
    }
}
