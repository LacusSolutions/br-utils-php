---
id: new-package
title: Scaffold a new workspace package
scope: packages/
triggers:
  - adding a new package to the monorepo
  - scaffolding a new cpf-*, cnpj-*, or br-* package
  - creating a new workspace member
---

# new-package

Step-by-step checklist for adding a new package to the br-utils-php monorepo. Adding a package is a rare, high-blast-radius operation. All paths are relative to the repo root.

## Prerequisites

- **Developer approval is required** before adding any new package or dependency. Stop and confirm before starting. See [`.context/dependencies.md`](dependencies.md).
- Identify the archetype (DV / Val / Fmt / Gen / Foundation / Aggregator) — this determines the `src/` layout. See [`.context/package-arch.md`](package-arch.md).
- Identify the canonical sibling package to clone from (table below).
- All new packages must use the **v2** pattern (Pest, `Lacus\BrUtils\{Cpf|Cnpj}\` namespace, full Exceptions/). Do not scaffold new packages in the v1 style.

## Clone-from table

| New package type | Clone from |
|-----------------|-----------|
| `{domain}-fmt` | `cnpj-fmt` |
| `{domain}-val` | `cnpj-val` |
| `{domain}-gen` | `cnpj-gen` |
| `{domain}-dv` | `cnpj-dv` |
| `{domain}-utils` (aggregator) | `cnpj-utils` |
| `br-*` (multi-domain aggregator) | `br-utils` |
| Foundation utility | `utils` |

## Step 1 — Create the package directory

```bash
mkdir -p packages/<pkg>/src packages/<pkg>/tests/specs
```

Create these files (copy from sibling and adapt):

```
packages/<pkg>/
  src/
    {Domain}{Role}.php
    {Domain}{Role}Options.php     # Val/Fmt/Gen only
    {domain}-{role}.php           # snake_case helper (Val/Fmt/Gen only)
    Exceptions/
    Enums/                        # Gen/Val only
  tests/
    specs/
      {ClassName}.spec.php
      {ClassName}Options.spec.php # Val/Fmt/Gen only
      Exceptions.spec.php
  composer.json
  .pest.config.xml
```

## Step 2 — `composer.json`

Copy from the sibling package of the same archetype and update all package-specific fields:

```json
{
  "name": "lacus/<pkg>",
  "type": "library",
  "description": "<one-line description>",
  "license": "MIT",
  "authors": [
    {
      "name": "Julio L. Muller",
      "email": "juliolmuller@outlook.com",
      "homepage": "https://juliolmuller.github.io"
    }
  ],
  "keywords": ["<domain>", "<role>", "pt-br", "br"],
  "support": {
    "issues": "https://github.com/LacusSolutions/br-utils-php/issues",
    "source": "https://github.com/LacusSolutions/br-utils-php"
  },
  "scripts": {
    "lint": ["@lint:format", "@lint:check"],
    "lint:ci": ["@lint:format --dry-run", "@lint:check --dry-run"],
    "lint:format": "@php ../../run lint:format <pkg>",
    "lint:check": "@php ../../run lint:check <pkg>",
    "test": "pest --configuration=.pest.config.xml",
    "test:watch": "@test --watch",
    "test:cov": "@test --coverage-html coverage"
  },
  "config": {
    "sort-packages": true,
    "allow-plugins": {
      "pestphp/pest-plugin": true
    }
  },
  "require": {
    "php": "^8.2",
    "lacus/utils": "^1.0"
  },
  "require-dev": {
    "pestphp/pest": "^3.8"
  },
  "autoload": {
    "psr-4": {
      "Lacus\\BrUtils\\{Cpf|Cnpj}\\": "src/"
    },
    "files": [
      "src/{domain}-{role}.php"
    ]
  },
  "autoload-dev": {
    "psr-4": {
      "Lacus\\BrUtils\\Tests\\{Cpf|Cnpj}\\": "tests/specs/"
    }
  }
}
```

## Step 3 — `.pest.config.xml`

Copy from `packages/cnpj-fmt/.pest.config.xml` and update the testsuite name and source paths:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit
  xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
  xsi:noNamespaceSchemaLocation="vendor/phpunit/phpunit/phpunit.xsd"
  bootstrap="vendor/autoload.php"
  cacheDirectory="vendor/.pest.cache/"
  beStrictAboutOutputDuringTests="true"
  colors="true"
  failOnRisky="true"
  processIsolation="false"
  stopOnFailure="false"
>
  <testsuites>
    <testsuite name="{Domain} {Role} Test Suite">
      <directory suffix=".spec.php">tests/</directory>
    </testsuite>
  </testsuites>
  <source>
    <include>
      <directory suffix=".php">src/</directory>
    </include>
    <exclude>
      <directory>vendor/</directory>
      <directory>tests/</directory>
    </exclude>
  </source>
  <php>
    <env name="APP_ENV" value="testing"/>
  </php>
  <logging>
    <junit outputFile="vendor/.pest.cache/test-results.xml"/>
  </logging>
</phpunit>
```

## Step 4 — Implement `src/`

Follow [`.context/package-arch.md`](package-arch.md):

- Choose the layout for the archetype (DV / Val / Fmt / Gen / Foundation / Aggregator).
- Implement the main class with v2 namespace `Lacus\BrUtils\{Cpf|Cnpj}\`.
- Write `Exceptions/` with abstract base classes + concrete subclasses.
- Write the snake_case helper function file for Val/Fmt/Gen.
- Write `Enums/` for Gen and Val packages.
- Add PHPDoc per [`.context/phpdoc.md`](phpdoc.md).

## Step 5 — Add `tests/`

Follow [`.context/unit-tests.md`](unit-tests.md):

- `tests/specs/{ClassName}.spec.php` — behavior tests (happy path, edge cases, error cases)
- `tests/specs/{ClassName}Options.spec.php` — options class defaults and validation
- `tests/specs/Exceptions.spec.php` — error class inheritance and message

## Step 6 — Wire root scripts

Add the package's test script to root `composer.json` following the existing naming pattern:

```json
{
  "scripts": {
    "test:<pkg>": "cd packages/<pkg> && composer test"
  }
}
```

If adding to a domain group (`test:cnpj`, `test:cpf`), add it inside the existing array. If adding a new domain group, add the group script too.

## Step 7 — Install dependencies

```bash
composer install --working-dir=packages/<pkg>
```

This installs `vendor/` for the new package and generates `packages/<pkg>/composer.lock`.

## Step 8 — README and CHANGELOG

- Write `README.md` and `README.pt.md` per [`.context/readme-docs.md`](readme-docs.md).
- Create `CHANGELOG.md` with a `## 1.0.0` section for the initial release.

## Final checklist

- [ ] Directory structure matches the archetype
- [ ] `composer.json`: `lacus/<pkg>` name, `lint:ci` and `test` scripts (for CI discovery), PSR-4 autoload
- [ ] v2 namespace: `Lacus\BrUtils\{Cpf|Cnpj}\`
- [ ] `.pest.config.xml` present and correct
- [ ] `src/` implemented per `package-arch.md`
- [ ] `tests/specs/` implemented per `unit-tests.md`
- [ ] Root `composer.json` `test:<pkg>` script added
- [ ] `composer install --working-dir=packages/<pkg>` runs successfully
- [ ] Internal `"require"` edges respect dependency direction — verify with `php run deps <pkg>` (see [`.context/dependencies.md`](dependencies.md#inspecting-internal-dependencies))
- [ ] `composer run lint:ci` passes from the package directory
- [ ] `composer run test` passes from the package directory
- [ ] `README.md` and `README.pt.md` written
- [ ] `CHANGELOG.md` created with initial section

## Package-level overrides

Before applying this harness, check whether a package-level `AGENTS.md` or `agents/` directory was created for this package. If so, follow it over this file for any conflicting instructions (see [`.context/README.md`](README.md#instruction-precedence)).
