# PHP Testing Guide

Simple, single-source-of-truth testing for BR Utils PHP packages.

## Quick Start

```bash
# Install dependencies
composer install

# Run all tests
composer test

# Run with coverage
composer test-coverage
```

## Available Commands

### All Tests
- `composer test` - Run all tests
- `composer test-coverage` - Run all tests with coverage report

### Individual Packages
- `composer test:cpf-val` - CPF validation tests
- `composer test:cnpj-val` - CNPJ validation tests
- `composer test:cpf-gen` - CPF generation tests
- `composer test:cnpj-gen` - CNPJ generation tests
- `composer test:cpf-fmt` - CPF formatting tests
- `composer test:cnpj-fmt` - CNPJ formatting tests
- `composer test:cpf-utils` - CPF utilities tests
- `composer test:cnpj-utils` - CNPJ utilities tests
- `composer test:br-utils` - BR utilities tests

### Grouped Tests
- `composer test:validators` - All validator tests (CPF + CNPJ)
- `composer test:generators` - All generator tests (CPF + CNPJ)
- `composer test:formatters` - All formatter tests (CPF + CNPJ)
- `composer test:utils` - All utils tests (CPF + CNPJ + BR)
- `composer test:cpf` - All CPF-related tests
- `composer test:cnpj` - All CNPJ-related tests

## Package-Level Testing

To test individual packages in isolation:

```bash
# Navigate to package directory
cd packages/cpf-val

# Install package dependencies
composer install

# Run package tests
composer test
# or
vendor/bin/phpunit
```

## Examples

```bash
# Test everything
composer test

# Test only validators
composer test:validators

# Test only CPF packages
composer test:cpf

# Test with coverage
composer test-coverage

# Test specific package
composer test:cpf-val
```

## Requirements

- PHP 7.3+
- Composer
- PHPUnit 10.0+

## Why Composer Scripts Only?

1. **Single source of truth** - All test commands in one place
2. **PHP standard** - Familiar to all PHP developers
3. **Simple maintenance** - Update one file, not three
4. **Cross-platform** - Works on Windows, Mac, Linux
5. **Integrated** - Part of dependency management
6. **No confusion** - One way to do things

## Project Structure

```
php/
├── composer.json              # All test commands here
├── phpunit.xml.dist          # Test configuration
└── packages/                 # Individual packages
    ├── cpf-val/
    │   ├── composer.json     # Package dependencies
    │   ├── phpunit.xml       # Package test config
    │   └── tests/            # Test files
    └── ...
```

## Test Suites

The monorepo is organized into logical test suites:

| Suite | Description | Packages |
|-------|-------------|----------|
| All Tests | Complete test suite | All 9 packages |
| CPF Validator | CPF validation tests | cpf-val |
| CNPJ Validator | CNPJ validation tests | cnpj-val |
| CPF Generator | CPF generation tests | cpf-gen |
| CNPJ Generator | CNPJ generation tests | cnpj-gen |
| CPF Formatter | CPF formatting tests | cpf-fmt |
| CNPJ Formatter | CNPJ formatting tests | cnpj-fmt |
| CPF Utils | CPF utilities tests | cpf-utils |
| CNPJ Utils | CNPJ utilities tests | cnpj-utils |
| BR Utils | Combined utilities tests | br-utils |

## Coverage Reports

Coverage reports are generated in the `coverage/` directory:

```bash
composer test-coverage
```

Open `coverage/index.html` in your browser to view the detailed coverage report.

## Troubleshooting

### Common Issues

1. **Dependencies not installed:**
   ```bash
   composer install
   ```

2. **Autoloader issues:**
   ```bash
   composer dump-autoload
   ```

3. **PHPUnit not found:**
   ```bash
   composer install --dev
   ```

### Debug Mode

```bash
# Run with verbose output
vendor/bin/phpunit --verbose

# Run specific test with debug
vendor/bin/phpunit --verbose --debug tests/CpfValidatorTest.php
```

That's it! No bash scripts, no Makefile, no confusion. Just Composer - the PHP way.
