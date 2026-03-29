# lacus/cpf-dv

## 1.2.0

### New Features

- 4866d4089da3b8b79d7fd3b0b9fe56ad607e4dbd Created **`getName()`** to all package-specific errors and exceptions. Now `CpfCheckDigitsException`, `CpfCheckDigitsTypeError` and all their subclasses can return their class names without namespaces. This change is an API alignment across all **BR Utils** initiatives.

## 1.1.0

### Refactorings

- d746c9dbf2f9ece8622d009d2e07d4923c2d875a: Dropped duplicate constant declarations.
- 2ee783e2b670819ff751fd1ae76d24026b1486c6: Moved some input parsing logic to dedicate private method inside class `CpfCheckDigits`.

## 1.0.0

### 🚀 Stable Version Released!

Utility class to calculate check digits on CPF (Brazilian Individual's Taxpayer ID). Main features:

- **Flexible input**: Accepts string or array of strings (formatted or raw).
- **Format agnostic**: Automatically strips non-numeric characters from input.
- **Lazy evaluation & caching**: Check digits are calculated only when accessed for the first time.
- **Minimal dependencies**: [`lacus/utils`](https://packagist.org/packages/lacus/utils) only.
- **Error handling**: Specific types for type, length, and invalid input scenarios (`TypeError` / `Exception` hierarchy).

For detailed usage and API reference, see the [README](./README.md).
