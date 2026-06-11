---
id: dependencies
title: Dependency policy
scope: packages/*/composer.json, packages/*/composer.lock, composer.json
triggers:
  - adding a new Composer dependency
  - adding a devDependency or changing require-dev
  - changing runtime require constraints
  - deciding whether a dependency is allowed
  - updating composer.lock
  - exploring the internal dependency graph
  - identifying downstream packages affected by a dep change
---

# dependencies

Manage dependencies in the br-utils-php monorepo following the rules below. All paths are relative to the **php/** subrepo root.

## Repository constraints

### Hard rules

- **Always ask the developer** before adding any new runtime or dev dependency to any package or the root. Do not assume approval is implied by any task description.
- Follow the strict **dependency direction** — upstream packages must not depend on downstream ones:

```
utils → {cpf,cnpj}-dv → {cpf,cnpj}-{fmt,gen,val} → {cpf,cnpj}-utils → br-utils
```

Reverse edges (e.g. `utils` importing from `cnpj-fmt`) are forbidden.

- Do not add per-package build, lint, or analysis tooling to `"require-dev"` of multiple packages individually. Shared dev tooling lives in the root `composer.json`.
- Each package has its own `composer.json` and `vendor/` — there is no hoisted workspace install. Internal dependencies must reference published Packagist versions, not path repositories.

### When developer approval is NOT needed

Bumping an **already-declared internal dependency** to a new published semver range that mirrors what a sibling package uses (e.g. upgrading `lacus/cnpj-dv: ~1.1.0` to `~1.2.0` across all packages) is safe to replicate without explicit approval. Verify the existing declaration before updating.

## Before changing dependencies

1. Check `packages/<pkg>/composer.json` `"require"` and `"require-dev"` fields.
2. Check the root `composer.json` `"require-dev"` to confirm shared tooling is not already available at root.
3. Identify downstream packages that will be affected by a new internal dep version bump — use `scripts/deps-tree.php` (see [Inspecting internal dependencies](#inspecting-internal-dependencies)).
4. Confirm the proposed edge respects [dependency direction](#dependency-direction-reference); a reverse edge will show up as an unexpected branch in the forward tree.
5. If approval is needed, stop and ask — do not speculatively add the dependency.

## Inspecting internal dependencies

`scripts/deps-tree.php` scans every `packages/*/composer.json`, builds the `lacus/*` dependency graph from declared constraints, and prints box-drawing trees to the console. Use it before adding or bumping internal deps to verify direction and blast radius.

```bash
# All packages — forward trees from roots (default)
php scripts/deps-tree.php

# All packages — who depends on whom
php scripts/deps-tree.php -r

# One package — its internal dependencies
php scripts/deps-tree.php cnpj-utils

# One package — packages that depend on it (e.g. before bumping utils)
php scripts/deps-tree.php -r utils

# Include require-dev edges (marked [dev])
php scripts/deps-tree.php --dev
```

Package names accept the folder name (`cnpj-utils`) or Composer name (`lacus/cnpj-utils`). Only `"require"` edges are shown unless `--dev` is passed.

## Internal dependencies (Packagist versioning)

PHP packages depend on each other via published Packagist semver constraints — not path repositories or workspace symlinks.

### Version constraint convention

Use **tilde notation** (`~X.Y.Z`) for BR Utils monorepo packages (`lacus/cpf-*`, `lacus/cnpj-*`, `lacus/br-utils`):

```json
{
  "require": {
    "php": "^8.2",
    "lacus/cnpj-dv": "~1.1.0",
    "lacus/utils": "^1.0"
  }
}
```

Tilde notation allows only patch-level updates within the pinned minor line (`~1.1.0` accepts `>=1.1.0 <1.2.0`). This prevents unexpected minor-version feature additions from propagating between BR Utils packages without explicit constraint bumps and changelog visibility.

**Exception — `lacus/utils`:** Use **caret notation** (`^X.Y`). `lacus/utils` is a standalone foundation package (planned to detach from this monorepo) with its own release roadmap. BR Utils packages should accept minor-version updates from `lacus/utils` — bug fixes and new features alike — while still excluding major versions (`^1.0` accepts `>=1.0.0 <2.0.0`).

When bumping a **tilde-constrained** internal dependency, specify the **full `X.Y.Z` version** based on the currently published release — look up the latest tag with:

```bash
cd php && git tag -l 'lacus/<pkg>@*' | sort -V | tail -n 1
```

Strip the `lacus/<pkg>@` prefix to get the bare SemVer (e.g. `1.1.0`), then write `~1.1.0`. For `lacus/utils`, bump the caret minor line instead (e.g. `^1.0` → `^1.1`) when a new minor release should be adopted.

After editing any `composer.json` dependency field, install and regenerate the lockfile:

```bash
composer install --working-dir=packages/<pkg>
```

This updates `packages/<pkg>/composer.lock`. Commit the updated lockfile alongside the `composer.json` change.

## Dependency direction reference

| Package | Allowed upstream deps |
|---------|----------------------|
| `utils` | (none — foundation) |
| `{cpf,cnpj}-dv` | `lacus/utils` |
| `{cpf,cnpj}-{fmt,gen,val}` | `lacus/utils`, same-domain `-dv` |
| `{cpf,cnpj}-utils` | all same-domain leaf packages |
| `br-utils` | `lacus/cpf-utils`, `lacus/cnpj-utils` (and by extension all leaves) |

## Root `"require-dev"` (shared dev tooling)

The root `composer.json` holds dev tooling used across all packages:

- `friendsofphp/php-cs-fixer` — code style
- `phpstan/phpstan` — static analysis
- `captainhook/captainhook` — git hooks
- `ramsey/conventional-commits` — commit message validation
- `symfony/process`, `symfony/filesystem`, `symfony/console` — used by lint scripts

Package-level `"require-dev"` holds only the test runner for that package: `pestphp/pest` (v2 packages) or `phpunit/phpunit` (v1 legacy).

## Changelog

Adding or bumping a runtime `"require"` constraint in `packages/<pkg>/composer.json` is user-facing and requires a CHANGELOG entry (see [`agents/changelogs.md`](changelogs.md)). Changing `"require-dev"` or `"scripts"` only does not require a CHANGELOG entry.

## Package-level overrides

Before applying this harness, check whether the target package defines `packages/<pkg>/AGENTS.md` or `packages/<pkg>/agents/`. If either exists and contradicts this file on the same topic, **follow the package-level instruction** (see [`agents/README.md`](README.md#instruction-precedence)).

## Reference

| Concern | File |
|---------|------|
| Internal dependency graph (CLI) | `scripts/deps-tree.php` |
| Root dev tooling | `composer.json` `"require-dev"` |
| Package runtime deps | `packages/<pkg>/composer.json` `"require"` |
| Package lockfile | `packages/<pkg>/composer.lock` |
| Canonical package config | `packages/cnpj-fmt/composer.json` |
