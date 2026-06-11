# lacus/br-utils

## 2.0.1

### Patch Changes

- **Dependency constraints** — Changed internal dependency version constraints from caret to tilde notation for more predictable feature propagation.

## 2.0.0

### 🎉 v2 at a glance 🎊

- 🆕 **Alphanumeric CNPJ** — Full support for the new [14-character alphanumeric CNPJ](https://www.gov.br/receitafederal/pt-br/assuntos/noticias/2023/julho/cnpj-alfa-numerico) via upgraded `lacus/cnpj-utils` ^2.0 and bundled `lacus/cnpj-*` v2 components.
- ⚙️ **Validator options** — New `CnpjValidatorOptions`; configure `type` and `caseSensitive` on `BrUtils`, `CnpjUtils`, or per `isValid()` call.
- 🛡️ **Structured CNPJ errors** — Typed exceptions from bundled CNPJ packages propagate through `$brUtils->cnpj` for clearer error handling.
- 📁 **Namespace realignment** — `BrUtils` lives in `Lacus\`; CPF and CNPJ utilities move to `Lacus\BrUtils\Cpf\` and `Lacus\BrUtils\Cnpj\`.
- ⚙️ **`BrUtils` constructor** — Accepts pre-built `CpfUtils` / `CnpjUtils` instances or config arrays with `*Options` objects (including CNPJ `validator` settings).
- 📦 **Unified import surface** — CPF component classes and helpers stay under `Lacus\BrUtils\Cpf\`; CNPJ symbols are provided by bundled packages under `Lacus\BrUtils\Cnpj\`.

### BREAKING CHANGES

- **Namespaces**:
  - `BrUtils` is now `Lacus\BrUtils` (was `Lacus\BrUtils\BrUtils`);
  - `CpfUtils` → `Lacus\BrUtils\Cpf\CpfUtils`;
  - `CnpjUtils` → `Lacus\BrUtils\Cnpj\CnpjUtils`.
- **PHP 8.2** — Minimum PHP raised from `>=8.1` to `^8.2`.
- **`BrUtils` constructor** — `$cpf` is the first argument (was second); each parameter accepts a utils instance or a named config array spread into the utils constructor.
- **Autoload root** — PSR-4 prefix changed from `Lacus\BrUtils\` to `Lacus\` (only `BrUtils.php` at the root; domain code lives under `src/BrUtils/`).
- **CNPJ local wrappers removed** — `CnpjFormatter`, `CnpjGenerator`, and `CnpjValidator` are no longer defined in this package; import them from `lacus/cnpj-fmt`, `lacus/cnpj-gen`, and `lacus/cnpj-val` (still under `Lacus\BrUtils\Cnpj\`).
- **CNPJ helpers** — `cnpj_fmt()`, `cnpj_gen()`, and `cnpj_val()` are no longer autoloaded by this package; they are provided by the bundled CNPJ component packages (same namespace, updated v2 signatures).
- **CNPJ API** — Inherits v2 changes from bundled `lacus/cnpj-*` packages:
  - **Alphanumeric CNPJ** — letters are kept during sanitization; default validation is **alphanumeric** (pass `type: 'numeric'` to restore legacy numeric-only behavior);
  - **Signatures** — `format()` / `isValid()` accept `string|list<string>`; `format()` adds `encode` and `CnpjFormatterOptions`; `generate()` adds `CnpjGeneratorOptions` and `CnpjType`; `isValid()` adds `CnpjValidatorOptions`;
  - **`onFail` default** — CNPJ formatter `onFail` now returns `''` on invalid length (v1 returned the original input);
  - **Options model** — `*Options` use property access and `overrides` merging; `merge()` and getter/setter style removed;
  - **Check digits** — generation and validation delegate to `lacus/cnpj-dv` (`CnpjCheckDigits`) instead of inline/`CnpjGeneratorVerifierDigit`;
  - **Input errors** — invalid input types throw typed `*InputTypeError` exceptions instead of native `TypeError` or unspecified behavior.
- **Dependencies** — Runtime requires `lacus/cpf-utils` ^1.1 and `lacus/cnpj-utils` ^2.0 (were ^1.0 each).

### New Features

- **`BrUtils` dependency injection** — Pass pre-built `CpfUtils` / `CnpjUtils` instances or spread config arrays with `formatter`, `generator`, and (for CNPJ) `validator` keys accepting `*Options` objects.
- **CPF options re-exports** — `CpfFormatterOptions` and `CpfGeneratorOptions` are now available under `Lacus\BrUtils\Cpf\`.
- **Alphanumeric CNPJ** — Format, generate, and validate the new 14-character alphanumeric CNPJ through `$brUtils->cnpj` (digits and `A`–`Z`, uppercased on input).
- **`encode` option** — `$brUtils->cnpj->format()` can URL-encode the formatted CNPJ (from `lacus/cnpj-fmt` ^2.0).
- **Array input** — `$brUtils->cnpj->format()` and `->isValid()` concatenate a `list<string>` (e.g. grouped or formatted segments).
- **`CnpjValidatorOptions`** — Configure `type` (`CnpjValidationType::Alphanumeric` or `::Numeric`) and `caseSensitive` on the `BrUtils` / `CnpjUtils` instance, per `isValid()` call, or via `getValidator()->getOptions()`.
- **`CnpjType` generation modes** — `$brUtils->cnpj->generate()` supports `Numeric`, `Alphabetic`, and `Alphanumeric` output via the `CnpjType` enum (from `lacus/cnpj-gen` ^2.1).
- **Alphanumeric prefix generation** — `$brUtils->cnpj->generate()` accepts alphanumeric prefixes (stripped, uppercased, capped at 12 base characters).
- **Structured CNPJ exceptions** — Typed `TypeError` / `Exception` hierarchies from `lacus/cnpj-fmt`, `lacus/cnpj-gen`, and `lacus/cnpj-val` propagate through `$brUtils->cnpj`.

### Improvements

- **New PT-BR documentation** — New [README in Brazilian Portuguese](./README.pt.md).
- **Documentation** — README and README.pt.md updated for the v2 API (namespaces, constructor, CNPJ validator options, bundled-package imports).
- **CNPJ dependency alignment** — Transitive runtime updated to `lacus/cnpj-fmt` ^2.0, `lacus/cnpj-gen` ^2.1, `lacus/cnpj-val` ^2.0, and `lacus/cnpj-dv` ^1.1.
- **CNPJ options objects** — Formatter, generator, and validator settings use `*Options` instances with per-call overrides via named parameters or an options object.
- **`CpfUtils` constructor** — Accepts `CpfFormatterOptions` / `CpfGeneratorOptions` instances or option arrays when constructing directly or via `BrUtils`.
- **CNPJ generator reliability** — Internal retry when check-digit computation rejects a generated candidate (from `lacus/cnpj-gen` ^2.0).
- **CNPJ validator reuse** — `cnpj_val()` keeps the `CnpjValidator` instance alive across calls (from `lacus/cnpj-val` ^2.0).
- **CNPJ check-digit performance** — Faster `CnpjCheckDigits` engine used by generation and validation (from `lacus/cnpj-dv` ^1.1).

## 1.0.0

### Stable v1 API

First stable release of **`lacus/br-utils`** — CPF and CNPJ formatting, generation, and validation in a single Composer package.

- **API namespace**: `Lacus\BrUtils\`.
- **Main resources**:
  - `BrUtils` (unified entry point with `$brUtils->cpf` and `$brUtils->cnpj`)
  - `CpfUtils`, `CnpjUtils`
  - `cpf_fmt()`, `cpf_gen()`, `cpf_val()` under `Lacus\BrUtils\Cpf\`
  - `cnpj_fmt()`, `cnpj_gen()`, `cnpj_val()` under `Lacus\BrUtils\Cnpj\`
  - `CpfFormatter`, `CpfGenerator`, `CpfValidator`, `CnpjFormatter`, `CnpjGenerator`, `CnpjValidator` (thin subclasses of bundled packages)
- **Unified façade**: `BrUtils`, `CpfUtils`, and `CnpjUtils` expose `format()`, `generate()`, and `isValid()`; constructor accepts `formatter` and `generator` option arrays per document type.
- **Component access**: `getFormatter()`, `getGenerator()`, and `getValidator()` expose the underlying instances for direct use.
- **Scope**: numeric CPF only (11 digits); numeric CNPJ only (14 digits).
- **Dependencies**: `lacus/cpf-utils` ^1.0 and `lacus/cnpj-utils` ^1.0 (transitive `lacus/cpf-*` and `lacus/cnpj-*` component packages).
- **Runtime**: PHP `>=8.1`.
- **Testing**: PHPUnit-based suite.
