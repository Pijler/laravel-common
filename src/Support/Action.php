<?php

namespace Common\Support;

use ReflectionClass;

abstract class Action
{
    /**
     * Execute the action instance.
     */
    public static function execute(...$args)
    {
        $parameters = static::normalize(static::class, $args);

        return resolve(static::class, $parameters)->handle();
    }

    /**
     * Execute the action with the given arguments if the given truth test passes.
     */
    public static function executeIf(bool $boolean, ...$args)
    {
        if ($boolean) {
            return static::execute(...$args);
        }
    }

    /**
     * Execute the action with the given arguments unless the given truth test passes.
     */
    public static function executeUnless(bool $boolean, ...$args)
    {
        if (! $boolean) {
            return static::execute(...$args);
        }
    }

    /**
     * Normalize parameters passed to static executors by mapping positional arguments
     * to the constructor's parameter names. This ensures compatibility with Laravel's
     * container, which expects an associative array keyed by parameter names.
     *
     * Example:
     *  class Foo {
     *      public function __construct($name, $age) {}
     *  }
     *
     *  normalize(Foo::class, ['John', 30])
     *  → ['name' => 'John', 'age' => 30]
     */
    protected static function normalize(string $className, array $args): array
    {
        // If the array already has named keys, assume it's properly keyed and return as-is.
        if (array_is_list($args) === false) {
            return $args;
        }

        $constructor = (new ReflectionClass($className))->getConstructor();

        // If the class has no constructor, return the original arguments unchanged.
        if (is_null($constructor)) {
            return $args;
        }

        $normalized = [];

        // Get all constructor parameter names in order.
        $params = collect($constructor->getParameters())->pluck('name');

        // Map positional arguments to their corresponding parameter names.
        foreach ($params as $index => $param) {
            if (array_key_exists($index, $args)) {
                $normalized[$param] = $args[$index];
            }
        }

        // Return normalized parameters if any mapping occurred, otherwise fallback to original args.
        return filled($normalized) ? $normalized : $args;
    }

    /**
     * Execute the action.
     */
    abstract protected function handle();
}
