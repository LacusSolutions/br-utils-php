---
id: package-arch
title: Package implementation architecture
scope: packages/*/src/**/*.php
triggers:
  - implementing or changing package source code
  - adding a new class, exception, or option
  - designing or reviewing src/ layout
  - adding error handling (throwing vs onFail)
  - changing or adding snake_case helper functions
  - working on Exceptions/ or Enums/ subdirectories
---

# package-arch

Follow the repeatable implementation architecture when adding or changing source code in any `packages/*` package. All paths are relative to the repo root.

## Migration context

The PHP monorepo currently has **two coexisting generations**. Target generation is **v2**; v1 CPF packages are being migrated. Understand which generation a package belongs to before making changes.

| Generation | Applies to | Namespace | Test runner |
|------------|------------|-----------|-------------|
| **v2 (target)** | All CNPJ packages, `cpf-dv`, `utils` | `Lacus\BrUtils\{Cpf\|Cnpj}\`, `Lacus\Utils\` | Pest 3 |
| **v1 (legacy)** | `cpf-fmt`, `cpf-gen`, `cpf-val`, `cpf-utils`, `br-utils` (main) | `Lacus\{CpfFmt\|CpfGen\|CpfVal\|CpfUtils}\` | PHPUnit 10 |

When migrating a v1 package to v2, follow the CNPJ packages (`cnpj-fmt`, `cnpj-gen`, `cnpj-val`) as the canonical reference.

## Package archetypes

| Archetype | Examples | Role |
|-----------|----------|------|
| **DV** (check digits) | `cpf-dv`, `cnpj-dv` | Main class only; no facade function |
| **Val** (validator) | `cpf-val`, `cnpj-val` | Main class + snake_case helper + Options |
| **Fmt** (formatter) | `cpf-fmt`, `cnpj-fmt` | Main class + snake_case helper + Options |
| **Gen** (generator) | `cpf-gen`, `cnpj-gen` | Main class + snake_case helper + Options |
| **Foundation** | `utils` | Named classes only; no aggregator |
| **Aggregator** | `cpf-utils`, `cnpj-utils`, `br-utils` | Facade class wrapping leaf packages |

## Canonical `src/` layout

### DV

```
src/
  {Domain}CheckDigits.php      # Main class
  Exceptions/
    {Domain}CheckDigitsTypeError.php
    {Domain}CheckDigitsException.php
    ...
```

### Val / Fmt / Gen (v2)

```
src/
  {Domain}{Role}.php           # Main class  (e.g. CnpjFormatter)
  {Domain}{Role}Options.php    # Options value object
  {domain}-{role}.php          # snake_case helper + constants (Composer autoload "files")
  Exceptions/
    {Domain}{Role}TypeError.php
    {Domain}{Role}Exception.php
    {Domain}{Role}Input...Exception.php
    {Domain}{Role}Options...Exception.php
    ...
  Enums/                       # Only Gen and Val — e.g. CnpjType, CnpjValidationType
    {Domain}Type.php
    {Domain}ValidationType.php
```

### Foundation (`utils`)

```
src/
  HtmlUtils.php
  UrlUtils.php
  TypeDescriber.php
  SequenceGenerator.php
  SequenceType.php             # Enum
```

### Aggregator (`cpf-utils`, `cnpj-utils`)

```
src/
  {Domain}Utils.php            # Main façade class
  {domain}-utils.php           # snake_case re-exports (Composer "files")
```

`br-utils` wraps both domains:

```
src/
  BrUtils.php
  CpfUtils.php
  CnpjUtils.php
  Cpf/
    CpfFormatter.php, CpfGenerator.php, CpfValidator.php, cpf_utils.php
  Cnpj/
    CnpjFormatter.php, CnpjGenerator.php, CnpjValidator.php, cnpj_utils.php
```

## snake_case helper pattern (Val / Fmt / Gen)

The snake_case helper file is autoloaded via Composer `"files"` and provides a stateless one-liner API:

```php
<?php

declare(strict_types=1);

namespace Lacus\BrUtils\Cnpj;

/**
 * The standard length of a CNPJ identifier (14 characters).
 */
const CNPJ_LENGTH = 14;

/**
 * Formats a CNPJ string using `CnpjFormatter`.
 *
 * @param string|list<string> $cnpjInput
 * @param ?CnpjFormatterOptions $options
 * ...named params...
 * @throws CnpjFormatterInputTypeError
 */
function cnpj_fmt(
    string|array $cnpjInput,
    ?CnpjFormatterOptions $options = null,
    // ...named params...
): string {
    return (new CnpjFormatter($options, /* named params */))->format($cnpjInput);
}
```

Rationale: the helper provides a stateless call-once API; the class provides a stateful configurable API. Both are first-class entry points.

## Error handling: throw vs `onFail`

| Error category | Handling |
|----------------|---------|
| **Type errors** (wrong PHP type passed) | Always `throw` — extends `*TypeError` which extends `\TypeError` |
| **Length / business-rule failures** (right type, wrong value) | Call `onFail` callback — extends `*Exception` which extends `\RuntimeException` |

The `onFail` callback signature is `Closure(mixed $value, *Exception $exception): string`. The default implementation returns an empty string. It must never throw by default.

## `Exceptions/` structure

Every v2 package that can fail exposes typed abstract base classes plus concrete subclasses:

```php
<?php

declare(strict_types=1);

namespace Lacus\BrUtils\Cnpj\Exceptions;

/**
 * Base type error for all cnpj-fmt type-related errors.
 * Extends \TypeError. Sets the error name automatically.
 */
abstract class CnpjFormatterTypeError extends \TypeError
{
    public function __construct(string $message)
    {
        parent::__construct($message);
        $this->code = 0;
    }
}

/**
 * Base domain exception for cnpj-fmt failures.
 * Extends \RuntimeException.
 */
abstract class CnpjFormatterException extends \RuntimeException
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
```

Each concrete subclass covers one specific failure (input type, input length, options type, options value, etc.). Do not collapse multiple failure cases into one exception class.

## Options class pattern (Fmt / Gen / Val)

```php
class CnpjFormatterOptions
{
    public const DEFAULT_HIDDEN = false;
    public const DEFAULT_HIDDEN_START = 5;
    public const DEFAULT_HIDDEN_END = 13;

    public bool $hidden;
    // ...

    public function __construct(
        ?CnpjFormatterOptions $overrides = null,
        // named params...
    ) {
        // validate types → throw *TypeError
        // validate values → throw *Exception
        $this->hidden = $hidden ?? self::DEFAULT_HIDDEN;
        // ...
    }
}
```

Defaults live as `public const` class constants. The constructor validates options and throws on invalid types or values. Options are exposed as public properties (not getters/setters) in v2.

## Dependency direction

```
utils → {cpf,cnpj}-dv → {cpf,cnpj}-{fmt,gen,val} → {cpf,cnpj}-utils → br-utils
```

Upstream packages must not import downstream ones. `utils` is a leaf with no internal deps. To inspect the live graph from `composer.json` declarations, run `php run deps` — see [`agents/dependencies.md`](dependencies.md#inspecting-internal-dependencies).

## Checklist

- [ ] `src/` layout matches the archetype (DV / Val / Fmt / Gen / Foundation / Aggregator)
- [ ] v2 namespace used: `Lacus\BrUtils\{Cpf|Cnpj}\` (not legacy `Lacus\CpfFmt\` etc.)
- [ ] `declare(strict_types=1);` at the top of every file
- [ ] snake_case helper present for Val/Fmt/Gen; class is the primary entry point for DV
- [ ] Type errors throw `*TypeError`; length/business-rule failures call `onFail`
- [ ] `Exceptions/` defines abstract base + concrete subclasses
- [ ] Options class uses `const` defaults; constructor validates and throws
- [ ] `Enums/` added for Gen and Val packages only
- [ ] PHPDoc on all exported symbols per [`agents/phpdoc.md`](phpdoc.md)
- [ ] Tests per [`agents/unit-tests.md`](unit-tests.md)

## Package-level overrides

Before applying this harness, check whether the target package defines `packages/<pkg>/AGENTS.md` or `packages/<pkg>/agents/`. If either exists and contradicts this file on the same topic, **follow the package-level instruction** (see [`agents/README.md`](README.md#instruction-precedence)).

## Reference packages

| Archetype | Canonical package | Key files |
|-----------|------------------|-----------|
| DV | `cnpj-dv` | `src/CnpjCheckDigits.php`, `src/Exceptions/` |
| Fmt | `cnpj-fmt` | `src/CnpjFormatter.php`, `src/CnpjFormatterOptions.php`, `src/cnpj-fmt.php` |
| Val | `cnpj-val` | `src/CnpjValidator.php`, `src/CnpjValidatorOptions.php`, `src/Enums/` |
| Gen | `cnpj-gen` | `src/CnpjGenerator.php`, `src/CnpjGeneratorOptions.php`, `src/Enums/` |
| Foundation | `utils` | `src/HtmlUtils.php`, `src/TypeDescriber.php` |
| Aggregator | `cnpj-utils` | `src/CnpjUtils.php` |
| Top aggregator | `br-utils` | `src/BrUtils.php`, `src/Cpf/`, `src/Cnpj/` |
