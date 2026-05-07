<?php

namespace Common\Traits;

use Common\Observers\ModelCacheObserver;
use Illuminate\Database\Eloquent\Model;

/**
 * Clears cache on model create/update/delete/restore.
 *
 * @mixin Model
 *
 * @method static bool cacheObserverAfterCommit()
 * @method static void observe(object|string|array $classes)
 */
trait ModelCache
{
    /**
     * Boot the model cache trait.
     */
    public static function bootModelCache(): void
    {
        $observer = app(static::modelCacheObserver());

        if (method_exists(static::class, 'cacheObserverAfterCommit')) {
            $observer->afterCommit = (bool) static::cacheObserverAfterCommit();
        }

        static::observe($observer);
    }

    /**
     * Resolve which observer should handle cache invalidation.
     */
    protected static function modelCacheObserver(): string
    {
        return ModelCacheObserver::class;
    }
}
