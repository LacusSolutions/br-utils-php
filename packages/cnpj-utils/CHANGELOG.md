# lacus/cnpj-utils

## 2.0.1

### Patch Changes

- **Dependency constraints** — Changed internal dependency version constraints from caret to tilde notation for more predictable feature propagation.

## 2.0.0

### 🎉 v2 at a glance 🎊

- 🆕 **Alphanumeric CNPJ** — Full support for the new [14-character alphanumeric CNPJ](https://www.gov.br/receitafederal/pt-br/assuntos/noticias/2023/julho/cnpj-alfa-numerico) through upgraded `lacus/cnpj-fmt`, `lacus/cnpj-gen`, and `lacus/cnpj-val` v2 dependencies.
- ⚙️ **Validator options** — New `CnpjValidatorOptions` (v1 had none); configure `type` and `caseSensitive` via the `CnpjUtils` or `CnpjValidator` constructors, or per `isValid()` call.
- 🛡️ **Structured errors** — Typed exceptions from bundled components propagate through the façade for clearer error handling.
- 🔧 **Options objects** — Formatter, generator, and validator settings use `*Options` instances with per-call overrides via named parameters or an options object.

### BREAKING CHANGES

- **Namespace** — Public API moved from `Lacus\CnpjUtils\` to `Lacus\BrUtils\Cnpj\`; update `use` imports for `CnpjUtils` and for instances returned by `getFormatter()`, `getGenerator()`, and `getValidator()`.
- **PHP 8.2** — Minimum PHP raised from `>=8.1` to `^8.2`.
- **Removed helpers** — Autoloaded `cnpj_fmt()`, `cnpj_gen()`, and `cnpj_val()` were removed from this package; call `CnpjUtils` methods or depend on `lacus/cnpj-fmt`, `lacus/cnpj-gen`, and `lacus/cnpj-val` directly.
- **Removed thin subclasses** — Local `CnpjFormatter`, `CnpjGenerator`, and `CnpjValidator` wrappers were removed; import the classes from the respective bundled packages under `Lacus\BrUtils\Cnpj\`.
- **Constructor** — Formatter and generator options accept `CnpjFormatterOptions` / `CnpjGeneratorOptions` instances or named arrays (not positional spread arrays); a new `$validator` argument configures default validation behavior.
- **Bundled component API** — Inherits v2 changes from `lacus/cnpj-fmt`, `lacus/cnpj-gen`, and `lacus/cnpj-val`:
  - **Alphanumeric CNPJ** — letters are kept during sanitization; default validation is **alphanumeric** (pass `type: 'numeric'` to restore legacy numeric-only behavior);
  - **Signatures** — `format()` / `isValid()` accept `string|list<string>`; `format()` adds `encode` and `CnpjFormatterOptions`; `generate()` adds `CnpjGeneratorOptions` and `CnpjType`; `isValid()` adds `CnpjValidatorOptions`;
  - **`onFail` default** — formatter `onFail` now returns `''` on invalid length (v1 returned the original input);
  - **Options model** — `*Options` use property access and `overrides` merging; `merge()` and getter/setter style removed;
  - **Check digits** — generation and validation delegate to `lacus/cnpj-dv` (`CnpjCheckDigits`) instead of inline/`CnpjGeneratorVerifierDigit`;
  - **Input errors** — invalid input types throw typed `*InputTypeError` exceptions instead of native `TypeError` or unspecified behavior.

### New Features

- **Alphanumeric CNPJ** — `format()`, `generate()`, and `isValid()` support the new 14-character alphanumeric CNPJ (digits and `A`–`Z`, uppercased on input).
- **`encode` option** — `format()` can URL-encode the formatted CNPJ (delegated to the formatter's `encode` option).
- **Array input** — `format()` and `isValid()` concatenate a `list<string>` (e.g. grouped or formatted segments).
- **`CnpjValidatorOptions`** — Validation is now configurable (v1 validator had no options); set `type` (`CnpjValidationType::Alphanumeric` or `::Numeric`) and `caseSensitive` on the `CnpjUtils` instance, per `isValid()` call, or via `getValidator()->getOptions()`.
- **`CnpjType` generation modes** — `generate()` supports `Numeric`, `Alphabetic`, and `Alphanumeric` output via the `CnpjType` enum (from `lacus/cnpj-gen` ^2.1).
- **Alphanumeric prefix generation** — `generate()` accepts alphanumeric prefixes (stripped, uppercased, capped at 12 base characters).
- **Structured exceptions** — Typed `TypeError` / `Exception` hierarchies from bundled CNPJ packages propagate through `CnpjUtils`.

### Improvements

- **New PT-BR documentation** — New [README in Brazilian Portuguese](./README.pt.md).
- **Documentation** — README and README.pt.md updated for the v2 API (namespaces, constructor, validator options, bundled-package imports).
- **Dependency alignment** — Runtime dependencies updated to:
  - `lacus/cnpj-fmt` ^2.0
  - `lacus/cnpj-gen` ^2.1
  - `lacus/cnpj-val` ^2.0
- **Generator reliability** — Internal retry when check-digit computation rejects a generated candidate (from `lacus/cnpj-gen` ^2.0).
- **Validator reuse** — `cnpj_val()` keeps the `CnpjValidator` instance alive across calls (from `lacus/cnpj-val` ^2.0).
- **Check-digit performance** — Faster `CnpjCheckDigits` engine used by generation and validation (from `lacus/cnpj-dv` ^1.1).

## 1.0.0

### Stable v1 API

First stable release of **`lacus/cnpj-utils`** — unified CNPJ formatting, generation, and validation in one package.

- **API namespace**: `Lacus\CnpjUtils\`.
- **Main resources**:
  - `cnpj_fmt()`, `cnpj_gen()`, `cnpj_val()` (autoloaded helpers)
  - `CnpjUtils`
  - `CnpjFormatter`, `CnpjGenerator`, `CnpjValidator` (thin subclasses of `lacus/cnpj-fmt`, `lacus/cnpj-gen`, and `lacus/cnpj-val`)
- **Unified façade**: `CnpjUtils::format()`, `::generate()`, and `::isValid()` delegate to bundled components; constructor accepts `formatter` and `generator` option arrays.
- **Component access**: `getFormatter()`, `getGenerator()`, and `getValidator()` expose the underlying instances for direct use.
- **Scope**: numeric CNPJ only (inherited from v1 formatter, generator, and validator).
- **Dependencies**: `lacus/cnpj-fmt` `^1.0`, `lacus/cnpj-gen` `^1.0`, `lacus/cnpj-val` `^1.0`.
- **Runtime**: PHP `>=8.1`.
- **Testing**: PHPUnit-based suite.
