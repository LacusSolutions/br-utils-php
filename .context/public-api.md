---
id: public-api
title: Public API change coordination
scope: packages/*/src/, packages/*/tests/, packages/*/README.md, packages/*/CHANGELOG.md
triggers:
  - adding, removing, or renaming a public class or method
  - changing a function or constructor signature
  - adding or changing options or defaults
  - changing a namespace visible to Packagist consumers
  - behavior changes visible to package consumers
  - reviewing a PR that modifies the public API
---

# public-api

This is a meta-checklist harness. When a change touches the public API of any `packages/*` package, use this file as the coordination checklist — it ties together the specialized harnesses that each govern one artifact type. All paths are relative to the repo root.

## What counts as a public API change

A change is public-API if it affects anything a downstream Composer consumer would observe:

- Adding, removing, or renaming an exported class, function, constant, or enum case
- Changing a method or function signature (parameter name, type, optionality, order)
- Adding or removing an option from an options class
- Changing a default value for an option or callback
- Changing thrown exception types or their class hierarchy
- Moving a class to a different namespace
- Changing `"autoload"` or `"autoload"."files"` in `composer.json` (alters what is autoloaded)
- Changing the minimum PHP version in `composer.json` `"require"`

Changes that are **not** public-API: test files, CI configs, internal-only private methods, `"require-dev"`, `"scripts"`, `composer.lock`, `.gitignore`.

## Coordinated artifacts checklist

For every public API change, work through the following artifacts in order:

| # | Artifact | Harness |
|---|----------|---------|
| 1 | Source (`src/`) changes + `Exceptions/` | [`.context/package-arch.md`](package-arch.md) |
| 2 | PHPDoc on all changed/new symbols | [`.context/phpdoc.md`](phpdoc.md) |
| 3 | Behavior unit tests | [`.context/unit-tests.md`](unit-tests.md) |
| 4 | README update (options table, usage example) | [`.context/readme-docs.md`](readme-docs.md) |
| 5 | CHANGELOG entry | [`.context/changelogs.md`](changelogs.md) |
| 6 | `composer.json` `"require"` / `"autoload"` (if changed) | [`.context/dependencies.md`](dependencies.md) |
| 7 | Domain parity check (if `cpf-*` / `cnpj-*`) | [`.context/domain-parity.md`](domain-parity.md) |
| 8 | Aggregator cascade (if sub-package changed) | [`.context/aggregator-package.md`](aggregator-package.md) |

> There is no step for distribution tests (`output.spec.*`) — PHP packages have no build step. The PSR-4 autoload contract is validated by the behavior tests and lint.

## Decision flow

```
src/ change?
  │
  ├─ yes → always update behavior tests (step 3)
  │
  └─ export surface change? (new/removed/renamed class, function, namespace)
       │
       ├─ yes → update README (step 4)
       │
       └─ user-facing? (src/, composer.json runtime keys, public README)
            │
            ├─ yes → add CHANGELOG entry (step 5)
            │
            └─ dev-only (tests, CI, lint, require-dev) → skip CHANGELOG
```

## Before starting

1. Identify all packages affected (direct change + any aggregator that wraps the changed package).
2. For each affected package, run through the 8-step checklist above.
3. Do not mark a task complete until every artifact step is verified or explicitly skipped with a reason.

## Aggregator cascade

When changing a sub-package public API, check whether the aggregator wrapping it needs updating:

| Changed sub-package | Check aggregator |
|--------------------|-----------------|
| `cpf-{fmt,gen,val}` | `cpf-utils` re-exports + `CpfUtils` class |
| `cnpj-{fmt,gen,val}` | `cnpj-utils` re-exports + `CnpjUtils` class |
| `cpf-utils` or `cnpj-utils` | `br-utils` |

If the aggregator re-export does not yet expose the new symbol, add it to the `src/` entry point of that aggregator. See [`.context/aggregator-package.md`](aggregator-package.md).

## `composer.json` `"autoload"` as public API

Changing `"autoload"."psr-4"` (moving a class to a new namespace) or `"autoload"."files"` (adding/removing a snake_case helper file) is a public API change. Both require:

- A `### BREAKING CHANGES` entry in `CHANGELOG.md` (namespace moves always break callers).
- A major version bump.

## Checklist

- [ ] All `src/` changes implemented per [`.context/package-arch.md`](package-arch.md)
- [ ] PHPDoc updated on all changed symbols per [`.context/phpdoc.md`](phpdoc.md)
- [ ] Behavior tests added or updated in `tests/`
- [ ] README updated if option, default, or public behavior changed
- [ ] CHANGELOG entry added unless change is entirely dev-only
- [ ] `composer.json` `"require"` / `"autoload"` updated if needed
- [ ] Domain parity check done if change is in `cpf-*` or `cnpj-*`
- [ ] Aggregator packages updated if new symbol needs to be re-exported

## Package-level overrides

Before applying this harness, check whether the target package defines `packages/<pkg>/AGENTS.md` or `packages/<pkg>/agents/`. If either exists and contradicts this file on the same topic, **follow the package-level instruction** (see [`.context/README.md`](README.md#instruction-precedence)).
