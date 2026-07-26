<?php

declare(strict_types=1);

namespace ShipWell\CornerstoneSupport\Console\Commands\Adapters;

use Illuminate\Queue\Console\TableCommand as LaravelQueueTableCommand;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'make:queue-table', aliases: ['queue:table'])]
class QueueTableCommand extends LaravelQueueTableCommand
{
    use ResolvesPublishedMigrationStub;

    protected string $cornerstoneMigrationStub = 'queue-table.stub';
}
