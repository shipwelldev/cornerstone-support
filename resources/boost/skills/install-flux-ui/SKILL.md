---
name: install-flux-ui
description: Install Flux UI dependencies and framework assets in a Cornerstone application. Use when a developer requests Flux installation or Flux Pro activation.
---

# Install Flux UI

Treat repeat runs as reconciliation: preserve valid existing setup and add only what is missing.

## 1. Establish The Baseline

Read `CODING_STANDARDS.md` and the current official Flux installation documentation. Inspect:

- Git status and the complete current diff.
- Installed Laravel, Livewire, Tailwind CSS, Flux, and Flux Pro versions from dependency manifests and lockfiles.
- Every first-party Blade layout that can host Livewire UI and the CSS entrypoints each layout loads.
- Existing Flux directives, Flux CSS imports, dark variants, appearance initialization, and typography configuration.
- The scripts behind `composer fix` and `composer verify`.

If the worktree has any changes, show the developer the status and ask whether to proceed before modifying files. Run `composer verify` as the baseline. If it fails, report the failures and ask whether to proceed with those known blockers.

The baseline is complete when the existing installation state, eligible layouts, CSS entrypoints, dependency compatibility, dirty files, and verification result are known.

## 2. Resolve Installation Choices

Compare the installed dependencies with the current Flux prerequisites. When a prerequisite is incompatible, propose the minimum upgrades and wait for explicit approval before changing Laravel, Livewire, Tailwind CSS, or related dependencies.

Ask the developer to choose Flux Free or Flux Pro unless their request already specifies an edition. Both editions begin with the public `livewire/flux` package.

When multiple eligible layouts exist, present them and ask which layout or layouts to configure. Configure only the selected layouts and their CSS entrypoints.

Flux owns appearance in every selected layout through `@fluxAppearance`. If custom code already initializes dark mode or changes the document's `dark` class, explain the conflict and ask before replacing or adapting that code.

Ask whether to adopt Flux's currently recommended Inter font. If accepted, use the installation documented by Flux and update only the selected layouts and their CSS entrypoints. Otherwise preserve the application's typography.

Choices are resolved when the edition, selected layouts, prerequisite changes, appearance conflicts, and typography decision are explicit.

## 3. Install Dependencies

Determine the current compatible stable release from the official documentation and Composer. When Flux is absent or behind that release, install or update it with the official unpinned command:

```shell
composer require livewire/flux
```

When Flux is already current, keep its installed constraint and reconcile the remaining setup without reinstalling it. Review every dependency-manifest and lockfile change before continuing; ask before accepting unrelated dependency movement.

When `livewire/flux-pro` is already installed but either Flux package is behind its current compatible stable release, use the official paired update command:

```shell
composer update livewire/flux livewire/flux-pro
```

For a new Flux Pro installation or missing local Pro authentication, first confirm `/auth.json` is ignored and add the ignore rule when it is missing. Never request, receive, or expose Flux credentials through chat, command arguments, logs, or source files. Then ask the developer to run this command directly in their own terminal:

```shell
php artisan flux:activate
```

When Pro is already installed and locally authenticated, skip activation. Otherwise wait for the developer to confirm activation, then inspect only non-secret results. Do not edit CI or deployment configuration; report that those environments need separately provisioned credentials.

Do not run `php artisan flux:publish`. Component customization and UI conversion are outside this installation.

Dependencies are installed when Composer records the selected edition, no secret is exposed or tracked, and every dependency change has been reviewed.

## 4. Configure Assets

Reconcile each selected layout idempotently:

- Place `@fluxAppearance` in `<head>` once.
- Place `@fluxScripts` before `</body>` once, after the layout's Livewire script directive when one is explicit.
- Preserve unrelated layout structure and behavior.

Reconcile each CSS entrypoint loaded by a selected layout, using the current paths from the official documentation. Calculate the relative POSIX import path from each entrypoint's directory to `vendor/livewire/flux/dist/flux.css`; do not copy a path calculated for a different directory. For the conventional `resources/css/app.css` entrypoint, Flux v2 uses:

```css
@import 'tailwindcss';
@import '../../vendor/livewire/flux/dist/flux.css';
@custom-variant dark (&:where(.dark, .dark *));
```

Keep each import and variant exactly once. Preserve application theme declarations and unrelated CSS. Apply the approved Inter configuration only when the developer selected it.

Assets are configured when every selected layout loads Flux appearance and scripts, every corresponding CSS entrypoint loads Flux styles and the dark variant, and no directive or declaration is duplicated.

## 5. Offer Flux Development Guidance

Offer to discover newly available Boost resources:

```shell
php artisan boost:update --discover
```

Explain that this lets the developer select Flux's official `fluxui-development` skill and may display other newly discovered package resources. Run it only when the developer accepts the offer.

Discovery is resolved when the developer either declines it or the accepted command completes and its selected resources are recorded.

## 6. Verify

Confirm the installed package state and inspect the complete diff. Check selected layouts and CSS entrypoints for required declarations and duplicates, then compile every Blade view:

```shell
php artisan view:cache
```

Run the frontend build command defined by the application's `package.json`, normally:

```shell
npm run build
```

Run the canonical workflows in order:

```shell
composer fix
composer verify
```

Review every correction from `composer fix`. If any later repair changes code, restart with `composer fix` before running `composer verify` again.

If targeted checks or final verification fail, keep the partial installation intact, report introduced failures separately from baseline failures, and ask the developer how to proceed. Keep repairs within the selected dependencies, layouts, and CSS entrypoints unless the developer approves broader work.

Report the installed edition and versions, configured layouts and CSS entrypoints, appearance and typography choices, Boost discovery outcome, verification result, and any remaining local Pro credential requirements.

Installation is complete only when the targeted checks and both canonical workflows pass. A precisely reported blocker leaves the installation incomplete.
