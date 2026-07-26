<?php

declare(strict_types=1);

namespace ShipWell\CornerstoneSupport\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use ShipWell\CornerstoneSupport\Console\Commands\Concerns\ValidatesGeneratorPaths;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'pest:dataset')]
class DatasetMakeCommand extends Command
{
    use ValidatesGeneratorPaths;

    protected $signature = 'pest:dataset {name : The dataset name} {--test-directory=tests : The test directory} {--force : Overwrite an existing dataset}';

    protected $description = 'Create a new Pest dataset';

    public function handle(Filesystem $files): int
    {
        if ( ! function_exists('Pest\\testDirectory')) {
            $this->components->error('Pest is not installed. Install development dependencies before generating datasets.');

            return self::FAILURE;
        }

        $nameInput = $this->argument('name');
        $testDirectoryInput = $this->option('test-directory');

        $name = $this->safeRelativePath($nameInput, 'dataset name');
        $testDirectory = $this->safeRelativePath($testDirectoryInput, 'test directory');

        if ($name === null || $testDirectory === null) {
            return self::INVALID;
        }

        $name = Str::of($name)->replaceEnd('.php', '')->toString();

        if ($name === '') {
            $this->components->error('The dataset name must identify a file.');

            return self::INVALID;
        }

        $relativePath = $testDirectory . '/Datasets/' . Str::ucfirst($name) . '.php';
        $target = $this->laravel->basePath($relativePath);

        if ( ! $this->pathIsWithinApplication($target)) {
            $this->components->error('The generated dataset path must remain within the application.');

            return self::INVALID;
        }

        if ($files->exists($target) && ! $this->option('force')) {
            $this->components->error("Dataset [{$relativePath}] already exists.");

            return self::FAILURE;
        }

        $contents = str_replace('{{ name }}', Str::lower($name), $files->get($this->laravel->basePath('stubs/pest.dataset.stub')));
        $files->ensureDirectoryExists(dirname($target));
        $files->put($target, $contents);

        $this->components->info("Dataset [{$relativePath}] created successfully.");

        return self::SUCCESS;
    }
}
