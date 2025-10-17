<?php

namespace Common\Traits;

use Closure;
use Illuminate\Database\Eloquent\Builder;

trait HasBuilder
{
    /**
     * The callback that should be used to create builder.
     */
    public static ?Closure $builderCallback = null;

    /**
     * Set a callback that should be used to generate builder class.
     */
    public static function builderUsing($callback): void
    {
        static::$builderCallback = $callback;
    }

    /**
     * Create a new Eloquent query builder for the model.
     */
    public function newEloquentBuilder($query): Builder
    {
        $name = class_basename($this);

        if (static::$builderCallback) {
            return call_user_func(static::$builderCallback, $query);
        }

        return resolve("Database\\Builders\\{$name}Builder", ['query' => $query]);
    }
}
