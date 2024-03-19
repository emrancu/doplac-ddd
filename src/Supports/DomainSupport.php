<?php

declare(strict_types=1);

namespace ZupiterDoplac\Domain\Supports;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
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
        $this->cacheData();
    }

    private function cacheData()
    {
        $storagePath = storage_path('app/domain-data.php');


        if (!file_exists($storagePath)) {
            File::makeDirectory(storage_path('app'));

            $content = $this->data();

            $returnStatement = $this->arrayToString($content, 1);

            $contentFinal = "<?php\n\n";
            $contentFinal .= "return [\n".$returnStatement."];\n";

            $this->seeders = $content['seeders'];
            $this->factories = $content['factories'];
            $this->domains = $content['domains'];

            if (file_put_contents($storagePath, $contentFinal) !== false) {
            }

            return;
        }

        $content = include $storagePath;
        $this->seeders = $content['seeders'];
        $this->factories = $content['factories'];
        $this->domains = $content['domains'];
    }

    protected function data(): array
    {
        /** @var array{ factories: array, domains: array, seeders: array } $cachedData */

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
                'real_path' => base_path($path),
                'config_files' => [],
                'providers' => [],
                'commands' => [],
            ];

            if (Str::contains($path, 'seeders')) {
                $seeders[$title] = $data;

                continue;
            }

            if (Str::contains($path, 'app')) {
                $domains[$title] = $data;

                if ($title !== 'app') {
                    $files = File::allFiles($data['real_path'].'../config');

                    foreach ($files as $file) {
                        $configName = explode('.', $file->getFilename())[0] ?? null;
                        $domains[$title]['config_files'][] = [
                            'path' => $file->getRealPath(),
                            'name' => $configName
                        ];
                    }


                    if (is_dir($data['real_path'].'/Providers')) {
                        foreach (File::allFiles($data['real_path'].'/Providers') as $file) {
                            $fileName = explode('.', $file->getFilename())[0];

                            $domains[$title]['providers'][] = [
                                'path' => '\\'.$data['namespace'].'Providers\\'.$fileName,
                            ];
                        }
                    }


                    if (is_dir($data['real_path'].'/Console/Commands')) {
                        foreach (File::allFiles($data['real_path'].'/Console/Commands') as $file) {
                            $fileName = explode('.', $file->getFilename())[0];
                            $domains[$title]['commands'][] = [
                                'path' => '\\'.$data['namespace'].'Console\\Commands\\'.$fileName,
                            ];
                        }
                    }
                }

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
    }

    private function arrayToString($array, $indent = 0)
    {
        $output = '';

        foreach ($array as $key => $value) {
            $output .= str_repeat(' ', $indent * 4)."'".$key."' => ";

            if (is_array($value)) {
                $output .= "[\n".$this->arrayToString($value, $indent + 1).str_repeat(' ', $indent * 4)."],\n";
            } else {
                $output .= "'".addslashes($value)."',\n";
            }
        }

        return $output;
    }

    public static function clearData()
    {
        $storagePath = storage_path('app/domain-data.php');

        if (file_exists($storagePath)) {
            unlink($storagePath);
        }
    }

    public static function init($withoutCache = false): DomainSupport
    {
        if (self::$instance === null) {
            self::$instance = new self();
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
