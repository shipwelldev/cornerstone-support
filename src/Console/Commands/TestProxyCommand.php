<?php

declare(strict_types=1);

namespace ShipWell\CornerstoneSupport\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'make:test')]
class TestProxyCommand extends Command
{
    protected $signature = 'make:test
        {name : The name of the test}
        {--f|force : Overwrite an existing test}
        {--u|unit : Create a unit test}
        {--browser : Create a Pest browser test}
        {--pest : Create a Pest test}
        {--phpunit : Create a PHPUnit test}';

    protected $description = 'Create a new Pest test';

    public function handle(): int
    {
        if ($this->option('phpunit')) {
            $this->components->error('Cornerstone permits Pest tests only; the --phpunit option is not supported.');

            return self::INVALID;
        }

        return $this->call('pest:test', array_filter([
            'name' => $this->argument('name'),
            '--unit' => (bool) $this->option('unit'),
            '--browser' => (bool) $this->option('browser'),
            '--force' => (bool) $this->option('force'),
        ]));
    }
}
