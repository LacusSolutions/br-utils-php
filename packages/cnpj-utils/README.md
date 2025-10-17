![cnpj-utils for PHP](https://github.com/user-attachments/assets/daebe359-0c12-4af5-8582-fa7f3b5cb9c9)

[![Packagist Version](https://img.shields.io/packagist/v/lacus/cnpj-utils)](https://packagist.org/packages/lacus/cnpj-utils)
[![Packagist Downloads](https://img.shields.io/packagist/dm/lacus/cnpj-utils)](https://packagist.org/packages/lacus/cnpj-utils)
[![PHP Version](https://img.shields.io/packagist/php-v/lacus/cnpj-utils)](https://www.php.net/)
[![Test Status](https://img.shields.io/github/actions/workflow/status/LacusSolutions/br-utils-php/ci.yml?label=ci/cd)](https://github.com/LacusSolutions/br-utils-php/actions)
[![Last Update Date](https://img.shields.io/github/last-commit/LacusSolutions/br-utils-php)](https://github.com/LacusSolutions/br-utils-php)
[![Project License](https://img.shields.io/github/license/LacusSolutions/br-utils-php)](https://github.com/LacusSolutions/br-utils-php/blob/main/LICENSE)

Toolkit to deal with CNPJ data (Brazilian employer ID): validation, formatting and generation of valid IDs.

## PHP Support

| ![PHP 8.1](https://img.shields.io/badge/PHP-8.1-777BB4?logo=php&logoColor=white) | ![PHP 8.2](https://img.shields.io/badge/PHP-8.2-777BB4?logo=php&logoColor=white) | ![PHP 8.3](https://img.shields.io/badge/PHP-8.3-777BB4?logo=php&logoColor=white) | ![PHP 8.4](https://img.shields.io/badge/PHP-8.4-777BB4?logo=php&logoColor=white) |
|--- | --- | --- | --- |
| Passing ✔ | Passing ✔ | Passing ✔ | Passing ✔ |

## Installation

```bash
$ composer require lacus/cnpj-utils
```

## Import

```php
<?php
// Using class-based resource
use Lacus\CnpjUtils\CnpjUtils;

// Or using function-based approach
use function Lacus\CnpjUtils\cnpj_fmt;
use function Lacus\CnpjUtils\cnpj_gen;
use function Lacus\CnpjUtils\cnpj_val;
```

## Usage

### Object-Oriented Usage

The `CnpjUtils` class provides a unified interface for all CNPJ operations:

```php
$cnpjUtils = new CnpjUtils();
$cnpj = '03603568000195';

// Format CNPJ
echo $cnpjUtils->format($cnpj);       // returns '03.603.568/0001-95'

// Validate CNPJ
echo $cnpjUtils->isValid($cnpj);      // returns true

// Generate CNPJ
echo $cnpjUtils->generate();          // returns '65453043000178'
```

#### With Configuration Options

You can configure the formatter and generator options in the constructor:

```php
$cnpjUtils = new CnpjUtils(
    formatter: [
        'hidden' => true,
        'hiddenKey' => '#',
        'hiddenStart' => 5,
        'hiddenEnd' => 13
    ],
    generator: [
        'format' => true
    ]
);

$cnpj = '03603568000195';
echo $cnpjUtils->format($cnpj);       // returns '03.603.###/####-##'
echo $cnpjUtils->generate();          // returns '73.008.535/0005-06'
```

### Functional Programming

The package also provides standalone functions for each operation:

```php
$cnpj = '03603568000195';

// Format CNPJ
echo cnpj_fmt($cnpj);                 // returns '03.603.568/0001-95'

// Validate CNPJ
echo cnpj_val($cnpj);                 // returns true

// Generate CNPJ
echo cnpj_gen();                      // returns '65453043000178'
```

## API Reference

### Formatting (`cnpj_fmt` / `CnpjUtils::format`)

Formats a CNPJ string with customizable delimiters and masking options.

```php
cnpj_fmt(
    string $cnpjString,
    ?bool $escape = null,
    ?bool $hidden = null,
    ?string $hiddenKey = null,
    ?int $hiddenStart = null,
    ?int $hiddenEnd = null,
    ?string $dotKey = null,
    ?string $slashKey = null,
    ?string $dashKey = null,
    ?Closure $onFail = null,
): string
```

**Parameters:**

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `escape` | `?bool` | `false` | Whether to HTML escape the result |
| `hidden` | `?bool` | `false` | Whether to hide digits with a mask |
| `hiddenKey` | `?string` | `'*'` | Character to replace hidden digits |
| `hiddenStart` | `?int` | `5` | Starting index for hidden range (0-13) |
| `hiddenEnd` | `?int` | `13` | Ending index for hidden range (0-13) |
| `dotKey` | `?string` | `'.'` | String to replace dot characters |
| `slashKey` | `?string` | `'/'` | String to replace slash character |
| `dashKey` | `?string` | `'-'` | String to replace dash character |
| `onFail` | `?callable` | `fn($v) => $v` | Fallback function for invalid input |

**Examples:**

```php
$cnpj = '03603568000195';

// Basic formatting
echo cnpj_fmt($cnpj);                 // '03.603.568/0001-95'

// With hidden digits
echo cnpj_fmt($cnpj, hidden: true);   // '03.603.***/****-**'

// Custom delimiters
echo cnpj_fmt($cnpj, dotKey: '', slashKey: '|', dashKey: '_');  // '03603568|0001_95'

// Custom hidden range
echo cnpj_fmt($cnpj, hidden: true, hiddenStart: 2, hiddenEnd: 8, hiddenKey: '#');  // '03###.###/0001-95'
```

### Generation (`cnpj_gen` / `CnpjUtils::generate`)

Generates valid CNPJ numbers with optional formatting and prefix completion.

```php
cnpj_gen(
    ?bool $format = null,
    ?string $prefix = null,
): string
```

**Parameters:**

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `format` | `?bool` | `false` | Whether to format the output |
| `prefix` | `?string` | `''` | Prefix to complete with valid digits (1-12 digits) |

**Examples:**

```php
// Generate random CNPJ
echo cnpj_gen();                      // '65453043000178'

// Generate formatted CNPJ
echo cnpj_gen(format: true);          // '73.008.535/0005-06'

// Complete a prefix
echo cnpj_gen(prefix: '45623767');    // '45623767000296'

// Complete and format
echo cnpj_gen(prefix: '456237670002', format: true);  // '45.623.767/0002-96'
```

### Validation (`cnpj_val` / `CnpjUtils::isValid`)

Validates CNPJ numbers using the official algorithm.

```php
cnpj_val(string $cnpjString): bool
```

**Examples:**

```php
// Valid CNPJ
echo cnpj_val('98765432000198');      // true
echo cnpj_val('98.765.432/0001-98');  // true

// Invalid CNPJ
echo cnpj_val('98765432000199');      // false
```

## Advanced Usage

### Accessing Individual Components

You can access the individual formatter, generator, and validator instances:

```php
$cnpjUtils = new CnpjUtils();

// Get individual components
$formatter = $cnpjUtils->getFormatter();
$generator = $cnpjUtils->getGenerator();
$validator = $cnpjUtils->getValidator();

// Use them directly
$formatter->format('03603568000195', hidden: true);
$generator->generate(format: true);
$validator->isValid('03603568000195');
```

### Custom Error Handling

```php
$cnpj = '123'; // Invalid length

// Custom fallback
echo cnpj_fmt($cnpj, onFail: fn($v) => "Invalid CNPJ: {$v}");  // 'Invalid CNPJ: 123'

// Return original value
echo cnpj_fmt($cnpj);  // '123'
```

## Dependencies

This package is built on top of the following specialized packages:

- [`lacus/cnpj-fmt`](https://packagist.org/packages/lacus/cnpj-fmt) - CNPJ formatting
- [`lacus/cnpj-gen`](https://packagist.org/packages/lacus/cnpj-gen) - CNPJ generation
- [`lacus/cnpj-val`](https://packagist.org/packages/lacus/cnpj-val) - CNPJ validation

## Contribution & Support

We welcome contributions! Please see our [Contributing Guidelines](https://github.com/LacusSolutions/br-utils-php/blob/main/CONTRIBUTING.md) for details. But if you find this project helpful, please consider:

- ⭐ Starring the repository
- 🤝 Contributing to the codebase
- 💡 [Suggesting new features](https://github.com/LacusSolutions/br-utils-php/issues)
- 🐛 [Reporting bugs](https://github.com/LacusSolutions/br-utils-php/issues)

## License

This project is licensed under the MIT License - see the [LICENSE](https://github.com/LacusSolutions/br-utils-php/blob/main/LICENSE) file for details.

## Changelog

See [CHANGELOG](https://github.com/LacusSolutions/br-utils-php/blob/main/packages/cnpj-utils/CHANGELOG.md) for a list of changes and version history.

---

Made with ❤️ by [Lacus Solutions](https://github.com/LacusSolutions)
