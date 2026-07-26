<?php

declare(strict_types=1);

namespace ShipWell\CornerstoneSupport\Tests;

use Illuminate\Filesystem\Filesystem;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use ShipWell\CornerstoneSupport\CornerstoneSupportServiceProvider;
use Symfony\Component\Process\Process;

abstract class TestCase extends Orchestra
{
    protected string $applicationPath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->applicationPath = sys_get_temp_dir() . '/cornerstone-support-' . bin2hex(random_bytes(8));
        $files = $this->app->make(Filesystem::class);
        $files->ensureDirectoryExists($this->applicationPath . '/app');
        $files->ensureDirectoryExists($this->applicationPath . '/app/Models');
        $files->ensureDirectoryExists($this->applicationPath . '/database/migrations');
        $files->ensureDirectoryExists($this->applicationPath . '/resources/views/livewire');
        $files->ensureDirectoryExists($this->applicationPath . '/tests');
        $files->put($this->applicationPath . '/composer.json', json_encode([
            'autoload' => ['psr-4' => ['App\\' => 'app/']],
        ], JSON_THROW_ON_ERROR));
        $files->put($this->applicationPath . '/tests/Pest.php', "<?php\n");

        $this->app->setBasePath($this->applicationPath);
        $this->app['config']->set('view.paths', [$this->applicationPath . '/resources/views']);
        $this->app['config']->set('livewire.class_namespace', 'App\\Livewire');
        $this->app['config']->set('livewire.class_path', $this->applicationPath . '/app/Livewire');
        $this->app['config']->set('livewire.view_path', $this->applicationPath . '/resources/views/livewire');

        $this->artisan('cornerstone:stubs')->assertSuccessful();
    }

    protected function tearDown(): void
    {
        $applicationPath = $this->applicationPath;

        parent::tearDown();

        (new Filesystem())->deleteDirectory($applicationPath);
    }

    protected function getPackageProviders($app): array
    {
        return [
            LivewireServiceProvider::class,
            CornerstoneSupportServiceProvider::class,
        ];
    }

    protected function assertPhpSyntaxValid(string $path): void
    {
        $process = new Process([PHP_BINARY, '-l', $path]);
        $process->run();

        $this->assertTrue($process->isSuccessful(), $process->getErrorOutput() . $process->getOutput());
    }

    protected function installSupportPackageFixture(): void
    {
        $files = $this->app->make(Filesystem::class);
        $installedPackagePath = $this->applicationPath . '/vendor/shipwelldev/cornerstone-support';
        $files->ensureDirectoryExists(dirname($installedPackagePath));
        $files->link(dirname(__DIR__), $installedPackagePath);
        $files->put($this->applicationPath . '/composer.json', json_encode([
            'require' => ['shipwelldev/cornerstone-support' => '@dev'],
        ], JSON_THROW_ON_ERROR));
    }
}
