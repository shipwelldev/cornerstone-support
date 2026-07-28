# Cornerstone Support

`shipwelldev/cornerstone-support` supplies the evolving tooling used by [Cornerstone](https://github.com/shipwelldev/cornerstone) Laravel applications after their starter snapshot is created.

The package provides:

- Standards-compliant Artisan generators and adapters for Laravel, Livewire, and Pest artifacts.
- A publishable set of Cornerstone generator stubs.
- Laravel Boost guidelines that preserve Cornerstone's application conventions.
- Guided installation skills for Flux UI, Filament, and Statamic.

## Requirements

- PHP 8.4 or 8.5
- Laravel 13.20+ within the 13.x release line
- Livewire 4.3+ within the 4.x release line

## Installation

Install the package with Composer:

```shell
composer require shipwelldev/cornerstone-support
```

Laravel discovers the package service provider automatically.

Pest and its Laravel plugin are optional package suggestions, but they are required before using Cornerstone's test generators or generated Livewire tests:

```shell
composer require --dev pestphp/pest pestphp/pest-plugin-laravel
```

Install `pestphp/pest-plugin-browser` as a development dependency before generating browser tests.

## Generators

The package adds Cornerstone generators such as:

```shell
php artisan make:data MissionBrief
php artisan make:service PlanExpedition
```

It also reconciles supported framework generators with Cornerstone's coding standards, including strict types, application placement, model metadata, class-based Livewire components with direct Pest tests, and Pest-only test generation.

Run `php artisan list` to inspect every generator available in the installed application.

## Stub Snapshots

Publish any missing Cornerstone stubs into the application's `stubs` directory:

```shell
php artisan cornerstone:stubs
```

Published stubs become application-owned snapshots. The command preserves existing local stubs unless forced. To replace every same-named file in the application's `stubs` directory with the package version, explicitly run:

```shell
php artisan cornerstone:stubs --force
```

Review local customizations before using `--force`.

## Laravel Boost

Cornerstone Support exposes application guidelines and installation skills through Laravel Boost. Install Boost before discovering those resources:

```shell
composer require --dev laravel/boost
```

Then run:

```shell
php artisan boost:update --discover
```

The available installer skills guide agents through safe, repeatable setup for Flux UI, Filament, and Statamic while preserving existing application code.

## Development

Install dependencies and run the canonical workflows in order:

```shell
composer install
composer fix
composer verify
```

`composer verify` checks formatting, static analysis, and the complete Pest test suite. Use `composer test -- --tia` to run only tests affected by local changes using the baseline published by CI.

## License

Cornerstone Support is open-source software licensed under the [MIT license](LICENSE.md).
