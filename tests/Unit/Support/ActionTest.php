<?php

use Common\Support\Action;

beforeEach(function () {
    $this->class = new class extends Action
    {
        /**
         * Execute the action instance.
         */
        protected function handle(): void
        {
            throw new Exception;
        }
    };
});

test('it should execute the action instance', function () {
    expect(fn () => $this->class::execute())->toThrow(Exception::class);

    expect(fn () => $this->class::executeIf(true))->toThrow(Exception::class);

    expect(fn () => $this->class::executeUnless(false))->toThrow(Exception::class);

    expect(fn () => $this->class::executeIf(false))->not->toThrow(Exception::class);

    expect(fn () => $this->class::executeUnless(true))->not->toThrow(Exception::class);
});
