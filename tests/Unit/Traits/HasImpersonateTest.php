<?php

use Illuminate\Support\Facades\Session;
use Workbench\App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create([
        'email' => 'user@example.com',
    ]);

    $this->admin = User::factory()->create([
        'email' => 'admin@example.com',
    ]);
});

test('it should check if user can impersonate another user', function () {
    expect($this->user->canImpersonate())->toBeFalse();

    expect($this->admin->canImpersonate())->toBeTrue();
});

test('it should check if user can be impersonated', function () {
    expect($this->user->canBeImpersonated())->toBeTrue();

    expect($this->admin->canBeImpersonated())->toBeFalse();
});

test('it should impersonate the user', function () {
    $this->admin->impersonate();

    expect(Session::get('session::user::impersonate'))->toBeNull();

    $this->user->impersonate();

    expect(Session::get('session::user::impersonate'))->toBe($this->user->id);
});

test('it should check if user is impersonated', function () {
    $this->admin->impersonate();

    expect($this->admin->isImpersonated())->toBeFalse();

    $this->user->impersonate();

    expect($this->user->isImpersonated())->toBeTrue();
});

test('it should stop impersonating the user', function () {
    $this->user->impersonate();

    expect(Session::get('session::user::impersonate'))->toBe($this->user->id);

    $this->admin->stopImpersonated();

    expect(Session::get('session::user::impersonate'))->toBe($this->user->id);

    $this->user->stopImpersonated();

    expect(Session::get('session::user::impersonate'))->toBeNull();
});
