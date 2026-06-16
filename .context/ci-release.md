---
id: ci-release
title: CI and release awareness
scope: .github/workflows/
triggers:
  - editing CI or release workflow files
  - understanding the build and test pipeline
  - verifying local changes before claiming done
  - investigating a CI failure
---

# ci-release

This harness documents CI and release workflows for awareness. Agents do **not** run releases. All paths are relative to the repo root.

## Repository constraints

- **Do not run** `php run release`, `composer run release`, create GitHub Releases, or push git tags. Only the developer (via the release workflow) does that.
- CI workflow edits must stay within `.github/workflows/`.
- Do not add secrets, tokens, or credentials to workflow files.
- Before claiming any implementation task is done, validate locally with the commands in the "Local validation" section below.

## CI workflow (`.github/workflows/ci.yml`)

Triggered on every push to any branch and on `workflow_dispatch`.

### Discovery step

The CI first **dynamically discovers** which packages have `lint:ci` and `test` scripts by scanning each `packages/*/composer.json`. Packages without these scripts are skipped automatically. This means:

- Adding a new package is picked up by CI as soon as its `composer.json` has `lint:ci` and `test` scripts.
- Removing a script drops the package from the corresponding matrix.

### Matrix jobs

After discovery, two reusable workflows run in parallel:

| Job | Workflow | Matrix |
|-----|----------|--------|
| Lint | `.github/workflows/.lint.yml` | `packages × PHP [8.2, 8.3, 8.4, 8.5]` |
| Test | `.github/workflows/.test.yml` | `packages × PHP [8.2, 8.3, 8.4, 8.5]` |

### Lint job (`.lint.yml`)

Per matrix cell:
1. Checkout + setup PHP + install root `vendor/` + install package `vendor/`
2. `composer validate --strict` (root and package)
3. `composer run lint:ci` in the package directory

### Test job (`.test.yml`)

Per matrix cell:
1. Checkout + setup PHP + install package `vendor/`
2. `composer validate --strict`
3. `composer test` in the package directory (passes `API_URL` and `API_TOKEN` env vars)

## Release workflow (`.github/workflows/release.yml`)

Triggered by **manual `workflow_dispatch`** only. Inputs: `package` (required), `version` (optional — defaults to latest section in `CHANGELOG.md`).

Steps:
1. Run lint + test for the package (same reusable workflows as CI)
2. Extract release notes from `packages/<pkg>/CHANGELOG.md` via `php run release` (`scripts/Commands/ReleaseCommand.php`)
3. Validate that a `{pkg}/main` branch exists
4. Create a GitHub Release with tag `lacus/<pkg>@X.Y.Z`
5. Push the version tag to the standalone subtree repo `LacusSolutions/br-utils-php_{pkg}`

Agents never trigger or simulate this workflow. If you need to release a package, ask the developer.

## Subtree sync (`.github/workflows/subtrees-sync.yml`)

On push to `main`, each package is synced to:
- In-monorepo branch: `{pkg}/main`
- Standalone repo: `git@github.com:LacusSolutions/br-utils-php_{pkg}.git`

Agents do not interact with subtree sync.

## Local validation commands

Run these from the repo root before declaring any implementation task complete:

```bash
# Single package — full CI equivalent
cd packages/<pkg>
composer install --no-interaction
composer run lint:ci
composer run test

# From the root — all packages
php run lint:ci
composer run test:all
```

If any command fails, fix the issue before marking the task done.

## When to edit workflow files

Edit `.github/workflows/ci.yml`, `.lint.yml`, `.test.yml`, or `release.yml` only when:
- Adding a new job or check required by a tooling decision.
- Bumping a pinned action version after developer approval.
- Fixing a broken workflow step.

Workflow file changes are dev-only and do **not** require a CHANGELOG entry.

## Reference

| Concern | Path |
|---------|------|
| CI entry | `.github/workflows/ci.yml` |
| Reusable lint workflow | `.github/workflows/.lint.yml` |
| Reusable test workflow | `.github/workflows/.test.yml` |
| Release workflow | `.github/workflows/release.yml` |
| Subtree sync | `.github/workflows/subtrees-sync.yml` |
| CLI router | `run` (`php run lint:ci`, `php run release`, etc.) |
| Release command | `php run release` (`scripts/Commands/ReleaseCommand.php`) |
| Changelog harness | [`.context/changelogs.md`](changelogs.md) |
