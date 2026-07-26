<?php

declare(strict_types=1);

use Illuminate\Filesystem\Filesystem;
use Laravel\Boost\Install\ThirdPartyPackage;

test('the package exposes Cornerstone guidance through the Boost package convention', function (): void {
    $files = $this->app->make(Filesystem::class);
    $packagePath = dirname(__DIR__, 2);
    $installedPackagePath = $this->applicationPath . '/vendor/shipwelldev/cornerstone-support';
    $files->ensureDirectoryExists(dirname($installedPackagePath));
    $files->link($packagePath, $installedPackagePath);
    $files->put($this->applicationPath . '/composer.json', json_encode([
        'require' => ['shipwelldev/cornerstone-support' => '@dev'],
    ], JSON_THROW_ON_ERROR));

    $package = ThirdPartyPackage::discover()->get('shipwelldev/cornerstone-support');

    expect($package)->not->toBeNull()
        ->and($package->hasGuidelines)->toBeTrue()
        ->and($package->hasSkills)->toBeFalse();
});
