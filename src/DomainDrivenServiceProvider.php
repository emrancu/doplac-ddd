<?php

namespace ZupiterDoplac\Domain;

use ZupiterDoplac\Domain\Supports\DomainSupport;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class DomainDrivenServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $support = DomainSupport::init();

        /**
         * set configuration file support from all domain with domain name's snake case prefix. like config('email_marketing.services')
         * and also set language file support from all domains with domain name's snake case namespace. Like: trans('data_store::campaign')
         */
        if (!app()->configurationIsCached()) {
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

      //  $this->loadRoutesFrom(__DIR__.'/routes/web.php');

        foreach ($support->getDomains() as $domain) {
            if ($domain['title'] === 'app') {
                continue;
            }

            if (is_dir($domain['real_path'].'/Providers')) {
                foreach (File::allFiles($domain['real_path'].'/Providers') as $file) {
                    $fileName = explode('.', $file->getFilename())[0];
                    app()->register('\\'.$domain['namespace'].'Providers\\'.$fileName);
                }
            }
        }

        if ($this->app->runningInConsole()) {
            $comands = [];

            foreach ($support->getDomains() as $domain) {
                if ($domain['title'] === 'app') {
                    continue;
                }

                if (is_dir($domain['real_path'].'/Console/Commands')) {
                    if (is_file($domain['real_path'].'/Console/Kernel.php')) {
                        app()->make('\\'.$domain['namespace'].'Console\\Kernel');
                    }

                    foreach (File::allFiles($domain['real_path'].'/Console/Commands') as $file) {
                        $fileName = explode('.', $file->getFilename())[0];
                        $comands[] = '\\'.$domain['namespace'].'Console\\Commands\\'.$fileName;
                    }
                }
            }

            $this->commands([
                \ZupiterDoplac\Domain\Commands\DomainMigration::class,
                \ZupiterDoplac\Domain\Commands\DomainSeed::class,
                ...$comands
            ]);
        }
    }

    public function register()
    {

    }

}