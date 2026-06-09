# lacus/cpf-utils

## 1.1.0

### Improvements

- **README banner**: Updated package cover image on the README.

## 1.0.0

### Stable v1 API

First stable release of **`lacus/cpf-utils`** — unified CPF formatting, generation, and validation in one package.

- **API namespace**: `Lacus\CpfUtils\`.
- **Main resources**:
  - `cpf_fmt()`, `cpf_gen()`, `cpf_val()` (autoloaded helpers)
  - `CpfUtils`
  - `CpfFormatter`, `CpfGenerator`, `CpfValidator` (thin subclasses of `lacus/cpf-fmt`, `lacus/cpf-gen`, and `lacus/cpf-val`)
- **Unified façade**: `CpfUtils::format()`, `::generate()`, and `::isValid()` delegate to bundled components; constructor accepts `formatter` and `generator` option arrays.
- **Component access**: `getFormatter()`, `getGenerator()`, and `getValidator()` expose the underlying instances for direct use.
- **Scope**: numeric CPF only (11 digits).
- **Dependencies**: `lacus/cpf-fmt` `^1.0`, `lacus/cpf-gen` `^1.0`, `lacus/cpf-val` `^1.0`.
- **Runtime**: PHP `>=8.1`.
- **Testing**: PHPUnit-based suite.
