---
name: install-filament
description: Install Filament dependencies and framework configuration in a Cornerstone application. Use when a developer requests Filament's panel builder or individual components.
---

# Install Filament

Treat repeat runs as reconciliation: preserve valid existing setup and add only what is missing.

## 1. Establish The Baseline

Read `CODING_STANDARDS.md` and the current official Filament installation, panel configuration, and user authorization documentation. Inspect:

- Git status and the complete current diff.
- Installed PHP, Laravel, Livewire, Tailwind CSS, Filament, and Filament plugin versions from dependency manifests and lockfiles.
- Existing Filament packages, panel providers, published configuration, assets, directives, and tests.
- Every first-party Blade layout that can host Filament components and the CSS and JavaScript entrypoints each layout loads.
- The configured authentication model, its factory, its existing contracts, and its authorization behavior.
- Existing routes that could conflict with a requested panel path.
- The scripts behind `composer fix` and `composer verify` and the frontend scripts in `package.json`.

If the worktree has any changes, show the developer the status and ask whether to proceed before modifying files. Run `composer verify` as the baseline. If it fails, report the failures and ask whether to proceed with those known blockers.

The baseline is complete when the installation mode, dependency compatibility, existing Filament state, eligible layouts and entrypoints, authentication boundary, route conflicts, dirty files, and verification result are known.

## 2. Resolve Installation Choices

Compare installed dependencies with Filament's current prerequisites. When a prerequisite is incompatible, propose the minimum upgrades and wait for explicit approval before changing PHP, Laravel, Livewire, Tailwind CSS, or related dependencies.

Ask whether the developer wants the panel builder or individual components unless their request already makes the mode explicit.

For panel mode:

- Ask for the panel ID and path. Prefer a lowercase kebab-case ID and validate it before using it as a command argument.
- Check whether that panel ID or its provider already exists. Confirm that the developer intends to reconcile it rather than create another panel.
- Check the requested path against application and existing Filament routes. Resolve every collision before installation, including an empty path that would own `/`.
- Ask which configured authentication model the panel uses when it is not unambiguous.
- Require a concrete production admission rule for `canAccessPanel()`. Do not invent a role, email domain, permission, or unconditional allow.

For individual-component mode:

- Ask which exact component packages to install from the packages in the current official documentation. Include `filament/support` as the Blade UI-only choice.
- Present every eligible layout and ask which layouts to configure, even when only one is eligible. Configure only the selected layouts and their entrypoints.
- If `filament/notifications` is selected, ask whether the selected layouts should render flash notifications.
- Explain any existing dark-mode, asset, Alpine, Livewire, or directive conflict and resolve it explicitly before replacement or adaptation.

Choices are resolved when the mode, prerequisites, package set or panel identity, selected layouts, route and asset conflicts, and panel admission rule when applicable are explicit.

## 3. Install Dependencies

For a new installation, determine the currently documented compatible stable major from official Filament documentation and Composer. Use the explicit stable-major constraint documented for the current shell instead of an unpinned require. Do not hard-code the major from this skill when newer official documentation applies.

For panel mode, require `filament/filament` with that constraint. For example, when the current documented major is 5 in a POSIX shell:

```shell
composer require filament/filament:"^5.0"
```

For individual-component mode, build one `composer require` command containing only the selected official packages with the same documented major constraint. Let Composer install their required shared dependencies; do not add unrelated Filament packages for convenience.

When the selected packages are already installed on an officially supported major, preserve that major by default and reconcile setup without reinstalling them. Update within that major when needed for the current compatible stable release. Treat a Filament major upgrade as separate migration work: review its upgrade guide, plugin compatibility, and application impact, then wait for explicit approval before changing the major constraint.

Review every dependency-manifest and lockfile change before continuing. Ask before accepting unrelated dependency movement.

Dependencies are installed when Composer records the selected mode or component set at the current compatible stable release and every dependency change has been reviewed.

## 4. Configure Panel Mode

Install Filament's base assets without generating the conventional admin panel:

```shell
php artisan filament:install --no-interaction
```

When a provider for the selected panel ID does not exist, create it explicitly:

```shell
php artisan make:filament-panel <panel-id> --no-interaction
```

Pass the validated ID in place of `<panel-id>`; never run the placeholder literally. Do not use `--force`. When the panel already exists, preserve and reconcile its provider instead of running the generator. Confirm the selected provider is registered exactly once in `bootstrap/providers.php`.

Reconcile the generated panel provider with the application standards and requested behavior:

- Add `declare(strict_types=1);` and apply every structurally determinable rule from `CODING_STANDARDS.md`.
- Preserve the generated panel ID and set the approved path explicitly when it differs from the ID.
- Enable `strictAuthorization()` so missing resource policies or policy methods fail closed.
- Preserve the default authenticated panel middleware and login unless the developer explicitly requested a different, fully specified authentication design.

Publish the shared configuration when `config/filament.php` is absent:

```shell
php artisan vendor:publish --tag=filament-config --no-interaction
```

Preserve an existing configuration file and reconcile only concrete settings required by this installation. Review a newly published file as application-owned code.

Implement `Filament\Models\Contracts\FilamentUser` on the selected authentication model and implement the developer's concrete `canAccessPanel()` rule. Preserve existing panel-specific admission behavior and account for the new panel by ID. An unresolved production admission rule is a blocker, not a reason to return `true`.

Add a focused Pest feature test at the public HTTP seam. Use the authentication model's factory and meaningful states to verify the configured path, guest behavior, an admitted user, and a denied user. Ensure the assertions exercise the chosen `canAccessPanel()` rule rather than restating provider configuration.

Offer to create the first Filament user. Because the command prompts for identity and password data, ask the developer to run it directly in their own terminal:

```shell
php artisan make:filament-user
```

Never request, receive, expose, or place those credentials in source, chat, logs, fixtures, or command arguments. Continue after the developer declines or confirms completion; user creation is optional and does not replace the admission test.

Panel mode is configured when the provider is registered, its ID and path are collision-free, strict resource authorization and production panel admission are enforced, configuration is published, and the focused test passes.

## 5. Configure Individual Components

Run the non-destructive asset installer:

```shell
php artisan filament:install --no-interaction
```

Never run `php artisan filament:install --scaffold`; Cornerstone and user-owned application files must be reconciled rather than overwritten.

For each selected layout and its entrypoints, follow the current official existing-application instructions:

- Add `@filamentStyles` in `<head>` exactly once.
- Add `@filamentScripts` near the end of `<body>` exactly once while preserving the existing Vite and Livewire directive order required by current documentation.
- Add `@livewire('notifications')` exactly once only when `filament/notifications` is installed and the developer selected flash notifications.
- Import only the CSS required by the installed selected packages and their Composer-installed Filament dependencies. Calculate every relative POSIX path from that CSS entrypoint to the relevant file under `vendor/filament`; do not reuse a path calculated for another entrypoint.
- Reconcile the current documented dark variant and existing application appearance behavior without duplicate declarations.
- Ensure the Tailwind Vite plugin and JavaScript entrypoint match current Filament prerequisites while preserving unrelated Vite configuration.

Do not create example components, schemas, forms, tables, widgets, actions, pages, resources, application migrations, or UI conversions during installation.

Individual-component mode is configured when every selected layout loads Filament styles and scripts, its entrypoints include exactly the assets required by the installed component set, optional notifications match the developer's choice, and no declaration is duplicated.

## 6. Offer Filament Development Guidance

Offer to discover newly available Boost resources:

```shell
php artisan boost:update --discover --no-interaction
```

Explain that discovery may offer official Filament guidance and other newly discovered package resources. Run it only when the developer accepts. If the installed Boost version requires interactive resource selection, ask the developer to run it directly in their own terminal instead. Record the selected resources.

Discovery is resolved when the developer either declines it or the accepted command completes and its selected resources are recorded.

## 7. Verify

Confirm the installed package state and inspect the complete diff. Check panel registration and routes in panel mode; check selected layouts, entrypoints, required declarations, and duplicates in component mode.

Run the focused panel test when panel mode added or changed one. Compile every Blade view:

```shell
php artisan view:cache --no-interaction
```

Run the frontend build command defined by `package.json`, normally:

```shell
npm run build
```

Run the canonical workflows in order:

```shell
composer fix
composer verify
```

Review every correction from `composer fix`. If any later repair changes code, restart with `composer fix` before running `composer verify` again.

If targeted checks or final verification fail, keep the partial installation intact, report introduced failures separately from baseline failures, and ask the developer how to proceed. Keep repairs within the selected packages, panel, authentication model, tests, layouts, and entrypoints unless the developer approves broader work.

Report the installed mode and versions, panel ID and path or component set, configured files, panel admission and user-creation outcome when applicable, Boost discovery outcome, verification result, and any remaining blockers.

Installation is complete only when the targeted checks and both canonical workflows pass. A precisely reported blocker leaves the installation incomplete.
