# lacus/cpf-dv

## 1.1.0

### Minor Changes

- d746c9dbf2f9ece8622d009d2e07d4923c2d875a: (refactoring) Dropped duplicate constant declarations.
- 2ee783e2b670819ff751fd1ae76d24026b1486c6: (refactoring) Moved some input parsing logic to dedicate private method inside class `CpfCheckDigits`.

## 1.0.0

### 🚀 Stable Version Released!

Utility class to calculate check digits on CPF (Brazilian Individual's Taxpayer ID). Main features:

- **Flexible input**: Accepts string or array of strings (formatted or raw).
- **Format agnostic**: Automatically strips non-numeric characters from input.
- **Lazy evaluation & caching**: Check digits are calculated only when accessed for the first time.
- **Minimal dependencies**: [`lacus/utils`](https://packagist.org/packages/lacus/utils) only.
- **Error handling**: Specific types for type, length, and invalid input scenarios (`TypeError` / `Exception` hierarchy).

For detailed usage and API reference, see the [README](./README.md).
