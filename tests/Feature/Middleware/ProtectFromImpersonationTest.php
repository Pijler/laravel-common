<?php

use Workbench\App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create([
        'email' => 'user@example.com',
    ]);

    $this->admin = User::factory()->create([
        'email' => 'admin@example.com',
    ]);
});

test('it should block the access if protected from impersonation', function () {
    $this->actingAs($this->admin);

    $this->user->impersonate();

    $response = $this->from('/')->get('/personal');

    $response->assertFound()->assertRedirect('/');
});

test('it should allow the access if not protected from impersonation', function () {
    $this->actingAs($this->user);

    $response = $this->get('/personal');

    $response->assertOk();
});
