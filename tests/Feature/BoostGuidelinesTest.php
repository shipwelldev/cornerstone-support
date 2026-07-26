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
        ->toContain('Add or edit a focused source guideline under `.ai/guidelines` instead')
        ->toContain('Follow `CODING_STANDARDS.md`. It is the authoritative application coding standard and overrides conflicting generic guidance.')
        ->toContain('Agents cannot weaken Rules, enforcement, analysis, or suppressions.')
        ->toContain('Agents follow Rules and Guidelines.')
        ->toContain('A human must explicitly authorize every Guideline override.')
        ->toContain('Application-owned agent skills belong under `.ai/skills`. Treat this directory as the source of truth')
        ->toContain('Laravel Boost publishes skills to the appropriate agent-specific paths')
        ->toContain('Only consider implementation work complete after both canonical workflows pass in this order:')
        ->toContain('Run `composer fix`, review every correction, and resolve unintended changes.')
        ->toContain('Run `composer verify` and fix every failing non-correcting gate.')
        ->toContain('If a later fix changes code, restart verification from `composer fix`.');
});
