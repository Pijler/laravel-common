<?php

namespace Common\Support;

use Common\Support\Testing\ActionFake;
use ReflectionClass;

abstract class Action
{
    /**
     * The fake instance when actions are being faked.
     */
    protected static ?ActionFake $fake = null;

    /**
     * Whether actions are currently being faked.
     */
    public static function isFake(): bool
    {
        return filled(static::$fake);
    }

    /**
     * Restore the real actions (clear the fake). Call in tearDown/afterEach if needed.
     */
    public static function restore(): void
    {
        static::$fake = null;
    }

    /**
     * Execute the action instance.
     */
    public static function execute(...$args)
    {
        $parameters = static::normalize(static::class, $args);

        if (filled(static::$fake)) {
            return static::$fake->execute(static::class, $parameters);
        }

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
     * Fake the return of handle() for the given actions in tests.
     * Each callback receives the normalized constructor parameters; its return (or throw) replaces handle().
     * Actions not in the map run for real.
     */
    public static function fake(array $actions = []): ActionFake
    {
        return tap(new ActionFake(app(), $actions), function (ActionFake $fake) {
            static::$fake = $fake;
        });
    }

    /**
     * Run the given callable with actions faked, then restore. Useful to scope fakes to a single test block.
     */
    public static function fakeFor(callable $callable, array $actions = []): mixed
    {
        $previous = static::$fake;

        static::fake($actions);

        try {
            return $callable();
        } finally {
            static::$fake = $previous;
        }
    }

    /**
     * Run the given callable without the fake active (so actions run for real). Used internally by ActionFake.
     */
    public static function runWithoutFake(callable $callable): mixed
    {
        $previous = static::$fake;

        static::$fake = null;

        try {
            return $callable();
        } finally {
            static::$fake = $previous;
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
