<?php

use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Facades\File;

beforeEach(function () {
    $this->key = Encrypter::generateKey('AES-256-CBC');

    $this->directory = sys_get_temp_dir().'/laravel-common-'.uniqid();

    File::ensureDirectoryExists($this->directory);
});

afterEach(function () {
    File::deleteDirectory($this->directory);
});

test('it should encrypt the file', function () {
    File::put("{$this->directory}/secret.txt", 'secret');

    $this->artisan('file:encrypt', [
        '--key' => 'base64:'.base64_encode($this->key),
        '--path' => $this->directory,
        '--filename' => 'secret.txt',
    ])->assertSuccessful();

    $encrypter = new Encrypter($this->key, 'AES-256-CBC');

    expect($encrypter->decrypt(File::get("{$this->directory}/secret.txt.encrypted")))->toBe('secret');

    expect(File::exists("{$this->directory}/secret.txt"))->toBeTrue();
});

test('it should delete the original file when pruning', function () {
    File::put("{$this->directory}/secret.txt", 'secret');

    $this->artisan('file:encrypt', [
        '--key' => 'base64:'.base64_encode($this->key),
        '--path' => $this->directory,
        '--filename' => 'secret.txt',
        '--prune' => true,
    ])->assertSuccessful();

    expect(File::exists("{$this->directory}/secret.txt.encrypted"))->toBeTrue();

    expect(File::exists("{$this->directory}/secret.txt"))->toBeFalse();
});

test('it should fail when the file does not exist', function () {
    $this->artisan('file:encrypt', [
        '--key' => 'base64:'.base64_encode($this->key),
        '--path' => $this->directory,
        '--filename' => 'missing.txt',
    ])->assertFailed();

    expect(File::exists("{$this->directory}/missing.txt.encrypted"))->toBeFalse();
});

test('it should not overwrite an existing encrypted file without force', function () {
    File::put("{$this->directory}/secret.txt", 'secret');

    File::put("{$this->directory}/secret.txt.encrypted", 'original');

    $this->artisan('file:encrypt', [
        '--key' => 'base64:'.base64_encode($this->key),
        '--path' => $this->directory,
        '--filename' => 'secret.txt',
    ])->assertFailed();

    expect(File::get("{$this->directory}/secret.txt.encrypted"))->toBe('original');
});

test('it should overwrite an existing encrypted file with force', function () {
    File::put("{$this->directory}/secret.txt", 'secret');

    File::put("{$this->directory}/secret.txt.encrypted", 'original');

    $this->artisan('file:encrypt', [
        '--key' => 'base64:'.base64_encode($this->key),
        '--path' => $this->directory,
        '--filename' => 'secret.txt',
        '--force' => true,
    ])->assertSuccessful();

    $encrypter = new Encrypter($this->key, 'AES-256-CBC');

    expect($encrypter->decrypt(File::get("{$this->directory}/secret.txt.encrypted")))->toBe('secret');
});

test('it should decrypt a file encrypted by the command', function () {
    File::put("{$this->directory}/secret.txt", 'secret');

    $this->artisan('file:encrypt', [
        '--key' => 'base64:'.base64_encode($this->key),
        '--path' => $this->directory,
        '--filename' => 'secret.txt',
        '--prune' => true,
    ])->assertSuccessful();

    $this->artisan('file:decrypt', [
        '--key' => 'base64:'.base64_encode($this->key),
        '--path' => $this->directory,
        '--filename' => 'secret.txt.encrypted',
    ])->assertSuccessful();

    expect(File::get("{$this->directory}/secret.txt"))->toBe('secret');
});
