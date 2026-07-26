<?php

declare(strict_types=1);

namespace ShipWell\CornerstoneSupport\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'cornerstone:stubs')]
class CornerstoneStubsCommand extends Command
{
    protected $signature = 'cornerstone:stubs {--force : Overwrite every existing published stub}';

    protected $description = 'Publish the Cornerstone generator stubs';

    public function handle(Filesystem $files): int
    {
        $source = dirname(__DIR__, 3) . '/stubs';
        $destination = $this->laravel->basePath('stubs');

        $files->ensureDirectoryExists($destination);

        foreach ($files->allFiles($source) as $stub) {
            $target = $destination . DIRECTORY_SEPARATOR . $stub->getRelativePathname();

            if ($files->exists($target) && ! $this->option('force')) {
                continue;
            }

            $files->ensureDirectoryExists(dirname($target));
            $files->copy($stub->getPathname(), $target);
        }

        $this->components->info('Cornerstone stubs published successfully.');

        return self::SUCCESS;
    }
}
