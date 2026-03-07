<?php

namespace Common\Traits;

trait ActionCache
{
    /**
     * The cache of the action.
     */
    private static mixed $cache = null;

    /**
     * Reset the cache of the action.
     */
    public static function resetCache(): void
    {
        self::$cache = null;
    }

    /**
     * Get the cache of the action.
     */
    public static function getCache(): mixed
    {
        return self::$cache;
    }
}
