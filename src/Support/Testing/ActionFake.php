<?php

namespace Common\Support\Testing;

use Common\Support\Action;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Arr;

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
     * Execute the real action (bypass fake so it doesn't re-enter).
     */
    protected function runReal(string $action, array $parameters): mixed
    {
        return Action::runWithoutFake(fn () => $action::execute(...$parameters));
    }
}
