---
id: lint-config
title: Lint and static analysis configuration
scope: .php-cs-fixer.config.php, .php-stan.config.neon, scripts/lint-*.php, packages/*/composer.json
triggers:
  - changing the shared php-cs-fixer configuration
  - changing the shared PHPStan configuration
  - modifying lint orchestration scripts
  - understanding how lint runs per-package vs root
  - adding or changing package lint scripts
---

# lint-config

Manage the lint and static analysis setup for br-utils-php packages. All paths are relative to the **php/** subrepo root.

> PHP packages have **no build step** — there is no `dist/` output. This harness replaces the JS `build-config` harness. The equivalent of "building" is linting and type-checking.

## Repository constraints

- **Do not duplicate lint config in packages.** Shared logic lives in root config files and `scripts/`; packages only invoke them by passing their own name as an argument.
- **Do not add per-package php-cs-fixer or PHPStan config files** unless a package genuinely cannot share the root config (document the reason if you must diverge).
- Lint and CI config changes are **dev-only** — they do not require a CHANGELOG entry.

## Shared lint setup

### `php-cs-fixer` (code style)

Config: [`.php-cs-fixer.config.php`](.php-cs-fixer.config.php)

Key rules:
- `@PSR12` — enforces the PSR-12 coding standard
- `array_syntax` → `short` — use `[]` not `array()`
- `trailing_comma_in_multiline` — trailing commas in arrays and function calls
- `ordered_imports` — alphabetically sorted `use` statements
- `blank_line_before_statement` — blank line before control structures

The config dynamically discovers `src/`, `tests/`, and `scripts/` directories in the working directory, so it works from both root and any package directory.

### PHPStan (static analysis)

Config: [`.php-stan.config.neon`](.php-stan.config.neon)

Key settings:
- Level: **10** (maximum strictness)
- Paths: resolved per-package by `scripts/lint-check.php`

### Lint orchestration scripts (`scripts/`)

Packages do not call lint tools directly — they invoke root PHP scripts that handle tool resolution and argument parsing:

| Script | Called by | Purpose |
|--------|-----------|---------|
| `scripts/lint-format.php` | Package `lint:format` script | Runs php-cs-fixer on the package's files |
| `scripts/lint-check.php` | Package `lint:check` script | Runs PHPStan on the package's files |
| `scripts/lint-staged.php` | CaptainHook pre-commit | Lint only git-staged files |

Package scripts delegate to these via:

```json
"lint:format": "@php ../../scripts/lint-format.php cnpj-fmt",
"lint:check": "@php ../../scripts/lint-check.php cnpj-fmt"
```

The package name argument tells the script which `packages/<pkg>/vendor/` to use for tool resolution.

## Per-package `composer.json` lint scripts

Every package must define these four scripts:

```json
"scripts": {
    "lint": ["@lint:format", "@lint:check"],
    "lint:ci": ["@lint:format --dry-run", "@lint:check --dry-run"],
    "lint:format": "@php ../../scripts/lint-format.php <pkg-name>",
    "lint:check": "@php ../../scripts/lint-check.php <pkg-name>"
}
```

- `lint` — applies style fixes and runs static analysis
- `lint:ci` — dry-run (checks without mutating); used in CI and pre-push hook

Do not add extra lint scripts or change the invocation pattern without updating the shared scripts.

## Running lint

### From the subrepo root:

```bash
# All packages
composer run lint:ci        # dry-run check
composer run lint:format    # apply style fixes to all packages
composer run lint:check     # run PHPStan on all packages
```

### From a package directory (`packages/<pkg>/`):

```bash
# After composer install
composer run lint:ci        # dry-run check (CI equivalent)
composer run lint           # format + check
composer run lint:format    # apply style fixes
composer run lint:check     # PHPStan
```

## Git hooks (CaptainHook)

The pre-commit hook runs `scripts/lint-staged.php` to lint only the staged files. The pre-push hook runs `composer run lint:ci` from the subrepo root. Config: [`.captainhook.config.json`](.captainhook.config.json).

## When to extend the shared config

**Edit root scripts or config** only when the change applies to **all** packages (e.g. adding a new php-cs-fixer rule, changing the PHPStan level).

**Add a package-level override** only when the package genuinely cannot follow the root config. Document the reason in the package's `composer.json` or `AGENTS.md`.

## Checklist

- [ ] Package has `lint`, `lint:ci`, `lint:format`, `lint:check` scripts in `composer.json`
- [ ] Scripts delegate to `../../scripts/lint-format.php <pkg>` and `../../scripts/lint-check.php <pkg>`
- [ ] No per-package `.php-cs-fixer.php` or `phpstan.neon` added
- [ ] `composer run lint:ci` passes from the package directory

## Package-level overrides

Before applying this harness, check whether the target package defines `packages/<pkg>/AGENTS.md` or `packages/<pkg>/agents/`. If either exists and contradicts this file on the same topic, **follow the package-level instruction** (see [`agents/README.md`](README.md#instruction-precedence)).

## Reference

| Concern | Path |
|---------|------|
| Shared CS fixer config | `.php-cs-fixer.config.php` |
| Shared PHPStan config | `.php-stan.config.neon` |
| Format orchestration | `scripts/lint-format.php` |
| Static analysis orchestration | `scripts/lint-check.php` |
| Staged-file lint | `scripts/lint-staged.php` |
| Git hook config | `.captainhook.config.json` |
| Canonical package scripts | `packages/cnpj-fmt/composer.json` (`scripts`) |
