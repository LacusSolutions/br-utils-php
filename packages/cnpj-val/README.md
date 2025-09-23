# Lacus :: cnpj-val

[![Packagist Version](https://img.shields.io/packagist/v/lacus/cnpj-val)](https://packagist.org/packages/lacus/cnpj-val)
[![Packagist Downloads](https://img.shields.io/packagist/dm/lacus/cnpj-val)](https://packagist.org/packages/lacus/cnpj-val)
[![PHP Version](https://img.shields.io/packagist/php-v/lacus/cnpj-val)](https://www.php.net/)
[![Test Status](https://img.shields.io/github/actions/workflow/status/LacusSolutions/br-utils-php/ci.yml?label=ci/cd)](https://github.com/LacusSolutions/br-utils-php/actions)
[![Last Update Date](https://img.shields.io/github/last-commit/LacusSolutions/br-utils-php)](https://github.com/LacusSolutions/br-utils-php)
[![Project License](https://img.shields.io/github/license/LacusSolutions/br-utils-php)](https://github.com/LacusSolutions/br-utils-php/blob/main/LICENSE)

Utility function/class to validate CNPJ (Brazilian employer ID).

## PHP Support

| ![PHP 8.1](https://img.shields.io/badge/PHP-8.1-777BB4?logo=php&logoColor=white) | ![PHP 8.2](https://img.shields.io/badge/PHP-8.2-777BB4?logo=php&logoColor=white) | ![PHP 8.3](https://img.shields.io/badge/PHP-8.3-777BB4?logo=php&logoColor=white) |
|--- | --- | --- |
| Passing ✔ | Passing ✔ | Passing ✔ |

## Installation

```bash
# using Composer
$ composer require lacus/cnpj-val
```

## Import

```php
<?php
// Using class-based resource
use Lacus\CnpjVal\CnpjValidator;

// Or using function-based one
use function Lacus\CnpjVal\cnpj_val;
```

## Usage

### Object-Oriented Usage

```php
$validator = new CnpjValidator();
$cnpj = '98765432000198';

echo $validator->isValid($cnpj) ? 'Valid' : 'Invalid';  // returns 'Valid'

$cnpj = '98.765.432/0001-98';
echo $validator->isValid($cnpj) ? 'Valid' : 'Invalid';  // returns 'Valid'

$cnpj = '98765432000199';
echo $validator->isValid($cnpj) ? 'Valid' : 'Invalid';  // returns 'Invalid'
```

### Imperative programming

The helper function `cnpj_val()` is just a functional abstraction. Internally it creates an instance of `CnpjValidator` and calls the `isValid()` method right away.

```php
$cnpj = '98765432000198';

echo cnpj_val($cnpj) ? 'Valid' : 'Invalid';      // returns 'Valid'

echo cnpj_val('98.765.432/0001-98') ? 'Valid' : 'Invalid';  // returns 'Valid'

echo cnpj_val('98765432000199') ? 'Valid' : 'Invalid';      // returns 'Invalid'
```

### Validation Examples

```php
// Valid CNPJ numbers
cnpj_val('98765432000198')      // returns true
cnpj_val('98.765.432/0001-98')  // returns true
cnpj_val('03603568000195')      // returns true

// Invalid CNPJ numbers
cnpj_val('98765432000199')      // returns false
cnpj_val('12345678901234')      // returns false
cnpj_val('00000000000000')      // returns false
cnpj_val('11111111111111')      // returns false
cnpj_val('123')                 // returns false (too short)
cnpj_val('')                    // returns false (empty)
```

## Features

- ✅ **Format Agnostic**: Accepts CNPJ with or without formatting (dots, slashes, dashes)
- ✅ **Strict Validation**: Validates both check digits according to Brazilian CNPJ algorithm
- ✅ **Type Safety**: Built with PHP 8.1+ strict types
- ✅ **Lightweight**: Minimal dependencies, only requires `lacus/cnpj-gen` for check digit calculation
- ✅ **Dual API**: Both object-oriented and functional programming styles supported

## API Reference

### CnpjValidator Class

#### `isValid(string $cnpjString): bool`

Validates a CNPJ string and returns `true` if valid, `false` otherwise.

**Parameters:**
- `$cnpjString` (string): The CNPJ to validate (with or without formatting)

**Returns:**
- `bool`: `true` if the CNPJ is valid, `false` otherwise

### cnpj_val() Function

#### `cnpj_val(string $cnpjString): bool`

Functional wrapper around `CnpjValidator::isValid()`.

**Parameters:**
- `$cnpjString` (string): The CNPJ to validate (with or without formatting)

**Returns:**
- `bool`: `true` if the CNPJ is valid, `false` otherwise

## Validation Algorithm

The package validates CNPJ using the official Brazilian algorithm:

1. **Length Check**: Ensures the CNPJ has exactly 14 digits
2. **First Check Digit**: Calculates and validates the 13th digit
3. **Second Check Digit**: Calculates and validates the 14th digit
4. **Format Tolerance**: Automatically strips non-numeric characters before validation

## Error Handling

The validator is designed to be forgiving with input format but strict with validation:

- Invalid formats (too short, too long) return `false`
- Invalid check digits return `false`
- Empty strings return `false`
- Non-numeric strings (after stripping formatting) return `false`

## Dependencies

- **PHP**: >= 8.1
- **lacus/cnpj-gen**: ^1.0 (for check digit calculation)

## Contribution & Support

We welcome contributions! Please see our [Contributing Guidelines](https://github.com/LacusSolutions/br-utils-php/blob/main/CONTRIBUTING.md) for details. But if you find this project helpful, please consider:

- ⭐ Starring the repository
- 🤝 Contributing to the codebase
- 💡 [Suggesting new features](https://github.com/LacusSolutions/br-utils-php/issues)
- 🐛 [Reporting bugs](https://github.com/LacusSolutions/br-utils-php/issues)

## License

This project is licensed under the MIT License - see the [LICENSE](https://github.com/LacusSolutions/br-utils-php/blob/main/LICENSE) file for details.

## Changelog

See [CHANGELOG](https://github.com/LacusSolutions/br-utils-php/blob/main/packages/cnpj-val/CHANGELOG.md) for a list of changes and version history.

---

Made with ❤️ by [Lacus Solutions](https://github.com/LacusSolutions)
