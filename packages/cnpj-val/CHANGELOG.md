# lacus/cnpj-val

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
- **Runtime**:
  - PHP `>=8.1`.
- **Testing**:
  - PHPUnit-based suite.
