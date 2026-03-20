![cpf-dv for PHP](https://br-utils.vercel.app/img/cover_cpf-dv.jpg)

[![Packagist Version](https://img.shields.io/packagist/v/lacus/cpf-dv)](https://packagist.org/packages/lacus/cpf-dv)
[![Packagist Downloads](https://img.shields.io/packagist/dm/lacus/cpf-dv)](https://packagist.org/packages/lacus/cpf-dv)
[![PHP Version](https://img.shields.io/packagist/php-v/lacus/cpf-dv)](https://www.php.net/)
[![Test Status](https://img.shields.io/github/actions/workflow/status/LacusSolutions/br-utils-php/ci.yml?label=ci/cd)](https://github.com/LacusSolutions/br-utils-php/actions)
[![Last Update Date](https://img.shields.io/github/last-commit/LacusSolutions/br-utils-php)](https://github.com/LacusSolutions/br-utils-php)
[![Project License](https://img.shields.io/github/license/LacusSolutions/br-utils-php)](https://github.com/LacusSolutions/br-utils-php/blob/main/LICENSE)

> 🌎 [Acessar documentação em português](https://github.com/LacusSolutions/br-utils-php/blob/main/packages/cpf-dv/README.pt.md)

A PHP utility to calculate check digits on CPF (Brazilian Individual's Taxpayer ID).

## PHP Support

| ![PHP 8.2](https://img.shields.io/badge/PHP-8.2-777BB4?logo=php&logoColor=white) | ![PHP 8.3](https://img.shields.io/badge/PHP-8.3-777BB4?logo=php&logoColor=white) | ![PHP 8.4](https://img.shields.io/badge/PHP-8.4-777BB4?logo=php&logoColor=white) | ![PHP 8.5](https://img.shields.io/badge/PHP-8.5-777BB4?logo=php&logoColor=white) |
| --- | --- | --- | --- |
| Passing ✔ | Passing ✔ | Passing ✔ | Passing ✔ |

## Features

- ✅ **Flexible input**: Accepts `string` or `array` of strings
- ✅ **Format agnostic**: Automatically strips non-numeric characters from string input
- ✅ **Auto-expansion**: Multi-character strings in arrays are expanded to individual digits
- ✅ **Lazy evaluation**: Check digits are calculated only when accessed (via properties)
- ✅ **Caching**: Calculated values are cached for subsequent access
- ✅ **Property-style API**: `first`, `second`, `both`, `cpf` (via magic `__get`)
- ✅ **Minimal dependencies**: Only [`lacus/utils`](https://packagist.org/packages/lacus/utils)
- ✅ **Error handling**: Specific types for type, length, and invalid CPF scenarios (`TypeError` vs `Exception` semantics)

## Installation

```bash
# using Composer
$ composer require lacus/cpf-dv
```

## Quick Start

```php
<?php

use Lacus\BrUtils\Cpf\CpfCheckDigits;

$checkDigits = new CpfCheckDigits('054496519');

$checkDigits->first;    // '1'
$checkDigits->second;   // '0'
$checkDigits->both;     // '10'
$checkDigits->cpf;      // '05449651910'
```

## Usage

The main resource of this package is the class `CpfCheckDigits`. Through an instance, you access CPF check-digit information:

- **`__construct`**: `new CpfCheckDigits(string|array $cpfInput)` — 9–11 digits (formatting stripped from strings).
- **`first`**: First check digit (10th digit of the CPF). Lazy, cached.
- **`second`**: Second check digit (11th digit of the CPF). Lazy, cached.
- **`both`**: Both check digits concatenated as a string.
- **`cpf`**: The complete CPF as a string of 11 digits (9 base digits + 2 check digits).

### Input formats

The `CpfCheckDigits` class accepts multiple input formats:

**String input:** plain digits or formatted CPF (e.g. `054.496.519-10`). Non-numeric characters are automatically stripped. Use 9 digits (base only) or 11 digits (only the first 9 are used).

**Array of strings:** single-character strings or multi-character strings (expanded to individual digits), e.g. `['0','5','4','4','9','6','5','1','9']`, `['054496519']`, `['054','496','519']`.

### Errors & exceptions handling

This package uses **TypeError vs Exception** semantics: *type errors* indicate incorrect API use (e.g. wrong type); *exceptions* indicate invalid or ineligible data (e.g. invalid CPF). You can catch specific classes or use the abstract bases.

- **CpfCheckDigitsTypeError** (_abstract_) — base for type errors; extends PHP’s `TypeError`
- **CpfCheckDigitsInputTypeError** — input is not `string` or `string[]`
- **CpfCheckDigitsException** (_abstract_) — base for data/flow exceptions; extends `Exception`
- **CpfCheckDigitsInputLengthException** — sanitized length is not 9–11
- **CpfCheckDigitsInputInvalidException** — input ineligible (e.g. repeated digits like `111111111`)

```php
<?php

use Lacus\BrUtils\Cpf\CpfCheckDigits;
use Lacus\BrUtils\Cpf\Exceptions\CpfCheckDigitsException;
use Lacus\BrUtils\Cpf\Exceptions\CpfCheckDigitsInputInvalidException;
use Lacus\BrUtils\Cpf\Exceptions\CpfCheckDigitsInputLengthException;
use Lacus\BrUtils\Cpf\Exceptions\CpfCheckDigitsInputTypeError;

// Input type (e.g. integer not allowed)
try {
    new CpfCheckDigits(12345678901);
} catch (CpfCheckDigitsInputTypeError $e) {
    echo $e->getMessage();
}

// Length (must be 9–11 digits after sanitization)
try {
    new CpfCheckDigits('12345678');
} catch (CpfCheckDigitsInputLengthException $e) {
    echo $e->getMessage();
}

// Invalid (e.g. repeated digits)
try {
    new CpfCheckDigits(['999', '999', '999']);
} catch (CpfCheckDigitsInputInvalidException $e) {
    echo $e->getMessage();
}

// Any data exception from the package
try {
    // risky code
} catch (CpfCheckDigitsException $e) {
    // handle
}
```

### Other available resources

- **`CPF_MIN_LENGTH`**: `9` — class constant `CpfCheckDigits::CPF_MIN_LENGTH`, and global `Lacus\BrUtils\Cpf\CPF_MIN_LENGTH` when the autoloaded `cpf-dv.php` file is loaded.
- **`CPF_MAX_LENGTH`**: `11` — class constant `CpfCheckDigits::CPF_MAX_LENGTH`, and global `Lacus\BrUtils\Cpf\CPF_MAX_LENGTH` when `cpf-dv.php` is loaded.

## Calculation algorithm

The package calculates CPF check digits using the official Brazilian algorithm:

1. **First check digit (10th position):** digits 1–9 of the CPF base; weights 10, 9, 8, 7, 6, 5, 4, 3, 2 (from left to right); `remainder = 11 - (sum(digit × weight) % 11)`; result is `0` if remainder > 9, otherwise `remainder`.
2. **Second check digit (11th position):** digits 1–9 + first check digit; weights 11, 10, 9, 8, 7, 6, 5, 4, 3, 2 (from left to right); same formula.

## Contribution & Support

We welcome contributions! Please see our [Contributing Guidelines](https://github.com/LacusSolutions/br-utils-php/blob/main/CONTRIBUTING.md) for details. If you find this project helpful, please consider:

- ⭐ Starring the repository
- 🤝 Contributing to the codebase
- 💡 [Suggesting new features](https://github.com/LacusSolutions/br-utils-php/issues)
- 🐛 [Reporting bugs](https://github.com/LacusSolutions/br-utils-php/issues)

## License

This project is licensed under the MIT License — see the [LICENSE](https://github.com/LacusSolutions/br-utils-php/blob/main/LICENSE) file for details.

## Changelog

See [CHANGELOG](https://github.com/LacusSolutions/br-utils-php/blob/main/packages/cpf-dv/CHANGELOG.md) for a list of changes and version history.

---

Made with ❤️ by [Lacus Solutions](https://github.com/LacusSolutions)
