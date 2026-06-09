# lacus/cpf-fmt

## 1.0.0

### Stable v1 API

The first major release of **`lacus/cpf-fmt`** under namespace **`Lacus\CpfFmt`**, focused on **numeric** CPF formatting (11 digits).

- **`CpfFormatter`**: Formats a CPF string into the usual `XXX.XXX.XXX-XX` pattern (with configurable separators).
- **`CpfFormatterOptions`**: Options for `escape`, `hidden`, `hiddenKey`, `hiddenStart`, `hiddenEnd`, `dotKey`, `dashKey`, and `onFail`; **`merge()`** for per-call overrides from `format()`.
- **`cpf_fmt()`**: Helper that instantiates `CpfFormatter` and calls `format()` with the same option parameters.
- **`CPF_LENGTH`**: Global constant `11` in `cpf-fmt.php` (aligned with `Lacus\CpfFmt` autoload).
- **Invalid length**: Invoked `onFail` with **`InvalidArgumentException`** as the second argument; default callback returned the **original input string**.
- **Numeric-only input**: Stripped non-digits; required exactly **11 digits** after stripping.
- **PHP**: PHP **≥ 8.1**; no external package dependencies.
- **Tests**: PHPUnit with shared test cases trait.
