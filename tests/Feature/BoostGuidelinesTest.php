<?php

declare(strict_types=1);

use Illuminate\Filesystem\Filesystem;
use Laravel\Boost\Install\GuidelineComposer;
use Laravel\Boost\Install\GuidelineConfig;
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
    $config = new GuidelineConfig();
    $config->aiGuidelines = ['shipwelldev/cornerstone-support'];
    $guidelines = $this->app->make(GuidelineComposer::class)->config($config)->compose();

    expect($package)->not->toBeNull()
        ->and($package->hasGuidelines)->toBeTrue()
        ->and($package->hasSkills)->toBeFalse()
        ->and($guidelines)->toContain('=== shipwelldev/cornerstone-support rules ===')
        ->toContain('Do not edit `AGENTS.md` or `CLAUDE.md` directly.')
        ->toContain('Follow `CODING_STANDARDS.md`.')
        ->toContain('Application-owned agent skills belong under `.ai/skills`.')
        ->toContain('Run `composer fix`')
        ->toContain('Run `composer verify`');
});
