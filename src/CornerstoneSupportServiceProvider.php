<?php

declare(strict_types=1);

namespace ShipWell\CornerstoneSupport;

use Illuminate\Contracts\Console\Kernel as ConsoleKernel;
use Illuminate\Support\ServiceProvider;
use ShipWell\CornerstoneSupport\Console\Commands\Adapters\BatchesTableCommand;
use ShipWell\CornerstoneSupport\Console\Commands\Adapters\CacheTableCommand;
use ShipWell\CornerstoneSupport\Console\Commands\Adapters\ChannelMakeCommand;
use ShipWell\CornerstoneSupport\Console\Commands\Adapters\ComponentMakeCommand;
use ShipWell\CornerstoneSupport\Console\Commands\Adapters\ControllerMakeCommand;
use ShipWell\CornerstoneSupport\Console\Commands\Adapters\ExceptionMakeCommand;
use ShipWell\CornerstoneSupport\Console\Commands\Adapters\FailedTableCommand;
use ShipWell\CornerstoneSupport\Console\Commands\Adapters\InterfaceMakeCommand;
use ShipWell\CornerstoneSupport\Console\Commands\Adapters\MailMakeCommand;
use ShipWell\CornerstoneSupport\Console\Commands\Adapters\NotificationMakeCommand;
use ShipWell\CornerstoneSupport\Console\Commands\Adapters\NotificationTableCommand;
use ShipWell\CornerstoneSupport\Console\Commands\Adapters\QueueTableCommand;
use ShipWell\CornerstoneSupport\Console\Commands\Adapters\SessionTableCommand;
use ShipWell\CornerstoneSupport\Console\Commands\Adapters\ViewMakeCommand;
use ShipWell\CornerstoneSupport\Console\Commands\CornerstoneStubsCommand;
use ShipWell\CornerstoneSupport\Console\Commands\DataMakeCommand;
use ShipWell\CornerstoneSupport\Console\Commands\DatasetMakeCommand;
use ShipWell\CornerstoneSupport\Console\Commands\LivewireMakeCommand;
use ShipWell\CornerstoneSupport\Console\Commands\ModelMakeCommand;
use ShipWell\CornerstoneSupport\Console\Commands\ServiceMakeCommand;
use ShipWell\CornerstoneSupport\Console\Commands\TestMakeCommand;
use ShipWell\CornerstoneSupport\Console\Commands\TestProxyCommand;

class CornerstoneSupportServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ( ! $this->app->runningInConsole()) {
            return;
        }

        $this->app->bind('Pest\\Laravel\\Commands\\PestDatasetCommand', DatasetMakeCommand::class);
        $this->app->bind('Pest\\Laravel\\Commands\\PestTestCommand', TestMakeCommand::class);

        $commands = [
            BatchesTableCommand::class,
            CacheTableCommand::class,
            ChannelMakeCommand::class,
            ComponentMakeCommand::class,
            ControllerMakeCommand::class,
            CornerstoneStubsCommand::class,
            DataMakeCommand::class,
            DatasetMakeCommand::class,
            ExceptionMakeCommand::class,
            FailedTableCommand::class,
            InterfaceMakeCommand::class,
            LivewireMakeCommand::class,
            MailMakeCommand::class,
            ModelMakeCommand::class,
            NotificationMakeCommand::class,
            NotificationTableCommand::class,
            QueueTableCommand::class,
            ServiceMakeCommand::class,
            SessionTableCommand::class,
            TestMakeCommand::class,
            TestProxyCommand::class,
            ViewMakeCommand::class,
        ];

        $kernel = $this->app->make(ConsoleKernel::class);
        $kernel->addCommands($commands);
    }
}
