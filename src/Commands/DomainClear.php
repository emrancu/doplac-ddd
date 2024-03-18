<?php

declare(strict_types=1);

namespace ZupiterDoplac\Domain\Commands;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use ZupiterDoplac\Domain\Supports\DomainSupport;
use Illuminate\Console\Command;

class DomainClear extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'domain:clear';

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
        DomainSupport::clearData();
        
        $this->info('Domain data cleared');
    }
}
