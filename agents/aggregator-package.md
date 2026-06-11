---
id: aggregator-package
title: Aggregator package implementation
scope: packages/cpf-utils/src/, packages/cnpj-utils/src/, packages/br-utils/src/
triggers:
  - implementing or changing an aggregator package
  - adding a method or option to CpfUtils, CnpjUtils, or BrUtils
  - reviewing aggregator src/ structure
  - updating cpf-utils or cnpj-utils after a sub-package API change
---

# aggregator-package

Implement and maintain the three aggregator packages (`cpf-utils`, `cnpj-utils`, `br-utils`) that bundle sub-packages into a unified API. All paths are relative to the **php/** subrepo root.

## Repository constraints

- Aggregator packages are **thin wrappers** — they delegate to sub-package class instances and add no new business logic.
- Aggregator packages depend on sub-packages via their published Packagist versions (`lacus/cnpj-fmt: ^2.0`, etc.); sub-packages must not depend on aggregators. After changing aggregator `"require"` constraints, confirm the graph with `php scripts/deps-tree.php` — see [`agents/dependencies.md`](dependencies.md#inspecting-internal-dependencies).
- Tests in aggregator packages import from their `vendor/` sub-packages (installed per-package).

## Migration context

`cnpj-utils` is **v2** (complete). `cpf-utils` and `br-utils` are **v1** (being migrated). When updating these packages, follow the `cnpj-utils` pattern as the reference.

| Package | Generation | Namespace | Pattern |
|---------|------------|-----------|---------|
| `cnpj-utils` | v2 | `Lacus\BrUtils\Cnpj\` | Direct class imports from leaf packages |
| `cpf-utils` | v1 | `Lacus\CpfUtils\` | Thin subclass re-exports in `src/` |
| `br-utils` | v1 (main) | `Lacus\BrUtils\` | `__get` magic property + `getCpfUtils()` / `getCnpjUtils()` |

## `src/` layout

### v2 aggregator (`cnpj-utils`)

```
src/
  CnpjUtils.php       # Main façade class — delegates to CnpjFormatter, CnpjGenerator, CnpjValidator
  cnpj-utils.php      # snake_case re-export helpers (Composer "files")
```

`CnpjUtils` imports classes directly from the `Lacus\BrUtils\Cnpj\` namespace (shared namespace with leaf packages):

```php
namespace Lacus\BrUtils\Cnpj;

use Lacus\BrUtils\Cnpj\CnpjFormatter;
use Lacus\BrUtils\Cnpj\CnpjGenerator;
use Lacus\BrUtils\Cnpj\CnpjValidator;

class CnpjUtils
{
    private CnpjFormatter $formatter;
    private CnpjGenerator $generator;
    private CnpjValidator $validator;

    public function __construct(
        CnpjFormatterOptions|array $formatter = [],
        CnpjGeneratorOptions|array $generator = [],
        CnpjValidatorOptions|array $validator = [],
    ) {
        $formatterOptions = $formatter instanceof CnpjFormatterOptions
            ? $formatter
            : new CnpjFormatterOptions(...$formatter);
        $this->formatter = new CnpjFormatter($formatterOptions);
        // ...same for generator and validator
    }
}
```

### Top aggregator (`br-utils`)

```
src/
  BrUtils.php         # @property-read $cpf, $cnpj via __get
  CpfUtils.php        # Thin v1 wrapper (extends or wraps lacus/cpf-utils)
  CnpjUtils.php       # Thin wrapper (extends or wraps lacus/cnpj-utils)
  Cpf/
    CpfFormatter.php, CpfGenerator.php, CpfValidator.php
    cpf_utils.php      # snake_case helpers
  Cnpj/
    CnpjFormatter.php, CnpjGenerator.php, CnpjValidator.php
    cnpj_utils.php
```

`BrUtils` exposes `$brUtils->cpf` and `$brUtils->cnpj` via `__get` / `getCpfUtils()` / `getCnpjUtils()`.

## Constructor pattern (v2)

The constructor accepts sub-package `*Options` instances or plain associative arrays for each sub-package:

```php
public function __construct(
    CnpjFormatterOptions|array $formatter = [],
    CnpjGeneratorOptions|array $generator = [],
    CnpjValidatorOptions|array $validator = [],
) {
    $formatterOptions = $formatter instanceof CnpjFormatterOptions
        ? $formatter
        : new CnpjFormatterOptions(...$formatter);
    $this->formatter = new CnpjFormatter($formatterOptions);
    // ...
}
```

This lets callers pass pre-configured `*Options` instances or a flat options array, mirroring how individual classes accept their options.

## PHPDoc on constructor

The aggregator constructor must declare every `@throws` that any of its composed sub-package constructors can raise. Use the PHPStan array shape syntax for array parameters:

```php
/**
 * @param CnpjFormatterOptions|array{
 *     hidden?: bool|null,
 *     ...
 * } $formatter
 * @param CnpjGeneratorOptions|array{...} $generator
 * @param CnpjValidatorOptions|array{...} $validator
 *
 * @throws CnpjFormatterOptionsTypeError
 * @throws CnpjFormatterOptionsHiddenRangeInvalidException
 * ...all sub-package throws...
 */
```

## Delegating methods

Each method on the aggregator must directly delegate to the corresponding sub-package instance:

```php
public function format(string|array $cnpjInput, ...): string
{
    return $this->formatter->format($cnpjInput, ...);
}
```

Do not add business logic inside aggregator methods.

## Tests

Aggregator tests import from the installed `vendor/` sub-packages. Since each package has its own `vendor/`, tests resolve classes via the package's own `composer.json` autoload.

For v1 aggregators (`cpf-utils`, `br-utils`), tests share case logic via traits loaded from `vendor/` — e.g. `CpfUtilsTestCases`, `CnpjFormatterTestCases`. Reference: `packages/br-utils/tests/CpfUtilsTest.php`.

## Aggregator cascade after sub-package API change

When a leaf package gains a new option, method, or exception:

1. Update the aggregator's `__construct` signature to accept the new option.
2. Add the new `@throws` annotations for any new exceptions.
3. Add a delegation method if the sub-package gains a new method.
4. Update the aggregator's `README.md` options table.
5. Add a CHANGELOG entry per [`agents/changelogs.md`](changelogs.md).

## Checklist

- [ ] Aggregator class constructor accepts `*Options` instance or associative array per sub-package
- [ ] All delegation methods call sub-package methods directly (no added logic)
- [ ] PHPDoc lists every `@throws` from all composed constructors
- [ ] CHANGELOG entry added if public API changed
- [ ] README options table updated to reflect sub-package option changes
- [ ] Tests validate delegation behavior

## Package-level overrides

Before applying this harness, check whether the target package defines `packages/<pkg>/AGENTS.md` or `packages/<pkg>/agents/`. If either exists and contradicts this file on the same topic, **follow the package-level instruction** (see [`agents/README.md`](README.md#instruction-precedence)).

## Reference

| Concern | Canonical file |
|---------|---------------|
| v2 aggregator class | `packages/cnpj-utils/src/CnpjUtils.php` |
| Top aggregator with `__get` | `packages/br-utils/src/BrUtils.php` |
| v1 aggregator tests with trait | `packages/br-utils/tests/CpfUtilsTest.php` |
