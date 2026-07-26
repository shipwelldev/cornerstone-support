<?php

declare(strict_types=1);

namespace ShipWell\CornerstoneSupport\Console\Commands\Adapters;

use Illuminate\Foundation\Console\ExceptionMakeCommand as LaravelExceptionMakeCommand;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'make:exception')]
class ExceptionMakeCommand extends LaravelExceptionMakeCommand
{
    use ResolvesPublishedStub;

    protected function getStub(): string
    {
        $suffix = match (true) {
            $this->option('render') && $this->option('report') => '-render-report',
            $this->option('render') => '-render',
            $this->option('report') => '-report',
            default => '',
        };

        return $this->cornerstoneStub('exception' . $suffix . '.stub');
    }
}
