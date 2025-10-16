<?php

namespace Common\Support\Migrations;

use Common\Support\Action;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\Finder\SplFileInfo;

class RenameMigrations extends Action
{
    /**
     * Execute the action.
     */
    protected function handle(): void
    {
        $files = File::allFiles(database_path('migrations'));

        collect($files)->each(function (SplFileInfo $file, int $key) {
            $newName = $this->newFilename($file, $key);

            $this->moveFile($file, $newName);
        });
    }

    /**
     * Move the migration file to a new location with a new name.
     */
    private function moveFile(SplFileInfo $file, string $newName): void
    {
        $newPath = Str::replace($file->getFilename(), $newName, $file->getPathname());

        File::move($file->getPathname(), $newPath);
    }

    /**
     * Generate a new filename for the migration file.
     */
    private function newFilename(SplFileInfo $file, int $key): string
    {
        $part = Str::after($file->getFilename(), '_');

        $key = Str::padLeft((string) $key + 1, 6, '0');

        return "{$key}_{$part}";
    }
}
