---
name: install-statamic
description: Install Statamic Core or Pro dependencies and framework configuration in an existing Cornerstone application, with optional starter-kit and add-on phases.
---

# Install Statamic

Treat repeat runs as reconciliation: preserve valid existing setup and add only what is missing.

This skill installs Statamic into the current Cornerstone application. Never create a separate project or silently convert existing content, routes, views, or authentication.

## 1. Establish The Baseline

Read `CODING_STANDARDS.md` and the current official Statamic existing-Laravel installation, requirements, licensing, Control Panel, user storage, content storage, REST API, GraphQL, starter-kit, add-on, and upgrade documentation. Inspect:

- Git status and the complete current diff.
- Installed PHP, Laravel, Statamic, Eloquent driver, starter-kit, and Statamic add-on versions from dependency manifests and lockfiles.
- Composer lifecycle scripts, cached configuration, published Statamic configuration, `content/`, `resources/users/`, application users, authentication providers and guards, migrations, and database connection.
- Existing application, Statamic, Control Panel, REST, GraphQL, and catch-all routes, including every collision with the proposed paths.
- Existing collections, repositories, sites, content, views, layouts, assets, and frontend entrypoints.
- Statamic edition and license configuration without reading or displaying license values.
- The scripts behind `composer fix` and `composer verify` and the frontend scripts in `package.json`.

If the worktree has any changes, show the developer the status and ask whether to proceed before modifying files. Run `composer verify` as the baseline. If it fails, report the failures and ask whether to proceed with those known blockers.

The baseline is complete when dependency compatibility, edition, installation mode, Control Panel path, route ownership, repository state, identity boundary, API exposure, existing Statamic state, dirty files, and verification result are known.

## 2. Resolve Installation Choices

Compare installed dependencies with Statamic's current prerequisites. When a prerequisite is incompatible, propose the minimum upgrades and wait for explicit approval before changing PHP, Laravel, Inertia, or related dependencies.

Ask whether the developer wants Statamic Core or Pro unless their request already specifies the edition. Explain the current documented limits and features of each edition. Core and Pro use the same `statamic/cms` package; do not add a separate Pro package.

Ask whether the installation uses site mode or headless mode:

- In site mode, ask for the exact public route boundary Statamic will own. Check explicit and catch-all application routes, preserve routes outside that boundary, and do not invent collections, content, views, or layouts.
- In headless mode, preserve all existing public page rendering. Ask which exact content interfaces and resources to expose. Keep every unselected API and resource disabled.

Only offer an interface supported by the selected edition in the current documentation. For each external interface, resolve its versioned route, exposed resources and sub-resources, allowed filters and fields, authentication, and a concrete rate limit. Never expose users or wildcard resources by convenience. If an interface needs a token, require the developer to provision it directly in each environment without sharing it through chat or command arguments.

Always ask the developer to confirm the Control Panel path, even when the current documented default is available. Validate the path, inspect application and Statamic routes, and resolve every route collision before installation.

Ask whether content remains in flat files or uses the Eloquent driver. For a new database-backed installation, ask which currently supported repositories to store in the database and preserve flat-file repositories that were not selected. Confirm the database connection and populated-schema impact before generating migrations. Treat changing an existing repository as separate storage migration work and do not import, export, or move existing content during reconciliation.

Ask whether Statamic uses native users or a shared application identity:

- For native users, confirm file or database persistence and preserve the application's existing authentication system.
- For a shared application identity, inspect the configured user model, key type, casts, factory, password brokers, sessions, roles, permissions, and existing authorization behavior. Use one identity rather than synchronizing duplicate users.

Require a concrete production Control Panel admission rule. Use Statamic's current supported authorization model and the selected edition's capabilities. Never invent a role, permission, email rule, or unconditional super-user grant, and never grant every application user Control Panel access.

Choices are resolved when prerequisites, edition, site or headless mode, public route ownership, Control Panel path, repository selection, identity model, admission rule, API exposure, authentication, and rate limits are explicit.

## 3. Install Dependencies

For a new installation, determine the current compatible stable Statamic major from official documentation and Composer. Follow the current existing-Laravel instructions rather than hard-coding a major from this skill.

Before requiring Statamic, clear cached configuration and idempotently reconcile the Composer lifecycle hooks required by the current official documentation. Preserve unrelated scripts and keep every hook exactly once. At the time this skill was written, the existing-Laravel workflow requires `Statamic\Console\Composer\Scripts::preUpdateCmd` in `pre-update-cmd` and `@php artisan statamic:install --ansi` in `post-autoload-dump`; verify those instructions are still current before editing.

Run the current official existing-application install command:

```shell
php artisan config:clear --no-interaction
composer require statamic/cms --with-dependencies
```

When Statamic is already installed on an officially supported major, preserve that major and reconcile the remaining setup without reinstalling it. Update within that major only when needed for the current compatible stable release. Treat a Statamic major upgrade as separate migration work: read every applicable upgrade guide, check add-on and starter-kit compatibility, and wait for explicit approval before changing the major constraint.

Review every dependency-manifest, lifecycle-script, and lockfile change before continuing. Ask before accepting unrelated dependency movement.

Dependencies are installed when Composer records a supported Statamic release, required lifecycle hooks are present exactly once, package discovery and Statamic installation complete, and every dependency change has been reviewed.

## 4. Configure Edition, Routes, And Content Interfaces

Preserve existing Statamic configuration and reconcile only settings required by the approved choices. Review newly published or generated files as application-owned code.

Keep Core disabled for Pro features. When Pro is selected and not already enabled, use the current official command:

```shell
php please pro:enable --no-interaction
```

Pro may be used in documented local trial conditions. Production Pro and commercial add-ons require a site license. Never request, receive, expose, or place license keys in source, chat, logs, fixtures, or command arguments. Ask the developer to provision `STATAMIC_LICENSE_KEY` directly in each deployment environment and confirm only that provisioning is complete. Do not edit CI or deployment secrets.

Set the approved Control Panel path through the current documented configuration mechanism. Confirm its routes are registered once and do not collide with application routes.

In site mode, configure only the approved public route boundary and explicitly specified content model and views. Existing explicit Laravel routes must retain their approved precedence. Do not create example content or claim `/` through a catch-all unless the developer explicitly approved that ownership.

In headless mode, leave Statamic public page rendering outside the application's route boundary. Enable only the selected REST or GraphQL interfaces and only their selected resources and sub-resources. Keep filters disabled unless exact allowed filters were approved. Apply the approved versioned route, authentication, and rate limit, then verify unselected interfaces and resources remain unavailable.

Route and interface configuration is complete when the Control Panel and selected public seams are collision-free, only approved resources are reachable, and authentication and rate limiting match the developer's decisions.

## 5. Configure Repositories And Users

For flat-file content, preserve Statamic's current file repositories and do not install the Eloquent driver merely because the application has a database.

For newly selected database repositories, inspect the current command help and official Eloquent driver documentation before running:

```shell
php please install:eloquent-driver
```

Use documented non-interactive options when they can express the exact approved repository set, including `--no-interaction`. Otherwise ask the developer to run the interactive command shown above directly in their own terminal and select only the approved repositories. Review package, configuration, and migration changes before running migrations. Do not import existing content; that is storage migration work.

For database-backed native users or a shared application identity, follow the current existing-Laravel user-storage instructions. Reconcile the authentication provider, password brokers, selected user model metadata, key types, sessions, roles, groups, and permissions without replacing unrelated authentication behavior.

Generate required authentication migrations with:

```shell
php please auth:migration --no-interaction
```

Inspect every generated migration for populated-database safety, duplicate schema changes, matching user key types, indexes, and existing columns before running it. Do not import existing file users during installation. Apply `CODING_STANDARDS.md` to application-owned model and migration changes.

Implement the approved production Control Panel admission behavior. Add focused Pest feature tests at the public HTTP seam for the configured Control Panel path, guest behavior, an admitted user, and a denied user. In site mode, test the approved public rendering boundary and preserved application routes. In headless mode, prove existing public application routes remain application-owned, test each selected interface's authentication and resources, and verify representative unselected resources remain unavailable. Use the application user factory for shared identities and exercise the actual admission rule rather than restating configuration.

Offer to create the first Control Panel user when the selected identity model requires one. Because the command prompts for identity and password data, ask the developer to run it directly in their own terminal:

```shell
php please make:user
```

Never request, receive, expose, or place those credentials in source, chat, logs, fixtures, or command arguments. User creation is optional and does not replace admission tests.

Repositories and users are configured when selected repositories are active without implicit migration, authentication preserves one approved identity model, Control Panel admission fails closed, migrations are reviewed and applied when required, and focused tests pass.

## 6. Offer Starter-Kit Adoption

Starter-kit adoption is a separate optional post-installation phase. Core Statamic installation can complete when the developer declines it.

If accepted, require an exact developer-selected package. Read that kit's current documentation and verify Statamic version, edition, add-on, license, frontend, and existing-site compatibility. Show the current status and complete diff again. If the worktree is dirty, explain that the kit may broadly overwrite application-owned routes, views, assets, configuration, and content, then wait for explicit approval before proceeding.

For private or paid kits, never request authentication or license data. Ask the developer to authenticate and run any credential-bearing or interactive licensing step directly in their own terminal.

Install an approved kit into the existing application using the exact validated package in place of the placeholder:

```shell
php please starter-kit:install vendor-name/starter-kit-name --no-interaction
```

Never run `statamic new`; this skill does not create another project. Never use `--clear-site` without separate explicit approval after listing the exact existing content and files that clearing can remove. Do not pass `--without-dependencies` unless the kit documentation and developer explicitly require it. If a paid kit requires an interactive license step that cannot be expressed non-interactively, ask the developer to run it directly instead of removing `--no-interaction` from an agent-run command.

Review the complete resulting diff, dependency movement, routes, content, migrations, build scripts, and application-owned files. Reconcile standards without undoing intentional kit behavior. Starter-kit adoption is complete only when its own documented checks and the verification workflow below pass.

## 7. Offer Add-on Installation

Add-on installation is a separate optional post-installation phase. Core Statamic installation can complete when the developer declines it.

If accepted, require an exact developer-selected package rather than choosing from the marketplace. Read its current official or vendor documentation and verify Statamic and Laravel compatibility, edition, license, dependencies, install command, configuration, migrations, routes, assets, and file impact.

Use the package's documented install command. Most add-ons use Composer:

```shell
composer require vendor/package
```

Replace the placeholder with the exact validated package and never execute it literally. Some first-party add-ons use dedicated `php please install:*` commands; use one only when the current package documentation requires it. For private or commercial add-ons, require the developer to complete authentication and licensing directly without exposing credentials. Review every changed file and dependency before continuing.

Add-on installation is complete only when the selected package's documented setup, focused behavior tests, and the verification workflow below pass.

## 8. Offer Statamic Development Guidance

Offer to discover newly available Boost resources:

```shell
php artisan boost:update --discover --no-interaction
```

Explain that discovery may offer Statamic or add-on guidance and other newly discovered package resources. Run it only when the developer accepts. If the installed Boost version requires interactive resource selection, ask the developer to run it directly in their own terminal instead. Record the selected resources.

Discovery is resolved when the developer either declines it or the accepted command completes and its selected resources are recorded.

## 9. Verify

Confirm installed package versions, Composer lifecycle hooks, edition state without exposing a license, Control Panel path, repository bindings, authentication configuration, routes, selected APIs, and disabled resources. Inspect the complete diff and run all focused tests added or changed by the installation.

Compile application views and run any current Statamic cache or repository health commands required by the installed version. Run the frontend build only when frontend files changed, using the command defined by `package.json`, normally:

```shell
php artisan view:cache --no-interaction
npm run build
```

Run the canonical workflows in order:

```shell
composer fix
composer verify
```

Review every correction from `composer fix`. If any later repair changes code, restart with `composer fix` before running `composer verify` again.

If targeted checks or final verification fail, keep the partial installation intact, report introduced failures separately from baseline failures, and ask the developer how to proceed. Keep repairs within Statamic dependencies, lifecycle scripts, configuration, selected repositories, authentication, routes, tests, and explicitly accepted optional packages unless the developer approves broader work.

Report the installed Statamic version and edition, site or headless mode, Control Panel path, public route ownership, repositories, identity model and admission behavior, enabled interfaces and resources, first-user outcome, starter-kit and add-on outcomes, Boost discovery outcome, frontend build outcome, verification result, and any remaining environment license requirements without exposing secret values.

Installation is complete only when targeted checks and both canonical workflows pass. A precisely reported blocker leaves the installation incomplete.
