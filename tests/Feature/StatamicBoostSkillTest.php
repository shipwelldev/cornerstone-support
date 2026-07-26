<?php

declare(strict_types=1);

use Laravel\Boost\Install\GuidelineConfig;
use Laravel\Boost\Install\SkillComposer;
use Laravel\Boost\Install\ThirdPartyPackage;

test('the package exposes the Statamic installer through Boost', function (): void {
    $this->installSupportPackageFixture();

    $config = new GuidelineConfig();
    $config->aiGuidelines = ['shipwelldev/cornerstone-support'];
    $package = ThirdPartyPackage::discover()->get('shipwelldev/cornerstone-support');
    $skill = $this->app->make(SkillComposer::class)->config($config)->skills()->get('install-statamic');

    expect($package)->not->toBeNull()
        ->and($package->hasSkills)->toBeTrue()
        ->and($skill)->not->toBeNull()
        ->and($skill->name)->toBe('install-statamic')
        ->and($skill->package)->toBe('shipwelldev/cornerstone-support')
        ->and($skill->description)->toContain('Install Statamic')
        ->toContain('Core or Pro');
});
