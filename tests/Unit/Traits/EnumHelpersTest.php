<?php

use Common\Enum\Alert;

test('it should get enum translation', function () {
    expect(Alert::INFO->trans())->toBe('enum.alert.info');

    expect(Alert::ERROR->trans())->toBe('enum.alert.error');

    expect(Alert::SUCCESS->trans())->toBe('enum.alert.success');

    expect(Alert::WARNING->trans())->toBe('enum.alert.warning');
});

test('it should get enum class slug', function () {
    expect(Alert::slug())->toBe('alert');
});

test('it should check if enum case is not the same as the given one', function () {
    expect(Alert::INFO->isNot('info'))->toBeFalse();
    expect(Alert::INFO->isNot(Alert::INFO))->toBeFalse();

    expect(Alert::INFO->isNot('error'))->toBeTrue();
    expect(Alert::INFO->isNot(Alert::ERROR))->toBeTrue();

    expect(Alert::INFO->isNot('success'))->toBeTrue();
    expect(Alert::INFO->isNot(Alert::SUCCESS))->toBeTrue();

    expect(Alert::INFO->isNot('warning'))->toBeTrue();
    expect(Alert::INFO->isNot(Alert::WARNING))->toBeTrue();
});

test('it should check if enum case is the same as the given one', function () {
    expect(Alert::INFO->is('info'))->toBeTrue();
    expect(Alert::INFO->is(Alert::INFO))->toBeTrue();

    expect(Alert::INFO->is('error'))->toBeFalse();
    expect(Alert::INFO->is(Alert::ERROR))->toBeFalse();

    expect(Alert::INFO->is('success'))->toBeFalse();
    expect(Alert::INFO->is(Alert::SUCCESS))->toBeFalse();

    expect(Alert::INFO->is('warning'))->toBeFalse();
    expect(Alert::INFO->is(Alert::WARNING))->toBeFalse();
});

test('it should check if enum case is not one of the given ones', function () {
    expect(Alert::INFO->notIn(['error', 'success']))->toBeTrue();
    expect(Alert::INFO->notIn([Alert::ERROR, Alert::SUCCESS]))->toBeTrue();

    expect(Alert::INFO->notIn(['info', 'warning']))->toBeFalse();
    expect(Alert::INFO->notIn([Alert::INFO, Alert::WARNING]))->toBeFalse();
});

test('it should check if enum case is one of the given ones', function () {
    expect(Alert::INFO->in(['info', 'error']))->toBeTrue();
    expect(Alert::INFO->in([Alert::INFO, Alert::ERROR]))->toBeTrue();

    expect(Alert::INFO->in(['success', 'warning']))->toBeFalse();
    expect(Alert::INFO->in([Alert::SUCCESS, Alert::WARNING]))->toBeFalse();
});

test('it should get all keys of the enum', function () {
    expect(Alert::keys())->toBe([
        'INFO',
        'ERROR',
        'SUCCESS',
        'WARNING',
    ]);
});

test('it should get all values of the enum', function () {
    expect(Alert::values())->toBe([
        'info',
        'error',
        'success',
        'warning',
    ]);
});

test('it should get random of the enum', function () {
    expect(Alert::random())->toBeIn([
        Alert::INFO,
        Alert::ERROR,
        Alert::SUCCESS,
        Alert::WARNING,
    ]);
});

test('it should get random key of the enum', function () {
    expect(Alert::randomKey())->toBeIn([
        'INFO',
        'ERROR',
        'SUCCESS',
        'WARNING',
    ]);
});

test('it should get random value of the enum', function () {
    expect(Alert::randomValue())->toBeIn([
        'info',
        'error',
        'success',
        'warning',
    ]);
});

test('it should check if the given key exists in the enum', function () {
    expect(Alert::existKey(null))->toBeFalse();
    expect(Alert::existKey('test'))->toBeFalse();

    expect(Alert::existKey('INFO'))->toBeTrue();
    expect(Alert::existKey('ERROR'))->toBeTrue();
    expect(Alert::existKey('SUCCESS'))->toBeTrue();
    expect(Alert::existKey('WARNING'))->toBeTrue();
});

test('it should check if the given value exists in the enum', function () {
    expect(Alert::existValue(null))->toBeFalse();
    expect(Alert::existValue('test'))->toBeFalse();

    expect(Alert::existValue('info'))->toBeTrue();
    expect(Alert::existValue('error'))->toBeTrue();
    expect(Alert::existValue('success'))->toBeTrue();
    expect(Alert::existValue('warning'))->toBeTrue();
});

test('it should get enum case by key', function () {
    expect(Alert::fromKey('test'))->toBeNull();

    expect(Alert::fromKey('INFO'))->toBe(Alert::INFO);
    expect(Alert::fromKey('ERROR'))->toBe(Alert::ERROR);
    expect(Alert::fromKey('SUCCESS'))->toBe(Alert::SUCCESS);
    expect(Alert::fromKey('WARNING'))->toBe(Alert::WARNING);

    expect(Alert::fromKey('TEST', Alert::INFO))->toBe(Alert::INFO);
    expect(Alert::fromKey('TEST', Alert::ERROR))->toBe(Alert::ERROR);
    expect(Alert::fromKey('TEST', Alert::SUCCESS))->toBe(Alert::SUCCESS);
    expect(Alert::fromKey('TEST', Alert::WARNING))->toBe(Alert::WARNING);
});

test('it should get enum case by value', function () {
    expect(Alert::fromValue('test'))->toBeNull();

    expect(Alert::fromValue('info'))->toBe(Alert::INFO);
    expect(Alert::fromValue('error'))->toBe(Alert::ERROR);
    expect(Alert::fromValue('success'))->toBe(Alert::SUCCESS);
    expect(Alert::fromValue('warning'))->toBe(Alert::WARNING);

    expect(Alert::fromValue('test', Alert::INFO))->toBe(Alert::INFO);
    expect(Alert::fromValue('test', Alert::ERROR))->toBe(Alert::ERROR);
    expect(Alert::fromValue('test', Alert::SUCCESS))->toBe(Alert::SUCCESS);
    expect(Alert::fromValue('test', Alert::WARNING))->toBe(Alert::WARNING);
});
