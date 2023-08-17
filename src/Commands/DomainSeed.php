<?php declare(strict_types=1);

namespace ZupiterDoplac\Domain\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class DomainSeed extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'domain:seed {--class= : The class name of the seeder}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed data for all domains';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $class = $this->option('class');

        if ($class) {
            $this->seedSpecificClass($class);
        } else {
            $this->seedAllClasses();
        }

        $this->info('---- All domain\'s seeders completed ----');
        $this->runJob();

        return 0;
    }


    /**
     * Get the list of available seeder classes.
     *
     * @return array
     */
    protected function getSeederClasses(): array
    {
        $composerData = json_decode(file_get_contents(base_path('composer.json')), true);
        $autoload = $composerData['autoload']['psr-4'] ?? [];

        $seederClasses = [];
        foreach ($autoload as $namespace => $path) {

            if(! Str::contains($path, 'app/')) {

                $seederDir =  base_path($path);

                if (is_dir($seederDir)) {

                    $files = File::allFiles($seederDir);

                    foreach ($files as $file) {

                        $className = explode('.', $file->getFileName())[0] ?? '';

                        $seederClasses[$className] =   str_replace('\\\\', '\\', $namespace.'\\'.$className);

                    }
                }
            }
        }

        return $seederClasses;
    }

    /**
    * Seed data for a specific seeder class.
    *
    * @param  string  $class
    * @return void
    */
    protected function seedSpecificClass(string $class)
    {

        $seederFile = $this->findSeederFile($class);

        if (! $seederFile) {
            $this->error("Seeder file '$class' not found.");

            return;
        }

        $seederClassPath = '';
        $seederClasses = $this->getSeederClasses();

        foreach ($seederClasses as $className => $classPath) {

            if($className == $class) {
                $seederClassPath =  $classPath;

                break;
            }
        }

        $this->call('db:seed', ['--class' =>  $seederClassPath]);
        $this->info("Seeded: $class");
    }
    /**
     * Seed data for all seeder classes.
     *
     * @return void
     */
    protected function seedAllClasses()
    {
        $seederClasses = $this->getSeederClasses();

        foreach ($seederClasses as $class) {
            $this->call('db:seed', ['--class' => $class]);
            $this->info("Seeded: $class");
        }
    }

    /**
     * Find the seeder file path for the given class.
     *
     * @param  string  $class
     * @return string|null
     */
    protected function findSeederFile(string $class): ?string
    {
        $domains = File::directories(base_path('domain'));

        foreach ($domains as $directory) {
            $seederFiles = File::allFiles($directory.'/database/seeders');

            foreach ($seederFiles as $file) {
                $className = $this->getClassName($file->getPathname());

                if ($className === $class) {
                    return $file->getPathname();
                }
            }
        }

        return null;
    }

    /**
     * Get the class name from the given file path.
     *
     * @param  string  $path
     * @return string
     */
    protected function getClassName(string $path): string
    {
        $filename = pathinfo($path, PATHINFO_FILENAME);

        return str_replace('/', '\\', $filename);
    }
}
