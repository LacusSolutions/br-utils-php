# Lacus :: cpf-gen

[![Packagist Version](https://img.shields.io/packagist/v/lacus/cpf-gen)](https://packagist.org/packages/lacus/cpf-gen)
[![Packagist Downloads](https://img.shields.io/packagist/dm/lacus/cpf-gen)](https://packagist.org/packages/lacus/cpf-gen)
[![PHP Version](https://img.shields.io/packagist/php-v/lacus/cpf-gen)](https://www.php.net/)
[![Test Status](https://img.shields.io/github/actions/workflow/status/LacusSolutions/br-utils-php/ci.yml?label=ci/cd)](https://github.com/LacusSolutions/br-utils-php/actions)
[![Last Update Date](https://img.shields.io/github/last-commit/LacusSolutions/br-utils-php)](https://github.com/LacusSolutions/br-utils-php)
[![Project License](https://img.shields.io/github/license/LacusSolutions/br-utils-php)](https://github.com/LacusSolutions/br-utils-php/blob/main/LICENSE)

Utility function/class to generate valid CPF (Brazilian personal ID).

## PHP Support

| ![PHP 8.1](https://img.shields.io/badge/PHP-8.1-777BB4?logo=php&logoColor=white) | ![PHP 8.2](https://img.shields.io/badge/PHP-8.2-777BB4?logo=php&logoColor=white) | ![PHP 8.3](https://img.shields.io/badge/PHP-8.3-777BB4?logo=php&logoColor=white) | ![PHP 8.4](https://img.shields.io/badge/PHP-8.4-777BB4?logo=php&logoColor=white) |
|--- | --- | --- | --- |
| Passing ✔ | Passing ✔ | Passing ✔ | Passing ✔ |

## Installation

```bash
# using Composer
$ composer require lacus/cpf-gen
```

## Import

```php
<?php
// Using class-based resource
use Lacus\CpfGen\CpfGenerator;

// Or using function-based one
use function Lacus\CpfGen\cpf_gen;
```

## Usage

### Object-Oriented Usage

```php
$generator = new CpfGenerator();
$cpf = $generator->generate(); // returns '47844241055'

// With options
$cpf = $generator->generate(
    format: true
); // returns '478.442.410-55'

$cpf = $generator->generate(
    prefix: '528250911'
); // returns '52825091138'

$cpf = $generator->generate(
    prefix: '528250911',
    format: true
); // returns '528.250.911-38'
```

The options can be provided to the constructor or the `generate()` method. If passed to the constructor, the options will be attached to the `CpfGenerator` instance. When passed to the `generate()` method, it only applies the options to that specific call.

```php
$generator = new CpfGenerator(format: true);

$cpf1 = $generator->generate(); // '478.442.410-55' (uses instance options)
$cpf2 = $generator->generate(format: false); // '47844241055' (overrides instance options)
$cpf3 = $generator->generate(); // '123.456.789-01' (uses instance options again)
```

### Imperative programming

The helper function `cpf_gen()` is just a functional abstraction. Internally it creates an instance of `CpfGenerator` and calls the `generate()` method right away.

```php
$cpf = cpf_gen(); // returns '47844241055'

$cpf = cpf_gen(format: true); // returns '478.442.410-55'

$cpf = cpf_gen(prefix: '528250911'); // returns '52825091138'

$cpf = cpf_gen(prefix: '528250911', format: true); // returns '528.250.911-38'
```

### Generator Options

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `format` | `?bool` | `false` | Whether to format the output with dots and dash |
| `prefix` | `?string` | `''` | If you have CPF initials and want to complete it with valid digits. The string provided must contain between 0 and 9 digits! |

## Contribution & Support

We welcome contributions! Please see our [Contributing Guidelines](https://github.com/LacusSolutions/br-utils-php/blob/main/CONTRIBUTING.md) for details. But if you find this project helpful, please consider:

- ⭐ Starring the repository
- 🤝 Contributing to the codebase
- 💡 [Suggesting new features](https://github.com/LacusSolutions/br-utils-php/issues)
- 🐛 [Reporting bugs](https://github.com/LacusSolutions/br-utils-php/issues)

## License

This project is licensed under the MIT License - see the [LICENSE](https://github.com/LacusSolutions/br-utils-php/blob/main/LICENSE) file for details.

## Changelog

See [CHANGELOG](https://github.com/LacusSolutions/br-utils-php/blob/main/packages/cpf-gen/CHANGELOG.md) for a list of changes and version history.

---

Made with ❤️ by [Lacus Solutions](https://github.com/LacusSolutions)
