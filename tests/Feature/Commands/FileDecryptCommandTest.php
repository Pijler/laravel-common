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

test('it should decrypt the file', function () {
    $encrypter = new Encrypter($this->key, 'AES-256-CBC');

    File::put("{$this->directory}/secret.txt.encrypted", $encrypter->encrypt('secret'));

    $this->artisan('file:decrypt', [
        '--key' => 'base64:'.base64_encode($this->key),
        '--path' => $this->directory,
        '--filename' => 'secret.txt.encrypted',
    ])->assertSuccessful();

    expect(File::get("{$this->directory}/secret.txt"))->toBe('secret');
});

test('it should only remove the encrypted suffix from the path', function () {
    $encrypter = new Encrypter($this->key, 'AES-256-CBC');

    $path = "{$this->directory}/backups.encrypted";

    File::ensureDirectoryExists($path);

    File::put("{$path}/config.encrypted.json.encrypted", $encrypter->encrypt('secret'));

    $this->artisan('file:decrypt', [
        '--key' => 'base64:'.base64_encode($this->key),
        '--path' => $path,
        '--filename' => 'config.encrypted.json.encrypted',
    ])->assertSuccessful();

    expect(File::get("{$path}/config.encrypted.json"))->toBe('secret');

    expect(File::exists("{$this->directory}/backups"))->toBeFalse();
});

test('it should not overwrite an existing file without force', function () {
    $encrypter = new Encrypter($this->key, 'AES-256-CBC');

    File::put("{$this->directory}/secret.txt", 'original');

    File::put("{$this->directory}/secret.txt.encrypted", $encrypter->encrypt('secret'));

    $this->artisan('file:decrypt', [
        '--key' => 'base64:'.base64_encode($this->key),
        '--path' => $this->directory,
        '--filename' => 'secret.txt.encrypted',
    ])->assertFailed();

    expect(File::get("{$this->directory}/secret.txt"))->toBe('original');
});
