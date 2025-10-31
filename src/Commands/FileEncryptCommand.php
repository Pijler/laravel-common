<?php

namespace Common\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

use function Laravel\Prompts\password;
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

class FileEncryptCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'file:encrypt
                    {--key= : The encryption key}
                    {--cipher= : The encryption cipher}
                    {--path= : Path to write the encrypted file}
                    {--filename= : Filename of the encrypted file}
                    {--prune : Delete the original file}
                    {--force : Overwrite the existing encrypted file}';

    /**
     * The console command description.
     */
    protected $description = 'Encrypt a file';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $key = $this->option('key');

        $filename = $this->option('filename');

        $cipher = $this->option('cipher') ?: 'AES-256-CBC';

        if (blank($filename) && $this->input->isInteractive()) {
            $filename = text('What is the filename to encrypt?');
        }

        if (blank($filename)) {
            $this->fail('A filename is required.');
        }

        if (blank($key) && $this->input->isInteractive()) {
            $ask = select(
                label: 'What encryption key would you like to use?',
                options: [
                    'generate' => 'Generate a random encryption key',
                    'ask' => 'Provide an encryption key',
                ],
                default: 'generate'
            );

            if ($ask == 'ask') {
                $key = password('What is the encryption key?');
            }
        }

        $keyPassed = filled($key);

        $mainFile = Str::finish($this->option('path') ?: base_path(), '/').$filename;

        $encryptedFile = $mainFile.'.encrypted';

        if (! $keyPassed) {
            $key = Encrypter::generateKey($cipher);
        }

        if (! File::exists($mainFile)) {
            $this->fail('File not found.');
        }

        if (File::exists($encryptedFile) && ! $this->option('force')) {
            $this->fail('Encrypted file already exists.');
        }

        rescue(function () use ($key, $cipher, $mainFile, $encryptedFile) {
            $encrypter = new Encrypter($this->parseKey($key), $cipher);

            File::put($encryptedFile, $encrypter->encrypt(File::get($mainFile)));
        }, function (Exception $e) {
            $this->fail($e->getMessage());
        });

        if ($this->option('prune')) {
            File::delete($mainFile);
        }

        $this->components->info('File successfully encrypted.');

        $this->components->twoColumnDetail('Key', $keyPassed ? $key : 'base64:'.base64_encode($key));
        $this->components->twoColumnDetail('Cipher', $cipher);
        $this->components->twoColumnDetail('Encrypted file', $encryptedFile);

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
