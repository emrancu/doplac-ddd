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

                foreach ($domain['config_files'] as $file) {
                    if($file['path'] ?? false){
                        $configName = $file['name'];
                        Config::set("$baseName.$configName", require base_path($file['path']));
                    }
                }
            }
        }

        foreach ($support->getDomains() as $domain) {
            if ($domain['title'] === 'app') {
                continue;
            }

            if (is_dir(base_path($domain['real_path']).'Providers')) {
                foreach ($domain['providers'] as $file) {
                    app()->register($file['path']);
                }
            }
        }

        if ($this->app->runningInConsole()) {
            $commands = [];

            foreach ($support->getDomains() as $domain) {
                if ($domain['title'] === 'app') {
                    continue;
                }

                if (is_dir(base_path($domain['real_path']).'Console/Commands')) {
                    foreach ($domain['commands'] as $file) {
                        $commands[] = $file['path'];
                    }
                }
            }

            $this->commands([
                \ZupiterDoplac\Domain\Commands\DomainClear::class,
                \ZupiterDoplac\Domain\Commands\DomainMigration::class,
                \ZupiterDoplac\Domain\Commands\DomainSeed::class,
                ...$commands
            ]);
        }
    }

    public function register()
    {

    }

}