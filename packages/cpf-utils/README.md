![cpf-utils for PHP](https://br-utils.vercel.app/img/cover_cpf-utils.jpg)

[![Packagist Version](https://img.shields.io/packagist/v/lacus/cpf-utils)](https://packagist.org/packages/lacus/cpf-utils)
[![Packagist Downloads](https://img.shields.io/packagist/dm/lacus/cpf-utils)](https://packagist.org/packages/lacus/cpf-utils)
[![PHP Version](https://img.shields.io/packagist/php-v/lacus/cpf-utils)](https://www.php.net/)
[![Test Status](https://img.shields.io/github/actions/workflow/status/LacusSolutions/br-utils-php/ci.yml?label=ci/cd)](https://github.com/LacusSolutions/br-utils-php/actions)
[![Last Update Date](https://img.shields.io/github/last-commit/LacusSolutions/br-utils-php)](https://github.com/LacusSolutions/br-utils-php)
[![Project License](https://img.shields.io/github/license/LacusSolutions/br-utils-php)](https://github.com/LacusSolutions/br-utils-php/blob/main/LICENSE)

Toolkit to deal with CPF data (Brazilian personal ID): validation, formatting and generation of valid IDs.

## PHP Support

| ![PHP 8.1](https://img.shields.io/badge/PHP-8.1-777BB4?logo=php&logoColor=white) | ![PHP 8.2](https://img.shields.io/badge/PHP-8.2-777BB4?logo=php&logoColor=white) | ![PHP 8.3](https://img.shields.io/badge/PHP-8.3-777BB4?logo=php&logoColor=white) | ![PHP 8.4](https://img.shields.io/badge/PHP-8.4-777BB4?logo=php&logoColor=white) |
|--- | --- | --- | --- |
| Passing ✔ | Passing ✔ | Passing ✔ | Passing ✔ |

## Installation

```bash
$ composer require lacus/cpf-utils
```

## Import

```php
<?php
// Using class-based resource
use Lacus\CpfUtils\CpfUtils;

// Or using function-based approach
use function Lacus\CpfUtils\cpf_fmt;
use function Lacus\CpfUtils\cpf_gen;
use function Lacus\CpfUtils\cpf_val;
```

## Usage

### Object-Oriented Usage

The `CpfUtils` class provides a unified interface for all CPF operations:

```php
$cpfUtils = new CpfUtils();
$cpf = '11144477735';

// Format CPF
echo $cpfUtils->format($cpf);       // returns '111.444.777-35'

// Validate CPF
echo $cpfUtils->isValid($cpf);      // returns true

// Generate CPF
echo $cpfUtils->generate();         // returns '12345678901'
```

#### With Configuration Options

You can configure the formatter and generator options in the constructor:

```php
$cpfUtils = new CpfUtils(
    formatter: [
        'hidden' => true,
        'hiddenKey' => '#',
        'hiddenStart' => 3,
        'hiddenEnd' => 9
    ],
    generator: [
        'format' => true
    ]
);

$cpf = '11144477735';
echo $cpfUtils->format($cpf);       // returns '111.###.###-##'
echo $cpfUtils->generate();         // returns '123.456.789-01'
```

### Functional Programming

The package also provides standalone functions for each operation:

```php
$cpf = '11144477735';

// Format CPF
echo cpf_fmt($cpf);                 // returns '111.444.777-35'

// Validate CPF
echo cpf_val($cpf);                 // returns true

// Generate CPF
echo cpf_gen();                     // returns '12345678901'
```

## API Reference

### Formatting (`cpf_fmt` / `CpfUtils::format`)

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
echo cpf_fmt($cpf);                 // '111.444.777-35'

// With hidden digits
echo cpf_fmt($cpf, hidden: true);   // '111.***.***-**'

// Custom delimiters
echo cpf_fmt($cpf, dotKey: '', dashKey: '_');  // '111444777_35'

// Custom hidden range
echo cpf_fmt($cpf, hidden: true, hiddenStart: 0, hiddenEnd: 6, hiddenKey: '#');  // '###.###.777-35'
```

### Generation (`cpf_gen` / `CpfUtils::generate`)

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
echo cpf_gen();                     // '12345678901'

// Generate formatted CPF
echo cpf_gen(format: true);         // '123.456.789-01'

// Complete a prefix
echo cpf_gen(prefix: '123456789');  // '12345678901'

// Complete and format
echo cpf_gen(prefix: '123456789', format: true);  // '123.456.789-01'
```

### Validation (`cpf_val` / `CpfUtils::isValid`)

Validates CPF numbers using the official algorithm.

```php
cpf_val(string $cpfString): bool
```

**Examples:**

```php
// Valid CPF
echo cpf_val('11144477735');        // true
echo cpf_val('111.444.777-35');     // true

// Invalid CPF
echo cpf_val('11144477736');        // false
```

## Advanced Usage

### Accessing Individual Components

You can access the individual formatter, generator, and validator instances:

```php
$cpfUtils = new CpfUtils();

// Get individual components
$formatter = $cpfUtils->getFormatter();
$generator = $cpfUtils->getGenerator();
$validator = $cpfUtils->getValidator();

// Use them directly
$formatter->format('11144477735', hidden: true);
$generator->generate(format: true);
$validator->isValid('11144477735');
```

### Custom Error Handling

```php
$cpf = '123'; // Invalid length

// Custom fallback
echo cpf_fmt($cpf, onFail: fn($v) => "Invalid CPF: {$v}");  // 'Invalid CPF: 123'

// Return original value
echo cpf_fmt($cpf);  // '123'
```

## Dependencies

This package is built on top of the following specialized packages:

- [`lacus/cpf-fmt`](https://packagist.org/packages/lacus/cpf-fmt) - CPF formatting
- [`lacus/cpf-gen`](https://packagist.org/packages/lacus/cpf-gen) - CPF generation
- [`lacus/cpf-val`](https://packagist.org/packages/lacus/cpf-val) - CPF validation

## Contribution & Support

We welcome contributions! Please see our [Contributing Guidelines](https://github.com/LacusSolutions/br-utils-php/blob/main/CONTRIBUTING.md) for details. But if you find this project helpful, please consider:

- ⭐ Starring the repository
- 🤝 Contributing to the codebase
- 💡 [Suggesting new features](https://github.com/LacusSolutions/br-utils-php/issues)
- 🐛 [Reporting bugs](https://github.com/LacusSolutions/br-utils-php/issues)

## License

This project is licensed under the MIT License - see the [LICENSE](https://github.com/LacusSolutions/br-utils-php/blob/main/LICENSE) file for details.

## Changelog

See [CHANGELOG](https://github.com/LacusSolutions/br-utils-php/blob/main/packages/cpf-utils/CHANGELOG.md) for a list of changes and version history.

---

Made with ❤️ by [Lacus Solutions](https://github.com/LacusSolutions)
