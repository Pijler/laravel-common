<?php

namespace Common\Support\Testing;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Arr;
use ReflectionMethod;

class ActionFake
{
    /**
     * Create a new action fake instance.
     */
    public function __construct(
        protected Application $app,
        protected array $callbacks = [],
    ) {}

    /**
     * Run the action: use fake value/callback if set, otherwise run the real action.
     */
    public function execute(string $action, array $parameters): mixed
    {
        if (Arr::exists($this->callbacks, $action)) {
            $value = Arr::get($this->callbacks, $action);

            return value($value, $parameters);
        }

        return $this->runReal($action, $parameters);
    }

    /**
     * Execute the real action without going through Action::execute (so we don't re-enter the fake).
     * The fake stays active so any other action called from within this one is still intercepted.
     */
    protected function runReal(string $action, array $parameters): mixed
    {
        $instance = resolve($action, $parameters);

        $method = new ReflectionMethod($instance, 'handle');

        return $method->invoke($instance);
    }
}
