<?php

declare(strict_types=1);

use Illuminate\Foundation\Application;
use ShipWell\CornerstoneSupport\CornerstoneSupportServiceProvider;

test('the package provider registers commands in console applications', function (): void {
    expect($this->app->getProvider(CornerstoneSupportServiceProvider::class))->not->toBeNull();

    $this->artisan('list', ['namespace' => 'cornerstone'])
        ->expectsOutputToContain('cornerstone:stubs')
        ->assertSuccessful();
});

test('the package provider is inert outside console requests', function (): void {
    $application = Mockery::mock(Application::class);
    $application->shouldReceive('runningInConsole')->once()->andReturnFalse();
    $application->shouldNotReceive('bind');
    $application->shouldNotReceive('make');

    (new CornerstoneSupportServiceProvider($application))->register();
});
