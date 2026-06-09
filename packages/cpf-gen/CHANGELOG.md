# lacus/cpf-gen

## 1.0.0

### Stable v1 API

First stable release of **`lacus/cpf-gen`** focused on numeric CPF generation.

- **API namespace**: `Lacus\CpfGen\`.
- **Main resources**:
  - `cpf_gen()`
  - `CpfGenerator`
  - `CpfGeneratorOptions`
  - `CpfGeneratorVerifierDigit`
- **Generation scope**:
  - Numeric-only generation.
  - Optional output formatting (`XXX.XXX.XXX-XX`).
  - Prefix-based generation for numeric seeds (1–9 digits).
- **Validation model**:
  - Option errors handled by generic exceptions.
- **Runtime**: PHP `>=8.1`.
- **Testing**: PHPUnit-based suite.
