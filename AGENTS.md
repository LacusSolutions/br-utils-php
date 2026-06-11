# AGENTS.md

This file is the **primary entry point** for AI agents working in the PHP subrepo. Read this file first. It provides baseline rules for every task and links to the specialized harnesses in [`agents/`](agents/) for task-specific instructions.

**Reference standard:** `packages/utils` and `packages/cnpj-*` reflect the current v2 conventions. `packages/cpf-*` (except `cpf-dv`) still follow older v1 patterns (PHPUnit, legacy namespaces, `*Test.php` files) and are being migrated — match `cnpj-*` for new or updated packages.

## Instruction precedence

When instructions conflict, **the more specific scope wins**:

1. **`packages/<pkg>/agents/`** — package-level harness (if present)
2. **`packages/<pkg>/AGENTS.md`** — package-level agent rules (if present)
3. **Repository root** — [`agents/`](agents/) harnesses, then this file

Apply every layer relevant to your task. Where a package-level `AGENTS.md` or `agents/` entry contradicts or overrides root-level guidance, follow the package-level instruction.

---

## Root-level guidelines

### Runtime and package manager

The project uses **PHP** (`^8.2`) and **Composer**. Each package has its own `composer.json` and `vendor/` directory — there is no hoisted monorepo install. Install dependencies per package:

```bash
composer install --working-dir=packages/<pkg>
```

Do not assume a root-level install covers package dependencies. Prefer Composer over any other PHP package manager.

### Dependencies

See [`agents/dependencies.md`](agents/dependencies.md) for the full policy (approval, Packagist versioning, internal dep direction, lockfile update).

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
- `scripts/lint-format.php`, `scripts/lint-check.php`, `scripts/lint-staged.php` — shared lint orchestration

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
composer run lint:ci        # dry-run check (CI equivalent)
composer run lint           # format + check
```

From inside a package directory (`packages/<pkg>/`):

```bash
composer run lint:ci
composer run lint
```

See [`agents/lint-config.md`](agents/lint-config.md) for the full setup, shared script invocation, and per-package script conventions.

### Commit and standards

**CaptainHook** (git hooks) and `ramsey/conventional-commits` enforce conventional commits on every commit. Commit messages must follow the conventional format. Use the package folder name as the scope when changes are isolated to one package: `<type>(<pkg-name>): <message>` (e.g. `fix(cnpj-val): correct check digit`).

### CI

See [`agents/ci-release.md`](agents/ci-release.md) for the full pipeline (matrix PHP versions, reusable lint and test workflows, what agents must not run, local validation commands).

---

## Package-specific guidelines

### PHP version and strictness

- Require `"php": "^8.2"` in all modern (v2) packages.
- Every PHP file must start with `declare(strict_types=1);`.
- Use typed properties, parameters, and return types.

### Lint / static analysis (DRY)

See [`agents/lint-config.md`](agents/lint-config.md) for shared config invocation patterns, PHPStan level, and the rule against adding per-package lint config files.

### Dev tool configs

Avoid adding per-package configuration files for shared tools (e.g. no `.php-cs-fixer.php` or `phpstan.neon` inside a package unless it genuinely needs to diverge from the root config). Use the root configs.

### Source layout

- Source must live under `src/`.
- There is no build output directory — PHP source is published as-is via PSR-4 autoload.

### PHPDoc

See [`agents/phpdoc.md`](agents/phpdoc.md) for conventions (class/method docs, `@throws`, `@param`, `@property-read`, constants, tone).

### Commit scope

If a commit touches only one package directory (`packages/<pkg-name>/`), use the package folder name as the conventional commit scope: `<type>(<pkg-name>): <message>` (e.g. `docs(cnpj-fmt): update README`, `fix(cpf-val): handle null input`).

### Changelog

See [`agents/changelogs.md`](agents/changelogs.md) for the full workflow (when to add an entry, SemVer bump decision, format, section headings, conciseness rules). Agents **do** edit `packages/<pkg>/CHANGELOG.md` directly — changelogs are managed manually (not by Changesets).

### API and docs

Use [`agents/public-api.md`](agents/public-api.md) as the coordination checklist for any public API change (new class, method signature, option, or namespace). It links to the specialized harnesses for source, PHPDoc, tests, README, and changelog. All README rules are in [`agents/readme-docs.md`](agents/readme-docs.md).

### CHANGELOG.md

Edit `packages/<pkg>/CHANGELOG.md` following the rules in [`agents/changelogs.md`](agents/changelogs.md). Do **not** run `composer run release` — that creates GitHub Releases and is the developer's responsibility.

---

## Agent harnesses

Task-specific instructions live in [`agents/`](agents/). The harness catalog — IDs, files, and triggers — is [`agents/README.md`](agents/README.md). Read and follow the matching harness file **in full** before starting the task.

A package may define its own `packages/<pkg>/agents/` or `packages/<pkg>/AGENTS.md`; those override conflicting root harness or README rules for that package (see [Instruction precedence](#instruction-precedence) above).

### Skill ↔ harness mapping

Cursor agents may load these workspace skills as a shortcut; each skill is a thin pointer to the canonical harness:

| Cursor skill | Harness file | When triggered |
|--------------|-------------|----------------|
| `readme-php` | [`agents/readme-docs.md`](agents/readme-docs.md) | Writing or reviewing `README.md` / `README.pt.md` |
| `unit-tests-php` | [`agents/unit-tests.md`](agents/unit-tests.md) | Writing, reviewing, or running tests |
| `changelogs-php` | [`agents/changelogs.md`](agents/changelogs.md) | Editing `CHANGELOG.md`; choosing a SemVer bump |
| `package-arch-php` | [`agents/package-arch.md`](agents/package-arch.md) | Adding or changing `src/` code |
| `public-api-php` | [`agents/public-api.md`](agents/public-api.md) | Any public API change |
| `new-package-php` | [`agents/new-package.md`](agents/new-package.md) | Scaffolding a new package |
| `lint-config-php` | [`agents/lint-config.md`](agents/lint-config.md) | Editing lint/static-analysis config |
| `phpdoc-php` | [`agents/phpdoc.md`](agents/phpdoc.md) | Adding or reviewing PHPDoc |
| `domain-parity-php` | [`agents/domain-parity.md`](agents/domain-parity.md) | CPF ↔ CNPJ parity check |
| `aggregator-package-php` | [`agents/aggregator-package.md`](agents/aggregator-package.md) | Working on `cpf-utils`, `cnpj-utils`, or `br-utils` |
| `ci-release-php` | [`agents/ci-release.md`](agents/ci-release.md) | Editing CI workflows; local validation |
| `dependencies-php` | [`agents/dependencies.md`](agents/dependencies.md) | Adding or changing Composer dependencies |

---

## Key paths

| Purpose | Path |
|---------|------|
| Agent harnesses (catalog) | `agents/` |
| Shared CS fixer config | `.php-cs-fixer.config.php` |
| Shared PHPStan config | `.php-stan.config.neon` |
| Lint orchestration scripts | `scripts/lint-format.php`, `scripts/lint-check.php`, `scripts/lint-staged.php` |
| Release script | `scripts/release.php` |
| Git hooks | `.captainhook.config.json` |
| CI / release workflows | `.github/workflows/` |
| Root Composer config | `composer.json` |
| Package Composer config | `packages/*/composer.json` |
| Package changelogs | `packages/*/CHANGELOG.md` |
| Cursor skills (PHP) | `.cursor/skills/*-php/SKILL.md` (workspace root; thin pointers only) |
