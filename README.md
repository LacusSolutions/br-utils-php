![cpf-val for PHP](https://github.com/user-attachments/assets/eb1cdd2b-8e01-4771-8ade-899f1f18349b)

[![Packagist Version](https://img.shields.io/packagist/v/lacus/cpf-val)](https://packagist.org/packages/lacus/cpf-val)
[![Packagist Downloads](https://img.shields.io/packagist/dm/lacus/cpf-val)](https://packagist.org/packages/lacus/cpf-val)
[![PHP Version](https://img.shields.io/packagist/php-v/lacus/cpf-val)](https://www.php.net/)
[![Test Status](https://img.shields.io/github/actions/workflow/status/LacusSolutions/br-utils-php/ci.yml?label=ci/cd)](https://github.com/LacusSolutions/br-utils-php/actions)
[![Last Update Date](https://img.shields.io/github/last-commit/LacusSolutions/br-utils-php)](https://github.com/LacusSolutions/br-utils-php)
[![Project License](https://img.shields.io/github/license/LacusSolutions/br-utils-php)](https://github.com/LacusSolutions/br-utils-php/blob/main/LICENSE)

Utility function/class to validate CPF (Brazilian personal ID).

## PHP Support

| ![PHP 8.1](https://img.shields.io/badge/PHP-8.1-777BB4?logo=php&logoColor=white) | ![PHP 8.2](https://img.shields.io/badge/PHP-8.2-777BB4?logo=php&logoColor=white) | ![PHP 8.3](https://img.shields.io/badge/PHP-8.3-777BB4?logo=php&logoColor=white) | ![PHP 8.4](https://img.shields.io/badge/PHP-8.4-777BB4?logo=php&logoColor=white) |
|--- | --- | --- | --- |
| Passing ✔ | Passing ✔ | Passing ✔ | Passing ✔ |

## Installation

```bash
# using Composer
$ composer require lacus/cpf-val
```

## Import

```php
<?php
// Using class-based resource
use Lacus\CpfVal\CpfValidator;

// Or using function-based one
use function Lacus\CpfVal\cpf_val;
```

## Usage

### Object-Oriented Usage

```php
$validator = new CpfValidator();
$cpf = '11144477735';

echo $validator->isValid($cpf) ? 'Valid' : 'Invalid';  // returns 'Valid'

$cpf = '111.444.777-35';
echo $validator->isValid($cpf) ? 'Valid' : 'Invalid';  // returns 'Valid'

$cpf = '11144477736';
echo $validator->isValid($cpf) ? 'Valid' : 'Invalid';  // returns 'Invalid'
```

### Imperative programming

The helper function `cpf_val()` is just a functional abstraction. Internally it creates an instance of `CpfValidator` and calls the `isValid()` method right away.

```php
$cpf = '11144477735';

echo cpf_val($cpf) ? 'Valid' : 'Invalid';      // returns 'Valid'

echo cpf_val('111.444.777-35') ? 'Valid' : 'Invalid';  // returns 'Valid'

echo cpf_val('11144477736') ? 'Valid' : 'Invalid';     // returns 'Invalid'
```

### Validation Examples

```php
// Valid CPF numbers
cpf_val('11144477735')      // returns true
cpf_val('111.444.777-35')   // returns true
cpf_val('12345678909')      // returns true

// Invalid CPF numbers
cpf_val('11144477736')      // returns false
cpf_val('12345678901')      // returns false
cpf_val('00000000000')      // returns false
cpf_val('11111111111')      // returns false
cpf_val('123')              // returns false (too short)
cpf_val('')                 // returns false (empty)
```

## Features

- ✅ **Format Agnostic**: Accepts CPF with or without formatting (dots, dashes)
- ✅ **Strict Validation**: Validates both check digits according to Brazilian CPF algorithm
- ✅ **Type Safety**: Built with PHP 8.1+ strict types
- ✅ **Lightweight**: Minimal dependencies, only requires `lacus/cpf-gen` for check digit calculation
- ✅ **Dual API**: Both object-oriented and functional programming styles supported

## API Reference

### CpfValidator Class

#### `isValid(string $cpfString): bool`

Validates a CPF string and returns `true` if valid, `false` otherwise.

**Parameters:**
- `$cpfString` (string): The CPF to validate (with or without formatting)

**Returns:**
- `bool`: `true` if the CPF is valid, `false` otherwise

### cpf_val() Function

#### `cpf_val(string $cpfString): bool`

Functional wrapper around `CpfValidator::isValid()`.

**Parameters:**
- `$cpfString` (string): The CPF to validate (with or without formatting)

**Returns:**
- `bool`: `true` if the CPF is valid, `false` otherwise

## Validation Algorithm

The package validates CPF using the official Brazilian algorithm:

1. **Length Check**: Ensures the CPF has exactly 11 digits
2. **First Check Digit**: Calculates and validates the 10th digit
3. **Second Check Digit**: Calculates and validates the 11th digit
4. **Format Tolerance**: Automatically strips non-numeric characters before validation

## Error Handling

The validator is designed to be forgiving with input format but strict with validation:

- Invalid formats (too short, too long) return `false`
- Invalid check digits return `false`
- Empty strings return `false`
- Non-numeric strings (after stripping formatting) return `false`

## Dependencies

- **PHP**: >= 8.1
- **lacus/cpf-gen**: ^1.0 (for check digit calculation)

## Contribution & Support

We welcome contributions! Please see our [Contributing Guidelines](https://github.com/LacusSolutions/br-utils-php/blob/main/CONTRIBUTING.md) for details. But if you find this project helpful, please consider:

- ⭐ Starring the repository
- 🤝 Contributing to the codebase
- 💡 [Suggesting new features](https://github.com/LacusSolutions/br-utils-php/issues)
- 🐛 [Reporting bugs](https://github.com/LacusSolutions/br-utils-php/issues)

## License

This project is licensed under the MIT License - see the [LICENSE](https://github.com/LacusSolutions/br-utils-php/blob/main/LICENSE) file for details.

## Changelog

See [CHANGELOG](https://github.com/LacusSolutions/br-utils-php/blob/main/packages/cpf-val/CHANGELOG.md) for a list of changes and version history.

---

Made with ❤️ by [Lacus Solutions](https://github.com/LacusSolutions)
