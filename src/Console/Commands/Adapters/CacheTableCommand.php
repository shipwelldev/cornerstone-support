<?php

declare(strict_types=1);

namespace ShipWell\CornerstoneSupport\Console\Commands\Adapters;

use Illuminate\Cache\Console\CacheTableCommand as LaravelCacheTableCommand;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'make:cache-table', aliases: ['cache:table'])]
class CacheTableCommand extends LaravelCacheTableCommand
{
    use ResolvesPublishedMigrationStub;

    protected string $cornerstoneMigrationStub = 'cache-table.stub';
}
