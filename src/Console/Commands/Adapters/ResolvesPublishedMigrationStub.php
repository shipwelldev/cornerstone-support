<?php

declare(strict_types=1);

namespace ShipWell\CornerstoneSupport\Console\Commands\Adapters;

trait ResolvesPublishedMigrationStub
{
    protected function migrationStubFile(): string
    {
        return $this->laravel->basePath('stubs/' . $this->cornerstoneMigrationStub);
    }
}
