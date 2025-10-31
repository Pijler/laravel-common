<?php

namespace Common\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

class FileDecryptCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'file:decrypt
                    {--key= : The encryption key}
                    {--cipher= : The encryption cipher}
                    {--path= : Path to write the decrypted file}
                    {--filename= : Filename of the decrypted file}
                    {--force : Overwrite the existing file}';

    /**
     * The console command description.
     */
    protected $description = 'Decrypt a file';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $filename = $this->option('filename');

        $key = $this->option('key') ?: env('LARAVEL_ENV_ENCRYPTION_KEY');

        if (! $filename && $this->input->isInteractive()) {
            $filename = text('What is the filename to decrypt?');
        }

        if (! $filename) {
            $this->fail('A filename is required.');
        }

        if (! $key && $this->input->isInteractive()) {
            $key = password('What is the decryption key?');
        }

        if (! $key) {
            $this->fail('A decryption key is required.');
        }

        $key = $this->parseKey($key);

        $cipher = $this->option('cipher') ?: 'AES-256-CBC';

        $encryptedFile = Str::finish($this->option('path') ?: base_path(), '/').$filename;

        $mainFile = Str::remove('.encrypted', $encryptedFile);

        if (! Str::endsWith($encryptedFile, '.encrypted')) {
            $this->fail('Invalid filename.');
        }

        if (! File::exists($encryptedFile)) {
            $this->fail('Encrypted file not found.');
        }

        if (File::exists($mainFile) && ! $this->option('force')) {
            $this->fail('File already exists.');
        }

        rescue(function () use ($key, $cipher, $encryptedFile, $mainFile) {
            $encrypter = new Encrypter($key, $cipher);

            File::put($mainFile, $encrypter->decrypt(File::get($encryptedFile)));
        }, function (Exception $e) {
            $this->fail($e->getMessage());
        });

        $this->components->info('File successfully decrypted.');

        $this->components->twoColumnDetail('Decrypted file', $mainFile);

        $this->newLine();

        return self::SUCCESS;
    }

    /**
     * Parse the key.
     */
    protected function parseKey(string $key): string
    {
        if (Str::startsWith($key, $prefix = 'base64:')) {
            $key = base64_decode(Str::after($key, $prefix));
        }

        return $key;
    }
}
