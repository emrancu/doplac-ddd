<?php
declare(strict_types=1);

namespace Doplac\Domain\Supports;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class DomainSupport
{
    private array $domains = [];
    private array $seeders = [];
    private array $factories = [];

    public function __construct(public readonly bool $withoutCache = false)
    {
        $this->generate();
    }

    protected function generate(): void
    {
        if ($this->withoutCache) {
            Cache::forget('doplac_domain_related_data');
        }

        /** @var array{ factories: array, domains: array, seeders: array } $cachedData */
        $cachedData = Cache::rememberForever('doplac_domain_related_data', function () {
            $composerData = json_decode(file_get_contents(base_path('composer.json')), true);
            $autoload = $composerData['autoload']['psr-4'] ?? [];

            ksort($autoload);
            $seeders = [];
            $domains = [];
            $factories = [];


            foreach ($autoload as $namespace => $path) {
                $title = trim($namespace, '\\');
                $data = [
                    'title' => $title,
                    'namespace' => $namespace,
                    'path' => $path,
                    'real_path' => base_path($path)
                ];

                if (Str::contains($path, 'seeders')) {
                    $seeders[$title] = $data;

                    continue;
                }

                if (Str::contains($path, 'app')) {
                    $domains[$title] = $data;

                    continue;
                }

                if (Str::contains($path, 'factories')) {
                    $factories[$title] = $data;
                }
            }

            return [
                'factories' => $factories,
                'domains' => $domains,
                'seeders' => $seeders,
            ];
        });

        $this->seeders = $cachedData['seeders'];
        $this->factories = $cachedData['factories'];
        $this->domains = $cachedData['domains'];
    }

    public function getOnlyDomains(): array
    {
        return array_map(function ($item) {
            return $item['title'];
        }, $this->domains);
    }

    public function getDomainDetails($domain): array
    {
        return $this->domains[$domain];
    }

    public function getDomains(): array
    {
        return $this->domains;
    }

    public function getSeeders(): array
    {
        return $this->seeders;
    }

    public function getFactories(): array
    {
        return $this->factories;
    }
}
