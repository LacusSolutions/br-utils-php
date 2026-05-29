# lacus/cnpj-utils

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
- **`format()` signature** — Accepts `string|list<string>` and an optional `CnpjFormatterOptions` instance as the second argument; adds `encode` and reorders options to match `lacus/cnpj-fmt` v2 (including alphanumeric normalization and updated `onFail` defaults).
- **`generate()` signature** — Accepts an optional `CnpjGeneratorOptions` instance and a `type` argument for numeric, alphabetic, or alphanumeric generation modes.
- **`isValid()` signature** — Accepts `string|list<string>` plus optional `CnpjValidatorOptions` (or named `type` / `caseSensitive`); default validation is **alphanumeric** — pass `type: 'numeric'` to restore legacy numeric-only behavior.

### New Features

- **`encode` option** — `format()` can URL-encode the formatted CNPJ (delegated to the formatter's `encode` option).
- **Array input** — `format()` and `isValid()` concatenate a `list<string>` (e.g. grouped or formatted segments).
- **`CnpjValidatorOptions`** — Validation is now configurable (v1 validator had no options); set `type` (`CnpjValidationType::Alphanumeric` or `::Numeric`) and `caseSensitive` on the `CnpjUtils` instance, per `isValid()` call, or via `getValidator()->getOptions()`.

### Improvements

- **New PT-BR documentation**: New [README in Brazilian Portuguese](./README.pt.md).
- **Dependency alignment** — Runtime dependencies updated to:
  - `lacus/cnpj-fmt`: `^2.0`
  - `lacus/cnpj-gen`: `^2.1`
  - `lacus/cnpj-val`: `^2.0`.

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
