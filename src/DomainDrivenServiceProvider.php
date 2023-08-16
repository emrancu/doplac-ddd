<?php

namespace Doplac\Domain;

use Doplac\Domain\Supports\DomainSupport;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class DomainDrivenServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $support = new DomainSupport();

        /**
         * set configuration file support from all domain with domain name's snake case prefix. like config('email_marketing.services')
         * and also set language file support from all domains with domain name's snake case namespace. Like: trans('data_store::campaign')
         */
        if (! app()->configurationIsCached()) {

            foreach ($support->getDomains() as $domain) {
                if ($domain['title'] === 'app') {
                    continue;
                }

                $baseName = Str::snake($domain['title']); // prefix of config

                $files = File::allFiles($domain['real_path'].'../config');

                foreach ($files as $file) {

                    $configName = explode('.', $file->getFilename())[0] ?? null;

                    Config::set("$baseName.$configName", require $file->getRealPath());
                }
            }
        }

        $this->loadRoutesFrom(__DIR__.'/routes/web.php');
    }

    public function register()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                \Doplac\Domain\Commands\DomainMigration::class,
                \Doplac\Domain\Commands\DomainSeed::class,
            ]);
        }

    }

}