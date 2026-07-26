<?php

declare(strict_types=1);

use Illuminate\Filesystem\Filesystem;
use Laravel\Boost\Install\GuidelineConfig;
use Laravel\Boost\Install\SkillComposer;
use Laravel\Boost\Install\ThirdPartyPackage;

test('the package exposes the Flux installer through Boost with its complete workflow', function (): void {
    $files = $this->app->make(Filesystem::class);
    $this->installSupportPackageFixture();

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
        ->toContain('Treat repeat runs as reconciliation')
        ->toContain('Read `CODING_STANDARDS.md` and the current official Flux installation documentation')
        ->toContain('Installed Laravel, Livewire, Tailwind CSS, Flux, and Flux Pro versions')
        ->toContain('Every first-party Blade layout')
        ->toContain('Existing Flux directives, Flux CSS imports, dark variants, appearance initialization, and typography configuration')
        ->toContain('If the worktree has any changes')
        ->toContain('If it fails, report the failures and ask whether to proceed')
        ->toContain('wait for explicit approval before changing Laravel, Livewire, Tailwind CSS')
        ->toContain('Ask the developer to choose Flux Free or Flux Pro')
        ->toContain('When multiple eligible layouts exist')
        ->toContain('Configure only the selected layouts and their CSS entrypoints')
        ->toContain('ask before replacing or adapting that code')
        ->toContain("Ask whether to adopt Flux's currently recommended Inter font")
        ->toContain('composer verify')
        ->toContain('When Flux is absent or behind that release')
        ->toContain('composer require livewire/flux')
        ->toContain('composer update livewire/flux livewire/flux-pro')
        ->toContain('For a new Flux Pro installation or missing local Pro authentication')
        ->toContain('When Pro is already installed and locally authenticated, skip activation')
        ->toContain('@fluxAppearance')
        ->toContain('@fluxScripts')
        ->toContain("@import '../../vendor/livewire/flux/dist/flux.css';")
        ->toContain("Calculate the relative POSIX import path from each entrypoint's directory")
        ->toContain('@custom-variant dark')
        ->toContain('php artisan flux:activate')
        ->toContain('php artisan boost:update --discover')
        ->toContain('php artisan view:cache')
        ->toContain('npm run build')
        ->toContain('composer fix')
        ->toContain('first confirm `/auth.json` is ignored')
        ->toContain('Never request, receive, or expose Flux credentials')
        ->toContain('run this command directly in their own terminal')
        ->toContain('Do not run `php artisan flux:publish`')
        ->toContain('Do not edit CI or deployment configuration')
        ->toContain('keep the partial installation intact')
        ->toContain('Discovery is resolved when the developer either declines it')
        ->toContain('A precisely reported blocker leaves the installation incomplete.');

    expect(mb_strpos($instructions, 'first confirm `/auth.json` is ignored'))
        ->toBeLessThan(mb_strpos($instructions, 'php artisan flux:activate'));
});
