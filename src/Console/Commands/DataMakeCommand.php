<?php

declare(strict_types=1);

namespace ShipWell\CornerstoneSupport\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use ShipWell\CornerstoneSupport\Console\Commands\Concerns\GeneratesSimpleClass;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'make:data')]
class DataMakeCommand extends GeneratorCommand
{
    use GeneratesSimpleClass;

    protected $name = 'make:data';

    protected $description = 'Create a new data object';

    protected $type = 'Data object';

    protected string $stubName = 'data.stub';

    protected string $targetNamespace = 'Data';
}
