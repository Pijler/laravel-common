<?php

namespace Common\Traits;

use Common\Observers\ModelCacheObserver;
use Illuminate\Database\Eloquent\Model;

/**
 * Clears cache on model create/update/delete/restore.
 *
 * @mixin Model
 *
 * @method static void whenBooted(callable $callback)
 * @method static void observe(object|string|array $classes)
 */
trait ModelCache
{
    /**
     * Boot the model cache trait.
     */
    public static function bootModelCache(): void
    {
        static::whenBooted(function () {
            static::observe(static::modelCacheObserver());
        });
    }

    /**
     * Resolve which observer should handle cache invalidation.
     */
    protected static function modelCacheObserver(): string
    {
        return ModelCacheObserver::class;
    }
}
