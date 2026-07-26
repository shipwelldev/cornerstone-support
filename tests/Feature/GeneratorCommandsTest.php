<?php

declare(strict_types=1);

use Illuminate\Filesystem\Filesystem;
use ShipWell\CornerstoneSupport\Console\Commands\TestMakeCommand;

class BrowserPluginUnavailableTestCommand extends TestMakeCommand
{
    protected function browserPluginInstalled(): bool
    {
        return false;
    }
}

test('stub publication preserves files unless force is requested', function (): void {
    $files = $this->app->make(Filesystem::class);
    $stub = $this->applicationPath . '/stubs/data.stub';
    $files->put($stub, 'locally customized');

    $this->artisan('cornerstone:stubs')->assertSuccessful();
    expect($files->get($stub))->toBe('locally customized');

    $this->artisan('cornerstone:stubs', ['--force' => true])->assertSuccessful();
    expect($files->get($stub))->toContain('readonly class {{ class }}');
});

test('data and service commands preserve exact names placement and valid syntax', function (): void {
    $this->artisan('make:data', ['name' => 'MissionBrief'])->assertSuccessful();
    $this->artisan('make:service', ['name' => 'PlanExpedition'])->assertSuccessful();

    $data = $this->applicationPath . '/app/Data/MissionBrief.php';
    $service = $this->applicationPath . '/app/Services/PlanExpedition.php';

    $this->assertPhpSyntaxValid($data);
    $this->assertPhpSyntaxValid($service);
    require_once $data;
    require_once $service;

    expect(new ReflectionClass('App\Data\MissionBrief'))->isReadOnly()->toBeTrue()
        ->and(new ReflectionClass('App\Services\PlanExpedition'))->isFinal()->toBeFalse();
});

test('canonical Pest generation supports feature unit browser custom directories and force', function (): void {
    $this->artisan('pest:test', ['name' => 'MissionTest'])->assertSuccessful();
    $this->artisan('pest:test', ['name' => 'OrbitTest', '--unit' => true])->assertSuccessful();
    $this->artisan('pest:test', ['name' => 'LaunchTest', '--browser' => true, '--test-directory' => 'specs'])->assertSuccessful();

    $feature = $this->applicationPath . '/tests/Feature/MissionTest.php';
    $unit = $this->applicationPath . '/tests/Unit/OrbitTest.php';
    $browser = $this->applicationPath . '/specs/Browser/LaunchTest.php';

    foreach ([$feature, $unit, $browser] as $test) {
        $this->assertPhpSyntaxValid($test);
        expect(file_get_contents($test))->toBe("<?php\n\ndeclare(strict_types=1);\n");
    }

    file_put_contents($feature, 'custom');
    $this->artisan('pest:test', ['name' => 'MissionTest'])->assertFailed();
    expect(file_get_contents($feature))->toBe('custom');

    $this->artisan('pest:test', ['name' => 'MissionTest', '--force' => true])->assertSuccessful();
    expect(file_get_contents($feature))->toContain('declare(strict_types=1);');
});

test('canonical Pest generation rejects unsafe paths before writing', function (array $arguments, string $outsidePath): void {
    $outside = sys_get_temp_dir() . '/' . $outsidePath;
    @unlink($outside);

    $this->artisan('pest:test', $arguments)
        ->expectsOutputToContain('safe relative path')
        ->assertExitCode(2);

    expect($outside)->not->toBeFile();
})->with([
    'parent test name' => [['name' => '../../../../tmp/cornerstone-test-escape'], 'cornerstone-test-escape.php'],
    'absolute test directory' => [['name' => 'EscapeTest', '--test-directory' => '/tmp'], 'Feature/EscapeTest.php'],
    'parent test directory' => [['name' => 'EscapeTest', '--test-directory' => '../outside'], 'outside/Feature/EscapeTest.php'],
    'null byte' => [['name' => "Escape\0Test"], 'cornerstone-null-test.php'],
]);

test('canonical Pest generation rejects symlink escapes', function (): void {
    $outside = sys_get_temp_dir() . '/cornerstone-support-outside-' . bin2hex(random_bytes(4));
    mkdir($outside);
    symlink($outside, $this->applicationPath . '/linked-tests');

    $this->artisan('pest:test', ['name' => 'EscapeTest', '--test-directory' => 'linked-tests'])
        ->expectsOutputToContain('must remain within the application')
        ->assertExitCode(2);

    expect($outside . '/Feature/EscapeTest.php')->not->toBeFile();
    rmdir($outside);
});

test('dataset generation supports custom directories and rejects unsafe paths', function (): void {
    $this->artisan('pest:dataset', ['name' => 'Destinations', '--test-directory' => 'specs'])->assertSuccessful();
    $dataset = $this->applicationPath . '/specs/Datasets/Destinations.php';
    $this->assertPhpSyntaxValid($dataset);
    expect(file_get_contents($dataset))->toContain("dataset('destinations', []);");

    file_put_contents($dataset, 'custom');
    $this->artisan('pest:dataset', ['name' => 'Destinations', '--test-directory' => 'specs'])->assertFailed();
    expect(file_get_contents($dataset))->toBe('custom');
    $this->artisan('pest:dataset', ['name' => 'Destinations', '--test-directory' => 'specs', '--force' => true])->assertSuccessful();

    $outside = sys_get_temp_dir() . '/CornerstoneDataset.php';
    @unlink($outside);
    $this->artisan('pest:dataset', ['name' => '../../../../tmp/CornerstoneDataset'])
        ->expectsOutputToContain('safe relative path')
        ->assertExitCode(2);
    $this->artisan('pest:dataset', ['name' => 'Outside', '--test-directory' => '/tmp'])
        ->expectsOutputToContain('safe relative path')
        ->assertExitCode(2);
    expect($outside)->not->toBeFile();
});

test('browser generation fails clearly when the browser plugin is unavailable', function (): void {
    $this->app->bind(TestMakeCommand::class, BrowserPluginUnavailableTestCommand::class);

    $this->artisan('pest:test', ['name' => 'UnavailableTest', '--browser' => true])
        ->expectsOutputToContain('pestphp/pest-plugin-browser')
        ->assertFailed();

    expect($this->applicationPath . '/tests/Browser/UnavailableTest.php')->not->toBeFile();
});

test('make test delegates to Pest and rejects PHPUnit before writing', function (): void {
    $this->artisan('make:test', ['name' => 'NavigationTest', '--unit' => true])->assertSuccessful();
    $this->artisan('make:test', ['name' => 'NavigationBrowserTest', '--browser' => true])->assertSuccessful();
    $test = $this->applicationPath . '/tests/Unit/NavigationTest.php';
    $browserTest = $this->applicationPath . '/tests/Browser/NavigationBrowserTest.php';
    $this->assertPhpSyntaxValid($test);
    $this->assertPhpSyntaxValid($browserTest);

    $this->artisan('make:test', ['name' => 'LegacyTest', '--phpunit' => true])
        ->expectsOutputToContain('Pest tests only')
        ->assertExitCode(2);
    expect($this->applicationPath . '/tests/Feature/LegacyTest.php')->not->toBeFile();
});

test('Livewire default and explicit class generation create external views and direct Pest tests', function (array $arguments, string $class): void {
    $this->artisan('make:livewire', $arguments)->assertSuccessful();

    $component = $this->applicationPath . '/app/Livewire/' . $class . '.php';
    $view = $this->applicationPath . '/resources/views/livewire/' . str($class)->kebab() . '.blade.php';
    $test = $this->applicationPath . '/tests/Feature/Livewire/' . $class . 'Test.php';

    $this->assertPhpSyntaxValid($component);
    $this->assertPhpSyntaxValid($test);
    expect($view)->toBeFile()
        ->and(file_get_contents($test))->toContain("Livewire::test(App\\Livewire\\{$class}::class)");
})->with([
    'default' => [['name' => 'MissionStatus'], 'MissionStatus'],
    'explicit class' => [['name' => 'CrewStatus', '--type' => 'class'], 'CrewStatus'],
]);

test('Livewire rejects every non class based format', function (array $options): void {
    $this->artisan('make:livewire', ['name' => 'RejectedStatus', ...$options])
        ->expectsOutputToContain('named class-based Livewire components')
        ->assertExitCode(2);

    expect($this->applicationPath . '/app/Livewire/RejectedStatus.php')->not->toBeFile();
})->with([
    'sfc flag' => [['--sfc' => true]],
    'mfc flag' => [['--mfc' => true]],
    'sfc type' => [['--type' => 'sfc']],
    'mfc type' => [['--type' => 'mfc']],
    'invalid type' => [['--type' => 'unexpected']],
    'javascript sidecar' => [['--js' => true]],
    'css sidecar' => [['--css' => true]],
]);

test('models declare safe metadata and wire optional factories with valid syntax', function (): void {
    $this->artisan('make:model', ['name' => 'Expedition'])->assertSuccessful();
    $this->artisan('make:model', ['name' => 'Mission', '--factory' => true])->assertSuccessful();
    $this->artisan('make:model', ['name' => 'CrewMission', '--factory' => true, '--pivot' => true])->assertSuccessful();

    $paths = [
        $this->applicationPath . '/app/Models/Expedition.php',
        $this->applicationPath . '/app/Models/Mission.php',
        $this->applicationPath . '/app/Models/CrewMission.php',
        $this->applicationPath . '/database/factories/MissionFactory.php',
        $this->applicationPath . '/database/factories/CrewMissionFactory.php',
    ];

    foreach ($paths as $path) {
        $this->assertPhpSyntaxValid($path);
    }

    expect(file_get_contents($paths[0]))->toContain('#[Fillable([])]')->not->toContain('HasFactory')
        ->and(file_get_contents($paths[1]))->toContain('#[UseFactory(MissionFactory::class)]')->toContain('use HasFactory;')
        ->and(file_get_contents($paths[3]))->toContain('#[UseModel(Mission::class)]')->not->toContain('/**')
        ->and(file_get_contents($paths[2]))->toContain('extends Pivot')->toContain('#[UseFactory(CrewMissionFactory::class)]');
});

test('view generation uses Pest for test and pest options and rejects PHPUnit before writing', function (): void {
    $this->artisan('make:view', ['name' => 'missions/index', '--test' => true])->assertSuccessful();
    $this->artisan('make:view', ['name' => 'missions/show', '--pest' => true])->assertSuccessful();

    $tests = (new Filesystem())->allFiles($this->applicationPath . '/tests/Feature/View');
    expect($tests)->toHaveCount(2);

    foreach ($tests as $testFile) {
        $test = $testFile->getPathname();
        $this->assertPhpSyntaxValid($test);
        expect(file_get_contents($test))->toContain('declare(strict_types=1);')->not->toContain('class ');
    }

    $this->artisan('make:view', ['name' => 'missions/legacy', '--phpunit' => true])
        ->expectsOutputToContain('Pest tests only')
        ->assertFailed();
    expect($this->applicationPath . '/resources/views/missions/legacy.blade.php')->not->toBeFile();
});

test('adapted secondary templates come from the published set', function (): void {
    $this->artisan('make:mail', ['name' => 'MissionBriefMail', '--markdown' => 'mail.mission-brief'])->assertSuccessful();
    $this->artisan('make:notification', ['name' => 'MissionAlert', '--markdown' => 'mail.mission-alert'])->assertSuccessful();
    $this->artisan('make:component', ['name' => 'MissionBadge'])->assertSuccessful();

    foreach ([
        $this->applicationPath . '/app/Mail/MissionBriefMail.php',
        $this->applicationPath . '/app/Notifications/MissionAlert.php',
        $this->applicationPath . '/app/View/Components/MissionBadge.php',
    ] as $class) {
        $this->assertPhpSyntaxValid($class);
    }

    expect(file_get_contents($this->applicationPath . '/resources/views/mail/mission-brief.blade.php'))->toBe("<x-mail::message>\n</x-mail::message>\n")
        ->and(file_get_contents($this->applicationPath . '/resources/views/mail/mission-alert.blade.php'))->toBe("<x-mail::message>\n</x-mail::message>\n")
        ->and(file_get_contents($this->applicationPath . '/resources/views/components/mission-badge.blade.php'))->toBe("<div>\n</div>\n")
        ->and(file_get_contents($this->applicationPath . '/app/Mail/MissionBriefMail.php'))->toContain("markdown: 'mail.mission-brief'")->not->toContain('{{')
        ->and(file_get_contents($this->applicationPath . '/app/Notifications/MissionAlert.php'))->toContain("markdown('mail.mission-alert')")->not->toContain('{{');
});

test('typed listeners use Laravel event replacements and valid queue scaffolding', function (): void {
    $this->artisan('make:event', ['name' => 'MissionStarted'])->assertSuccessful();
    $this->artisan('make:listener', ['name' => 'RecordMission', '--event' => 'App\\Events\\MissionStarted'])->assertSuccessful();
    $this->artisan('make:listener', ['name' => 'QueueMissionRecord', '--event' => 'App\\Events\\MissionStarted', '--queued' => true])->assertSuccessful();

    $listener = $this->applicationPath . '/app/Listeners/RecordMission.php';
    $queuedListener = $this->applicationPath . '/app/Listeners/QueueMissionRecord.php';

    $this->assertPhpSyntaxValid($listener);
    $this->assertPhpSyntaxValid($queuedListener);
    expect(file_get_contents($listener))->toContain('use App\\Events\\MissionStarted;')->toContain('handle(MissionStarted $event)')->not->toContain('{{')
        ->and(file_get_contents($queuedListener))->toContain('implements ShouldQueue')->toContain('use InteractsWithQueue;')->not->toContain('{{');
});

test('all specialized table commands generate forward only valid migrations', function (string $command, string $table): void {
    $this->artisan($command)->assertSuccessful();
    $matches = glob($this->applicationPath . '/database/migrations/*create_' . $table . '_table.php');

    expect($matches)->toHaveCount(1);
    $this->assertPhpSyntaxValid($matches[0]);
    expect(file_get_contents($matches[0]))->not->toContain('function down')->not->toContain('/**');
})->with([
    'cache' => ['make:cache-table', 'cache'],
    'queue' => ['make:queue-table', 'jobs'],
    'queue batches' => ['make:queue-batches-table', 'job_batches'],
    'failed queue' => ['make:queue-failed-table', 'failed_jobs'],
    'notifications' => ['make:notifications-table', 'notifications'],
    'sessions' => ['make:session-table', 'sessions'],
]);

test('hard coded class adapters generate valid strict typed PHP', function (string $command, array $arguments, string $relativePath): void {
    $this->artisan($command, $arguments)->assertSuccessful();
    $path = $this->applicationPath . '/' . $relativePath;

    $this->assertPhpSyntaxValid($path);
    expect(file_get_contents($path))->toContain('declare(strict_types=1);')->not->toContain('/**');
})->with([
    'channel' => ['make:channel', ['name' => 'MissionChannel'], 'app/Broadcasting/MissionChannel.php'],
    'interface' => ['make:interface', ['name' => 'Navigator'], 'app/Navigator.php'],
    'exception' => ['make:exception', ['name' => 'NavigationFailed', '--report' => true], 'app/Exceptions/NavigationFailed.php'],
]);

test('generated channels expose a fail closed authorization seam', function (): void {
    $this->artisan('make:channel', ['name' => 'SecureMissionChannel'])->assertSuccessful();
    $channel = $this->applicationPath . '/app/Broadcasting/SecureMissionChannel.php';

    $this->assertPhpSyntaxValid($channel);
    expect(file_get_contents($channel))->toContain('function join(User $user): array|bool')
        ->toContain("throw new LogicException('Channel authorization must be implemented.');")
        ->not->toContain('{{');
});

test('controller variants preserve their public method scaffolds and valid syntax', function (string $name, array $options, array $methods, array $absent): void {
    if (isset($options['--model'])) {
        $this->artisan('make:model', ['name' => $options['--model']])->assertSuccessful();
        require_once $this->applicationPath . '/app/Models/' . $options['--model'] . '.php';
    }

    $this->artisan('make:controller', ['name' => $name, ...$options, '--no-interaction' => true])->assertSuccessful();
    $path = $this->applicationPath . '/app/Http/Controllers/' . $name . '.php';
    $contents = file_get_contents($path);

    $this->assertPhpSyntaxValid($path);
    expect($contents)->not->toContain('/**')->not->toContain('//')->not->toContain('{{');

    foreach ($methods as $method) {
        expect($contents)->toContain('function ' . $method . '(');
    }

    foreach ($absent as $method) {
        expect($contents)->not->toContain('function ' . $method . '(');
    }
})->with([
    'resource' => ['MissionController', ['--resource' => true], ['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'], []],
    'api' => ['MissionApiController', ['--api' => true], ['index', 'store', 'show', 'update', 'destroy'], ['create', 'edit']],
    'model' => ['ModelMissionController', ['--model' => 'Mission'], ['index', 'create', 'store', 'show', 'edit', 'update', 'destroy'], []],
    'singleton' => ['MissionProfileController', ['--singleton' => true], ['create', 'store', 'show', 'edit', 'update', 'destroy'], ['index']],
    'creatable singleton' => ['CreatableMissionProfileController', ['--singleton' => true, '--creatable' => true], ['create', 'store', 'show', 'edit', 'update', 'destroy'], ['index']],
]);
