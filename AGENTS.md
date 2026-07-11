# AGENTS.md

This file is the **primary entry point** for AI agents working in the PHP subrepo. Read this file first. It provides baseline rules for every task and links to the specialized harnesses in [`context/`](context/) for task-specific instructions.

**Reference standard:** `packages/utils` and `packages/cnpj-*` reflect the current v2 conventions. `packages/cpf-*` (except `cpf-dv`) still follow older v1 patterns (PHPUnit, legacy namespaces, `*Test.php` files) and are being migrated — match `cnpj-*` for new or updated packages.

## Instruction precedence

When instructions conflict, **the more specific scope wins**:

1. **`packages/<pkg>/context/`** — package-level harness (if present)
2. **`packages/<pkg>/AGENTS.md`** — package-level agent rules (if present)
3. **Repository root** — [`context/`](context/) harnesses, then this file

Apply every layer relevant to your task. Where a package-level `AGENTS.md` or `context/` entry contradicts or overrides root-level guidance, follow the package-level instruction.

---

## Root-level guidelines

### Runtime and package manager

The project uses **PHP** (`^8.2`) and **Composer**. Each package has its own `composer.json` and `vendor/` directory — there is no hoisted monorepo install. Install dependencies per package:

```bash
composer install --working-dir=packages/<pkg>
```

Do not assume a root-level install covers package dependencies. Prefer Composer over any other PHP package manager.

### Dependencies

See [`context/dependencies.md`](context/dependencies.md) for the full policy (approval, Packagist versioning, internal dep direction, lockfile update).

### Project structure

The repository is a monorepo with 13 independent packages under `packages/*`. Source is shipped directly via PSR-4 autoload — there is no build step and no `dist/` directory.

```
packages/
  utils/           # Shared helpers (foundation)
  cpf-dv/          # CPF check digits
  cpf-fmt/         # CPF formatter
  cpf-gen/         # CPF generator
  cpf-val/         # CPF validator
  cpf-utils/       # CPF aggregator
  cnpj-dv/         # CNPJ check digits
  cnpj-fmt/        # CNPJ formatter
  cnpj-gen/        # CNPJ generator
  cnpj-val/        # CNPJ validator
  cnpj-utils/      # CNPJ aggregator
  br-utils/        # Top-level CPF + CNPJ aggregator
```

### Configurations

Shared tooling lives at the PHP subrepo root:

- `composer.json` — root dev tools, orchestration scripts
- `.php-cs-fixer.config.php` — shared PSR-12 formatting rules
- `.php-stan.config.neon` — shared PHPStan level-10 config
- `.captainhook.config.json` — git hooks (pre-commit, commit-msg, pre-push)
- `run` — Symfony Console CLI for monorepo dev commands (`php run <command> [args...]`)
- `scripts/Application.php`, `scripts/Commands/`, `scripts/helpers.php` — dev CLI and shared orchestration

Prefer changing these only when necessary and in line with existing patterns. Do not add per-package php-cs-fixer or PHPStan config files.

### Package strategy

Packages are split by domain (`utils`, `cpf-*`, `cnpj-*`, `br-utils`). Follow the existing dependency direction:

```
utils → {cpf,cnpj}-dv → {cpf,cnpj}-{fmt,gen,val} → {cpf,cnpj}-utils → br-utils
```

Upstream packages must not import downstream ones.

### Lint and format

Linting and formatting use **php-cs-fixer** (style) and **PHPStan** (static analysis, level 10). Run from the subrepo root:

```bash
php run lint:ci               # dry-run check (CI equivalent)
php run lint                  # format + check
composer run lint:ci        # same via Composer
composer run lint           # same via Composer
```

From inside a package directory (`packages/<pkg>/`):

```bash
composer run lint:ci
composer run lint
```

See [`context/lint-config.md`](context/lint-config.md) for the full setup, shared script invocation, and per-package script conventions.

### Commit and standards

**CaptainHook** (git hooks) and `ramsey/conventional-commits` enforce conventional commits on every commit. Commit messages must follow the conventional format. Use the package folder name as the scope when changes are isolated to one package: `<type>(<pkg-name>): <message>` (e.g. `fix(cnpj-val): correct check digit`).

### CI

See [`context/ci-release.md`](context/ci-release.md) for the full pipeline (matrix PHP versions, reusable lint and test workflows, what agents must not run, local validation commands).

---

## Package-specific guidelines

### PHP version and strictness

- Require `"php": "^8.2"` in all modern (v2) packages.
- Every PHP file must start with `declare(strict_types=1);`.
- Use typed properties, parameters, and return types.

### Lint / static analysis (DRY)

See [`context/lint-config.md`](context/lint-config.md) for shared config invocation patterns, PHPStan level, and the rule against adding per-package lint config files.

### Dev tool configs

Avoid adding per-package configuration files for shared tools (e.g. no `.php-cs-fixer.php` or `phpstan.neon` inside a package unless it genuinely needs to diverge from the root config). Use the root configs.

### Source layout

- Source must live under `src/`.
- There is no build output directory — PHP source is published as-is via PSR-4 autoload.

### PHPDoc

See [`context/phpdoc.md`](context/phpdoc.md) for conventions (class/method docs, `@throws`, `@param`, `@property-read`, constants, tone).

### Commit scope

If a commit touches only one package directory (`packages/<pkg-name>/`), use the package folder name as the conventional commit scope: `<type>(<pkg-name>): <message>` (e.g. `docs(cnpj-fmt): update README`, `fix(cpf-val): handle null input`).

### Changelog

See [`context/changelogs.md`](context/changelogs.md) for the full workflow (when to add an entry, SemVer bump decision, format, section headings, conciseness rules). Agents **do** edit `packages/<pkg>/CHANGELOG.md` directly — changelogs are managed manually (not by Changesets).

### API and docs

Use [`context/public-api.md`](context/public-api.md) as the coordination checklist for any public API change (new class, method signature, option, or namespace). It links to the specialized harnesses for source, PHPDoc, tests, README, and changelog. All README rules are in [`context/readme-docs.md`](context/readme-docs.md).

### CHANGELOG.md

Edit `packages/<pkg>/CHANGELOG.md` following the rules in [`context/changelogs.md`](context/changelogs.md). Do **not** run `php run release` or `composer run release` — that creates GitHub Releases and is the developer's responsibility.

---

## Agent harnesses

Task-specific instructions live in [`context/`](context/). The harness catalog — IDs, files, and triggers — is [`context/README.md`](context/README.md). Read and follow the matching harness file **in full** before starting the task.

A package may define its own `packages/<pkg>/context/` or `packages/<pkg>/AGENTS.md`; those override conflicting root harness or README rules for that package (see [Instruction precedence](#instruction-precedence) above).

### Skill ↔ harness mapping

Cursor agents may load these workspace skills as a shortcut; each skill is a thin pointer to the canonical harness:

| Cursor skill | Harness file | When triggered |
|--------------|-------------|----------------|
| `readme-php` | [`context/readme-docs.md`](context/readme-docs.md) | Writing or reviewing `README.md` / `README.pt.md` |
| `unit-tests-php` | [`context/unit-tests.md`](context/unit-tests.md) | Writing, reviewing, or running tests |
| `changelogs-php` | [`context/changelogs.md`](context/changelogs.md) | Editing `CHANGELOG.md`; choosing a SemVer bump |
| `package-arch-php` | [`context/package-arch.md`](context/package-arch.md) | Adding or changing `src/` code |
| `public-api-php` | [`context/public-api.md`](context/public-api.md) | Any public API change |
| `new-package-php` | [`context/new-package.md`](context/new-package.md) | Scaffolding a new package |
| `lint-config-php` | [`context/lint-config.md`](context/lint-config.md) | Editing lint/static-analysis config |
| `phpdoc-php` | [`context/phpdoc.md`](context/phpdoc.md) | Adding or reviewing PHPDoc |
| `domain-parity-php` | [`context/domain-parity.md`](context/domain-parity.md) | CPF ↔ CNPJ parity check |
| `aggregator-package-php` | [`context/aggregator-package.md`](context/aggregator-package.md) | Working on `cpf-utils`, `cnpj-utils`, or `br-utils` |
| `ci-release-php` | [`context/ci-release.md`](context/ci-release.md) | Editing CI workflows; local validation |
| `dependencies-php` | [`context/dependencies.md`](context/dependencies.md) | Adding or changing Composer dependencies |

---

## Key paths

| Purpose | Path |
|---------|------|
| Agent harnesses (catalog) | `context/` |
| Shared CS fixer config | `.php-cs-fixer.config.php` |
| Shared PHPStan config | `.php-stan.config.neon` |
| CLI entry | `run` (`php run deps`, `php run lint:ci`, etc.) |
| Dev CLI application | `scripts/Application.php` + `scripts/Commands/` |
| Shared helpers | `scripts/helpers.php` |
| Internal dependency graph | `php run deps` (see [`context/dependencies.md`](context/dependencies.md)) |
| Release command | `php run release` (`scripts/Commands/ReleaseCommand.php`) |
| Git hooks | `.captainhook.config.json` |
| CI / release workflows | `.github/workflows/` |
| Root Composer config | `composer.json` |
| Package Composer config | `packages/*/composer.json` |
| Package changelogs | `packages/*/CHANGELOG.md` |
