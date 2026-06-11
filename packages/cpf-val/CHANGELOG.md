# lacus/cpf-val

## 1.0.1

### Patch Changes

- **Dependency constraints** — Changed internal dependency version constraints from caret to tilde notation for more predictable feature propagation.

## 1.0.0

### Stable v1 API

First stable release of **`lacus/cpf-val`** focused on CPF checksum validation.

- **API namespace**: `Lacus\CpfVal\`.
- **Main resources**:
  - `cpf_val()`
  - `CpfValidator`
  - `CPF_LENGTH`
- **Validation scope**:
  - 11-digit CPF with first and second verifier digits checked against the official algorithm.
  - Masked or plain input supported by stripping non-digits before validation.
- **Verifier digits**:
  - Calculations reuse `CpfGeneratorVerifierDigit` from **`lacus/cpf-gen`** `^1.0`.
- **Return model**:
  - Ordinary validation failures return `false` (no exceptions for invalid CPF).
- **Runtime**: PHP `>=8.1`.
- **Testing**: PHPUnit-based suite.
