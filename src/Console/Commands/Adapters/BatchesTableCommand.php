<?php

declare(strict_types=1);

namespace ShipWell\CornerstoneSupport\Console\Commands\Adapters;

use Illuminate\Queue\Console\BatchesTableCommand as LaravelBatchesTableCommand;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'make:queue-batches-table', aliases: ['queue:batches-table'])]
class BatchesTableCommand extends LaravelBatchesTableCommand
{
    use ResolvesPublishedMigrationStub;

    protected string $cornerstoneMigrationStub = 'queue-batches-table.stub';
}
