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

Bumping an **already-declared internal dependency** to a new published semver range that mirrors what a sibling package uses (e.g. upgrading `lacus/utils: ^1.0` to `^1.1` across all packages) is safe to replicate without explicit approval. Verify the existing declaration before updating.

## Before changing dependencies

1. Check `packages/<pkg>/composer.json` `"require"` and `"require-dev"` fields.
2. Check the root `composer.json` `"require-dev"` to confirm shared tooling is not already available at root.
3. Identify downstream packages that will be affected by a new internal dep version bump.
4. If approval is needed, stop and ask — do not speculatively add the dependency.

## Internal dependencies (Packagist versioning)

PHP packages depend on each other via published Packagist semver constraints — not path repositories or workspace symlinks:

```json
{
  "require": {
    "php": "^8.2",
    "lacus/utils": "^1.0"
  }
}
```

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
| Root dev tooling | `composer.json` `"require-dev"` |
| Package runtime deps | `packages/<pkg>/composer.json` `"require"` |
| Package lockfile | `packages/<pkg>/composer.lock` |
| Canonical package config | `packages/cnpj-fmt/composer.json` |
