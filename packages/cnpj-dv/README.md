![cnpj-dv for PHP](https://br-utils.vercel.app/img/cover_cnpj-dv.jpg)

[![Packagist Version](https://img.shields.io/packagist/v/lacus/cnpj-dv)](https://packagist.org/packages/lacus/cnpj-dv)
[![Packagist Downloads](https://img.shields.io/packagist/dm/lacus/cnpj-dv)](https://packagist.org/packages/lacus/cnpj-dv)
[![PHP Version](https://img.shields.io/packagist/php-v/lacus/cnpj-dv)](https://www.php.net/)
[![Test Status](https://img.shields.io/github/actions/workflow/status/LacusSolutions/br-utils-php/ci.yml?label=ci/cd)](https://github.com/LacusSolutions/br-utils-php/actions)
[![Last Update Date](https://img.shields.io/github/last-commit/LacusSolutions/br-utils-php)](https://github.com/LacusSolutions/br-utils-php)
[![Project License](https://img.shields.io/github/license/LacusSolutions/br-utils-php)](https://github.com/LacusSolutions/br-utils-php/blob/main/LICENSE)

> 🚀 **Full support for the [new alphanumeric CNPJ format](https://github.com/user-attachments/files/23937961/calculodvcnpjalfanaumerico.pdf).**

> 🌎 [Acessar documentação em português](https://github.com/LacusSolutions/br-utils-php/blob/main/packages/cnpj-dv/README.pt.md)

A PHP utility to calculate check digits on CNPJ (Brazilian Business Tax ID).

## PHP Support

| ![PHP 8.2](https://img.shields.io/badge/PHP-8.2-777BB4?logo=php&logoColor=white) | ![PHP 8.3](https://img.shields.io/badge/PHP-8.3-777BB4?logo=php&logoColor=white) | ![PHP 8.4](https://img.shields.io/badge/PHP-8.4-777BB4?logo=php&logoColor=white) | ![PHP 8.5](https://img.shields.io/badge/PHP-8.5-777BB4?logo=php&logoColor=white) |
| --- | --- | --- | --- |
| Passing ✔ | Passing ✔ | Passing ✔ | Passing ✔ |

## Features

- ✅ **Alphanumeric CNPJ**: Full support for the new alphanumeric CNPJ format (introduced in 2026)
- ✅ **Flexible input**: Accepts `string` or `array` of strings
- ✅ **Format agnostic**: Strips non-alphanumeric characters from string input and uppercases letters
- ✅ **Auto-expansion**: Multi-character strings in arrays are joined and parsed like a single string
- ✅ **Input validation**: Rejects ineligible CNPJs (all-zero base ID `00000000`, all-zero branch `0000`, or 12 numeric-only repeated digits)
- ✅ **Lazy evaluation**: Check digits are calculated only when accessed (via properties)
- ✅ **Caching**: Calculated values are cached for subsequent access
- ✅ **Property-style API**: `first`, `second`, `both`, `cnpj` (via magic `__get`)
- ✅ **Minimal dependencies**: Only [`lacus/utils`](https://packagist.org/packages/lacus/utils)
- ✅ **Error handling**: Specific types for type, length, and invalid CNPJ scenarios (`TypeError` vs `Exception` semantics)

## Installation

```bash
# using Composer
$ composer require lacus/cnpj-dv
```

## Quick Start

```php
<?php

use Lacus\BrUtils\Cnpj\CnpjCheckDigits;

$checkDigits = new CnpjCheckDigits('914157320007');

$checkDigits->first;    // '9'
$checkDigits->second;   // '3'
$checkDigits->both;     // '93'
$checkDigits->cnpj;     // '91415732000793'
```

With alphanumeric CNPJ (new format):

```php
<?php

use Lacus\BrUtils\Cnpj\CnpjCheckDigits;

$checkDigits = new CnpjCheckDigits('MGKGMJ9X0001');

$checkDigits->first;    // '6'
$checkDigits->second;   // '8'
$checkDigits->both;     // '68'
$checkDigits->cnpj;     // 'MGKGMJ9X000168'
```

## Usage

The main resource of this package is the class `CnpjCheckDigits`. Through an instance, you access CNPJ check-digit information:

- **`__construct`**: `new CnpjCheckDigits(string|array $cnpjInput)` — 12–14 alphanumeric characters after sanitization (formatting stripped from strings; letters uppercased). Only the **first 12** characters are used as the base; if you pass 13 or 14 characters (e.g. a full CNPJ including prior check digits), characters 13–14 are **ignored** and the digits are recalculated.
- **`first`**: First check digit (13th character of the full CNPJ). Lazy, cached.
- **`second`**: Second check digit (14th character of the full CNPJ). Lazy, cached.
- **`both`**: Both check digits concatenated as a string.
- **`cnpj`**: The complete CNPJ as a string of 14 characters (12 base characters + 2 check digits).

### Input formats

The `CnpjCheckDigits` class accepts multiple input formats:

**String input:** raw digits and/or letters, or formatted CNPJ (e.g. `91.415.732/0007-93`, `MG.KGM.J9X/0001-68`). Non-alphanumeric characters are removed; lowercase letters are uppercased.

**Array of strings:** each element must be a string; values are concatenated and then parsed like a single string (e.g. `['9','1','4',…]`, `['9141','5732','0007']`, `['MG','KGM','J9X','0001']`). Non-string elements are not allowed.

### Errors & exceptions handling

This package uses **TypeError vs Exception** semantics: *type errors* indicate incorrect API use (e.g. wrong type); *exceptions* indicate invalid or ineligible data (e.g. invalid length or business rules). You can catch specific classes or use the abstract bases.

- **CnpjCheckDigitsTypeError** (_abstract_) — base for type errors; extends PHP’s `TypeError`
- **CnpjCheckDigitsInputTypeError** — input is not `string` or `array` of strings (or array contains a non-string element)
- **CnpjCheckDigitsException** (_abstract_) — base for data/flow exceptions; extends `Exception`
- **CnpjCheckDigitsInputLengthException** — sanitized length is not 12–14
- **CnpjCheckDigitsInputInvalidException** — base ID `00000000`, branch ID `0000`, or 12 identical numeric digits (repeated-digit pattern)

```php
<?php

use Lacus\BrUtils\Cnpj\CnpjCheckDigits;
use Lacus\BrUtils\Cnpj\Exceptions\CnpjCheckDigitsException;
use Lacus\BrUtils\Cnpj\Exceptions\CnpjCheckDigitsInputInvalidException;
use Lacus\BrUtils\Cnpj\Exceptions\CnpjCheckDigitsInputLengthException;
use Lacus\BrUtils\Cnpj\Exceptions\CnpjCheckDigitsInputTypeError;

// Input type (e.g. integer not allowed)
try {
    new CnpjCheckDigits(12345678000100);
} catch (CnpjCheckDigitsInputTypeError $e) {
    echo $e->getMessage();
}

// Length (must be 12–14 alphanumeric characters after sanitization)
try {
    new CnpjCheckDigits('12345678901');
} catch (CnpjCheckDigitsInputLengthException $e) {
    echo $e->getMessage();
}

// Invalid (e.g. all-zero base or branch, or repeated numeric digits)
try {
    new CnpjCheckDigits('000000000001');
} catch (CnpjCheckDigitsInputInvalidException $e) {
    echo $e->getMessage();
}

// Any data exception from the package
try {
    // risky code
} catch (CnpjCheckDigitsException $e) {
    // handle
}
```

### Other available resources

- **`CNPJ_MIN_LENGTH`**: `12` — class constant `CnpjCheckDigits::CNPJ_MIN_LENGTH`, and global `Lacus\BrUtils\Cnpj\CNPJ_MIN_LENGTH` when the autoloaded `cnpj-dv.php` file is loaded.
- **`CNPJ_MAX_LENGTH`**: `14` — class constant `CnpjCheckDigits::CNPJ_MAX_LENGTH`, and global `Lacus\BrUtils\Cnpj\CNPJ_MAX_LENGTH` when `cnpj-dv.php` is loaded.

## Calculation algorithm

The package computes check digits with the official Brazilian modulo-11 rules extended to alphanumeric characters:

1. **Character value:** each character contributes `ord(character) − 48` (so `0`–`9` stay 0–9; letters use their ASCII offset from `0`).
2. **Weights:** from **right to left**, multiply by weights that cycle **2, 3, 4, 5, 6, 7, 8, 9**, then repeat from 2.
3. **First check digit (13th position):** apply steps 1–2 to the first **12** base characters; let `r = sum % 11`. The digit is `0` if `r < 2`, otherwise `11 − r`.
4. **Second check digit (14th position):** apply steps 1–2 to the first 12 characters **plus** the first check digit; same formula for `r`.

## Contribution & Support

We welcome contributions! Please see our [Contributing Guidelines](https://github.com/LacusSolutions/br-utils-php/blob/main/CONTRIBUTING.md) for details. If you find this project helpful, please consider:

- ⭐ Starring the repository
- 🤝 Contributing to the codebase
- 💡 [Suggesting new features](https://github.com/LacusSolutions/br-utils-php/issues)
- 🐛 [Reporting bugs](https://github.com/LacusSolutions/br-utils-php/issues)

## License

This project is licensed under the MIT License — see the [LICENSE](https://github.com/LacusSolutions/br-utils-php/blob/main/LICENSE) file for details.

## Changelog

See [CHANGELOG](https://github.com/LacusSolutions/br-utils-php/blob/main/packages/cnpj-dv/CHANGELOG.md) for a list of changes and version history.

---

Made with ❤️ by [Lacus Solutions](https://github.com/LacusSolutions)
