<?php

use Common\Support\Action;

beforeEach(function () {
    Action::restore();

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
    expect(function () {
        $this->class::execute();
    })->toThrow(Exception::class);

    expect(function () {
        $this->class::executeIf(true);
    })->toThrow(Exception::class);

    expect(function () {
        $this->class::executeUnless(false);
    })->toThrow(Exception::class);

    expect(function () {
        $this->class::executeIf(false);
    })->not->toThrow(Exception::class);

    expect(function () {
        $this->class::executeUnless(true);
    })->not->toThrow(Exception::class);
});

test('it can fake an action using a callback', function () {
    expect(function () {
        $this->class::execute();
    })->toThrow(Exception::class);

    Action::fake([
        $this->class::class => fn () => 'ok',
    ]);

    expect($this->class::execute())->toBe('ok');
});

test('it can fake an action using a fixed value', function () {
    expect(function () {
        $this->class::execute();
    })->toThrow(Exception::class);

    Action::fake([
        $this->class::class => 'value',
    ]);

    expect($this->class::execute())->toBe('value');
});

test('fakeFor only fakes inside the given callback', function () {
    expect(function () {
        $this->class::execute();
    })->toThrow(Exception::class);

    $result = Action::fakeFor(function () {
        return $this->class::execute();
    }, [
        $this->class::class => fn () => 'inside',
    ]);

    expect($result)->toBe('inside');

    expect(function () {
        $this->class::execute();
    })->toThrow(Exception::class);
});
