# lacus/cnpj-gen

## 2.1.1

### Patch Changes

- **Dependency constraints** — Changed internal dependency version constraints from caret to tilde notation for more predictable feature propagation.

## 2.1.0

### Improvements

- Runtime dependency `lacus/cnpj-dv` updated to v1.1, with a slight performance improvement.

### Deprecations

- **`CnpjType` rename planned** — `CnpjType` was deprecated and will be renamed to `CnpjGenerationType` in the next major version of the package. Use an import alias (`use Lacus\BrUtils\Cnpj\Enums\CnpjType as CnpjGenerationType`) to prepare for the change in near future.

## 2.0.0

### 🎉 v2 at a glance 🎊

- 🆕 **Alphanumeric CNPJ** — Full support for the new [14-character alphanumeric CNPJ](https://www.gov.br/receitafederal/pt-br/assuntos/noticias/2023/julho/cnpj-alfa-numerico) (digits and letters); input is sanitized and uppercased before formatting.
- 🛡️ **Structured errors** — Typed exceptions (`CnpjGeneratorTypeError`, `CnpjGeneratorException` and their subclasses variants) for clearer error handling.

### BREAKING CHANGES

- **Namespace migration**: public API moved from `Lacus\CnpjGen\` to `Lacus\BrUtils\Cnpj\`.
  - Update `use` imports for `CnpjGenerator`, `CnpjGeneratorOptions`, and `cnpj_gen()`.
- **Drop support to PHP v8.1**: Minimum version for the package is now **PHP 8.2** (`^8.2`). It may even run forcedly in earlier versions, but it's not recommended to keep running stale versions of PHP in production.
- **Generation engine changed**:
  - Internal `CnpjGeneratorVerifierDigit` was removed.
  - Check digits are now generated through `lacus/cnpj-dv` (`CnpjCheckDigits`).
- **Options model refactor**:
  - `CnpjGeneratorOptions::merge()` and classic `getPrefix()` / `isFormatting()` style were replaced by a property-based options object (`format`, `prefix`, `type`) plus `set()` / `getAll()`.
- **Validation/exception model changed**:
  - Generic `InvalidArgumentException`-style flow was replaced with package-specific typed errors/exceptions.
- **Test stack migration**:
  - PHPUnit suite replaced by Pest specs (`tests/Specs/*`).
  - Legacy env/external-validator test utilities were removed.

### New Features

- **Alphanumeric CNPJ support**: generator now supports base characters from `0-9` and `A-Z` (with numeric check digits), aligned with the new format.
- **`CnpjType` enum**: added `Alphanumeric`, `Alphabetic`, and `Numeric` generation modes.
- **Expanded constructor and per-call options**:
  - `CnpjGenerator` now accepts an optional `CnpjGeneratorOptions` instance and/or named arguments.
  - `generate()` can receive per-call named options or a `CnpjGeneratorOptions` instance.
- **Layered overrides**:
  - `CnpjGeneratorOptions` now supports `overrides` (array/object chain; last override wins).
- **Structured exception hierarchy**:
  - Added `CnpjGeneratorTypeError`, `CnpjGeneratorException` and concrete subclasses:
    - `CnpjGeneratorOptionsTypeError`
    - `CnpjGeneratorOptionPrefixInvalidException`
    - `CnpjGeneratorOptionTypeInvalidException`
- **Prefix behavior improvements**:
  - Prefix accepts alphanumeric characters.
  - Non-alphanumeric chars are stripped, letters are uppercased.
  - Prefix is capped to 12 base characters (characters from index `12+` are ignored).
- **Retry-on-invalid-generated-sequence**:
  - Generator retries internally when check-digit computation rejects a generated candidate.

### Improvements

- **Dependency alignment**: now depends on `lacus/cnpj-dv` and `lacus/utils`.
- **Autoload alignment**: package autoload namespace updated to the BR Utils standard.
- **Docs refresh**: English and pt-BR READMEs updated with v2 API and usage guidance.
- **Test quality**:
  - Added process-isolation tests for retry behavior and static sequence generation mocking.
  - Added dedicated specs for options, helper function, and exception contracts.

## 1.0.0

### Stable v1 API

First stable release of **`lacus/cnpj-gen`** focused on numeric CNPJ generation.

- **API namespace**: `Lacus\CnpjGen\`.
- **Main resources**:
  - `cnpj_gen()`
  - `CnpjGenerator`
  - `CnpjGeneratorOptions`
  - `CnpjGeneratorVerifierDigit`
- **Generation scope**:
  - Numeric-only generation.
  - Optional output formatting (`XX.XXX.XXX/XXXX-XX`).
  - Prefix-based generation for numeric seeds.
- **Validation model**:
  - Option errors handled by generic exceptions.
- **Runtime**: PHP `>=8.1`.
- **Testing**: PHPUnit-based suite.
