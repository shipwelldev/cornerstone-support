<?php

declare(strict_types=1);

test('the package exposes Cornerstone guidance through the Boost package convention', function (): void {
    $guidelines = dirname(__DIR__, 2) . '/resources/boost/guidelines/core.blade.php';

    expect($guidelines)->toBeFile();

    $contents = file_get_contents($guidelines);

    expect($contents)
        ->toContain('## Agent instructions')
        ->toContain('## Coding standards')
        ->toContain('## Skills')
        ->toContain('## Verification');
});
