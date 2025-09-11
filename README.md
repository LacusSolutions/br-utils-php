# Lacus :: cpf-fmt

![Packagist Version](https://img.shields.io/packagist/v/lacus/cpf-fmt)
![Packagist Downloads](https://img.shields.io/packagist/dm/lacus/cpf-fmt)
![PHP Version](https://img.shields.io/packagist/php-v/lacus/cpf-fmt)
![Test Status](https://img.shields.io/github/actions/workflow/status/LacusSolutions/br-utils-php/ci.yml?label=ci/cd)
![Last Update Date](https://img.shields.io/github/last-commit/LacusSolutions/br-utils-php)
![Project License](https://img.shields.io/packagist/l/lacus/cpf-fmt)

Utility function to format CPF (Brazilian ID document).

## PHP Support

![PHP 8.1](https://img.shields.io/badge/PHP-8.1-777BB4?logo=php&logoColor=white) | ![PHP 8.2](https://img.shields.io/badge/PHP-8.2-777BB4?logo=php&logoColor=white) | ![PHP 8.3](https://img.shields.io/badge/PHP-8.3-777BB4?logo=php&logoColor=white) |
|--- | --- | --- |
| 8.1+ ✔ | 8.2+ ✔ | 8.3+ ✔ |

## Installation

```bash
# using Composer
$ composer require lacus/cpf-fmt
```

## Import

```php
<?php
// Using class-based resource
use Lacus\CpfFmt\CpfFormatter;

// Or using function-based one
use function Lacus\CpfFmt\cpf_fmt;
```

## Usage

### Object-Oriented Usage

```php
$formatter = new CpfFormatter();
$cpf = '47844241055';

echo $formatter->format($cpf);       // returns '478.442.410-55'

// With options
echo $formatter->format(
    $cpf,
    hidden: true,
    hiddenKey: '#',
    hiddenStart: 3,
    hiddenEnd: 10
);  // returns '478.###.###-##'
```

The options can be provided to the constructor or the `format()` method. If passed to the constructor, the options will be attached to the `CpfFormatter` instance. When passed to the `format()` method, it only applies the options to that specific call.

```php
$cpf = '12345678910';
$formatter = new CpfFormatter(hidden: true);

echo $formatter->format($cpf);                  // '123.***.***-**'
echo $formatter->format($cpf, hidden: false);   // '123.456.789-10' merges the options to the instance's
echo $formatter->format($cpf);                  // '123.***.***-**' uses only the instance options
```

### Imperative programming

The helper function `cpf_fmt()` is just a functional abstraction. Internally it creates an instance of `CpfFormatter` and calls the `format()` method right away.

```php
$cpf = '47844241055';

echo cpf_fmt($cpf);       // returns '478.442.410-55'

echo cpf_fmt($cpf, hidden: true);     // returns '478.***.***-**'

echo cpf_fmt($cpf, dotKey: '', dashKey: '_');     // returns '478442410_55'
```

### Formatting Options

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `escape` | `?bool` | `false` | Whether to HTML escape the result |
| `hidden` | `?bool` | `false` | Whether to hide digits with a mask |
| `hiddenKey` | `?string` | `'*'` | Character to replace hidden digits |
| `hiddenStart` | `?int` | `3` | Starting index for hidden range (0-10) |
| `hiddenEnd` | `?int` | `10` | Ending index for hidden range (0-10) |
| `dotKey` | `?string` | `'.'` | String to replace dot characters |
| `dashKey` | `?string` | `'-'` | String to replace dash character |
| `onFail` | `?callable` | `fn($v) => $v` | Fallback function for invalid input |

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'feat: add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for a list of changes and version history.

---

Made with ❤️ by [Lacus Solutions](https://github.com/LacusSolutions)
