<?php

namespace Scripts\Console\Commands;

use Common\Support\Migrations\RenameMigrations;
use Illuminate\Console\Command;

class RenameMigrationsCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'common:rename-migrations';

    /**
     * The console command description.
     */
    protected $description = 'Rename database migrations';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        RenameMigrations::execute();

        $this->info('Migration files have been renamed successfully!');

        return self::SUCCESS;
    }
}
