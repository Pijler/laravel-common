<?php

use Common\DTO\ActionData;
use Common\Enum\Alert;
use Common\Rules\MediaRule;
use Illuminate\Http\Request;
use Illuminate\Testing\TestResponse;
use Illuminate\Validation\Rules\File;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Inertia\ResponseFactory;
use Workbench\App\Models\User;

use function Illuminate\Support\enum_value;

function inertiaTestPageProps(InertiaResponse $response): array
{
    $reflection = new ReflectionClass($response);

    $property = $reflection->getProperty('props');

    /** @var array<string, mixed> */
    return $property->getValue($response);
}

function removeTestResponseMacro(string $name): void
{
    $reflection = new ReflectionClass(TestResponse::class);

    $property = $reflection->getProperty('macros');

    /** @var array<string, mixed> $macros */
    $macros = $property->getValue();

    unset($macros[$name]);

    $property->setValue($macros);
}

test('it should return MediaRule from File::media macro', function () {
    expect(File::media())->toBeInstanceOf(MediaRule::class);
});

test('it should prefer request input over defaults for Inertia Response filters macro', function () {
    $request = Request::create('/test', 'GET', [
        'status' => 'from_request',
    ]);

    $this->app->instance('request', $request);

    $response = Inertia::render('Test', [])->filters([
        'status' => 'default',
        'other' => 'fallback',
    ]);

    $props = inertiaTestPageProps($response);

    expect(data_get($props, 'filters'))->toMatchArray([
        'other' => 'fallback',
        'status' => 'from_request',
    ]);
});

test('it should merge pagination keys and use request input for Inertia Response params macro', function () {
    $request = Request::create('/list', 'GET', [
        'page' => '3',
        'search' => 'needle',
    ]);

    $this->app->instance('request', $request);

    $response = Inertia::render('List', [])->params([]);

    $props = inertiaTestPageProps($response);

    expect(data_get($props, 'params'))->toMatchArray([
        'page' => '3',
        'sort' => null,
        'limit' => null,
        'cursor' => null,
        'search' => 'needle',
    ]);
});

test('it should flash alert via Inertia for RedirectResponse message macro', function () {
    $this->mock(ResponseFactory::class, function ($mock) {
        $mock->shouldReceive('flash')->once()->with('alert', [
            'text' => 'Hello',
            'color' => enum_value(Alert::INFO),
        ]);
    });

    $redirect = redirect('/')->message('Hello', Alert::INFO);

    expect($redirect->getSession()->get('session::alert'))->toMatchArray([
        'text' => 'Hello',
        'color' => enum_value(Alert::INFO),
    ]);
});

test('it should flash action via Inertia for RedirectResponse action macro', function () {
    $action = new ActionData(
        url: '/target',
        method: 'post',
        text: 'Continue',
        params: ['a' => 1],
    );

    $payload = $action->toArray();

    $this->mock(ResponseFactory::class, function ($mock) use ($payload) {
        $mock->shouldReceive('flash')->once()->with('action', $payload);
    });

    $redirect = redirect('/')->action($action);

    expect($redirect->getSession()->get('session::action'))->toMatchArray($payload);
});

test('it should call assertInertiaFlash from assertMessage when that macro exists', function () {
    $called = false;

    TestResponse::macro('assertInertiaFlash', function (string $key, array $expected) use (&$called) {
        $called = true;
        expect($key)->toBe('alert');
        expect($expected)->toMatchArray([
            'text' => 'Hi',
            'color' => enum_value(Alert::INFO),
        ]);

        return $this;
    });

    $user = User::firstRandom();

    $response = $this->actingAs($user)->withSession([
        'session::alert' => [
            'text' => 'Hi',
            'color' => enum_value(Alert::INFO),
        ],
    ])->get('/');

    $response->assertMessage('Hi', Alert::INFO);

    expect($called)->toBeTrue();

    removeTestResponseMacro('assertInertiaFlash');
});

test('it should call assertInertiaFlash from assertAction when that macro exists', function () {
    $called = false;

    $action = new ActionData(url: '/go', text: 'Go');

    TestResponse::macro('assertInertiaFlash', function (string $key, array $expected) use (&$called, $action) {
        $called = true;
        expect($key)->toBe('action');
        expect($expected)->toMatchArray($action->toArray());

        return $this;
    });

    $user = User::firstRandom();

    $response = $this->actingAs($user)->withSession([
        'session::action' => $action->toArray(),
    ])->get('/');

    $response->assertAction($action);

    expect($called)->toBeTrue();

    removeTestResponseMacro('assertInertiaFlash');
});
