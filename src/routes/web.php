<?php


use Doplac\Domain\Supports\DomainSupport;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;



   $support = new DomainSupport();


    foreach ($support->getDomains() as $domain) {
        if ($domain['title'] !== 'app') {
            Route::middleware('web')->prefix(Str::snake($domain['title'], '-'))->group(function () use ($domain) {
                require base_path($domain['path'].'../routes/web.php');
            });

            Route::middleware('api')->prefix('api/'.Str::snake($domain['title'], '-'))->group(function () use ($domain) {
                require base_path($domain['path'].'../routes/api.php');
            });
        }
    }
