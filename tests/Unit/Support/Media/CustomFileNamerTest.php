<?php

use Common\Support\Media\CustomFileNamer;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\Conversions\Conversion;

beforeEach(function () {
    $this->fileNamer = new CustomFileNamer;
});

test('it should generates a random string of 40 characters for the original file name', function () {
    $fileName = 'example.jpg';

    $generatedName = $this->fileNamer->originalFileName($fileName);

    expect($generatedName)->toBeString()->and(Str::length($generatedName))->toBe(40);
});

test('it should generates a conversion file name with original name and conversion name', function () {
    $fileName = 'photo.png';

    $conversion = new Conversion('thumb');

    $generatedName = $this->fileNamer->conversionFileName($fileName, $conversion);

    expect($generatedName)->toBe('photo-thumb');
});

test('it should returns the base name for a responsive file name', function () {
    $fileName = 'video.mp4';

    $generatedName = $this->fileNamer->responsiveFileName($fileName);

    expect($generatedName)->toBe('video');
});
