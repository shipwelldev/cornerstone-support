<?php

declare(strict_types=1);

namespace ShipWell\CornerstoneSupport\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use ShipWell\CornerstoneSupport\Console\Commands\Concerns\GeneratesSimpleClass;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'make:service')]
class ServiceMakeCommand extends GeneratorCommand
{
    use GeneratesSimpleClass;

    protected $name = 'make:service';

    protected $description = 'Create a new service';

    protected $type = 'Service';

    protected string $stubName = 'service.stub';

    protected string $targetNamespace = 'Services';
}
