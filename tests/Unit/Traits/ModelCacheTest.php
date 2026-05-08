<?php

use Common\Observers\ModelCacheObserver;
use Common\Traits\ModelCache;
use Illuminate\Support\Facades\Cache;
use Workbench\App\Models\User;

beforeEach(function () {
    $this->modelWithCacheKey = new class extends User
    {
        use ModelCache;

        protected $table = 'users';

        public static function cacheKey($model): array
        {
            return ["model-cache:{$model->id}"];
        }
    };

    $this->modelWithoutCacheKey = new class extends User
    {
        use ModelCache;

        protected $table = 'users';
    };

    $this->modelStringCacheKey = new class extends User
    {
        use ModelCache;

        protected $table = 'users';

        public static function cacheKey($model): string
        {
            return "model-cache-str:{$model->id}";
        }
    };
});

test('it should resolve ModelCacheObserver class for observe()', function () {
    $reflection = new ReflectionClass($this->modelWithCacheKey);

    $method = $reflection->getMethod('modelCacheObserver');

    expect($method->invoke(null))->toBe(ModelCacheObserver::class);
});

test('it should clear cache keys on create when cacheKey is defined', function () {
    Cache::spy();

    $model = $this->modelWithCacheKey::query()->create([
        'name' => 'Created',
        'password' => 'secret',
        'email' => 'create-cache@example.com',
    ]);

    Cache::shouldHaveReceived('deleteMultiple')->once()->with(["model-cache:{$model->id}"]);
});

test('it should clear cache keys on update when cacheKey is defined', function () {
    $model = $this->modelWithCacheKey::query()->create([
        'name' => 'Before',
        'password' => 'secret',
        'email' => 'update-cache@example.com',
    ]);

    Cache::spy();

    $model->update(['name' => 'After']);

    Cache::shouldHaveReceived('deleteMultiple')->once()->with(["model-cache:{$model->id}"]);
});

test('it should clear cache keys on delete when cacheKey is defined', function () {
    $model = $this->modelWithCacheKey::query()->create([
        'name' => 'Deleted',
        'password' => 'secret',
        'email' => 'delete-cache@example.com',
    ]);

    Cache::spy();

    $model->delete();

    Cache::shouldHaveReceived('deleteMultiple')->once()->with(["model-cache:{$model->id}"]);
});

test('it should wrap string cacheKey for deleteMultiple', function () {
    Cache::spy();

    $model = $this->modelStringCacheKey::query()->create([
        'name' => 'Str key',
        'password' => 'secret',
        'email' => 'string-key@example.com',
    ]);

    Cache::shouldHaveReceived('deleteMultiple')->once()->with(["model-cache-str:{$model->id}"]);
});

test('it should not touch cache when cacheKey is not defined', function () {
    Cache::spy();

    $model = $this->modelWithoutCacheKey::query()->create([
        'name' => 'No key',
        'password' => 'secret',
        'email' => 'no-cache-key@example.com',
    ]);

    expect($model)->toBeInstanceOf($this->modelWithoutCacheKey::class);

    Cache::shouldNotHaveReceived('deleteMultiple');
});
