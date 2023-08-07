<?php

declare(strict_types=1);

namespace Doplac\Domain\Commands;

use Doplac\Domain\Supports\DomainSupport;
use Illuminate\Console\Command;

class DomainMigration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'domain:migrate {--fresh : Drop all tables and re-run all migrations}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run migrations from all domain';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $fresh = $this->option('fresh');

        if ($fresh) {
            $this->call('migrate:fresh');
        } else {
            $this->call('migrate');
        }

        $this->info('Migrations completed for App----');

        $support =  new DomainSupport();
        $domains =  $support->getDomains();
        unset($domains['App']);

        foreach ($domains as $domain) {

            $this->info('Start migration from '.$domain['title']);

            $this->call('migrate', ['--path' => $domain['path'].'../database/migrations']);

            $this->info('Completed migration from '.$domain['title']);
        }

        $this->alert('All domain\'s migration completed.');
    }
}
