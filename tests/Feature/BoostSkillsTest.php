<?php

declare(strict_types=1);

use Illuminate\Filesystem\Filesystem;
use Laravel\Boost\Install\GuidelineConfig;
use Laravel\Boost\Install\SkillComposer;
use Laravel\Boost\Install\ThirdPartyPackage;

test('the package exposes the Flux installer through Boost with its complete workflow', function (): void {
    $files = $this->app->make(Filesystem::class);
    $packagePath = dirname(__DIR__, 2);
    $installedPackagePath = $this->applicationPath . '/vendor/shipwelldev/cornerstone-support';
    $files->ensureDirectoryExists(dirname($installedPackagePath));
    $files->link($packagePath, $installedPackagePath);
    $files->put($this->applicationPath . '/composer.json', json_encode([
        'require' => ['shipwelldev/cornerstone-support' => '@dev'],
    ], JSON_THROW_ON_ERROR));

    $config = new GuidelineConfig();
    $config->aiGuidelines = ['shipwelldev/cornerstone-support'];
    $package = ThirdPartyPackage::discover()->get('shipwelldev/cornerstone-support');
    $skill = $this->app->make(SkillComposer::class)->config($config)->skills()->get('install-flux-ui');

    expect($package)->not->toBeNull()
        ->and($package->hasSkills)->toBeTrue()
        ->and($skill)->not->toBeNull()
        ->and($skill->name)->toBe('install-flux-ui')
        ->and($skill->package)->toBe('shipwelldev/cornerstone-support')
        ->and($skill->description)->toContain('Install Flux UI')
        ->toContain('Flux Pro activation');

    $instructions = $files->get($skill->path . '/SKILL.md');

    expect($instructions)
        ->not->toContain('disable-model-invocation')
        ->toContain('without converting application UI or publishing package components')
        ->toContain('Treat repeat runs as reconciliation')
        ->toContain('If the worktree has any changes')
        ->toContain('If it fails, report the failures and ask whether to proceed')
        ->toContain('wait for explicit approval before changing Laravel, Livewire, Tailwind CSS')
        ->toContain('When multiple eligible layouts exist')
        ->toContain('ask before replacing or adapting that code')
        ->toContain("Ask whether to adopt Flux's currently recommended Inter font")
        ->toContain('composer verify')
        ->toContain('composer require livewire/flux')
        ->toContain('@fluxAppearance')
        ->toContain('@fluxScripts')
        ->toContain("@import '../../vendor/livewire/flux/dist/flux.css';")
        ->toContain('@custom-variant dark')
        ->toContain('php artisan flux:activate')
        ->toContain('php artisan boost:update --discover')
        ->toContain('php artisan view:cache')
        ->toContain('composer fix')
        ->toContain('never request, receive, or expose Flux credentials')
        ->toContain('run this command directly in their own terminal')
        ->toContain('Do not run `php artisan flux:publish`')
        ->toContain('Do not edit CI or deployment configuration')
        ->toContain('Do not roll back or broaden the work without approval');
});
