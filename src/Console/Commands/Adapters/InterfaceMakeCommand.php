<?php

declare(strict_types=1);

namespace ShipWell\CornerstoneSupport\Console\Commands\Adapters;

use Illuminate\Foundation\Console\InterfaceMakeCommand as LaravelInterfaceMakeCommand;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'make:interface')]
class InterfaceMakeCommand extends LaravelInterfaceMakeCommand
{
    use ResolvesPublishedStub;

    protected function getStub(): string
    {
        return $this->cornerstoneStub('interface.stub');
    }
}
