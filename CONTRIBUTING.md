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

- **PHP** (v8.1 or higher)
- **Composer** (v2.0 or higher) - for package management
- **Git** - for version control

### Installation

```bash
# Clone your fork
git clone https://github.com/YOUR_USERNAME/br-utils-php.git
cd br-utils-php

# Install dependencies
composer install

# Verify setup
composer test
composer analyze
```

### Available Scripts

```bash
# Development
composer test              # Run all tests
composer test:watch        # Run tests in watch mode
composer test-coverage     # Run tests with coverage report
composer analyze           # Run PHPStan static analysis
composer check             # Check code style (dry run)
composer fix               # Fix code style issues

# Package-specific testing
composer test:cnpj         # Test all CNPJ packages
composer test:cpf          # Test all CPF packages
composer test:formatters   # Test formatter packages
composer test:generators   # Test generator packages
composer test:validators   # Test validator packages
composer test:utils        # Test utility packages
```

## Project Structure

```
br-utils-php/
├── packages/               # Monorepo packages
│   ├── br-utils/           # Core BR utilities
│   │   ├── src/            # Source code
│   │   ├── tests/          # Test files
│   │   ├── vendor/         # Composer dependencies
│   │   ├── composer.json   # Package configuration
│   │   └── composer.lock   # Locked dependencies
│   ├── cnpj-fmt/           # CNPJ formatter package
│   │   ├── src/            # Source code
│   │   ├── tests/          # Test files
│   │   ├── vendor/         # Composer dependencies
│   │   ├── composer.json   # Package configuration
│   │   └── composer.lock   # Locked dependencies
│   ├── cnpj-gen/           # CNPJ generator package
│   │   ├── src/            # Source code
│   │   ├── tests/          # Test files
│   │   ├── vendor/         # Composer dependencies
│   │   ├── composer.json   # Package configuration
│   │   └── composer.lock   # Locked dependencies
│   ├── cnpj-utils/         # CNPJ utilities package
│   │   └── ...
│   ├── cnpj-val/           # CNPJ validator package
│   │   └── ...
│   ├── cpf-fmt/            # CPF formatter package
│   │   └── ...
│   ├── cpf-gen/            # CPF generator package
│   │   └── ...
│   ├── cpf-utils/          # CPF utilities package
│   │   └── ...
│   └── cpf-val/            # CPF validator package
│       └── ...
├── vendor/                 # Composer dependencies
├── captainhook.json        # Git hooks configuration
├── composer.json           # Root composer configuration
├── composer.lock           # Locked dependencies
├── phpstan.neon            # PHPStan configuration
├── phpunit.xml.dist        # PHPUnit configuration
└── README.md               # Project documentation
```

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
# Run all tests
composer run test

# Run with coverage
composer run test-coverage

# Static analysis
composer run analyze

# Check code style
composer run check

# Fix code style issues
composer run fix
```

### 4. Commit Your Changes

Use conventional commit messages:

```bash
git commit -m "feat: add string field processor"
git commit -m "fix: resolve validation error in int processor"
git commit -m "docs: update README with new examples"
git commit -m "test: add tests for bail option"
```

### 5. Push and Create PR

```bash
git push origin feature/your-feature-name
```

Then create a pull request on GitHub.

## Testing

### Test Structure

- Tests are located in the `tests/` directory within each package
- Test files use the `Test.php` suffix (e.g., `CnpjGeneratorTest.php`)
- Tests mirror the `src/` directory structure
- Use PHPUnit as the test runner

### Writing Tests

```php
<?php

declare(strict_types=1);

namespace Lacus\BrUtils\Tests;

use PHPUnit\Framework\TestCase;
use Lacus\BrUtils\CnpjGenerator;

class CnpjGeneratorTest extends TestCase
{
    private CnpjGenerator $generator;

    protected function setUp(): void
    {
        $this->generator = new CnpjGenerator();
    }

    public function testShouldGenerateValidCnpj(): void
    {
        $cnpj = $this->generator->generate();

        $this->assertIsString($cnpj);
        $this->assertMatchesRegularExpression('/^\d{2}\.\d{3}\.\d{3}\/\d{4}-\d{2}$/', $cnpj);
    }
}
```

### Test Requirements

- **Coverage**: Maintain 100% line coverage
- **Edge Cases**: Test boundary conditions and error cases
- **Performance**: Consider performance implications
- **Documentation**: Tests should be self-documenting

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
- [ ] All tests pass (`composer run test`)
- [ ] PHPStan analysis passes (`composer run analyze`)
- [ ] Code style check passes (`composer run check`)
- [ ] Mutation testing passes (`composer run infection`)
- [ ] Documentation is updated
- [ ] Commit messages follow conventional format

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

1. **Automated Checks**: CI will run tests, linting, and type checking
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
- PHP version: [e.g. 8.1.0]
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
- **Documentation**: Check the README and inline code comments

## Recognition

Contributors will be recognized in:
- **README.md**: Contributors section
- **CHANGELOG.md**: Release notes
- **GitHub**: Contributor statistics

## License

By contributing to `br-utils`, you agree that your contributions will be licensed under the MIT License.

---

Thank you for contributing to `br-utils`! 🎉
