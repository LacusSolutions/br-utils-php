![cnpj-gen for PHP](https://br-utils.vercel.app/img/cover_cnpj-gen.jpg)

[![Packagist Version](https://img.shields.io/packagist/v/lacus/cnpj-gen)](https://packagist.org/packages/lacus/cnpj-gen)
[![Packagist Downloads](https://img.shields.io/packagist/dm/lacus/cnpj-gen)](https://packagist.org/packages/lacus/cnpj-gen)
[![PHP Version](https://img.shields.io/packagist/php-v/lacus/cnpj-gen)](https://www.php.net/)
[![Test Status](https://img.shields.io/github/actions/workflow/status/LacusSolutions/br-utils-php/ci.yml?label=ci/cd)](https://github.com/LacusSolutions/br-utils-php/actions)
[![Last Update Date](https://img.shields.io/github/last-commit/LacusSolutions/br-utils-php)](https://github.com/LacusSolutions/br-utils-php)
[![Project License](https://img.shields.io/github/license/LacusSolutions/br-utils-php)](https://github.com/LacusSolutions/br-utils-php/blob/main/LICENSE)

> 🚀 **Full support for the [new alphanumeric CNPJ format](https://github.com/user-attachments/files/23937961/calculodvcnpjalfanaumerico.pdf).**

> 🌎 [Acessar documentação em português](https://github.com/LacusSolutions/br-utils-php/blob/main/packages/cnpj-gen/README.pt.md)

A PHP utility to generate valid CNPJ (Brazilian Business Tax ID).

## PHP Support

| ![PHP 8.2](https://img.shields.io/badge/PHP-8.2-777BB4?logo=php&logoColor=white) | ![PHP 8.3](https://img.shields.io/badge/PHP-8.3-777BB4?logo=php&logoColor=white) | ![PHP 8.4](https://img.shields.io/badge/PHP-8.4-777BB4?logo=php&logoColor=white) | ![PHP 8.5](https://img.shields.io/badge/PHP-8.5-777BB4?logo=php&logoColor=white) |
| --- | --- | --- | --- |
| Passing ✔ | Passing ✔ | Passing ✔ | Passing ✔ |

## Features

- ✅ **Alphanumeric CNPJ generation**: Supports base characters from `0-9` and `A-Z` with numeric check digits
- ✅ **Flexible options API**: Use named arguments or a `CnpjGeneratorOptions` instance
- ✅ **Configurable base prefix**: Provide up to 12 base characters and generate only missing positions
- ✅ **Format output toggle**: Return compact (`14` chars) or formatted (`18` chars) output
- ✅ **Per-call override model**: Instance defaults can be overridden for one `generate()` call only
- ✅ **Typed option validation**: Dedicated `TypeError`/`Exception` subclasses for invalid option usage
- ✅ **Dual API style**: Object-oriented (`CnpjGenerator`) and functional (`cnpj_gen()`)

## Installation

```bash
# using Composer
$ composer require lacus/cnpj-gen
```

## Import

```php
<?php

use Lacus\BrUtils\Cnpj\CnpjGenerator;
use Lacus\BrUtils\Cnpj\CnpjGeneratorOptions;
use Lacus\BrUtils\Cnpj\Enums\CnpjType;

use function Lacus\BrUtils\Cnpj\cnpj_gen;
```

## Quick start

```php
<?php

use Lacus\BrUtils\Cnpj\CnpjGenerator;

$generator = new CnpjGenerator();

$generator->generate();                // e.g. "AB123CDE000196"
$generator->generate(format: true);    // e.g. "AB.123.CDE/0001-96"
```

## Usage

The main entry points are the class `CnpjGenerator`, the options object `CnpjGeneratorOptions`, the enum `CnpjType`, and the helper `cnpj_gen()`.

### `CnpjGenerator`

- **`__construct`**: `new CnpjGenerator(?CnpjGeneratorOptions $options = null, $format = null, $prefix = null, $type = null)`

  If `$options` is a `CnpjGeneratorOptions` instance, that same instance is stored internally (mutations later affect future `generate()` calls with no per-call override). Otherwise, a new options object is built from named values.

- **`getOptions()`**: Returns the internal `CnpjGeneratorOptions` instance.
- **`generate`**: `generate(?CnpjGeneratorOptions $options = null, $format = null, $prefix = null, $type = null): string`

  Per-call options are merged over instance defaults only for that call. Returned value is:

  - `14` characters when `format = false` (default)
  - `18` characters with separators when `format = true` (`XX.XXX.XXX/XXXX-XX`)

```php
<?php

use Lacus\BrUtils\Cnpj\CnpjGenerator;
use Lacus\BrUtils\Cnpj\Enums\CnpjType;

$generator = new CnpjGenerator(type: CnpjType::Numeric);

$generator->generate();                                 // e.g. "12345678000195"
$generator->generate(format: true);                     // e.g. "12.345.678/0001-95"
$generator->generate(prefix: 'AB123CDE');               // e.g. "AB123CDE000196"
$generator->generate(prefix: 'AB123CDE', format: true); // e.g. "AB.123.CDE/0001-96"
```

Default options on the instance; per-call overrides:

```php
$generator = new CnpjGenerator(format: true, type: CnpjType::Numeric);

$generator->generate();                // formatted numeric CNPJ
$generator->generate(format: false);   // this call only: unformatted
$generator->generate();                // formatted again (instance defaults preserved)
```

### `CnpjGeneratorOptions`

`CnpjGeneratorOptions` encapsulates generator configuration (`format`, `prefix`, `type`), supports magic property access (`__get`/`__set`), and can merge layered values through `overrides`.

- **Constructor**: `new CnpjGeneratorOptions($format = null, $prefix = null, $type = null, ?array $overrides = [])`
  - `overrides` accepts a list of arrays and/or other `CnpjGeneratorOptions` instances
  - merge order is left to right (last override wins)
- **`set(...)`**: Updates one or more option values and returns `$this`
- **`getAll()`**: Returns a shallow snapshot array (`format`, `prefix`, `type`)

### Generation options

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `format` | `?bool` | `false` | When `true`, returns formatted CNPJ (`XX.XXX.XXX/XXXX-XX`); otherwise returns compact 14-character output |
| `prefix` | `?string` | `''` | Base seed for generation. Non-alphanumeric chars are stripped, letters are uppercased, and only first 12 chars (indexes `0`-`11`) are used; characters at index `12+` are ignored |
| `type` | `CnpjType\|'alphanumeric'\|'alphabetic'\|'numeric'\|null` | `CnpjType::Alphanumeric` | Character family used for generated base positions (`0-9`, `A-Z`, or both) |

`prefix` validation rules:

- base ID `00000000` is rejected (when first 8 chars are present)
- branch ID `0000` is rejected (when chars 9-12 are present)
- 12 repeated numeric digits are rejected (e.g. `111111111111`)

### `CnpjType`

Available enum cases:

- `CnpjType::Alphanumeric`
- `CnpjType::Alphabetic`
- `CnpjType::Numeric`

Helper methods:

- `CnpjType::values(): list<string>`
- `CnpjType::toSequenceType(): SequenceType`

### Functional helper

`cnpj_gen()` is a convenience wrapper:

- Builds a new `CnpjGenerator` with the same constructor arguments
- Calls `generate()` once

```php
$cnpj = cnpj_gen();               // e.g. "AB123CDE000196"
$cnpj = cnpj_gen(format: true);   // e.g. "AB.123.CDE/0001-96"
$cnpj = cnpj_gen(prefix: '12345678', type: CnpjType::Numeric);
```

### Errors & exceptions

This package uses **TypeError vs Exception** semantics:

- **Type errors** indicate wrong API/option types
- **Exceptions** indicate invalid option values or business-rule violations

Relevant classes:

- `CnpjGeneratorTypeError` (abstract, extends PHP `TypeError`)
- `CnpjGeneratorOptionsTypeError`
- `CnpjGeneratorException` (abstract, extends `Exception`)
- `CnpjGeneratorOptionPrefixInvalidException`
- `CnpjGeneratorOptionTypeInvalidException`

```php
<?php

use Lacus\BrUtils\Cnpj\CnpjGenerator;
use Lacus\BrUtils\Cnpj\Exceptions\CnpjGeneratorOptionPrefixInvalidException;
use Lacus\BrUtils\Cnpj\Exceptions\CnpjGeneratorOptionTypeInvalidException;
use Lacus\BrUtils\Cnpj\Exceptions\CnpjGeneratorOptionsTypeError;

try {
    $generator = new CnpjGenerator(prefix: '00000000');
    $generator->generate();
} catch (CnpjGeneratorOptionPrefixInvalidException $e) {
    echo $e->getMessage();
}

try {
    new CnpjGenerator(type: 'invalid');
} catch (CnpjGeneratorOptionTypeInvalidException $e) {
    echo $e->getMessage();
}

try {
    new CnpjGenerator(prefix: 123);
} catch (CnpjGeneratorOptionsTypeError $e) {
    echo $e->getMessage();
}
```

### Other available resources

- `CnpjGeneratorOptions::CNPJ_LENGTH` (`14`)
- `CnpjGeneratorOptions::CNPJ_PREFIX_MAX_LENGTH` (`12`)

## Contribution & Support

We welcome contributions! Please see our [Contributing Guidelines](https://github.com/LacusSolutions/br-utils-php/blob/main/CONTRIBUTING.md) for details. If you find this project helpful, please consider:

- ⭐ Starring the repository
- 🤝 Contributing to the codebase
- 💡 [Suggesting new features](https://github.com/LacusSolutions/br-utils-php/issues)
- 🐛 [Reporting bugs](https://github.com/LacusSolutions/br-utils-php/issues)

## License

This project is licensed under the MIT License — see the [LICENSE](https://github.com/LacusSolutions/br-utils-php/blob/main/LICENSE) file for details.

## Changelog

See [CHANGELOG](https://github.com/LacusSolutions/br-utils-php/blob/main/packages/cnpj-gen/CHANGELOG.md) for a list of changes and version history.

---

Made with ❤️ by [Lacus Solutions](https://github.com/LacusSolutions)
