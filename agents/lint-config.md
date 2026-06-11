---
id: lint-config
title: Lint and static analysis configuration
scope: .php-cs-fixer.config.php, .php-stan.config.neon, run, scripts/Application.php, scripts/Commands/, scripts/helpers.php, packages/*/composer.json
triggers:
  - changing the shared php-cs-fixer configuration
  - changing the shared PHPStan configuration
  - modifying dev CLI commands or shared helpers
  - adding or changing `run` command aliases
  - understanding how lint runs per-package vs root
  - adding or changing package lint scripts
---

# lint-config

Manage the lint and static analysis setup for br-utils-php packages. All paths are relative to the repo root.

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
- Paths: resolved per-package by the `lint:check` command (`scripts/Commands/LintCheckCommand.php`)

### Dev CLI (`run`)

Monorepo dev commands are implemented with **Symfony Console**. The entry point is [`run`](run); it boots [`scripts/Application.php`](scripts/Application.php), which registers command classes under [`scripts/Commands/`](scripts/Commands/). Shared utilities (path resolution, process spawning, git helpers) live in [`scripts/helpers.php`](scripts/helpers.php).

| Command | Class | Purpose |
|---------|-------|---------|
| `lint:format` | `LintFormatCommand` | Runs php-cs-fixer on monorepo paths |
| `lint:check` | `LintCheckCommand` | Runs PHPStan on monorepo paths |
| `lint` | `LintCommand` | Runs `lint:format` then `lint:check` |
| `lint:ci` | `LintCiCommand` | Dry-run format + static analysis (CI equivalent) |
| `lint:staged` | `LintStagedCommand` | CaptainHook pre-commit — lint only git-staged files |
| `lint:staged:test` | `LintStagedTestCommand` | Exercise `lint:staged` against the current git index |
| `phpstan:staged` | `PhpStanStagedCommand` | Run PHPStan only on git-staged PHP files |
| `deps` | `DepsCommand` | Internal `lacus/*` dependency graph |
| `release` | `ReleaseCommand` | Extract release notes from a package `CHANGELOG.md` |

Package scripts delegate to `run` with the package name as a path argument:

```json
"lint:format": "@php ../../run lint:format cnpj-fmt",
"lint:check": "@php ../../run lint:check cnpj-fmt"
```

The package name argument tells the command which `packages/<pkg>/` directory to use for tool resolution.

## Per-package `composer.json` lint scripts

Every package must define these four scripts:

```json
"scripts": {
    "lint": ["@lint:format", "@lint:check"],
    "lint:ci": ["@lint:format --dry-run", "@lint:check --dry-run"],
    "lint:format": "@php ../../run lint:format <pkg-name>",
    "lint:check": "@php ../../run lint:check <pkg-name>"
}
```

- `lint` — applies style fixes and runs static analysis
- `lint:ci` — dry-run (checks without mutating); used in CI and pre-push hook

Do not add extra lint scripts or change the invocation pattern without updating the shared commands.

## Running lint

### From the subrepo root:

```bash
# All packages
php run lint:ci               # dry-run check
php run lint:format           # apply style fixes to all packages
php run lint:check            # run PHPStan on all packages

# Equivalent via Composer
composer run lint:ci
composer run lint:format
composer run lint:check
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

The pre-commit hook runs `php run lint:staged` to lint only the staged files. The pre-push hook runs `composer run lint:ci` from the subrepo root (equivalent to `php run lint:ci`). Config: [`.captainhook.config.json`](.captainhook.config.json).

## Adding a new dev command

When adding a new root orchestration command that should be user-facing:

1. Create a command class in [`scripts/Commands/`](scripts/Commands/) extending `Symfony\Component\Console\Command\Command`.
2. Register it in [`scripts/Application.php`](scripts/Application.php).
3. Add a root `composer.json` script that delegates to `@php run <command>` when appropriate.
4. Extend [`scripts/run.test.php`](scripts/run.test.php) if the new command has non-trivial routing behavior.

Package-level `composer.json` scripts call `php ../../run <command> <pkg-name>` directly — they do not need a separate script file per package.

## When to extend the shared config

**Edit root commands, helpers, or config** only when the change applies to **all** packages (e.g. adding a new php-cs-fixer rule, changing the PHPStan level).

**Add a package-level override** only when the package genuinely cannot follow the root config. Document the reason in the package's `composer.json` or `AGENTS.md`.

## Checklist

- [ ] Package has `lint`, `lint:ci`, `lint:format`, `lint:check` scripts in `composer.json`
- [ ] Scripts delegate to `../../run lint:format <pkg>` and `../../run lint:check <pkg>`
- [ ] No per-package `.php-cs-fixer.php` or `phpstan.neon` added
- [ ] `composer run lint:ci` passes from the package directory

## Package-level overrides

Before applying this harness, check whether the target package defines `packages/<pkg>/AGENTS.md` or `packages/<pkg>/agents/`. If either exists and contradicts this file on the same topic, **follow the package-level instruction** (see [`agents/README.md`](README.md#instruction-precedence)).

## Reference

| Concern | Path |
|---------|------|
| CLI entry | `run` → `scripts/Application.php` |
| Command classes | `scripts/Commands/` |
| Shared helpers | `scripts/helpers.php` |
| Shared CS fixer config | `.php-cs-fixer.config.php` |
| Shared PHPStan config | `.php-stan.config.neon` |
| Format orchestration | `php run lint:format` (`LintFormatCommand`) |
| Static analysis orchestration | `php run lint:check` (`LintCheckCommand`) |
| Staged-file lint | `php run lint:staged` (`LintStagedCommand`) |
| CLI smoke tests | `scripts/run.test.php` |
| Git hook config | `.captainhook.config.json` |
| Canonical package scripts | `packages/cnpj-fmt/composer.json` (`scripts`) |
