# Contributing to `br-utils`

Thank you for your interest in contributing to this initiative! This document provides guidelines and information for contributors.

## Table of Contents

- [Code of Conduct](#code-of-conduct)
- [Getting Started](#getting-started)
- [Development Setup](#development-setup)
- [Project Structure](#project-structure)
- [Contributing Guidelines](#contributing-guidelines)
- [Development Workflow](#development-workflow)
- [Testing](#testing)
- [Code Style](#code-style)
- [Pull Request Process](#pull-request-process)
- [Issue Reporting](#issue-reporting)
- [Feature Requests](#feature-requests)

## Code of Conduct

This project adheres to a code of conduct that we expect all contributors to follow. Please be respectful, inclusive, and constructive in all interactions.

## Getting Started

Before contributing, please:

1. **Fork the repository** on GitHub
2. **Clone your fork** locally
3. **Set up the development environment** (see [Development Setup](#development-setup))
4. **Create a feature branch** for your changes
5. **Make your changes** following our guidelines
6. **Test your changes** thoroughly
7. **Submit a pull request**

## Development Setup

### Prerequisites

- **PHP** 8.2 or higher
- **Composer** 2.x — package management
- **Git** — version control

All commands below assume your shell is in the root directory (the monorepo directory that contains `run`, `composer.json`, and `packages/`).

### Installation

```bash
# Clone your fork
git clone https://github.com/YOUR_USERNAME/br-utils-php.git
# Root dev tooling (lint, hooks, run router)
composer install

# Each package has its own vendor/ — install per package
for pkg in packages/*/; do composer install --no-interaction --working-dir="$pkg"; done

# Verify setup
php run lint:ci
composer run test:all
```

CaptainHook installs git hooks automatically via the root `prepare` script when you run `composer install`.

### CLI router (`run`)

The `run` file is the preferred entry point for monorepo scripts. It works with `php run …` or `php run …`:

```bash
php run --help                # List available commands
php run lint:ci               # Dry-run format + PHPStan (CI equivalent)
php run lint                  # Apply fixes, then run static analysis
php run lint:format           # Apply php-cs-fixer to all packages
php run lint:check            # Run PHPStan on all packages
php run deps                  # Internal lacus/* dependency graph
php run deps -r utils         # Packages that depend on utils
php run deps cnpj-utils       # Dependencies of one package
```

Root `composer.json` scripts delegate to the same router (`composer run lint:ci` is equivalent to `php run lint:ci`).

Optional path arguments narrow lint to specific packages or paths, for example `php run lint:ci cnpj-fmt`.

### Available scripts

#### From the repo root

```bash
# Lint (via run — see above)
composer run lint:ci
composer run lint
composer run lint:format
composer run lint:check
composer run lint:staged          # Lint only staged PHP files
composer run lint:staged:test     # Exercise lint-staged locally
composer run run:test             # Smoke tests for the run router

# Tests (orchestrate per-package composer test)
composer run test:all
composer run test:cnpj
composer run test:cpf
composer run test:formatters
composer run test:generators
composer run test:validators
composer run test:utils
composer run test:cnpj-fmt        # Single package (and similar test:<pkg> aliases)

# Commits
composer run commit               # Prepare a conventional commit message
```

`composer run release` exists for maintainers only — do not run it when contributing.

#### From a package directory (`packages/<pkg>/`)

After `composer install` in that package:

```bash
composer run lint:ci              # Dry-run check (what CI runs)
composer run lint                 # Format + static analysis
composer run lint:format
composer run lint:check
composer test                     # Package test suite (Pest or PHPUnit)
composer run test:cov             # HTML coverage (Pest packages)
composer run test:watch           # Watch mode (Pest packages)
```

### Git hooks (CaptainHook)

Configured in [`.captainhook.config.json`](.captainhook.config.json):

| Hook | Action |
|------|--------|
| **pre-commit** | Lint staged PHP files (`php run lint:staged`) |
| **commit-msg** | Validate conventional commit format |
| **pre-push** | `composer run lint:ci` and `composer run test:all` at the root |

## Project Structure

```text
(root)                         # Subrepo root — run all dev commands from here
├── packages/                  # 13 independent Composer packages
│   ├── utils/                 # Shared foundation
│   ├── cpf-dv/, cpf-fmt/, cpf-gen/, cpf-val/, cpf-utils/
│   ├── cnpj-dv/, cnpj-fmt/, cnpj-gen/, cnpj-val/, cnpj-utils/
│   └── br-utils/              # Top-level CPF + CNPJ aggregator
├── scripts/                   # Symfony Console app (Commands/, helpers.php, run.test.php)
├── run                        # Dev CLI entry (php run <command> [args...])
├── vendor/                    # Root dev-tool dependencies
├── .php-stan.config.neon      # Shared PHPStan config (level 10)
├── .php-cs-fixer.config.php   # Shared php-cs-fixer rules (PSR-12)
├── .captainhook.config.json   # Git hooks
├── composer.json              # Root dev tooling and orchestration scripts
├── AGENTS.md                  # Agent/contributor baseline rules
└── agents/                    # Task-specific contributor harnesses
```

Each package under `packages/<pkg>/` has its own `composer.json`, `vendor/`, `src/`, and `tests/` (or `tests/specs/` for Pest packages). Lint and static analysis use the **root** configs — packages do not ship their own php-cs-fixer or PHPStan config files.

## Contributing Guidelines

### What We're Looking For

We welcome contributions in the following areas:

- **🐛 Bug Fixes**: Fix issues and improve stability
- **✨ New Features**: Add new field types, processors, or functionality
- **📚 Documentation**: Improve docs, examples, and guides
- **🧪 Tests**: Add test coverage for new or existing features
- **⚡ Performance**: Optimize validation performance
- **🔧 Tooling**: Improve testing, static analysis, or development tools

### What We're NOT Looking For

- Breaking changes to the public API without discussion
- Changes that reduce test coverage
- Code that doesn't follow our style guidelines
- Features that don't align with the project's goals

## Development Workflow

### 1. Create a Feature Branch

```bash
git checkout -b feature/your-feature-name
# or
git checkout -b fix/issue-description
```

### 2. Make Your Changes

- Write clean, readable code
- Follow our coding standards
- Add tests for new functionality
- Update documentation as needed

### 3. Test Your Changes

```bash
# Full CI equivalent — from the subrepo root
php run lint:ci
composer run test:all

# When changes are isolated to one package
cd packages/<pkg>
composer run lint:ci
composer test
```

Before pushing, hooks run root `lint:ci` and `test:all` automatically. Fix failures locally first.

Inspect internal dependency edges after changing `composer.json` `"require"` fields:

```bash
php run deps <pkg>
php run deps -r <pkg>
```

### 4. Commit Your Changes

Use [conventional commits](https://www.conventionalcommits.org/). When a change touches a single package, use the package folder name as the scope:

```bash
git commit -m "feat(cnpj-fmt): add alphanumeric mask option"
git commit -m "fix(cpf-val): reject empty input"
git commit -m "docs(br-utils): update README examples"
git commit -m "test(cnpj-gen): cover edge case for check digits"
```

Run `composer run commit` to prepare an interactive commit message.

### 5. Push and Create PR

```bash
git push origin feature/your-feature-name
```

Then create a pull request on GitHub.

## Testing

### Test structure

The monorepo is mid-migration between two runners:

| Generation | Runner | Packages |
|------------|--------|----------|
| **v2** | Pest 3 | All `cnpj-*`, `cpf-dv`, `utils` |
| **v1 (legacy)** | PHPUnit 10 | `cpf-fmt`, `cpf-gen`, `cpf-val`, `cpf-utils`, `br-utils` |

**v2 (Pest):** tests live in `tests/specs/` with a `.spec.php` suffix (e.g. `CnpjFormatter.spec.php`). Config: `.pest.config.xml`.

**v1 (PHPUnit):** tests live in `tests/` with a `Test.php` suffix. Config: `phpunit.xml`.

Match the structure and style of sibling tests in the package you are editing. New or migrated packages should follow the v2 (CNPJ) conventions.

### Running tests

```bash
# All packages
composer run test:all

# One package — from packages/<pkg>/
composer test
```

### Test requirements

- Cover public behavior, options, and error paths
- Test edge cases and boundary conditions
- Keep tests self-documenting; follow existing naming in the package

## Code Style

### PHP Guidelines

- Use **strict types** (`declare(strict_types=1);`)
- Follow **PSR-12** coding standards
- Use **type declarations** for all parameters and return types
- Prefer **interfaces** over abstract classes when possible
- Use **readonly properties** when appropriate
- Follow **PSR-4** autoloading standards

### Code Formatting

- Use **4 spaces** for indentation (not tabs)
- Use **semicolons** at the end of statements
- Use **single quotes** for strings when possible
- Use **trailing commas** in arrays and function calls
- Use **short array syntax** `[]` instead of `array()`

### Naming Conventions

- **Classes**: PascalCase (`CnpjGenerator`)
- **Methods**: camelCase (`generateCnpj`)
- **Properties**: camelCase (`$fieldName`)
- **Files**: Match class name (`CnpjGenerator.php`)
- **Constants**: UPPER_SNAKE_CASE (`MAX_RETRIES`)
- **Variables**: camelCase (`$variable`)
- **Functions**: snake_case (`some_function`)

### Namespace Structure

- Root namespace: `Lacus\`
- Package namespaces: `Lacus\{PackageName}\`
- Test namespaces: `Lacus\{PackageName}\Tests\`

### Example Code Style

```php
<?php

declare(strict_types=1);

namespace Lacus\CnpjGen;

class CnpjGenerator
{
    private readonly string $customProperty;

    public function __construct()
    {
        $this->customProperty = 'example';
    }

    public function generate(): string
    {
        // Implementation
        return $this->customProperty;
    }

    private function helperMethod(): void
    {
        // Private implementation
    }
}
```

## Pull Request Process

### Before Submitting

- [ ] Code follows our style guidelines (PSR-12)
- [ ] Lint passes (`php run lint:ci` from root, or `composer run lint:ci` in the affected package)
- [ ] Tests pass (`composer run test:all` from root, or `composer test` in the affected package)
- [ ] User-facing changes have a `CHANGELOG.md` entry in the affected package
- [ ] Documentation is updated
- [ ] Commit messages follow conventional format (with package scope when applicable)

### PR Description Template

```markdown
## Description
Brief description of changes

## Type of Change
- [ ] Bug fix
- [ ] New feature
- [ ] Breaking change
- [ ] Documentation update

## Testing
- [ ] Tests pass
- [ ] New tests added
- [ ] Coverage maintained

## Checklist
- [ ] Code follows style guidelines
- [ ] Self-review completed
- [ ] Documentation updated
- [ ] No breaking changes (or documented)
```

### Review Process

1. **Automated Checks**: CI runs `lint:ci` and `test` per package across PHP 8.2–8.5
2. **Code Review**: Maintainers will review your code
3. **Feedback**: Address any requested changes
4. **Approval**: Once approved, your PR will be merged

## Issue Reporting

### Bug Reports

When reporting bugs, please include:

- **Description**: Clear description of the issue
- **Steps to Reproduce**: Minimal steps to reproduce
- **Expected Behavior**: What should happen
- **Actual Behavior**: What actually happens
- **Environment**: PHP version, OS, etc.
- **Code Example**: Minimal code that demonstrates the issue

### Bug Report Template

```markdown
**Describe the bug**
A clear description of what the bug is.

**To Reproduce**
Steps to reproduce the behavior:
1. Create schema with...
2. Call validate with...
3. See error

**Expected behavior**
What you expected to happen.

**Environment:**
- PHP version: [e.g. 8.2.0]
- OS: [e.g. macOS 13.0]
- The version of the package you are changing: [e.g. 1.1.2]

**Code example**
```php
<?php

declare(strict_types=1);

// Minimal code that reproduces the issue
```

**Additional context**
Any other context about the problem.
```

## Feature Requests

### Suggesting Features

When suggesting features, please include:

- **Use Case**: Why is this feature needed?
- **Proposed Solution**: How should it work?
- **Alternatives**: Other ways to solve the problem
- **Additional Context**: Any other relevant information

### Feature Request Template

```markdown
**Is your feature request related to a problem?**
A clear description of what the problem is.

**Describe the solution you'd like**
A clear description of what you want to happen.

**Describe alternatives you've considered**
A clear description of any alternative solutions.

**Additional context**
Add any other context or screenshots about the feature request.
```

## Getting Help

- **GitHub Issues**: For bugs and feature requests
- **GitHub Discussions**: For questions and general discussion
- **Documentation**: Package `README.md` files, [`AGENTS.md`](AGENTS.md), and harnesses under [`agents/`](agents/)

## Recognition

Contributors will be recognized in:
- **README.md**: Contributors section
- **CHANGELOG.md**: Release notes
- **GitHub**: Contributor statistics

## License

By contributing to `br-utils`, you agree that your contributions will be licensed under the MIT License.

---

Thank you for contributing to `br-utils`! 🎉
