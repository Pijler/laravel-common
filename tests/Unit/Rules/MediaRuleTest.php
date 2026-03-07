<?php

use Common\Rules\MediaRule;

beforeEach(function () {
    $this->failMessage = null;

    $this->rule = new MediaRule();

    $this->fail = function (string $message) {
        $this->failMessage = $message;
    };
});

test('it should fail validation when value is missing required keys', function () {
    $this->rule->validate('media', [], $this->fail);

    expect($this->failMessage)->not->toBeNull();
});

test('it should fail validation when value is missing id', function () {
    $this->rule->validate('media', [
        'size' => 1024,
        'name' => 'file.jpg',
        'url' => 'https://example.com/file.jpg',
    ], $this->fail);

    expect($this->failMessage)->not->toBeNull();
});

test('it should fail validation when value is missing name', function () {
    $this->rule->validate('media', [
        'id' => 1,
        'size' => 1024,
        'url' => 'https://example.com/file.jpg',
    ], $this->fail);

    expect($this->failMessage)->not->toBeNull();
});

test('it should fail validation when value is missing size', function () {
    $this->rule->validate('media', [
        'id' => 1,
        'name' => 'file.jpg',
        'url' => 'https://example.com/file.jpg',
    ], $this->fail);

    expect($this->failMessage)->not->toBeNull();
});

test('it should fail validation when value is missing url', function () {
    $this->rule->validate('media', [
        'id' => 1,
        'size' => 1024,
        'name' => 'file.jpg',
    ], $this->fail);

    expect($this->failMessage)->not->toBeNull();
});

test('it should pass validation when value has all required keys', function () {
    $this->rule->validate('media', [
        'id' => 1,
        'size' => 1024,
        'name' => 'file.jpg',
        'url' => 'https://example.com/file.jpg',
    ], $this->fail);

    expect($this->failMessage)->toBeNull();
});

test('it should call fail with a string message when validation fails', function () {
    $this->rule->validate('media', [], $this->fail);

    expect($this->failMessage)->toBeString();
});
