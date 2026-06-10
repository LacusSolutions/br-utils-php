---
id: phpdoc
title: PHPDoc conventions
scope: packages/*/src/**/*.php
triggers:
  - adding or updating PHPDoc comments
  - documenting a new class, function, or constant
  - reviewing PHPDoc on a changed API
  - adding @throws, @param, or @property-read annotations
---

# phpdoc

Write and maintain PHPDoc for all exported and internal API symbols across br-utils-php v2 packages. All paths are relative to the **php/** subrepo root.

## Repository constraints

- **All public symbols get PHPDoc** — exported classes, methods, functions, constants, exception classes, and enum cases.
- Do **not** use PHPDoc to narrate implementation steps or restate what the code obviously does. Document intent, constraints, and behavior the code cannot express alone.
- Tone: concise and user-facing, as if writing Packagist package documentation.
- Follow the v2 reference implementations: `packages/cnpj-fmt/src/CnpjFormatter.php`, `packages/cnpj-fmt/src/CnpjFormatterOptions.php`, `packages/cnpj-dv/src/CnpjCheckDigits.php`.

## Before writing PHPDoc

1. Read the symbol's source and any related `*Options.php` and `Exceptions/` files.
2. Identify: what does it do, what can go wrong (exceptions thrown or `onFail` called), what options control behavior.
3. Skim PHPDoc in sibling classes in the same package — match style and verbosity.

## Class documentation

```php
/**
 * Formatter for CNPJ (Cadastro Nacional da Pessoa Jurídica) identifiers.
 * Normalizes and optionally masks, HTML-escapes, or URL-encodes 14-character
 * alphanumeric CNPJ input. Invalid input type is handled by throwing;
 * invalid length is handled via the configured `onFail` callback.
 */
class CnpjFormatter
{
    /**
     * Creates a new `CnpjFormatter` with optional default options.
     *
     * When `$options` is a `CnpjFormatterOptions` instance, that instance is
     * used directly (no copy is created). Mutating it later affects future
     * `format` calls that do not pass per-call options. When null or named
     * parameters are passed, a new `CnpjFormatterOptions` is constructed.
     *
     * @param ?CnpjFormatterOptions $options
     * @param ?bool $hidden
     * @param ?string $hiddenKey
     * ...
     * @param ?Closure(mixed, CnpjFormatterException): string $onFail
     *
     * @throws CnpjFormatterOptionsTypeError If any option has an invalid type.
     * @throws CnpjFormatterOptionsHiddenRangeInvalidException If `hiddenStart`
     *   or `hiddenEnd` are out of valid range.
     */
    public function __construct(...) {}
}
```

### Rules

- One-sentence class summary unless a second sentence is necessary for constraints or usage notes.
- Constructor doc: describe input shapes and behavioral differences between them. List every `@throws` with its trigger condition.
- Method docs: describe the behavior, not the implementation. Include `@throws` for type errors; mention `onFail` for length/validation failures.

## Exception and abstract base documentation

```php
/**
 * Base type error for all `cnpj-fmt` type-related errors.
 *
 * Extends `\TypeError`. Use concrete subclasses to distinguish specific
 * type violations.
 */
abstract class CnpjFormatterTypeError extends \TypeError {}

/**
 * Thrown when the input passed to `CnpjFormatter::format()` is not a
 * `string` or `list<string>`.
 */
class CnpjFormatterInputTypeError extends CnpjFormatterTypeError {}
```

Abstract bases explain the inheritance hierarchy and which `\PHP` base they extend. Concrete subclasses document what specific violation they capture.

## Constants and option defaults

Document inline at the declaration:

```php
/**
 * The standard length of a CNPJ identifier (14 characters).
 */
const CNPJ_LENGTH = 14;

/**
 * Default value for the `hidden` option.
 */
public const DEFAULT_HIDDEN = false;
```

## `@param` and `@return` annotations

- Use `@param` when the parameter name alone is ambiguous or the type is a complex union / shape.
- Use typed Closure shapes when the parameter is a callable: `@param Closure(mixed, CnpjFormatterException): string $onFail`.
- Use `@return` only when the return type is non-obvious or the method is a factory returning a subtype.
- Use PHPStan array shapes when an array argument has a known structure: `@param array{hidden?: bool, ...} $options`.

## `@throws` annotation style

```php
 * @throws CnpjFormatterOptionsTypeError If any option has an invalid type.
 * @throws CnpjFormatterInputLengthException If the sanitized CNPJ length is not 14.
```

- Use the concrete exception class name (not `\RuntimeException` or `\TypeError` generically).
- Describe the trigger condition in one short clause.
- List every exception that can propagate from the documented method, including those thrown by called sub-methods when relevant.

## `@property-read` (magic property classes)

When a class exposes readable properties via `__get` (e.g. `CnpjFormatterOptions`), document them with `@property-read` on the class docblock:

```php
/**
 * Value object holding all options for `CnpjFormatter`.
 *
 * @property-read bool $hidden
 * @property-read string $hiddenKey
 * @property-read int $hiddenStart
 * @property-read int $hiddenEnd
 * ...
 */
class CnpjFormatterOptions {}
```

## snake_case function files

Document constants and functions in `{domain}-{role}.php` files:

```php
/**
 * The standard length of a CNPJ identifier (14 characters).
 */
const CNPJ_LENGTH = 14;

/**
 * Formats a CNPJ value using a one-shot `CnpjFormatter`.
 *
 * Builds a `CnpjFormatter` from the provided options and calls `format()`
 * once. Use named arguments for options: `cnpj_fmt($cnpj, hidden: true)`.
 *
 * @param string|list<string> $cnpjInput
 * @param ?CnpjFormatterOptions $options
 * ...
 *
 * @throws CnpjFormatterInputTypeError If input is not a string or list<string>.
 * @throws CnpjFormatterOptionsTypeError If any option has an invalid type.
 */
function cnpj_fmt(string|array $cnpjInput, ...): string {}
```

## What not to document

- Do not add `@param` for every parameter when the name and type declaration are already self-explanatory.
- Do not add `@return` for trivial getters or methods whose return type declaration is clear.
- Do not repeat the type declaration in words ("string — the formatted CNPJ").
- Do not add `@see` links unless the reference genuinely helps navigation.
- Do not narrate what the code obviously does.

## Checklist

- [ ] Every exported class has a PHPDoc block
- [ ] Constructors document input shapes, behavioral differences, and every `@throws`
- [ ] Abstract base exceptions describe the inheritance hierarchy
- [ ] `@property-read` annotations present on magic-property classes
- [ ] Constants have inline one-line doc comments
- [ ] snake_case helper functions are documented with `@param`, `@throws`, and usage note
- [ ] `@throws` uses concrete class names with trigger conditions
- [ ] No narration of obvious code

## Package-level overrides

Before applying this harness, check whether the target package defines `packages/<pkg>/AGENTS.md` or `packages/<pkg>/agents/`. If either exists and contradicts this file on the same topic, **follow the package-level instruction** (see [`agents/README.md`](README.md#instruction-precedence)).

## Reference

| Concern | Canonical example |
|---------|-------------------|
| Class + constructor PHPDoc | `packages/cnpj-fmt/src/CnpjFormatter.php` |
| Options class with `@property-read` | `packages/cnpj-fmt/src/CnpjFormatterOptions.php` |
| Abstract base exception PHPDoc | `packages/cnpj-fmt/src/Exceptions/CnpjFormatterTypeError.php` |
| Concrete exception PHPDoc | `packages/cnpj-fmt/src/Exceptions/CnpjFormatterInputTypeError.php` |
| Constants + helper function | `packages/cnpj-fmt/src/cnpj-fmt.php` |
