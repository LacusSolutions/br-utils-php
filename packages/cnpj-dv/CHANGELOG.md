# lacus/cnpj-dv

## 1.1.0

### New Features

- Created **`getName()`** to all package-specific errors and exceptions. Now `CnpjCheckDigitsException`, `CnpjCheckDigitsTypeError` and all their subclasses can return their class names without namespaces. This change is an API alignment across all **BR Utils** initiatives.

### Improvements

- **Performance refactor of `CnpjCheckDigits`**:
  - **Internal representation**: Switched from a `list<string>` of one-char zvals to a 12-character `string` (`$cnpjBase`).
  - **Validator optimizations**: Now uses C-level primitives (`strlen`, `str_starts_with`, `substr` equality, `str_repeat`) instead of `array_slice` + `implode` + `array_unique` + `preg_match('/^\d$/', ...)`.
  - **Parser improvements**: `parseStringInput()` runs a case-sensitive PCRE on an already-uppercased buffer; `parseArrayInput()` folds type-checking and concatenation into a single pass.
  - **Backward compatibility**: `calculate()` keeps its protected `array $cnpjSequence` signature and still uses the factor-rolling logic (`factor === 9 ? 2 : factor + 1`); `getBoth()` and `getCnpj()` cache with explicit `if ($cache === null)` checks.
  - **Performance gains** (PHP 8.2.30, 100k iterations, OPcache off): +40% on full pipeline, +52% on cache-hit reads, +21% on array-input parsing, +24% on invalid-input throw path.
  - **API stability**: No public API change.

## 1.0.0

### 🚀 Stable Version Released!

Utility class to calculate check digits on CNPJ (Cadastro Nacional da Pessoa Jurídica). Main features:

- **Flexible input**: Accepts string or array of strings (formatted or raw).
- **Format agnostic**: Automatically strips non-numeric characters from input.
- **Lazy evaluation & caching**: Check digits are calculated only when accessed for the first time.
- **Minimal dependencies**: [`lacus/utils`](https://packagist.org/packages/lacus/utils) only.
- **Error handling**: Specific types for type, length, and invalid input scenarios (`TypeError` / `Exception` hierarchy).

For detailed usage and API reference, see the [README](./README.md).
