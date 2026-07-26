<?php

declare(strict_types=1);

namespace ShipWell\CornerstoneSupport\Console\Commands\Adapters;

use Illuminate\Session\Console\SessionTableCommand as LaravelSessionTableCommand;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'make:session-table', aliases: ['session:table'])]
class SessionTableCommand extends LaravelSessionTableCommand
{
    use ResolvesPublishedMigrationStub;

    protected string $cornerstoneMigrationStub = 'session-table.stub';
}
