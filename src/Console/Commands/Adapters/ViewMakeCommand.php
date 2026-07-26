<?php

declare(strict_types=1);

namespace ShipWell\CornerstoneSupport\Console\Commands\Adapters;

use Illuminate\Foundation\Console\ViewMakeCommand as LaravelViewMakeCommand;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'make:view')]
class ViewMakeCommand extends LaravelViewMakeCommand
{
    public function handle(): ?bool
    {
        if ($this->option('phpunit')) {
            $this->fail('Cornerstone permits Pest tests only; the --phpunit option is not supported.');
        }

        if ($this->option('test')) {
            $this->input->setOption('pest', true);
        }

        return parent::handle();
    }

    protected function usingPest(): bool
    {
        return true;
    }
}
