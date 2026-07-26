<?php

declare(strict_types=1);

namespace ShipWell\CornerstoneSupport\Console\Commands\Adapters;

use Illuminate\Routing\Console\ControllerMakeCommand as LaravelControllerMakeCommand;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'make:controller')]
class ControllerMakeCommand extends LaravelControllerMakeCommand
{
    protected function buildClass(mixed $name): string
    {
        return str_replace("        //\n", '', parent::buildClass($name));
    }
}
