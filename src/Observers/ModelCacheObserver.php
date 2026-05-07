<?php

namespace Common\Observers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

class ModelCacheObserver
{
    /**
     * Delay observer handlers until the active transaction commits.
     */
    public bool $afterCommit = true;

    /**
     * Clear model cache after the model is created.
     */
    public function created(Model $model): void
    {
        $this->deleteCache($model);
    }

    /**
     * Clear model cache after the model is updated.
     */
    public function updated(Model $model): void
    {
        $this->deleteCache($model);
    }

    /**
     * Clear model cache after the model is deleted.
     */
    public function deleted(Model $model): void
    {
        $this->deleteCache($model);
    }

    /**
     * Clear model cache after the model is restored.
     */
    public function restored(Model $model): void
    {
        $this->deleteCache($model);
    }

    /**
     * Clear model cache after the model is force deleted.
     */
    public function forceDeleted(Model $model): void
    {
        $this->deleteCache($model);
    }

    /**
     * Remove all cache keys provided by the model cache key resolver.
     */
    protected function deleteCache(Model $model): void
    {
        $modelClass = get_class($model);

        if (method_exists($modelClass, 'cacheKey')) {
            $keys = $modelClass::cacheKey($model);

            Cache::deleteMultiple(Arr::wrap($keys));
        }
    }
}
