# lacus/cnpj-val

## 2.0.1

### Patch Changes

- **Dependency constraints** — Changed internal dependency version constraints from caret to tilde notation for more predictable feature propagation.

## 2.0.0

### 🎉 v2 at a glance 🎊

- 🆕 **Alphanumeric CNPJ** — Full support for the new [14-character alphanumeric CNPJ](https://www.gov.br/receitafederal/pt-br/assuntos/noticias/2023/julho/cnpj-alfa-numerico) (digits and letters).
- ⚙️ **`CnpjValidatorOptions`** — Configure `type` (`numeric` vs `alphanumeric`) and `caseSensitive` on the instance or per `isValid()` call.
- 🛡️ **Structured errors** — Typed exceptions (`CnpjValidatorTypeError`, `CnpjValidatorException` and their subclasses) for clearer error handling.

### BREAKING CHANGES

- **Namespace migration** — Public API moved from `Lacus\CnpjVal\` to `Lacus\BrUtils\Cnpj\`; update `use` imports for `CnpjValidator`, `CnpjValidatorOptions`, `CnpjValidationType`, and `cnpj_val()`.
- **Drop support to PHP v8.1**: Minimum version for the package is now **PHP 8.2** (`^8.2`). It may even run forcedly in earlier versions, but it's not recommended to keep running stale versions of PHP in production.
- **`CNPJ_LENGTH` constant** — Removed from `cnpj-val.php`; use `CnpjValidatorOptions::CNPJ_LENGTH` instead.
- **Default character set** — Default validation is **alphanumeric** (letters are kept after sanitization). For legacy numeric-only behavior, pass `type: 'numeric'` or `CnpjValidationType::Numeric`.
- **`cnpj_val()` / `isValid()` signatures** — Accept `string|list<string>` and optional `CnpjValidatorOptions` (or named `type` / `caseSensitive`); v1 accepted only a single `string`.
- **Invalid input** — Non-string / non–`string[]` values throw `CnpjValidatorInputTypeError` instead of a native `TypeError`.
- **Check-digit engine** — Validation delegates to `CnpjCheckDigits` from **`lacus/cnpj-dv`** `^1.1` (v1 calculated digits inline via `CnpjGeneratorVerifierDigit` from **`lacus/cnpj-gen`** only).

### New features

- **`CnpjValidatorOptions`** — Options object with property access, `getAll()`, `set()`, and `overrides` merging (constructor and per-call).
- **`CnpjValidationType`** — Backed enum (`Alphanumeric`, `Numeric`) for the `type` option.
- **`caseSensitive` option** — When `false`, lowercase letters are uppercased before alphanumeric validation.
- **`getOptions()`** — Returns the shared default `CnpjValidatorOptions` instance used by `CnpjValidator`.
- **Array input** — `isValid()` and `cnpj_val()` concatenate a `list<string>` (e.g. grouped or formatted segments).
- **Verifier-digit rule** — Rejects CNPJs whose last two characters are not digits (`0`–`9`).
- **Exception hierarchy** — `CnpjValidatorInputTypeError`, `CnpjValidatorOptionsTypeError`, `CnpjValidatorOptionTypeInvalidException`, plus `getName()` on base error classes.

### Improvements

- **`cnpj_val()` reuse** — The instance of `CnpjValidator` is kept alive and reused across multiple calls.
- **Dependency alignment**: now depends on `lacus/cnpj-dv` and `lacus/utils`.
- **Autoload alignment**: package autoload namespace updated to the BR Utils standard.
- **Docs refresh**: English and pt-BR READMEs updated with v2 API and usage guidance.

## 1.0.0

### Stable v1 API

First stable release of **`lacus/cnpj-val`** focused on CNPJ checksum validation.

- **API namespace**: `Lacus\CnpjVal\`.
- **Main resources**:
  - `cnpj_val()`
  - `CnpjValidator`
  - `CNPJ_LENGTH`
- **Validation scope**:
  - 14-digit CNPJ with first and second verifier digits checked against the official algorithm.
  - Masked or plain input supported by stripping non-digits before validation.
- **Verifier digits**:
  - Calculations reuse `CnpjGeneratorVerifierDigit` from **`lacus/cnpj-gen`** `^1.0`.
- **Return model**:
  - Ordinary validation failures return `false` (no exceptions for invalid CNPJ).
- **Runtime**: PHP `>=8.1`.
- **Testing**: PHPUnit-based suite.
