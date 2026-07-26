<?php

declare(strict_types=1);

namespace ShipWell\CornerstoneSupport\Console\Commands;

use Composer\InstalledVersions;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use ShipWell\CornerstoneSupport\Console\Commands\Concerns\ValidatesGeneratorPaths;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'pest:test')]
class TestMakeCommand extends Command
{
    use ValidatesGeneratorPaths;

    protected $signature = 'pest:test
        {name : The name of the test}
        {--unit : Create a unit test}
        {--browser : Create a Pest browser test}
        {--test-directory=tests : The test directory relative to the application root}
        {--force : Overwrite an existing test}';

    protected $description = 'Create a new Pest test';

    public function handle(Filesystem $files): int
    {
        if ( ! function_exists('Pest\\testDirectory')) {
            $this->components->error('Pest is not installed. Install development dependencies before generating tests.');

            return self::FAILURE;
        }

        if ($this->option('unit') && $this->option('browser')) {
            $this->components->error('A test cannot be both unit and browser.');

            return self::INVALID;
        }

        if ($this->option('browser') && ! $this->browserPluginInstalled()) {
            $this->components->error('Pest browser support is not installed. Install pestphp/pest-plugin-browser before generating browser tests.');

            return self::FAILURE;
        }

        $nameInput = $this->argument('name');
        $testDirectoryInput = $this->option('test-directory');

        $name = $this->safeRelativePath($nameInput, 'test name');
        $testDirectory = $this->safeRelativePath($testDirectoryInput, 'test directory');

        if ($name === null || $testDirectory === null) {
            return self::INVALID;
        }

        $name = Str::of($name)->replaceEnd('.php', '')->toString();

        if ($name === '') {
            $this->components->error('The test name must identify a file.');

            return self::INVALID;
        }

        $directory = $this->option('browser') ? 'Browser' : ($this->option('unit') ? 'Unit' : 'Feature');
        $relativePath = $testDirectory . '/' . $directory . '/' . Str::ucfirst($name) . '.php';
        $target = $this->laravel->basePath($relativePath);

        if ( ! $this->pathIsWithinApplication($target)) {
            $this->components->error('The generated test path must remain within the application.');

            return self::INVALID;
        }

        if ($files->exists($target) && ! $this->option('force')) {
            $this->components->error("Test [{$relativePath}] already exists.");

            return self::FAILURE;
        }

        $stub = $this->option('browser') ? 'pest.browser.stub' : ($this->option('unit') ? 'pest.unit.stub' : 'pest.stub');
        $files->ensureDirectoryExists(dirname($target));
        $files->put($target, $files->get($this->laravel->basePath('stubs/' . $stub)));

        $this->components->info("Test [{$relativePath}] created successfully.");

        return self::SUCCESS;
    }

    protected function browserPluginInstalled(): bool
    {
        return InstalledVersions::isInstalled('pestphp/pest-plugin-browser');
    }
}
