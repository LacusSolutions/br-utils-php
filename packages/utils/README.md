# Lacus Solutions' Utils

[![Packagist Version](https://img.shields.io/packagist/v/lacus/utils)](https://packagist.org/packages/lacus/utils)
[![Packagist Downloads](https://img.shields.io/packagist/dm/lacus/utils)](https://packagist.org/packages/lacus/utils)
[![PHP Version](https://img.shields.io/packagist/php-v/lacus/utils)](https://www.php.net/)
[![Test Status](https://img.shields.io/github/actions/workflow/status/LacusSolutions/br-utils-php/ci.yml?label=ci/cd)](https://github.com/LacusSolutions/br-utils-php/actions)
[![Last Update Date](https://img.shields.io/github/last-commit/LacusSolutions/utils-php)](https://github.com/LacusSolutions/utils-php)
[![Project License](https://img.shields.io/github/license/LacusSolutions/utils-php)](https://github.com/LacusSolutions/utils-php/blob/main/LICENSE)

A PHP reusable utilities library for Lacus Solutions' packages.

| ![PHP 8.1](https://img.shields.io/badge/PHP-8.1-777BB4?logo=php&logoColor=white) | ![PHP 8.2](https://img.shields.io/badge/PHP-8.2-777BB4?logo=php&logoColor=white) | ![PHP 8.3](https://img.shields.io/badge/PHP-8.3-777BB4?logo=php&logoColor=white) | ![PHP 8.4](https://img.shields.io/badge/PHP-8.4-777BB4?logo=php&logoColor=white) | ![PHP 8.5](https://img.shields.io/badge/PHP-8.5-777BB4?logo=php&logoColor=white) |
|--- | --- | --- | --- | --- |
| Passing ✔ | Passing ✔ | Passing ✔ | Passing ✔ | Passing ✔ |

## Features

- **Type description**: Human-readable type strings for error messages (primitives, arrays, `NaN`, `Infinity`)
- **HTML escaping**: Escape `&`, `<`, `>`, `"`, `'` for safe output and XSS mitigation
- **Random sequences**: Generate numeric, alphabetic, or alphanumeric sequences of any length
- **URI encoding**: Percent-encode/decode URI components (path, query, fragment) per RFC 3986
- **Zero dependencies**: No production dependencies

## Installation

```bash
composer require lacus/utils
```

## Quick Start

```php
<?php

use Lacus\Utils\HtmlUtils;
use Lacus\Utils\SequenceGenerator;
use Lacus\Utils\SequenceType;
use Lacus\Utils\TypeDescriber;
use Lacus\Utils\UrlUtils;

TypeDescriber::describe(null);                    // 'null'
TypeDescriber::describe('hello');                 // 'string'
TypeDescriber::describe(42);                      // 'integer number'
TypeDescriber::describe([1, 2, 3]);               // 'number[]'

HtmlUtils::escape('Tom & Jerry');                 // 'Tom &amp; Jerry'
HtmlUtils::unescape('Tom &amp; Jerry');           // 'Tom & Jerry'

SequenceGenerator::generate(10, SequenceType::Numeric);       // e.g. '9956000611'
SequenceGenerator::generate(6, SequenceType::Alphabetic);      // e.g. 'AXQMZB'

UrlUtils::encodeUriComponent('a b');                  // 'a%20b'
UrlUtils::decodeUriComponent('a%20b');            // 'a b'
```

## API

All classes live under the `Lacus\Utils` namespace.

| Class | Method | Description |
|-------|--------|-------------|
| `TypeDescriber` | `describe(mixed $value): string` | Type description for error messages |
| `HtmlUtils` | `escape`, `unescape` | HTML entity escaping and decoding |
| `SequenceGenerator` | `generate(int $size, SequenceType $type): string` | Random sequence generation |
| `SequenceType` | enum: `Numeric`, `Alphabetic`, `Alphanumeric` | Sequence kind |
| `UrlUtils` | `encodeUriComponent`, `decodeUriComponent` | URI component percent-encoding (RFC 3986) |

### TypeDescriber::describe()

Same behaviour as JS `describeType`: `null`, `string`, `boolean`, `integer number`, `float number`, `NaN`, `Infinity`, `Array (empty)`, `number[]`, `(number | string)[]`, `object`, `resource`, etc.

### HtmlUtils

- **escape(string $value): string** — Escapes HTML special characters: `&` → `&amp;`, `<` → `&lt;`, `>` → `&gt;`, `"` → `&quot;`, `'` → `&#039;`. Use for safe output and XSS mitigation.
- **unescape(string $value): string** — Decodes those entities (reverse of `escape`). Use only on previously escaped or trusted content; decoded output is not safe for direct insertion into HTML.

### SequenceGenerator::generate()

- **Numeric**: digits `0-9`
- **Alphabetic**: uppercase `A-Z`
- **Alphanumeric**: `0-9` and `A-Z`

### UrlUtils (URI encoding)

- **encodeUriComponent(string $value): string** — Percent-encode a string for use as any URI component (path segment, query name/value, or fragment). E.g. `a/b` → `a%2Fb`, `a b` → `a%20b`.
- **decodeUriComponent(string $value): string** — Decode a percent-encoded URI component (reverse of `encodeUriComponent`).
