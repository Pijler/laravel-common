<?php

namespace Common\Support;

use Common\DTO\ActionData;
use Common\Enum\Alert;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Testing\TestResponse;

class Macros
{
    /**
     * Boot the custom macros.
     */
    public static function boot(): void
    {
        Builder::macro('firstRandom', function () {
            /** @var Builder $this * */
            return $this->inRandomOrder()->first();
        });

        RedirectResponse::macro('info', function (string $value) {
            /** @var RedirectResponse $this * */
            return $this->message($value, Alert::INFO);
        });

        RedirectResponse::macro('error', function (string $value) {
            /** @var RedirectResponse $this * */
            return $this->message($value, Alert::ERROR);
        });

        RedirectResponse::macro('success', function (string $value) {
            /** @var RedirectResponse $this * */
            return $this->message($value, Alert::SUCCESS);
        });

        RedirectResponse::macro('warning', function (string $value) {
            /** @var RedirectResponse $this * */
            return $this->message($value, Alert::WARNING);
        });

        RedirectResponse::macro('message', function (string $text, Alert $color) {
            /** @var RedirectResponse $this * */
            return $this->with('session::alert', [
                'text' => $text,
                'color' => $color->value,
            ]);
        });

        RedirectResponse::macro('action', function (ActionData $action) {
            /** @var RedirectResponse $this * */
            return $this->with('session::action', $action->toArray());
        });

        TestResponse::macro('assertInfoMessage', function (string $text) {
            /** @var TestResponse $this * */
            return $this->assertMessage($text, Alert::INFO);
        });

        TestResponse::macro('assertErrorMessage', function (string $text) {
            /** @var TestResponse $this * */
            return $this->assertMessage($text, Alert::ERROR);
        });

        TestResponse::macro('assertSuccessMessage', function (string $text) {
            /** @var TestResponse $this * */
            return $this->assertMessage($text, Alert::SUCCESS);
        });

        TestResponse::macro('assertWarningMessage', function (string $text) {
            /** @var TestResponse $this * */
            return $this->assertMessage($text, Alert::WARNING);
        });

        TestResponse::macro('assertMessage', function (string $text, Alert $color) {
            /** @var TestResponse $this * */
            return $this->assertSessionHas('session::alert', [
                'text' => $text,
                'color' => $color->value,
            ]);
        });

        TestResponse::macro('assertAction', function (ActionData $action) {
            /** @var TestResponse $this * */
            return $this->assertSessionHas('session::action', $action->toArray());
        });

        if (class_exists(\Inertia\Response::class)) {
            \Inertia\Response::macro('filters', function (array $filters = []) {
                /** @var \Inertia\Response $this * */
                return $this->with('filters', collect($filters)->map(function ($value, $key) {
                    return request()->get($key, $value);
                })->toArray());
            });

            \Inertia\Response::macro('params', function (array $params = []) {
                /** @var \Inertia\Response $this * */
                return $this->with('params', collect($params)->merge([
                    'page' => null,
                    'sort' => null,
                    'limit' => null,
                    'cursor' => null,
                    'search' => null,
                ])->map(function ($value, $key) {
                    return request()->get($key, $value);
                })->toArray());
            });
        }
    }
}
