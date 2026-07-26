<?php

declare(strict_types=1);

namespace ShipWell\CornerstoneSupport\Console\Commands;

use Illuminate\Foundation\Console\ModelMakeCommand as LaravelModelMakeCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'make:model')]
class ModelMakeCommand extends LaravelModelMakeCommand
{
    protected function buildFactoryReplacements(): array
    {
        if ( ! $this->option('factory') && ! $this->option('all')) {
            return [
                '{{ factoryImport }}' => '',
                '{{ factoryAttribute }}' => '',
                '{{ factoryTraitImport }}' => '',
                '{{ factoryTrait }}' => '',
            ];
        }

        $name = $this->argument('name');

        if ( ! is_string($name)) {
            return [];
        }

        $modelPath = Str::of($name)->studly()->replace('/', '\\')->toString();
        $factory = 'Database\\Factories\\' . $modelPath . 'Factory';

        return [
            '{{ factoryImport }}' => 'use ' . $factory . ';',
            '{{ factoryAttribute }}' => '#[UseFactory(' . class_basename($factory) . '::class)]',
            '{{ factoryTraitImport }}' => "use Illuminate\\Database\\Eloquent\\Attributes\\UseFactory;\nuse Illuminate\\Database\\Eloquent\\Factories\\HasFactory;",
            '{{ factoryTrait }}' => 'use HasFactory;',
        ];
    }
}
