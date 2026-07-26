<?php

declare(strict_types=1);

namespace ShipWell\CornerstoneSupport\Console\Commands\Adapters;

use Illuminate\Foundation\Console\MailMakeCommand as LaravelMailMakeCommand;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'make:mail')]
class MailMakeCommand extends LaravelMailMakeCommand
{
    protected function writeMarkdownTemplate(): void
    {
        $this->writeCornerstoneView('markdown.stub', 'Markdown view');
    }

    protected function writeView(): void
    {
        $this->writeCornerstoneView('view.stub', 'View');
    }

    private function writeCornerstoneView(string $stub, string $type): void
    {
        $path = $this->viewPath(str_replace('.', DIRECTORY_SEPARATOR, $this->getView()) . '.blade.php');

        if ($this->files->exists($path)) {
            $this->components->error("{$type} [{$path}] already exists.");

            return;
        }

        $this->files->ensureDirectoryExists(dirname($path));
        $this->files->put($path, $this->files->get($this->laravel->basePath('stubs/' . $stub)));
        $this->components->info("{$type} [{$path}] created successfully.");
    }
}
