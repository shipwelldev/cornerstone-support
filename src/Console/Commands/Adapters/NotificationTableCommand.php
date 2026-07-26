<?php

declare(strict_types=1);

namespace ShipWell\CornerstoneSupport\Console\Commands\Adapters;

use Illuminate\Notifications\Console\NotificationTableCommand as LaravelNotificationTableCommand;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'make:notifications-table', aliases: ['notifications:table'])]
class NotificationTableCommand extends LaravelNotificationTableCommand
{
    use ResolvesPublishedMigrationStub;

    protected string $cornerstoneMigrationStub = 'notifications-table.stub';
}
