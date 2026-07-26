<?php

declare(strict_types=1);

namespace ShipWell\CornerstoneSupport\Console\Commands\Adapters;

use Illuminate\Foundation\Console\NotificationMakeCommand as LaravelNotificationMakeCommand;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'make:notification')]
class NotificationMakeCommand extends LaravelNotificationMakeCommand
{
    protected function writeMarkdownTemplate(): void
    {
        $markdown = $this->option('markdown');

        if ( ! is_string($markdown)) {
            return;
        }
        $path = $this->viewPath(str_replace('.', DIRECTORY_SEPARATOR, $markdown) . '.blade.php');

        $this->files->ensureDirectoryExists(dirname($path));
        $this->files->put($path, $this->files->get($this->laravel->basePath('stubs/markdown.stub')));
        $this->components->info("Markdown [{$path}] created successfully.");
    }
}
