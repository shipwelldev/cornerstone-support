<?php

declare(strict_types=1);

use Illuminate\Filesystem\Filesystem;

test('every standalone PHP stub declares strict types and contains no DocBlocks', function (): void {
    $files = new Filesystem();

    foreach ($files->allFiles(dirname(__DIR__, 2) . '/stubs') as $stub) {
        $contents = $files->get($stub->getPathname());

        if ( ! str_starts_with($contents, '<?php')) {
            continue;
        }

        $tokens = token_get_all($contents);
        $hasStrictTypes = false;
        $hasDocBlock = false;

        foreach ($tokens as $token) {
            if ( ! is_array($token)) {
                continue;
            }

            $hasStrictTypes = $hasStrictTypes || ($token[0] === T_STRING && $token[1] === 'strict_types');
            $hasDocBlock = $hasDocBlock || $token[0] === T_DOC_COMMENT;
        }

        expect($hasStrictTypes, $stub->getRelativePathname())->toBeTrue()
            ->and($hasDocBlock, $stub->getRelativePathname())->toBeFalse();
    }
});

test('the package owns every current generator stub family', function (): void {
    $required = [
        'cache-table.stub',
        'cast.inbound.stub',
        'cast.stub',
        'channel.stub',
        'class.invokable.stub',
        'class.stub',
        'config.stub',
        'console.stub',
        'controller.api.stub',
        'controller.invokable.stub',
        'controller.model.api.stub',
        'controller.model.stub',
        'controller.nested.api.stub',
        'controller.nested.singleton.api.stub',
        'controller.nested.singleton.stub',
        'controller.nested.stub',
        'controller.plain.stub',
        'controller.singleton.api.stub',
        'controller.singleton.stub',
        'controller.stub',
        'data.stub',
        'enum.backed.stub',
        'enum.stub',
        'event.stub',
        'exception-render-report.stub',
        'exception-render.stub',
        'exception-report.stub',
        'exception.stub',
        'factory.stub',
        'interface.stub',
        'job.batched.queued.stub',
        'job.middleware.stub',
        'job.queued.stub',
        'job.stub',
        'listener.queued.stub',
        'listener.stub',
        'listener.typed.queued.stub',
        'listener.typed.stub',
        'livewire-mfc-class.stub',
        'livewire-mfc-css.stub',
        'livewire-mfc-js.stub',
        'livewire-mfc-test.stub',
        'livewire-mfc-view.stub',
        'livewire-sfc.stub',
        'livewire.attribute.stub',
        'livewire.form.stub',
        'livewire.inline.stub',
        'livewire.layout.stub',
        'livewire.pest.stub',
        'livewire.stub',
        'livewire.test.stub',
        'livewire.view.stub',
        'mail.stub',
        'markdown-mail.stub',
        'markdown-notification.stub',
        'markdown.stub',
        'mcp-app-resource.stub',
        'mcp-app-resource.view.stub',
        'mcp-prompt.stub',
        'mcp-resource.stub',
        'mcp-server.stub',
        'mcp-tool.stub',
        'middleware.stub',
        'migration.create.stub',
        'migration.stub',
        'migration.update.stub',
        'model.morph-pivot.stub',
        'model.pivot.stub',
        'model.stub',
        'notification.stub',
        'notifications-table.stub',
        'observer.plain.stub',
        'observer.stub',
        'pest.browser.stub',
        'pest.dataset.stub',
        'pest.stub',
        'pest.unit.stub',
        'policy.plain.stub',
        'policy.stub',
        'provider.stub',
        'queue-batches-table.stub',
        'queue-failed-table.stub',
        'queue-table.stub',
        'request.stub',
        'resource-collection.stub',
        'resource-json-api.stub',
        'resource.stub',
        'rule.implicit.stub',
        'rule.stub',
        'scope.stub',
        'seeder.stub',
        'service.stub',
        'session-table.stub',
        'test.stub',
        'test.unit.stub',
        'trait.stub',
        'view-component.stub',
        'view-mail.stub',
        'view.pest.stub',
        'view.stub',
        'view.test.stub',
    ];

    foreach ($required as $stub) {
        expect(dirname(__DIR__, 2) . '/stubs/' . $stub)->toBeFile();
    }
});
