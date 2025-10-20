<?php

use Common\Exceptions\Alert\ErrorException;
use Common\Exceptions\Alert\InfoException;
use Common\Exceptions\Alert\WarningException;

test('it should throw an exception if the exception is an alert exception - info', function () {
    alert_throw_exception(InfoException::make('Info exception'));
})->throws(InfoException::class);

test('it should throw an exception if the exception is an alert exception - error', function () {
    alert_throw_exception(ErrorException::make('Error exception'));
})->throws(ErrorException::class);

test('it should throw an exception if the exception is an alert exception - warning', function () {
    alert_throw_exception(WarningException::make('Warning exception'));
})->throws(WarningException::class);

test('it should not throw an exception if the exception is not an alert exception', function () {
    alert_throw_exception(new Exception('Exception'));
})->throwsNoExceptions();

test('it should return true if the exception is an alert exception', function () {
    $result = alert_check_exception(InfoException::make('Info exception'));

    expect($result)->toBeTrue();
});

test('it should return false if the exception is not an alert exception', function () {
    $result = alert_check_exception(new Exception('Exception'));

    expect($result)->toBeFalse();
});
