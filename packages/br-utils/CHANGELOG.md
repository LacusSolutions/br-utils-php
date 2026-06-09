# lacus/br-utils

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
- **Dependencies**: `lacus/cpf-utils` `^1.1`, `lacus/cnpj-utils` `^1.1`, plus the underlying `lacus/cpf-*` and `lacus/cnpj-*` component packages.
- **Runtime**: PHP `>=8.1`.
- **Testing**: PHPUnit-based suite.
