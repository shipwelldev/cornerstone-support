<?php

declare(strict_types=1);

namespace ShipWell\CornerstoneSupport\Console\Commands\Adapters;

trait ResolvesPublishedStub
{
    protected function cornerstoneStub(string $name): string
    {
        return $this->laravel->basePath('stubs/' . $name);
    }
}
