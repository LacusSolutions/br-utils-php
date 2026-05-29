![cnpj-utils for PHP](https://br-utils.vercel.app/img/cover_cnpj-utils.jpg)

[![Packagist Version](https://img.shields.io/packagist/v/lacus/cnpj-utils)](https://packagist.org/packages/lacus/cnpj-utils)
[![Packagist Downloads](https://img.shields.io/packagist/dm/lacus/cnpj-utils)](https://packagist.org/packages/lacus/cnpj-utils)
[![PHP Version](https://img.shields.io/packagist/php-v/lacus/cnpj-utils)](https://www.php.net/)
[![Test Status](https://img.shields.io/github/actions/workflow/status/LacusSolutions/br-utils-php/ci.yml?label=ci/cd)](https://github.com/LacusSolutions/br-utils-php/actions)
[![Last Update Date](https://img.shields.io/github/last-commit/LacusSolutions/br-utils-php)](https://github.com/LacusSolutions/br-utils-php)
[![Project License](https://img.shields.io/github/license/LacusSolutions/br-utils-php)](https://github.com/LacusSolutions/br-utils-php/blob/main/LICENSE)

> 🚀 **Full support for the [new alphanumeric CNPJ format](https://github.com/user-attachments/files/23937961/calculodvcnpjalfanaumerico.pdf).**

> 🌎 [Acessar documentação em português](https://github.com/LacusSolutions/br-utils-php/blob/main/packages/cnpj-utils/README.pt.md)

A PHP utility to format, generate, and validate CNPJ (Brazilian Business Tax ID). It provides a wrapper class to [`lacus/cnpj-fmt`](https://packagist.org/packages/lacus/cnpj-fmt), [`lacus/cnpj-gen`](https://packagist.org/packages/lacus/cnpj-gen), and [`lacus/cnpj-val`](https://packagist.org/packages/lacus/cnpj-val) alongside the resources provided by these packages.

## PHP Support

| ![PHP 8.2](https://img.shields.io/badge/PHP-8.2-777BB4?logo=php&logoColor=white) | ![PHP 8.3](https://img.shields.io/badge/PHP-8.3-777BB4?logo=php&logoColor=white) | ![PHP 8.4](https://img.shields.io/badge/PHP-8.4-777BB4?logo=php&logoColor=white) | ![PHP 8.5](https://img.shields.io/badge/PHP-8.5-777BB4?logo=php&logoColor=white) |
| --- | --- | --- | --- |
| Passing ✔ | Passing ✔ | Passing ✔ | Passing ✔ |

## Features

- ✅ **Unified API**: One `CnpjUtils` class for formatting, generation, and validation
- ✅ **Bundled components**: All formatter, generator, and validator classes, options objects, enums, and helpers from the co-related packages are available under `Lacus\BrUtils\Cnpj\`
- ✅ **Alphanumeric CNPJ**: Full support for the new alphanumeric CNPJ format (introduced in 2026)
- ✅ **Flexible input**: `format()` and `isValid()` accept `string` or `list<string>`
- ✅ **Configurable defaults**: Set formatter, generator, and validator options on the instance
- ✅ **Per-call overrides**: Override any component option for a single method call
- ✅ **Validator options**: Configure `type` and `caseSensitive` (new in v2; v1 had no validator settings)
- ✅ **Dual API style**: Unified façade (`CnpjUtils`) or standalone components (`CnpjFormatter`, `CnpjGenerator`, `CnpjValidator`) and functional helpers (`cnpj_fmt()`, `cnpj_gen()`, `cnpj_val()`)
- ✅ **Typed error handling**: Dedicated `TypeError` / `Exception` hierarchies from bundled packages

## Installation

```bash
# using Composer
$ composer require lacus/cnpj-utils
```

This installs **`lacus/cnpj-utils`** together with [`lacus/cnpj-fmt`](https://packagist.org/packages/lacus/cnpj-fmt), [`lacus/cnpj-gen`](https://packagist.org/packages/lacus/cnpj-gen), and [`lacus/cnpj-val`](https://packagist.org/packages/lacus/cnpj-val). You do **not** need separate `composer require` calls for the component packages when using **`lacus/cnpj-utils`**.

## Import

Pick the API that fits your use case. All symbols below share the namespace **`Lacus\BrUtils\Cnpj\`** and are available after installing **`lacus/cnpj-utils`**.

**Unified façade:**

```php
<?php

use Lacus\BrUtils\Cnpj\CnpjUtils;
```

**Standalone components (object-oriented):**

```php
<?php

use Lacus\BrUtils\Cnpj\CnpjFormatter;
use Lacus\BrUtils\Cnpj\CnpjFormatterOptions;
use Lacus\BrUtils\Cnpj\CnpjGenerator;
use Lacus\BrUtils\Cnpj\CnpjGeneratorOptions;
use Lacus\BrUtils\Cnpj\CnpjValidator;
use Lacus\BrUtils\Cnpj\CnpjValidatorOptions;
use Lacus\BrUtils\Cnpj\Enums\CnpjType as CnpjGenerationType;
use Lacus\BrUtils\Cnpj\Enums\CnpjValidationType;
```

**Functional helpers** (autoloaded from the bundled component packages):

```php
<?php

use function Lacus\BrUtils\Cnpj\cnpj_fmt;
use function Lacus\BrUtils\Cnpj\cnpj_gen;
use function Lacus\BrUtils\Cnpj\cnpj_val;
```

## Quick start

**With `CnpjUtils` (all-in-one):**

```php
<?php

use Lacus\BrUtils\Cnpj\CnpjUtils;

$utils = new CnpjUtils();
$cnpj = '03603568000195';

$utils->format($cnpj);    // '03.603.568/0001-95'
$utils->isValid($cnpj);   // true
$utils->generate();       // e.g. 'AB123CDE000196'
```

**With standalone components:**

```php
<?php

use Lacus\BrUtils\Cnpj\CnpjFormatter;
use Lacus\BrUtils\Cnpj\CnpjGenerator;
use Lacus\BrUtils\Cnpj\CnpjValidator;

$cnpj = '03603568000195';

(new CnpjFormatter())->format($cnpj);   // '03.603.568/0001-95'
(new CnpjValidator())->isValid($cnpj);  // true
(new CnpjGenerator())->generate();        // e.g. 'AB123CDE000196'
```

**With functional helpers:**

```php
<?php

use function Lacus\BrUtils\Cnpj\cnpj_fmt;
use function Lacus\BrUtils\Cnpj\cnpj_gen;
use function Lacus\BrUtils\Cnpj\cnpj_val;

$cnpj = '03603568000195';

cnpj_fmt($cnpj);   // '03.603.568/0001-95'
cnpj_val($cnpj);   // true
cnpj_gen();        // e.g. 'AB123CDE000196'
```

## Usage

You can work in three equivalent ways:

1. **`CnpjUtils`** — single instance with shared defaults across format, generate, and validate.
2. **Component classes** — `CnpjFormatter`, `CnpjGenerator`, and `CnpjValidator` directly (same classes used internally by `CnpjUtils`).
3. **Functional helpers** — `cnpj_fmt()`, `cnpj_gen()`, and `cnpj_val()` for one-off calls without managing instances.

All three approaches expose the same options and behavior. For full option tables and component-specific details, see the README of each [bundled package](#bundled-packages).

### `CnpjUtils`

- **`__construct`**: `new CnpjUtils($formatter = [], $generator = [], $validator = [])`

  Each argument may be an options array (spread into the component’s `*Options` constructor), a `CnpjFormatterOptions` / `CnpjGeneratorOptions` / `CnpjValidatorOptions` instance (stored by reference — mutating it later affects subsequent calls with no per-call override), or omitted for defaults.

  Example: `new CnpjUtils(formatter: ['hidden' => true], generator: ['format' => true], validator: ['type' => CnpjValidationType::Numeric])`.

- **`format`**: `format(string|list<string> $cnpjInput, ?CnpjFormatterOptions $options = null, …named formatter options…): string`

  Delegates to `CnpjFormatter::format()`. Per-call options are merged over instance defaults for that call only.

- **`generate`**: `generate(?CnpjGeneratorOptions $options = null, $format = null, $prefix = null, $type = null): string`

  Delegates to `CnpjGenerator::generate()`. Per-call options are merged over instance defaults for that call only.

- **`isValid`**: `isValid(string|list<string> $cnpjInput, ?CnpjValidatorOptions $options = null, $type = null, $caseSensitive = null): bool`

  Delegates to `CnpjValidator::isValid()`. Per-call options are merged over instance defaults for that call only.

- **`getFormatter()`**, **`getGenerator()`**, **`getValidator()`**: Return the internal component instances for direct use.

```php
<?php

use Lacus\BrUtils\Cnpj\CnpjUtils;
use Lacus\BrUtils\Cnpj\Enums\CnpjType as CnpjGenerationType;
use Lacus\BrUtils\Cnpj\Enums\CnpjValidationType;

$utils = new CnpjUtils();

$utils->format('03603568000195');              // '03.603.568/0001-95'
$utils->format('12ABC34500DE99');              // '12.ABC.345/00DE-99'
$utils->isValid('98.765.432/0001-98');         // true
$utils->isValid('1QB5UKALPYFP59');             // true (alphanumeric)
$utils->generate(format: true);                // e.g. 'AB.123.CDE/0001-96'
$utils->generate(type: CnpjGenerationType::Numeric);  // e.g. '12345678000195'
```

### Instance defaults and per-call overrides

```php
$utils = new CnpjUtils(
    formatter: ['hidden' => true, 'hiddenKey' => '#'],
    generator: ['format' => true],
    validator: ['type' => CnpjValidationType::Numeric],
);

$cnpj = '03603568000195';

$utils->format($cnpj);                  // masked (instance formatter defaults)
$utils->format($cnpj, hidden: false);    // this call only: unmasked
$utils->generate(format: false);        // this call only: compact output
$utils->isValid('1QB5UKALPYFP59');      // false (instance validator is numeric-only)
$utils->isValid('1QB5UKALPYFP59', type: CnpjValidationType::Alphanumeric);  // true for this call
```

### Formatting (`format`)

Supports the same options as [`lacus/cnpj-fmt`](https://github.com/LacusSolutions/br-utils-php/blob/main/packages/cnpj-fmt/README.md). Pass them to the `CnpjUtils` constructor (`formatter` argument), per `format()` call, or via `CnpjFormatter` / `cnpj_fmt()`.

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `hidden` | `?bool` | `false` | When `true`, replaces the inclusive index range `[hiddenStart, hiddenEnd]` on the normalized 14-character string before punctuation is applied |
| `hiddenKey` | `?string` | `'*'` | Replacement for each hidden position (may be multi-character or empty); must not use disallowed key characters |
| `hiddenStart` | `?int` | `5` | Start index `0`–`13` (inclusive) |
| `hiddenEnd` | `?int` | `13` | End index `0`–`13` (inclusive); if `hiddenStart > hiddenEnd`, they are swapped |
| `dotKey` | `?string` | `'.'` | Separator between groups `XX` / `XXX` / `XXX` |
| `slashKey` | `?string` | `'/'` | Separator before the branch block |
| `dashKey` | `?string` | `'-'` | Separator before the last two characters |
| `escape` | `?bool` | `false` | When `true`, HTML-escapes the final string (`HtmlUtils::escape`) |
| `encode` | `?bool` | `false` | When `true`, URL-encodes the final string (`UrlUtils::encodeUriComponent`) |
| `onFail` | `?\Closure` | see below | `Closure(mixed $value, CnpjFormatterException $e): string` — used when sanitized length ≠ 14 |

Default **`onFail`** returns an empty string. The exception passed for length failures is **`CnpjFormatterInputLengthException`**. Invalid length does **not** throw from `format()`; wrong input types throw **`CnpjFormatterInputTypeError`**.

```php
$cnpj = '03603568000195';

$utils->format($cnpj);                                        // '03.603.568/0001-95'
$utils->format($cnpj, hidden: true, hiddenKey: '#');          // '03.603.###/####-##'
$utils->format($cnpj, dotKey: '', slashKey: '|', dashKey: '_');  // '03603568|0001_95'
$utils->format($cnpj, encode: true);                          // URL-encoded output
```

### Generation (`generate`)

Supports the same options as [`lacus/cnpj-gen`](https://github.com/LacusSolutions/br-utils-php/blob/main/packages/cnpj-gen/README.md). Pass them to the `CnpjUtils` constructor (`generator` argument), per `generate()` call, or via `CnpjGenerator` / `cnpj_gen()`.

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `format` | `?bool` | `false` | When `true`, returns formatted CNPJ (`XX.XXX.XXX/XXXX-XX`); otherwise returns compact 14-character output |
| `prefix` | `?string` | `''` | Base seed for generation. Non-alphanumeric chars are stripped, letters are uppercased, and only first 12 chars (indexes `0`–`11`) are used; characters at index `12+` are ignored |
| `type` | `CnpjGenerationType\|'alphanumeric'\|'alphabetic'\|'numeric'\|null` | `CnpjGenerationType::Alphanumeric` | Character family used for generated base positions (`0`–`9`, `A`–`Z`, or both) |

`prefix` validation rules:

- base ID `00000000` is rejected (when first 8 chars are present)
- branch ID `0000` is rejected (when chars 9–12 are present)
- 12 repeated numeric digits are rejected (e.g. `111111111111`)

```php
$utils->generate();                              // e.g. 'AB123CDE000196'
$utils->generate(format: true);                  // e.g. 'AB.123.CDE/0001-96'
$utils->generate(prefix: '12345678', type: CnpjGenerationType::Numeric);
```

### Validation (`isValid`)

Supports the same options as [`lacus/cnpj-val`](https://github.com/LacusSolutions/br-utils-php/blob/main/packages/cnpj-val/README.md). Unlike v1, validation is now configurable through **`CnpjValidatorOptions`**. Pass them to the `CnpjUtils` constructor (`validator` argument), per `isValid()` call, or via `CnpjValidator` / `cnpj_val()`.

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `type` | `CnpjValidationType\|'alphanumeric'\|'numeric'\|null` | `CnpjValidationType::Alphanumeric` | Character set after sanitization |
| `caseSensitive` | `?bool` | `true` | When `false`, lowercase letters are uppercased before alphanumeric validation |

```php
$utils = new CnpjUtils();

$utils->isValid('98765432000198');       // true
$utils->isValid('98765432000199');       // false
$utils->isValid('1QB5UKALPYFP59');       // true
$utils->isValid('1QB5UKALpyfp59');       // false (default is case-sensitive)
$utils->isValid('1QB5UKALpyfp59', caseSensitive: false);  // true

$utils->isValid('1QB5UKALPYFP59', type: CnpjValidationType::Numeric);  // false

// Legacy numeric-only validation as instance default
$numericUtils = new CnpjUtils(validator: ['type' => CnpjValidationType::Numeric]);
$numericUtils->isValid('98.765.432/0001-98');   // true
$numericUtils->isValid('1QB5UKALPYFP59');       // false
```

Invalid CNPJ returns **`false`** without throwing. Wrong input types throw **`CnpjValidatorInputTypeError`**.

### Bundled components (standalone)

Install **`lacus/cnpj-utils`** once; import and use any resource from the co-related packages without requiring them separately.

#### `CnpjFormatter` and `cnpj_fmt()`

Same API as [`lacus/cnpj-fmt`](https://github.com/LacusSolutions/br-utils-php/blob/main/packages/cnpj-fmt/README.md).

```php
<?php

use Lacus\BrUtils\Cnpj\CnpjFormatter;

use function Lacus\BrUtils\Cnpj\cnpj_fmt;

$cnpj = '03603568000195';

$formatter = new CnpjFormatter(hidden: true);
$formatter->format($cnpj);                    // masked with instance defaults
$formatter->format($cnpj, hidden: false);    // per-call override

cnpj_fmt($cnpj);                              // '03.603.568/0001-95'
cnpj_fmt($cnpj, hidden: true, hiddenKey: '#'); // '03.603.###/####-##'
cnpj_fmt('12ABC34500DE99');                   // '12.ABC.345/00DE-99'
```

#### `CnpjGenerator` and `cnpj_gen()`

Same API as [`lacus/cnpj-gen`](https://github.com/LacusSolutions/br-utils-php/blob/main/packages/cnpj-gen/README.md).

```php
<?php

use Lacus\BrUtils\Cnpj\CnpjGenerator;
use Lacus\BrUtils\Cnpj\Enums\CnpjType as CnpjGenerationType;

use function Lacus\BrUtils\Cnpj\cnpj_gen;

$generator = new CnpjGenerator(format: true, type: CnpjGenerationType::Numeric);

$generator->generate();                       // formatted numeric CNPJ
$generator->generate(format: false);         // per-call: compact output

cnpj_gen();                                   // e.g. 'AB123CDE000196'
cnpj_gen(format: true);                       // e.g. 'AB.123.CDE/0001-96'
cnpj_gen(prefix: '12345678', type: CnpjGenerationType::Numeric);
```

#### `CnpjValidator` and `cnpj_val()`

Same API as [`lacus/cnpj-val`](https://github.com/LacusSolutions/br-utils-php/blob/main/packages/cnpj-val/README.md).

```php
<?php

use Lacus\BrUtils\Cnpj\CnpjValidator;
use Lacus\BrUtils\Cnpj\Enums\CnpjValidationType;

use function Lacus\BrUtils\Cnpj\cnpj_val;

$validator = new CnpjValidator(type: CnpjValidationType::Numeric);

$validator->isValid('98.765.432/0001-98');    // true
$validator->isValid('1QB5UKALPYFP59');      // false (numeric-only instance)

cnpj_val('98765432000198');                   // true
cnpj_val('1QB5UKALpyfp59', caseSensitive: false);  // true
cnpj_val('1QB5UKALPYFP59', type: CnpjValidationType::Numeric);  // false
```

#### Mixing styles

Use `CnpjUtils` where a shared configuration helps, and standalone components elsewhere — they are the same underlying classes:

```php
<?php

use Lacus\BrUtils\Cnpj\CnpjFormatter;
use Lacus\BrUtils\Cnpj\CnpjUtils;
use Lacus\BrUtils\Cnpj\Enums\CnpjValidationType;

use function Lacus\BrUtils\Cnpj\cnpj_val;

$utils = new CnpjUtils(validator: ['type' => CnpjValidationType::Numeric]);

// Via façade
$utils->format('03603568000195');

// Via component returned by the façade (same formatter instance)
$utils->getFormatter()->format('12ABC34500DE99');

// Via a separate component instance
(new CnpjFormatter())->format('03603568000195');

// Via functional helper
cnpj_val('98.765.432/0001-98');
```

### Accessing components from `CnpjUtils`

```php
$utils = new CnpjUtils();

$formatter = $utils->getFormatter();
$generator = $utils->getGenerator();
$validator = $utils->getValidator();

$formatter->format('03603568000195', hidden: true);
$generator->generate(format: true);
$validator->isValid('03603568000195');
```

Use **`getFormatter()`**, **`getGenerator()`**, and **`getValidator()`** when you already have a `CnpjUtils` instance and want the configured component without creating a new one. The returned instances share the same options you passed to the `CnpjUtils` constructor.

### Errors & exceptions

`CnpjUtils` does not define its own exception types; it propagates errors from the bundled packages:

- **Formatting**: `CnpjFormatterInputTypeError`, `CnpjFormatterOptionsTypeError`, `CnpjFormatterOptionsHiddenRangeInvalidException`, `CnpjFormatterOptionsForbiddenKeyCharacterException`, and related classes.
- **Generation**: `CnpjGeneratorOptionsTypeError`, `CnpjGeneratorOptionPrefixInvalidException`, `CnpjGeneratorOptionTypeInvalidException`, and related classes.
- **Validation**: `CnpjValidatorInputTypeError`, `CnpjValidatorOptionsTypeError`, `CnpjValidatorOptionTypeInvalidException`, and related classes.

Invalid option types are **`TypeError`** subclasses; invalid option values are **`Exception`** subclasses. Validation failure returns `false`; formatting length failure is handled by **`onFail`**.

```php
<?php

use Lacus\BrUtils\Cnpj\CnpjUtils;
use Lacus\BrUtils\Cnpj\Exceptions\CnpjFormatterInputTypeError;
use Lacus\BrUtils\Cnpj\Exceptions\CnpjValidatorInputTypeError;

try {
    (new CnpjUtils())->format(12345);
} catch (CnpjFormatterInputTypeError $e) {
    echo $e->getMessage();
}

try {
    (new CnpjUtils())->isValid(12345678000198);
} catch (CnpjValidatorInputTypeError $e) {
    echo $e->getMessage();
}
```

### Bundled packages

| Package | Main resources | README |
|---------|----------------|--------|
| [`lacus/cnpj-fmt`](https://packagist.org/packages/lacus/cnpj-fmt) | `CnpjFormatter`, `CnpjFormatterOptions`, `cnpj_fmt()` | [docs](https://github.com/LacusSolutions/br-utils-php/blob/main/packages/cnpj-fmt/README.md) |
| [`lacus/cnpj-gen`](https://packagist.org/packages/lacus/cnpj-gen) | `CnpjGenerator`, `CnpjGeneratorOptions`, `CnpjType`, `cnpj_gen()` | [docs](https://github.com/LacusSolutions/br-utils-php/blob/main/packages/cnpj-gen/README.md) |
| [`lacus/cnpj-val`](https://packagist.org/packages/lacus/cnpj-val) | `CnpjValidator`, `CnpjValidatorOptions`, `CnpjValidationType`, `cnpj_val()` | [docs](https://github.com/LacusSolutions/br-utils-php/blob/main/packages/cnpj-val/README.md) |

All of the above are pulled in as dependencies of **`lacus/cnpj-utils`** and share the namespace **`Lacus\BrUtils\Cnpj\`**. For exhaustive option tables, exception lists, and edge-case behavior, see each package README.

## Contribution & Support

We welcome contributions! Please see our [Contributing Guidelines](https://github.com/LacusSolutions/br-utils-php/blob/main/CONTRIBUTING.md) for details. If you find this project helpful, please consider:

- ⭐ Starring the repository
- 🤝 Contributing to the codebase
- 💡 [Suggesting new features](https://github.com/LacusSolutions/br-utils-php/issues)
- 🐛 [Reporting bugs](https://github.com/LacusSolutions/br-utils-php/issues)

## License

This project is licensed under the MIT License — see the [LICENSE](https://github.com/LacusSolutions/br-utils-php/blob/main/LICENSE) file for details.

## Changelog

See [CHANGELOG](https://github.com/LacusSolutions/br-utils-php/blob/main/packages/cnpj-utils/CHANGELOG.md) for a list of changes and version history.

---

Made with ❤️ by [Lacus Solutions](https://github.com/LacusSolutions)
