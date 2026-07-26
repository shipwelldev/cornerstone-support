<?php

declare(strict_types=1);

namespace ShipWell\CornerstoneSupport\Console\Commands\Adapters;

use Illuminate\Queue\Console\FailedTableCommand as LaravelFailedTableCommand;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'make:queue-failed-table', aliases: ['queue:failed-table'])]
class FailedTableCommand extends LaravelFailedTableCommand
{
    use ResolvesPublishedMigrationStub;

    protected string $cornerstoneMigrationStub = 'queue-failed-table.stub';
}
