<?php

use Common\Support\Media\CustomPathGenerator;
use Illuminate\Support\Facades\Config;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

beforeEach(function () {
    $this->pathGenerator = new CustomPathGenerator;
});

test('it should generates the base path with default prefix', function () {
    $media = Media::make(['id' => 123]);

    Config::set('media-library.prefix', '');

    $path = $this->invokeMethod($this->pathGenerator, 'getBasePath', [$media]);

    expect($path)->toBe('testing/123');
});

test('it should generates the base path with custom prefix', function () {
    $media = Media::make(['id' => 456]);

    Config::set('media-library.prefix', 'uploads');

    $path = $this->invokeMethod($this->pathGenerator, 'getBasePath', [$media]);

    expect($path)->toBe('uploads/testing/456');
});

test('it should returns the correct path for the media', function () {
    $media = Media::make(['id' => 789]);

    Config::set('media-library.prefix', '');

    expect($this->pathGenerator->getPath($media))->toBe('testing/789/');
});

test('it should returns the correct path for conversions', function () {
    $media = Media::make(['id' => 321]);

    Config::set('media-library.prefix', '');

    expect($this->pathGenerator->getPathForConversions($media))->toBe('testing/321/conversions/');
});

test('it should returns the correct path for responsive images', function () {
    $media = Media::make(['id' => 654]);

    Config::set('media-library.prefix', '');

    expect($this->pathGenerator->getPathForResponsiveImages($media))->toBe('testing/654/responsive-images/');
});
