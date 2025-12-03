![br-utils for PHP](https://br-utils.vercel.app/img/cover_br-utils.jpg)

[![Packagist Version](https://img.shields.io/packagist/v/lacus/br-utils)](https://packagist.org/packages/lacus/br-utils)
[![Packagist Downloads](https://img.shields.io/packagist/dm/lacus/br-utils)](https://packagist.org/packages/lacus/br-utils)
[![PHP Version](https://img.shields.io/packagist/php-v/lacus/br-utils)](https://www.php.net/)
[![Test Status](https://img.shields.io/github/actions/workflow/status/LacusSolutions/br-utils-php/ci.yml?label=ci/cd)](https://github.com/LacusSolutions/br-utils-php/actions)
[![Last Update Date](https://img.shields.io/github/last-commit/LacusSolutions/br-utils-php)](https://github.com/LacusSolutions/br-utils-php)
[![Project License](https://img.shields.io/github/license/LacusSolutions/br-utils-php)](https://github.com/LacusSolutions/br-utils-php/blob/main/LICENSE)

Toolkit to handle the main operations with Brazilian-related data for PHP programming language:

- CPF (personal ID) ([demo](https://cpf-utils.vercel.app/))
- CNPJ (employer ID) ([demo](https://cnpj-utils.vercel.app/))

## PHP Support

| ![PHP 8.1](https://img.shields.io/badge/PHP-8.1-777BB4?logo=php&logoColor=white) | ![PHP 8.2](https://img.shields.io/badge/PHP-8.2-777BB4?logo=php&logoColor=white) | ![PHP 8.3](https://img.shields.io/badge/PHP-8.3-777BB4?logo=php&logoColor=white) | ![PHP 8.4](https://img.shields.io/badge/PHP-8.4-777BB4?logo=php&logoColor=white) |
|--- | --- | --- | --- |
| Passing ✔ | Passing ✔ | Passing ✔ | Passing ✔ |

## Installation

```bash
$ composer require lacus/br-utils
```

## Import

```php
<?php
// Using the main BrUtils class
use Lacus\BrUtils\BrUtils;

// Or using individual utility classes
use Lacus\BrUtils\CpfUtils;
use Lacus\BrUtils\CnpjUtils;

// Or using function-based approach
use function Lacus\BrUtils\Cpf\cpf_fmt;
use function Lacus\BrUtils\Cpf\cpf_gen;
use function Lacus\BrUtils\Cpf\cpf_val;
use function Lacus\BrUtils\Cnpj\cnpj_fmt;
use function Lacus\BrUtils\Cnpj\cnpj_gen;
use function Lacus\BrUtils\Cnpj\cnpj_val;
```

## Usage

### Unified Interface with BrUtils

The `BrUtils` class provides a unified interface for all Brazilian ID operations:

```php
$brUtils = new BrUtils();

// CPF operations
$cpf = '11144477735';
echo $brUtils->cpf->format($cpf);       // returns '111.444.777-35'
echo $brUtils->cpf->isValid($cpf);      // returns true
echo $brUtils->cpf->generate();         // returns '12345678901'

// CNPJ operations
$cnpj = '03603568000195';
echo $brUtils->cnpj->format($cnpj);     // returns '03.603.568/0001-95'
echo $brUtils->cnpj->isValid($cnpj);    // returns true
echo $brUtils->cnpj->generate();        // returns '65453043000178'
```

#### With Configuration Options

You can configure both CPF and CNPJ utilities with custom options:

```php
$brUtils = new BrUtils(
    cpf: [
        'formatter' => [
            'hidden' => true,
            'hiddenKey' => '#',
            'hiddenStart' => 3,
            'hiddenEnd' => 9
        ],
        'generator' => [
            'format' => true
        ]
    ],
    cnpj: [
        'formatter' => [
            'hidden' => true,
            'hiddenKey' => '#',
            'hiddenStart' => 5,
            'hiddenEnd' => 13
        ],
        'generator' => [
            'format' => true
        ]
    ]
);

$cpf = '11144477735';
$cnpj = '03603568000195';

echo $brUtils->cpf->format($cpf);       // returns '111.###.###-##'
echo $brUtils->cnpj->format($cnpj);     // returns '03.603.###/####-##'
echo $brUtils->cpf->generate();         // returns '123.456.789-01'
echo $brUtils->cnpj->generate();        // returns '73.008.535/0005-06'
```

### Individual Utility Classes

You can also use the individual utility classes directly:

```php
// CPF utilities
$cpfUtils = new CpfUtils();
$cpf = '11144477735';

echo $cpfUtils->format($cpf);           // returns '111.444.777-35'
echo $cpfUtils->isValid($cpf);          // returns true
echo $cpfUtils->generate();             // returns '12345678901'

// CNPJ utilities
$cnpjUtils = new CnpjUtils();
$cnpj = '03603568000195';

echo $cnpjUtils->format($cnpj);         // returns '03.603.568/0001-95'
echo $cnpjUtils->isValid($cnpj);        // returns true
echo $cnpjUtils->generate();            // returns '65453043000178'
```

### Functional Programming

The package also provides standalone functions for each operation:

```php
$cpf = '11144477735';
$cnpj = '03603568000195';

// CPF functions
echo cpf_fmt($cpf);                     // returns '111.444.777-35'
echo cpf_val($cpf);                     // returns true
echo cpf_gen();                         // returns '12345678901'

// CNPJ functions
echo cnpj_fmt($cnpj);                   // returns '03.603.568/0001-95'
echo cnpj_val($cnpj);                   // returns true
echo cnpj_gen();                        // returns '65453043000178'
```

## API Reference

### CPF Operations

#### Formatting (`cpf_fmt` / `CpfUtils::format`)

Formats a CPF string with customizable delimiters and masking options.

```php
cpf_fmt(
    string $cpfString,
    ?bool $escape = null,
    ?bool $hidden = null,
    ?string $hiddenKey = null,
    ?int $hiddenStart = null,
    ?int $hiddenEnd = null,
    ?string $dotKey = null,
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
| `hiddenStart` | `?int` | `3` | Starting index for hidden range (0-10) |
| `hiddenEnd` | `?int` | `9` | Ending index for hidden range (0-10) |
| `dotKey` | `?string` | `'.'` | String to replace dot characters |
| `dashKey` | `?string` | `'-'` | String to replace dash character |
| `onFail` | `?callable` | `fn($v) => $v` | Fallback function for invalid input |

**Examples:**

```php
$cpf = '11144477735';

// Basic formatting
echo cpf_fmt($cpf);                     // '111.444.777-35'

// With hidden digits
echo cpf_fmt($cpf, hidden: true);       // '111.***.***-**'

// Custom delimiters
echo cpf_fmt($cpf, dotKey: '', dashKey: '_');  // '111444777_35'

// Custom hidden range
echo cpf_fmt($cpf, hidden: true, hiddenStart: 0, hiddenEnd: 6, hiddenKey: '#');  // '###.###.777-35'
```

#### Generation (`cpf_gen` / `CpfUtils::generate`)

Generates valid CPF numbers with optional formatting and prefix completion.

```php
cpf_gen(
    ?bool $format = null,
    ?string $prefix = null,
): string
```

**Parameters:**

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `format` | `?bool` | `false` | Whether to format the output |
| `prefix` | `?string` | `''` | Prefix to complete with valid digits (1-9 digits) |

**Examples:**

```php
// Generate random CPF
echo cpf_gen();                         // '12345678901'

// Generate formatted CPF
echo cpf_gen(format: true);             // '123.456.789-01'

// Complete a prefix
echo cpf_gen(prefix: '123456789');      // '12345678901'

// Complete and format
echo cpf_gen(prefix: '123456789', format: true);  // '123.456.789-01'
```

#### Validation (`cpf_val` / `CpfUtils::isValid`)

Validates CPF numbers using the official algorithm.

```php
cpf_val(string $cpfString): bool
```

**Examples:**

```php
// Valid CPF
echo cpf_val('11144477735');            // true
echo cpf_val('111.444.777-35');        // true

// Invalid CPF
echo cpf_val('11144477736');            // false
```

### CNPJ Operations

#### Formatting (`cnpj_fmt` / `CnpjUtils::format`)

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
echo cnpj_fmt($cnpj);                   // '03.603.568/0001-95'

// With hidden digits
echo cnpj_fmt($cnpj, hidden: true);     // '03.603.***/****-**'

// Custom delimiters
echo cnpj_fmt($cnpj, dotKey: '', slashKey: '|', dashKey: '_');  // '03603568|0001_95'

// Custom hidden range
echo cnpj_fmt($cnpj, hidden: true, hiddenStart: 2, hiddenEnd: 8, hiddenKey: '#');  // '03###.###/0001-95'
```

#### Generation (`cnpj_gen` / `CnpjUtils::generate`)

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
echo cnpj_gen();                        // '65453043000178'

// Generate formatted CNPJ
echo cnpj_gen(format: true);            // '73.008.535/0005-06'

// Complete a prefix
echo cnpj_gen(prefix: '45623767');      // '45623767000296'

// Complete and format
echo cnpj_gen(prefix: '456237670002', format: true);  // '45.623.767/0002-96'
```

#### Validation (`cnpj_val` / `CnpjUtils::isValid`)

Validates CNPJ numbers using the official algorithm.

```php
cnpj_val(string $cnpjString): bool
```

**Examples:**

```php
// Valid CNPJ
echo cnpj_val('98765432000198');        // true
echo cnpj_val('98.765.432/0001-98');    // true

// Invalid CNPJ
echo cnpj_val('98765432000199');        // false
```

## Advanced Usage

### Accessing Individual Components

You can access the individual formatter, generator, and validator instances:

```php
$brUtils = new BrUtils();

// Get individual components
$cpfFormatter = $brUtils->cpf->getFormatter();
$cpfGenerator = $brUtils->cpf->getGenerator();
$cpfValidator = $brUtils->cpf->getValidator();

$cnpjFormatter = $brUtils->cnpj->getFormatter();
$cnpjGenerator = $brUtils->cnpj->getGenerator();
$cnpjValidator = $brUtils->cnpj->getValidator();

// Use them directly
$cpfFormatter->format('11144477735', hidden: true);
$cpfGenerator->generate(format: true);
$cpfValidator->isValid('11144477735');

$cnpjFormatter->format('03603568000195', hidden: true);
$cnpjGenerator->generate(format: true);
$cnpjValidator->isValid('03603568000195');
```

### Custom Error Handling

```php
$cpf = '123'; // Invalid length
$cnpj = '456'; // Invalid length

// Custom fallback
echo cpf_fmt($cpf, onFail: fn($v) => "Invalid CPF: {$v}");  // 'Invalid CPF: 123'
echo cnpj_fmt($cnpj, onFail: fn($v) => "Invalid CNPJ: {$v}");  // 'Invalid CNPJ: 456'

// Return original value
echo cpf_fmt($cpf);  // '123'
echo cnpj_fmt($cnpj);  // '456'
```

## Dependencies

This package is built on top of the following specialized packages:

- [`lacus/cnpj-utils`](https://packagist.org/packages/lacus/cnpj-utils) - CNPJ utilities
- [`lacus/cpf-utils`](https://packagist.org/packages/lacus/cpf-utils) - CPF utilities

## Contribution & Support

We welcome contributions! Please see our [Contributing Guidelines](https://github.com/LacusSolutions/br-utils-php/blob/main/CONTRIBUTING.md) for details. But if you find this project helpful, please consider:

- ⭐ Starring the repository
- 🤝 Contributing to the codebase
- 💡 [Suggesting new features](https://github.com/LacusSolutions/br-utils-php/issues)
- 🐛 [Reporting bugs](https://github.com/LacusSolutions/br-utils-php/issues)

## License

This project is licensed under the MIT License - see the [LICENSE](https://github.com/LacusSolutions/br-utils-php/blob/main/LICENSE) file for details.

## Changelog

See [CHANGELOG](https://github.com/LacusSolutions/br-utils-php/blob/main/packages/br-utils/CHANGELOG.md) for a list of changes and version history.

---

Made with ❤️ by [Lacus Solutions](https://github.com/LacusSolutions)
