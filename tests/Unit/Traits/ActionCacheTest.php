<?php

use Common\Traits\ActionCache;

beforeEach(function () {
    $this->class = new class
    {
        use ActionCache;
    };
});

test('it should return null from getCache by default', function () {
    expect($this->class::getCache())->toBeNull();
});

test('it should reset cache to null when calling resetCache', function () {
    $reflection = new ReflectionClass($this->class);

    $property = $reflection->getProperty('cache');

    $property->setValue(null, 'cached-value');

    expect($this->class::getCache())->toBe('cached-value');

    $this->class::resetCache();

    expect($this->class::getCache())->toBeNull();
});

test('it should allow reading cache after it is set internally', function () {
    $reflection = new ReflectionClass($this->class);

    $property = $reflection->getProperty('cache');

    $property->setValue(null, ['key' => 'value']);

    expect($this->class::getCache())->toBe(['key' => 'value']);
});
