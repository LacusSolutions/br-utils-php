![cnpj-gen for PHP](https://github.com/user-attachments/assets/80ba4abd-169b-456a-acc3-d10759856a1e)

[![Packagist Version](https://img.shields.io/packagist/v/lacus/cnpj-gen)](https://packagist.org/packages/lacus/cnpj-gen)
[![Packagist Downloads](https://img.shields.io/packagist/dm/lacus/cnpj-gen)](https://packagist.org/packages/lacus/cnpj-gen)
[![PHP Version](https://img.shields.io/packagist/php-v/lacus/cnpj-gen)](https://www.php.net/)
[![Test Status](https://img.shields.io/github/actions/workflow/status/LacusSolutions/br-utils-php/ci.yml?label=ci/cd)](https://github.com/LacusSolutions/br-utils-php/actions)
[![Last Update Date](https://img.shields.io/github/last-commit/LacusSolutions/br-utils-php)](https://github.com/LacusSolutions/br-utils-php)
[![Project License](https://img.shields.io/github/license/LacusSolutions/br-utils-php)](https://github.com/LacusSolutions/br-utils-php/blob/main/LICENSE)

Utility function/class to generate valid CNPJ (Brazilian employer ID).



| ![PHP 8.1](https://img.shields.io/badge/PHP-8.1-777BB4?logo=php&logoColor=white) | ![PHP 8.2](https://img.shields.io/badge/PHP-8.2-777BB4?logo=php&logoColor=white) | ![PHP 8.3](https://img.shields.io/badge/PHP-8.3-777BB4?logo=php&logoColor=white) | ![PHP 8.4](https://img.shields.io/badge/PHP-8.4-777BB4?logo=php&logoColor=white) |
|--- | --- | --- | --- |
| Passing ✔ | Passing ✔ | Passing ✔ | Passing ✔ |

## Installation

```bash
# using Composer
$ composer require lacus/cnpj-gen
```

## Import

```php
<?php
// Using class-based resource
use Lacus\CnpjGen\CnpjGenerator;

// Or using function-based one
use function Lacus\CnpjGen\cnpj_gen;
```

## Usage

### Object-Oriented Usage

```php
$generator = new CnpjGenerator();
$cnpj = $generator->generate(); // returns '65453043000178'

// With options
$cnpj = $generator->generate(
    format: true
); // returns '65.453.043/0001-78'

$cnpj = $generator->generate(
    prefix: '45623767'
); // returns '45623767000296'

$cnpj = $generator->generate(
    prefix: '456237670002',
    format: true
); // returns '45.623.767/0002-96'
```

The options can be provided to the constructor or the `generate()` method. If passed to the constructor, the options will be attached to the `CnpjGenerator` instance. When passed to the `generate()` method, it only applies the options to that specific call.

```php
$generator = new CnpjGenerator(format: true);

$cnpj1 = $generator->generate(); // '65.453.043/0001-78' (uses instance options)
$cnpj2 = $generator->generate(format: false); // '65453043000178' (overrides instance options)
$cnpj3 = $generator->generate(); // '12.345.678/0001-95' (uses instance options again)
```

### Imperative programming

The helper function `cnpj_gen()` is just a functional abstraction. Internally it creates an instance of `CnpjGenerator` and calls the `generate()` method right away.

```php
$cnpj = cnpj_gen(); // returns '65453043000178'

$cnpj = cnpj_gen(format: true); // returns '65.453.043/0001-78'

$cnpj = cnpj_gen(prefix: '45623767'); // returns '45623767000296'

$cnpj = cnpj_gen(prefix: '456237670002', format: true); // returns '45.623.767/0002-96'
```

### Generator Options

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `format` | `?bool` | `false` | Whether to format the output with dots, slash, and dash |
| `prefix` | `?string` | `''` | If you have CNPJ initials and want to complete it with valid digits. The string provided must contain between 0 and 12 digits. The branch ID (characters 8 to 11) cannot be "0000". |

## Contribution & Support

We welcome contributions! Please see our [Contributing Guidelines](https://github.com/LacusSolutions/br-utils-php/blob/main/CONTRIBUTING.md) for details. But if you find this project helpful, please consider:

- ⭐ Starring the repository
- 🤝 Contributing to the codebase
- 💡 [Suggesting new features](https://github.com/LacusSolutions/br-utils-php/issues)
- 🐛 [Reporting bugs](https://github.com/LacusSolutions/br-utils-php/issues)

## License

This project is licensed under the MIT License - see the [LICENSE](https://github.com/LacusSolutions/br-utils-php/blob/main/LICENSE) file for details.

## Changelog

See [CHANGELOG](https://github.com/LacusSolutions/br-utils-php/blob/main/packages/cnpj-gen/CHANGELOG.md) for a list of changes and version history.

---

Made with ❤️ by [Lacus Solutions](https://github.com/LacusSolutions)
