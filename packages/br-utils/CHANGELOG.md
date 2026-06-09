# lacus/br-utils

## 2.0.0

### 🎉 v2 at a glance 🎊

- 🆕 **Alphanumeric CNPJ** — Full support via upgraded `lacus/cnpj-utils` ^2.0 (validator options, `*Options` objects, updated method signatures).
- 📁 **Namespace realignment** — `BrUtils` lives in `Lacus\`; CPF and CNPJ utilities move to `Lacus\BrUtils\Cpf\` and `Lacus\BrUtils\Cnpj\`.
- ⚙️ **`BrUtils` constructor** — Accepts pre-built `CpfUtils` / `CnpjUtils` instances or config arrays with `*Options` objects.

### BREAKING CHANGES

- **Namespaces**
  - `BrUtils` is now `Lacus\BrUtils` (was `Lacus\BrUtils\BrUtils`);
  - `CpfUtils` → `Lacus\BrUtils\Cpf\CpfUtils`;
  - `CnpjUtils` → `Lacus\BrUtils\Cnpj\CnpjUtils`.
- **PHP 8.2** — Minimum PHP raised from `>=8.1` to `^8.2`.
- **`BrUtils` constructor** — `$cpf` is the first argument (was second); each parameter accepts a utils instance or a named config array spread into the utils constructor.
- **Dependencies** — Direct requires on individual `lacus/cpf-*` and `lacus/cnpj-*` packages removed; runtime depends on `lacus/cpf-utils` ^1.1 and `lacus/cnpj-utils` ^2.0.
- **CNPJ API** — Inherits v2 changes from `lacus/cnpj-utils` (alphanumeric CNPJ, default alphanumeric validation, updated `format()` / `generate()` / `isValid()` signatures); `cnpj_fmt()`, `cnpj_gen()`, and `cnpj_val()` are no longer autoloaded by this package (available transitively from bundled CNPJ component packages).

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
- **Dependencies**: `lacus/cpf-utils` ^1.1, `lacus/cnpj-utils` ^1.1, plus the underlying `lacus/cpf-*` and `lacus/cnpj-*` component packages.
- **Runtime**: PHP `>=8.1`.
- **Testing**: PHPUnit-based suite.
