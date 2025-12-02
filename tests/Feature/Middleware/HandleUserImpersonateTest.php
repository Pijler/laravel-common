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

test('it should impersonate the user', function () {
    $this->actingAs($this->admin);

    $this->user->impersonate();

    $this->get('/')->assertOk();

    expect(Session::get('session::super::user'))->toBe($this->admin->id);

    expect(Session::get('session::user::impersonate'))->toBe($this->user->id);
});

test('it should not impersonate the user', function () {
    $this->actingAs($this->user);

    $this->admin->impersonate();

    $this->get('/')->assertOk();

    expect(Session::get('session::super::user'))->toBeNull();

    expect(Session::get('session::user::impersonate'))->toBeNull();
});
