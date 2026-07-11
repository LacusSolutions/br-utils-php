---
id: unit-tests
title: Package unit tests
scope: packages/*/tests/
triggers:
  - writing or updating unit tests
  - adding test coverage for new behavior
  - fixing failing tests
  - reviewing test changes
  - running package tests
---

# unit-tests

Write and maintain unit tests under `packages/<pkg>/tests/` using the established br-utils-php conventions.

## Repository constraints

### Two coexisting test runners

The PHP monorepo is mid-migration. Use the runner that matches the package generation:

| Generation | Runner | Applies to |
|------------|--------|------------|
| **v2** | **Pest 3** | All CNPJ packages, `cpf-dv`, `utils` |
| **v1 (legacy)** | **PHPUnit 10** | `cpf-fmt`, `cpf-gen`, `cpf-val`, `cpf-utils`, `br-utils` (main) |

When migrating a CPF package to v2, switch from PHPUnit to Pest and follow the CNPJ test structure as the canonical reference.

### Location and naming

**v2 (Pest):**
- Tests live in `tests/specs/` at the package root.
- Files use the `.spec.php` suffix.
- Name the file after the class under test: `CnpjFormatter` → `tests/specs/CnpjFormatter.spec.php`.

**v1 (PHPUnit):**
- Tests live in `tests/` at the package root.
- Files use the `Test.php` suffix: `CpfFormatterClassTest.php`, `CpfFormatterFunctionTest.php`.
- Shared cases extracted into traits: `CpfFormatterTestCases`.

### Test config

**v2:** `.pest.config.xml` (PHPUnit schema, `pestphp/pest` runner).
**v1:** `phpunit.xml` (`phpunit/phpunit` runner).

### Imports

- Tests import directly from `../src/<File>` (or the package namespace if using autoload).
- Aggregator packages may import from their vendor sub-packages by Packagist name.
- Always include `declare(strict_types=1);` and a proper namespace (`Lacus\BrUtils\Tests\{Cpf|Cnpj}\`).

### Lint

Tests are linted with the package's sources: `composer run lint:ci`. Follow existing patterns in sibling test files.

### Changelog

Test-only changes are dev-only — do not add a changelog entry for test edits, coverage tooling, or test refactors with no user-facing change.

### Package-level overrides

Before applying this harness, check whether the target package defines `packages/<pkg>/AGENTS.md` or `packages/<pkg>/context/`. If either exists and contradicts this file, **follow the package-level instruction** (see [`context/README.md`](README.md#instruction-precedence)).

---

## Before writing tests

1. Check for `packages/<pkg>/AGENTS.md` and `packages/<pkg>/context/`; apply overrides when present.
2. Read the source file(s) under test and list public behaviors, options, and error paths.
3. Skim existing specs in `packages/<pkg>/tests/` — match structure, naming, and assertion style.
4. Identify the **package archetype** (see below); only create or extend the test files that archetype uses.

---

## Package archetypes

| Archetype | Examples | Typical test files |
|-----------|----------|-------------------|
| **Foundation** | `utils` | One `*.spec.php` per `src/` class |
| **Single-purpose (v2)** | `cnpj-fmt`, `cnpj-val`, `cnpj-gen`, `cpf-dv` | Main class spec, options spec, exceptions spec, helper spec |
| **Single-purpose (v1)** | `cpf-fmt`, `cpf-val`, `cpf-gen` | `*ClassTest.php`, `*FunctionTest.php`, shared trait |
| **Aggregator** | `cnpj-utils`, `cpf-utils`, `br-utils` | Aggregator class spec; re-uses leaf package test traits |

---

## Test file roles (v2 / Pest)

| File pattern | Tests |
|--------------|-------|
| `{ClassName}.spec.php` | Primary class — constructor, methods, edge cases, `onFail` |
| `{ClassName}Options.spec.php` | Options class — defaults, validation, invalid inputs |
| `Exceptions.spec.php` | Exception classes — inheritance, message, properties |
| `{function-name}.spec.php` | snake_case helper — delegates to class; input/output contract |

---

## Structure and style (Better Specs / Pest)

```php
<?php

declare(strict_types=1);

namespace Lacus\BrUtils\Tests\Cnpj;

use Lacus\BrUtils\Cnpj\CnpjFormatter;
use Lacus\BrUtils\Cnpj\CnpjFormatterOptions;
use Lacus\BrUtils\Cnpj\Exceptions\CnpjFormatterInputTypeError;

describe('CnpjFormatter', function () {
    describe('constructor', function () {
        describe('when called with no arguments', function () {
            it('creates an instance with default options', function () {
                $formatter = new CnpjFormatter();
                $defaults = new CnpjFormatterOptions();

                expect($formatter->getOptions()->getAll())->toBe($defaults->getAll());
            });
        });
    });

    describe('`format` method', function () {
        describe('when input is a valid CNPJ string', function () {
            it('returns the formatted CNPJ', function () {
                $result = (new CnpjFormatter())->format('03603568000195');

                expect($result)->toBe('03.603.568/0001-95');
            });
        });
    });
});
```

### Rules

- **`describe`** — component or context (class name, method name in backticks, or `when <condition>`).
- **`it`** — one behavior per example; present-tense phrasing (`returns …`, `throws …`, `calls onFail …`).
- **Nesting** — group by method, input type, or option.
- **Arrange–act–assert** — keep each `it` focused.
- **`beforeEach`** — per-example shared setup; `beforeAll` only for expensive shared setup.

### Error and exception testing (v2)

Two patterns:

1. **Thrown type errors** — assert the exception class and property:

```php
it('throws CnpjFormatterInputTypeError when input is not a string', function () {
    expect(fn () => (new CnpjFormatter())->format(123))
        ->toThrow(CnpjFormatterInputTypeError::class);
});
```

2. **`onFail` callback** — pass a spy closure in the options and assert it is called with the correct exception:

```php
it('calls onFail when length is invalid', function () {
    $called = false;
    $formatter = new CnpjFormatter(onFail: function ($value, $exception) use (&$called) {
        $called = true;
        return '';
    });

    $formatter->format('123');

    expect($called)->toBeTrue();
});
```

### PHPUnit style (v1 legacy)

For packages still on PHPUnit, follow existing test files in `packages/cpf-fmt/tests/` — method naming `testShould*`, `TestCase` extension, shared trait patterns. Do not introduce Pest matchers in PHPUnit files.

### Isolated-process tests

`cnpj-gen`, `cnpj-val`, and `cnpj-utils` run some Pest tests with `--group isolated-process-tests`. These are tests that require process isolation (e.g. global state side-effects). Mark them with `->group('isolated-process-tests')` and document why isolation is needed.

### External API tests

`cpf-gen` and `br-utils` call a remote validation API in some tests. These require `.env.test` with `API_URL` and `API_TOKEN`. Do not add new external API test dependencies without developer approval.

---

## Running tests

From the repo root:

| Goal | Command |
|------|---------|
| All packages | `composer run test:all` |
| Single CNPJ package | `composer run test:cnpj-fmt` (or `cnpj-gen`, `cnpj-val`, `cnpj-utils`) |
| Single CPF package | `composer run test:cpf-fmt` (or `cpf-gen`, `cpf-val`, `cpf-utils`) |
| `br-utils` | `composer run test:br-utils` |
| By role | `composer run test:formatters`, `test:generators`, `test:validators`, `test:utils` |
| By domain | `composer run test:cnpj`, `test:cpf` |

From a **package directory** (`packages/<pkg>/`):

| Goal | Command |
|------|---------|
| Run tests | `composer run test` |
| With HTML coverage | `composer run test:cov` |
| Watch mode | `composer run test:watch` (Pest packages only) |

---

## Checklist for agents

When implementing or reviewing test changes:

- [ ] New behavior has at least one `it` in the appropriate `*.spec.php` (or `*Test.php` for v1).
- [ ] Error paths covered: type errors (`throw`), length/business failures (`onFail`), invalid options.
- [ ] Options class tests cover defaults and all validation branches.
- [ ] Exception spec verifies inheritance chain and message.
- [ ] Style matches siblings: nested `describe`, Better Specs naming, correct runner (Pest or PHPUnit).
- [ ] `composer run test:<pkg>` passes.
- [ ] No new test frameworks or dependencies without developer approval.
