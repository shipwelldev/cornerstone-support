<?php

declare(strict_types=1);

namespace ShipWell\CornerstoneSupport\Console\Commands\Adapters;

use Illuminate\Foundation\Console\ChannelMakeCommand as LaravelChannelMakeCommand;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'make:channel')]
class ChannelMakeCommand extends LaravelChannelMakeCommand
{
    use ResolvesPublishedStub;

    protected function getStub(): string
    {
        return $this->cornerstoneStub('channel.stub');
    }
}
