<?php

declare(strict_types=1);

namespace ShipWell\CornerstoneSupport\Console\Commands\Adapters;

use Illuminate\Foundation\Console\ComponentMakeCommand as LaravelComponentMakeCommand;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'make:component')]
class ComponentMakeCommand extends LaravelComponentMakeCommand
{
    protected function writeView(): void
    {
        $path = $this->viewPath(str_replace('.', DIRECTORY_SEPARATOR, $this->getView()) . '.blade.php');

        if ($this->files->exists($path) && ! $this->option('force')) {
            $this->components->error('View already exists.');

            return;
        }

        $this->files->ensureDirectoryExists(dirname($path));
        $this->files->put($path, $this->files->get($this->laravel->basePath('stubs/view.stub')));
        $this->components->info("View [{$path}] created successfully.");
    }
}
