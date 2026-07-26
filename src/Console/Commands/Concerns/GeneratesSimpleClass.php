<?php

declare(strict_types=1);

namespace ShipWell\CornerstoneSupport\Console\Commands\Concerns;

trait GeneratesSimpleClass
{
    protected function getStub(): string
    {
        return dirname(__DIR__, 4) . '/stubs/' . $this->stubName;
    }

    protected function getDefaultNamespace(mixed $rootNamespace): string
    {
        return (string) $rootNamespace . '\\' . $this->targetNamespace;
    }
}
