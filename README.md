![cnpj-val for PHP](https://br-utils.vercel.app/img/cover_cnpj-val.jpg)

[![Packagist Version](https://img.shields.io/packagist/v/lacus/cnpj-val)](https://packagist.org/packages/lacus/cnpj-val)
[![Packagist Downloads](https://img.shields.io/packagist/dm/lacus/cnpj-val)](https://packagist.org/packages/lacus/cnpj-val)
[![PHP Version](https://img.shields.io/packagist/php-v/lacus/cnpj-val)](https://www.php.net/)
[![Test Status](https://img.shields.io/github/actions/workflow/status/LacusSolutions/br-utils-php/ci.yml?label=ci/cd)](https://github.com/LacusSolutions/br-utils-php/actions)
[![Last Update Date](https://img.shields.io/github/last-commit/LacusSolutions/br-utils-php)](https://github.com/LacusSolutions/br-utils-php)
[![Project License](https://img.shields.io/github/license/LacusSolutions/br-utils-php)](https://github.com/LacusSolutions/br-utils-php/blob/main/LICENSE)

> 🚀 **Full support for the [new alphanumeric CNPJ format](https://github.com/user-attachments/files/23937961/calculodvcnpjalfanaumerico.pdf).**

> 🌎 [Acessar documentação em português](https://github.com/LacusSolutions/br-utils-php/blob/main/packages/cnpj-val/README.pt.md)

A PHP utility to validate CNPJ (Brazilian Business Tax ID) values.

## PHP Support

| ![PHP 8.2](https://img.shields.io/badge/PHP-8.2-777BB4?logo=php&logoColor=white) | ![PHP 8.3](https://img.shields.io/badge/PHP-8.3-777BB4?logo=php&logoColor=white) | ![PHP 8.4](https://img.shields.io/badge/PHP-8.4-777BB4?logo=php&logoColor=white) | ![PHP 8.5](https://img.shields.io/badge/PHP-8.5-777BB4?logo=php&logoColor=white) |
| --- | --- | --- | --- |
| Passing ✔ | Passing ✔ | Passing ✔ | Passing ✔ |

## Features

- ✅ **Alphanumeric CNPJ**: Validates 14-character CNPJ in numeric or alphanumeric format
- ✅ **Flexible input**: Accepts `string` or `list<string>`; array elements are concatenated in order
- ✅ **Format agnostic**: Strips non-alphanumeric characters (or non-digits when `type` is `numeric`) and optionally uppercases letters
- ✅ **Optional case sensitivity**: When `caseSensitive` is `false`, lowercase letters are accepted for alphanumeric CNPJ
- ✅ **Per-call override model**: Instance defaults can be overridden for one `isValid()` call only
- ✅ **Typed option validation**: Dedicated `TypeError` / `Exception` subclasses for invalid option or input usage
- ✅ **Dual API style**: Object-oriented (`CnpjValidator`) and functional (`cnpj_val()`)

## Installation

```bash
# using Composer
$ composer require lacus/cnpj-val
```

## Import

```php
<?php

use Lacus\BrUtils\Cnpj\CnpjValidator;
use Lacus\BrUtils\Cnpj\CnpjValidatorOptions;
use Lacus\BrUtils\Cnpj\Enums\CnpjValidationType;

use function Lacus\BrUtils\Cnpj\cnpj_val;
```

## Quick start

```php
<?php

use Lacus\BrUtils\Cnpj\CnpjValidator;

$validator = new CnpjValidator();

$validator->isValid('98765432000198');       // true
$validator->isValid('98.765.432/0001-98');   // true
$validator->isValid('98765432000199');       // false

$validator->isValid('1QB5UKALPYFP59');                         // true (alphanumeric)
$validator->isValid('1QB5UKALpyfp59');                         // false (default is case-sensitive)
$validator->isValid('1QB5UKALpyfp59', caseSensitive: false);   // true

$validator->isValid('96206256120884');                                      // true (numeric)
$validator->isValid('1QB5UKALPYFP59', type: CnpjValidationType::Numeric);   // false
```

Functional helper:

```php
cnpj_val('98765432000198');      // true
cnpj_val('98.765.432/0001-98');  // true
cnpj_val('98765432000199');      // false
```

## Usage

The main entry points are the class `CnpjValidator`, the options value object `CnpjValidatorOptions`, the enum `CnpjValidationType`, and the helper `cnpj_val()`.

### `CnpjValidator`

- **`__construct`**: `new CnpjValidator(?CnpjValidatorOptions $options = null, $type = null, $caseSensitive = null)`

  If `$options` is a `CnpjValidatorOptions` instance, that same instance is stored internally (mutations later affect future `isValid()` calls with no per-call override). Otherwise, a new options object is built from named values.

- **`getOptions()`**: Returns the internal `CnpjValidatorOptions` instance.
- **`isValid`**: `isValid(string|list<string> $cnpjInput, ?CnpjValidatorOptions $options = null, $type = null, $caseSensitive = null): bool`

  Per-call options are merged over instance defaults only for that call. Returns `true` when the sanitized input has exactly **14** characters, the last two are digits, and check digits match (`CnpjCheckDigits` from **`lacus/cnpj-dv`**). Otherwise returns `false` (invalid CNPJ, wrong length, ineligible base/branch, etc.) without throwing.

  If the input is not a `string` or a `list` of strings, **`CnpjValidatorInputTypeError`** is thrown.

```php
<?php

use Lacus\BrUtils\Cnpj\CnpjValidator;
use Lacus\BrUtils\Cnpj\Enums\CnpjValidationType;

$validator = new CnpjValidator(type: CnpjValidationType::Numeric);

$validator->isValid('98.765.432/0001-98');   // true
$validator->isValid('1QB5UKALPYFP59');       // false (letters stripped → length ≠ 14)
$validator->isValid('1QB5UKALpyfp59', type: CnpjValidationType::Alphanumeric, caseSensitive: false);  // true
```

Default options on the instance; per-call overrides:

```php
$validator = new CnpjValidator(caseSensitive: false);

$validator->isValid('1qb5ukalpyfp59');                  // true (instance defaults)
$validator->isValid('1qb5ukalpyfp59', caseSensitive: true);  // this call only: false
$validator->isValid('1qb5ukalpyfp59');                  // true again
```

### `CnpjValidatorOptions`

Holds validator settings (`type`, `caseSensitive`). Construct with named parameters and optional `overrides` (list of arrays and/or other `CnpjValidatorOptions` instances, merged in order). Exposes properties via magic `__get` / `__set`.

- **`getAll()`**: Returns a shallow array snapshot of all options.
- **`set(...)`**: Updates multiple fields at once; returns `$this`.

### `CnpjValidationType`

Backed enum for the `type` option:

- `CnpjValidationType::Alphanumeric` (`"alphanumeric"`) — default; keeps `0`–`9` and `A`–`Z` after sanitization.
- `CnpjValidationType::Numeric` (`"numeric"`) — legacy numeric-only CNPJ; strips everything except `0`–`9`.

Helper methods:

- `CnpjValidationType::values(): list<string>`
- `toSequenceType(): SequenceType` (from **`lacus/utils`**)

String literals `'alphanumeric'` and `'numeric'` are also accepted wherever `type` is documented.

### Functional helper

`cnpj_val()` run the validation on a `CnpjValidator` instance with the same arguments passed to the function. Use named arguments for options:

```php
cnpj_val('98765432000198');                              // true
cnpj_val('1QB5UKALpyfp59', caseSensitive: false);        // true
cnpj_val('1QB5UKALPYFP59', type: CnpjValidationType::Numeric);  // false
```

To pass a full options object as the second argument: `cnpj_val($cnpj, new CnpjValidatorOptions(type: CnpjValidationType::Numeric))`.

### Input formats

**String:** Raw digits and/or letters, or formatted CNPJ (e.g. `98.765.432/0001-98`, `1Q.B5U.KAL/PYFP-59`). Characters are stripped according to `type`; when `caseSensitive` is `false`, letters are uppercased before alphanumeric validation.

**Array of strings:** Each element must be a string; values are concatenated (e.g. per digit, grouped segments, or mixed with punctuation). Non-string elements throw **`CnpjValidatorInputTypeError`**.

### Validation options

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `type` | `CnpjValidationType\|'alphanumeric'\|'numeric'\|null` | `CnpjValidationType::Alphanumeric` | Character set after sanitization: alphanumeric (`0`–`9`, `A`–`Z`) or numeric-only (`0`–`9`) |
| `caseSensitive` | `?bool` | `true` | When `false`, lowercase letters are uppercased before alphanumeric validation |

Invalid CNPJ (wrong length after sanitization, invalid check digits, ineligible base/branch `00000000` / `0000`, repeated digits, non-numeric verifier digits) returns **`false`** — no exception is thrown for validation failure.

### Errors & exceptions

This package uses **TypeError** for invalid option/input types and **Exception** for invalid option values. Validation failures return `false`.

- **Wrong input type** (not `string` or `list<string>`): **`CnpjValidatorInputTypeError`** — extends **`CnpjValidatorTypeError`** (extends PHP `TypeError`).
- **Invalid option types when constructing or merging options**: **`CnpjValidatorOptionsTypeError`**.
- **Invalid `type` value** (not `alphanumeric` / `numeric`): **`CnpjValidatorOptionTypeInvalidException`** — extends **`CnpjValidatorException`**.

```php
<?php

use Lacus\BrUtils\Cnpj\CnpjValidator;
use Lacus\BrUtils\Cnpj\Exceptions\CnpjValidatorInputTypeError;
use Lacus\BrUtils\Cnpj\Exceptions\CnpjValidatorOptionTypeInvalidException;

try {
    (new CnpjValidator())->isValid(12345678000198);
} catch (CnpjValidatorInputTypeError $e) {
    echo $e->getMessage();
}

try {
    new CnpjValidator(type: 'invalid');
} catch (CnpjValidatorOptionTypeInvalidException $e) {
    echo $e->getMessage();
}
```

### Other available resources

- **`CnpjValidatorOptions::CNPJ_LENGTH`**: `14` — standard CNPJ length after sanitization.

## Contribution & Support

We welcome contributions! Please see our [Contributing Guidelines](https://github.com/LacusSolutions/br-utils-php/blob/main/CONTRIBUTING.md) for details. If you find this project helpful, please consider:

- ⭐ Starring the repository
- 🤝 Contributing to the codebase
- 💡 [Suggesting new features](https://github.com/LacusSolutions/br-utils-php/issues)
- 🐛 [Reporting bugs](https://github.com/LacusSolutions/br-utils-php/issues)

## License

This project is licensed under the MIT License — see the [LICENSE](https://github.com/LacusSolutions/br-utils-php/blob/main/LICENSE) file for details.

## Changelog

See [CHANGELOG](https://github.com/LacusSolutions/br-utils-php/blob/main/packages/cnpj-val/CHANGELOG.md) for a list of changes and version history.

---

Made with ❤️ by [Lacus Solutions](https://github.com/LacusSolutions)
