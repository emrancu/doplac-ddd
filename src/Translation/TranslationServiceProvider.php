<?php

namespace Illuminate\Translation;

use Doplac\Domain\Supports\DomainSupport;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class TranslationServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerLoader();

        $this->app->singleton('translator', function ($app) {
            $loader = $app['translation.loader'];

            // When registering the translator component, we'll need to set the default
            // locale as well as the fallback locale. So, we'll grab the application
            // configuration so we can easily get both of these values from there.
            $locale = $app->getLocale();

            $trans = new Translator($loader, $locale);

            $trans->setFallback($app->getFallbackLocale());

            return $trans;
        });
    }

    /**
     * Register the translation line loader.
     *
     * @return void
     */
    protected function registerLoader()
    {
        $this->app->singleton('translation.loader', function ($app) {

            $support = new DomainSupport();
            $domainLangDirectories = [__DIR__.'/lang', $app['path.lang']];
            foreach ($support->getDomains() as $domain) {
                if ($domain['title'] === 'App') {
                    continue;
                }

                $langDir = $domain['real_path'].'../lang';
                if(is_dir($langDir)) {
                    $domainLangDirectories[] = $langDir;
                }

            }
            
            return new FileLoader($app['files'], $domainLangDirectories);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['translator', 'translation.loader'];
    }
}
