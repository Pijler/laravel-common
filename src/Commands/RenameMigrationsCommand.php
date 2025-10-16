<?php

namespace Scripts\Console\Commands;

use Illuminate\Console\Command;
use Common\Support\Migrations\RenameMigrations;

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
