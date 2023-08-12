<?php

namespace Doplac\Domain\Traits;

use Doplac\Domain\Supports\DomainSupport;

use Illuminate\Support\Str;

use function Laravel\Prompts\select;

trait GeneratorOverride
{

    protected array $domain = [];

    /**
     * Get the root namespace for the class.
     *
     * @return string
     */
    protected function rootNamespace()
    {
        if(! empty($this->domain)) {
            return $this->domain['namespace'];
        }

        $comm = new DomainSupport(true);

        $domain = select(
            label: 'Please select domain!',
            options: $comm->getOnlyDomains(),
            default: 'App'
        );

        $this->domain = $comm->getDomainDetails($domain);

        return $this->domain['namespace'];
    }

    /**
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getPath($name)
    {
        $name = Str::replaceFirst($this->rootNamespace(), '', $name);

        return $this->domain['real_path'].str_replace('\\', '/', $name).'.php';
    }

}