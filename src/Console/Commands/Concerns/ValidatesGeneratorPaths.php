<?php

declare(strict_types=1);

namespace ShipWell\CornerstoneSupport\Console\Commands\Concerns;

trait ValidatesGeneratorPaths
{
    protected function safeRelativePath(mixed $value, string $label): ?string
    {
        if ( ! is_string($value) || $value === '' || str_contains($value, "\0")) {
            $this->components->error("The {$label} must be a safe relative path.");

            return null;
        }

        $path = str_replace('\\', '/', $value);

        if (str_starts_with($path, '/') || preg_match('/^[A-Za-z]:\//', $path) === 1) {
            $this->components->error("The {$label} must be a safe relative path.");

            return null;
        }

        $segments = explode('/', $path);

        foreach ($segments as $segment) {
            if ($segment === '' || $segment === '.' || $segment === '..' || preg_match('/^[A-Za-z0-9_.-]+$/', $segment) !== 1) {
                $this->components->error("The {$label} must be a safe relative path.");

                return null;
            }
        }

        return implode('/', $segments);
    }

    protected function pathIsWithinApplication(string $path): bool
    {
        $basePath = realpath($this->laravel->basePath());

        if ($basePath === false) {
            return false;
        }

        $ancestor = $path;

        while ( ! file_exists($ancestor)) {
            $parent = dirname($ancestor);

            if ($parent === $ancestor) {
                return false;
            }

            $ancestor = $parent;
        }

        $resolvedAncestor = realpath($ancestor);

        return $resolvedAncestor !== false
            && ($resolvedAncestor === $basePath || str_starts_with($resolvedAncestor, $basePath . DIRECTORY_SEPARATOR));
    }
}
