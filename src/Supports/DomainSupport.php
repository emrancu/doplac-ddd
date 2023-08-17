<?php

declare(strict_types=1);

namespace ZupiterDoplac\Domain\Supports;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class DomainSupport
{
    private static DomainSupport|null $instance = null;
    public bool $withoutCache = false;
    private array $domains = [];
    private array $seeders = [];
    private array $factories = [];

    public function __construct()
    {
        $this->generate();
    }

    protected function generate(): void
    {
        if ($this->withoutCache || !app()->isProduction()) {
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

            $i = 1;
            foreach ($autoload as $namespace => $path) {
                if (!is_dir(base_path($path))) {
                    continue;
                }

                if ($namespace == 'App\\') {
                    $title = 'app';
                } else {
                    $pattern = '/domains\/([^\/]+)\//';
                    preg_match($pattern, $path, $matches);
                    $title = trim($matches[1] ?? $namespace, '\\');
                }

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

                $i++;
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

    public static function init($withoutCache = false): DomainSupport
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        if ($withoutCache) {
            self::$instance->withoutCache = $withoutCache;
            self::$instance->generate();
        }

        return self::$instance;
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
