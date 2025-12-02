<?php

use Common\Exceptions\Alert\ErrorException;
use Common\Exceptions\Alert\InfoException;
use Common\Exceptions\Alert\WarningException;
use Illuminate\Validation\ValidationException;

test('it should throw an exception if the exception is an alert exception - info', function () {
    throw_exception(InfoException::make('Info exception'));
})->throws(InfoException::class);

test('it should throw an exception if the exception is an alert exception - error', function () {
    throw_exception(ErrorException::make('Error exception'));
})->throws(ErrorException::class);

test('it should throw an exception if the exception is an alert exception - warning', function () {
    throw_exception(WarningException::make('Warning exception'));
})->throws(WarningException::class);

test('it should throw an exception if the exception is a validation exception', function () {
    throw_exception(ValidationException::withMessages(['name' => 'Name is required']));
})->throws(ValidationException::class);

test('it should not throw an exception if the exception is not an alert exception', function () {
    throw_exception(new Exception('Exception'));
})->throwsNoExceptions();

test('it should return true if the exception is an alert exception', function () {
    $result = check_exception(InfoException::make('Info exception'));

    expect($result)->toBeTrue();
});

test('it should return true if the exception is a validation exception', function () {
    $result = check_exception(ValidationException::withMessages(['name' => 'Name is required']));

    expect($result)->toBeTrue();
});

test('it should return false if the exception is not an alert exception', function () {
    $result = check_exception(new Exception('Exception'));

    expect($result)->toBeFalse();
});
