# lacus/cnpj-dv

## 1.1.0

### New Features

- a90dd0a89b18a19bbb8ad72200d65df01c465fdb Created **`getName()`** to all package-specific errors and exceptions. Now `CnpjCheckDigitsException`, `CnpjCheckDigitsTypeError` and all their subclasses can return its class names without namespaces. This change is an API alignment across all **BR Utils** initiatives.

## 1.0.0

### 🚀 Stable Version Released!

Utility class to calculate check digits on CNPJ (Cadastro Nacional da Pessoa Jurídica). Main features:

- **Flexible input**: Accepts string or array of strings (formatted or raw).
- **Format agnostic**: Automatically strips non-numeric characters from input.
- **Lazy evaluation & caching**: Check digits are calculated only when accessed for the first time.
- **Minimal dependencies**: [`lacus/utils`](https://packagist.org/packages/lacus/utils) only.
- **Error handling**: Specific types for type, length, and invalid input scenarios (`TypeError` / `Exception` hierarchy).

For detailed usage and API reference, see the [README](./README.md).
