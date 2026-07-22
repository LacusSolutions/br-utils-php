---
id: readme-docs
title: Package README authoring
scope: packages/*/README.md, packages/*/README.pt.md
triggers:
  - creating or updating a package README
  - rewriting or reviewing README.md or README.pt.md
  - editing the root PHP repository README
  - npm or package documentation
  - translating README to Portuguese (README.pt.md)
---

# readme-php

Author and maintain `README.md` files under `packages/<pkg>/` following the established br-utils-php conventions.

## Repository constraints

### Root README

The root `README.md` at `php/README.md` documents `lacus/br-utils`. Edit it directly — unlike the JS project there is no lint-staged sync between the root and a package subdirectory.

### Portuguese parity

English `README.md` is the source of truth for structure and content. Any change to a package's `README.md` must be reflected in that package's `README.pt.md` (faithful translation), **except** the English-only elements listed below. V2 packages have both; v1 CPF packages currently have English only — add `README.pt.md` during v2 migration.

**Omit from `README.pt.md` (English `README.md` only):**

- The **badges row** (Packagist / CI / license shields). Keep the cover image (or H1) and optional callouts.
- The **`## PHP Support`** section (and its Portuguese heading `## Suporte a PHP`). Do not translate or recreate the PHP version badge table in Portuguese docs.

### Changelog links

Package `CHANGELOG.md` files are edited manually. READMEs link to the changelog in the footer only. Do not recap changelog content in the README.

### Package-level overrides

Before applying this harness, check whether the target package defines `packages/<pkg>/AGENTS.md` or `packages/<pkg>/context/`. If either exists and contradicts this file on the same topic, **follow the package-level instruction** (see [`context/README.md`](README.md#instruction-precedence)).

## Before writing

1. Check for `packages/<pkg>/AGENTS.md` and `packages/<pkg>/context/`; apply package-level overrides when present.
2. Read `packages/<pkg>/composer.json` for the Packagist name and description.
3. Read `src/` to list public classes, functions, constants, and namespaces accurately.
4. Skim tests in `tests/` for realistic examples.
5. Identify the **package archetype** (below) — section depth depends on it.
6. Check sibling packages for the same domain (e.g. `cnpj-fmt` when documenting `cpf-utils`).

## Package archetypes

| Archetype | Examples | Distinct traits |
|-----------|----------|-----------------|
| **Foundation** | `utils` | H1 title; per-class API docs; no formatter/generator/validator usage sections |
| **Single-purpose** | `cnpj-fmt`, `cnpj-val`, `cnpj-gen`, `cnpj-dv`, `cpf-*` | Cover image; Usage + API; options tables; class + helper function pattern |
| **Aggregator** | `cnpj-utils`, `cpf-utils` | Cover image; wraps sub-packages; Usage inlines sub-package options; links to sub-package READMEs for full error details |
| **Top aggregator** | `br-utils` | Cover image; wraps both domains; links to domain aggregator READMEs |

Special sections (only when relevant):

- **`## Calculation algorithm`** — DV packages (`cpf-dv`, `cnpj-dv`).
- **Announcement blockquote** — major features (e.g. alphanumeric CNPJ).

---

## Section order (mandatory)

Use these headings in this order. Omit optional sections; never reorder core sections.

```
[Cover image OR H1 title]
[Badges row]            ← English README.md only; omit from README.pt.md
[Optional blockquote callouts]
[One-paragraph description]
## PHP Support          ← English README.md only; omit from README.pt.md
## Features
## Installation
## Import
## Quick start
## Usage              ← omit for foundation packages
## API
[## Calculation algorithm]  ← DV packages only
## Contribution & Support
## License
## Changelog
---
Made with ❤️ by Lacus Solutions
```

---

## Header block

### Cover image (single-purpose & aggregator)

```markdown
![<pkg> for PHP](https://br-utils.vercel.app/img/cover_<pkg>.jpg)
```

Use the package folder slug (e.g. `cnpj-fmt`, `br-utils`). Foundation packages use an H1 instead.

### Badges (always six, in this order) — English only

> Include only in `README.md`. Never add these shields to `README.pt.md`.

Replace `<packagist-name>` with `lacus/<pkg>`:

```markdown
[![Packagist Version](https://img.shields.io/packagist/v/<packagist-name>)](https://packagist.org/packages/<packagist-name>)
[![Packagist Downloads](https://img.shields.io/packagist/dm/<packagist-name>)](https://packagist.org/packages/<packagist-name>)
[![PHP Version](https://img.shields.io/packagist/php-v/<packagist-name>)](https://www.php.net/)
[![Test Status](https://img.shields.io/github/actions/workflow/status/LacusSolutions/br-utils-php/ci.yml?label=ci/cd)](https://github.com/LacusSolutions/br-utils-php/actions)
[![Last Update Date](https://img.shields.io/github/last-commit/LacusSolutions/br-utils-php)](https://github.com/LacusSolutions/br-utils-php)
[![Project License](https://img.shields.io/github/license/LacusSolutions/br-utils-php)](https://github.com/LacusSolutions/br-utils-php/blob/main/LICENSE)
```

### Optional callouts (before description)

Portuguese doc link (all v2 packages):

```markdown
> 🌎 [Acessar documentação em português](https://github.com/LacusSolutions/br-utils-php/blob/main/packages/<pkg>/README.pt.md)
```

Feature announcement (when applicable):

```markdown
> 🚀 **Full support for the [new alphanumeric CNPJ format](https://github.com/user-attachments/files/23937961/calculodvcnpjalfanaumerico.pdf).**
```

### Description (one paragraph)

Pattern:

> A PHP **{noun}** to **{primary action}** **{subject}** ({expanded name}).

---

## PHP Support — English only

> Include only in `README.md`. Omit from `README.pt.md` (do not add `## Suporte a PHP`).

Use the PHP version badge table (not a plain text table). Copy from `packages/cnpj-fmt/README.md`:

```markdown
## PHP Support

| ![PHP 8.2](...) | ![PHP 8.3](...) | ![PHP 8.4](...) | ![PHP 8.5](...) |
| --- | --- | --- | --- |
| Passing ✔ | Passing ✔ | Passing ✔ | Passing ✔ |
```

Adjust PHP versions to match the package's `"require"` in `composer.json`.

---

## Features

Bulleted list:

```markdown
- ✅ **{Short label}**: {One sentence benefit or capability}
```

Standard features to include when applicable:

- **Flexible input** / **Format agnostic** — accepted types and normalization behavior
- **Alphanumeric CNPJ** — full support (CNPJ packages only)
- **Masking** — hidden range, configurable replacement
- **Error handling** — summarize throw vs `onFail` behavior
- **Minimal dependencies** — name internal `lacus/*` deps explicitly
- Aggregator-only: **Unified API**, **Reusable instance**

---

## Installation

Always show Composer:

```markdown
## Installation

```bash
# using Composer
$ composer require lacus/<pkg>
```
```

---

## Import

Show all public class and function imports the user will need:

```php
<?php

use Lacus\BrUtils\Cnpj\CnpjFormatter;
use Lacus\BrUtils\Cnpj\CnpjFormatterOptions;

use function Lacus\BrUtils\Cnpj\cnpj_fmt;
```

---

## Quick start

Two to four lines showing the most common usage with output in comments:

```php
$formatter = new CnpjFormatter();

$formatter->format('03603568000195');   // '03.603.568/0001-95'
$formatter->format('12ABC34500DE99');   // '12.ABC.345/00DE-99'
```

---

## Usage

Structure with `###` subsections, one per main entry point.

### Class (constructor + methods)

```markdown
### `{ClassName}`

- **`__construct`**: Description of accepted arguments …
- **`methodName()`**: Description …
```

### Options class

```markdown
### `{ClassName}Options`

Holds all settings. Construct with named parameters …

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `optionKey` | `?bool` | `false` | When `true`, … |
```

### snake_case helper function

```markdown
### `{function_name}()`

One paragraph describing what it does, that it wraps the class, and how to use named arguments.

```php
{function_name}($input, option: value);
```
```

### Input formats

Document accepted types clearly (string vs `list<string>`, normalization).

### Errors & exceptions

Document the two-tier error model:

- Type errors → `{Domain}{Role}TypeError` (extends PHP `TypeError`) — always thrown
- Length/validation failures → `onFail` callback; `{Domain}{Role}Exception` hierarchy (extends `RuntimeException`)

Include a `try/catch` example when ≥2 exception types exist.

---

## API (exports summary)

List all public symbols:

```markdown
## API

- **`ClassName`**: Brief role description
- **`ClassNameOptions`**: Options value object for `ClassName`
- **`function_name()`**: Convenience wrapper for one-shot usage
- **`CONSTANT_NAME`**: Value and meaning
- **Exceptions**: `TypeErrorBase`, `ExceptionBase`, concrete subclasses …
```

---

## Footer sections (copy verbatim, adjust paths)

### Contribution & Support

```markdown
## Contribution & Support

We welcome contributions! Please see our [Contributing Guidelines](https://github.com/LacusSolutions/br-utils-php/blob/main/CONTRIBUTING.md) for details. If you find this project helpful, please consider:

- ⭐ Starring the repository
- 🤝 Contributing to the codebase
- 💡 [Suggesting new features](https://github.com/LacusSolutions/br-utils-php/issues)
- 🐛 [Reporting bugs](https://github.com/LacusSolutions/br-utils-php/issues)
```

### License

```markdown
## License

This project is licensed under the MIT License — see the [LICENSE](https://github.com/LacusSolutions/br-utils-php/blob/main/LICENSE) file for details.
```

### Changelog

```markdown
## Changelog

See [CHANGELOG](https://github.com/LacusSolutions/br-utils-php/blob/main/packages/<pkg>/CHANGELOG.md) for a list of changes and version history.
```

### Sign-off

```markdown
---

Made with ❤️ by [Lacus Solutions](https://github.com/LacusSolutions)
```

---

## Writing style

| Rule | Detail |
|------|--------|
| Language | English in `README.md`; mirror structure in `README.pt.md` except badges and `## PHP Support` / `## Suporte a PHP` (English only) |
| Voice | Direct, technical, third-person; present tense |
| Formatting | Backticks for identifiers, options, types; **`bold`** for class/method names in prose |
| Code comments | `// 'formatted result'` for output; use realistic domain values |
| Links | GitHub tree paths for repo docs; Packagist.org for package links |
| Accuracy | Document actual exports and defaults from source — never invent APIs |

---

## Workflow checklist

When creating or updating a README:

```
- [ ] Archetype identified (foundation / single-purpose / aggregator)
- [ ] Packagist name matches composer.json "name"
- [ ] All public exports documented under ## API
- [ ] PHP Support table versions match composer.json "require" php version
- [ ] Installation shows composer require
- [ ] Import section shows all needed use statements
- [ ] Options table defaults match source code constants
- [ ] Error behavior (throw vs onFail) is explicit
- [ ] Sub-package README links present (aggregators)
- [ ] CHANGELOG footer links to packages/<pkg>/CHANGELOG.md
- [ ] README.pt.md link present (v2 packages)
- [ ] README.pt.md updated when README.md changes
- [ ] README.pt.md omits badges row and ## Suporte a PHP
- [ ] Footer boilerplate unchanged
```

---

## Reference packages

| Archetype | Canonical example |
|-----------|-------------------|
| Formatter | `packages/cnpj-fmt/README.md` |
| Validator | `packages/cnpj-val/README.md` |
| Generator | `packages/cnpj-gen/README.md` |
| Check digits | `packages/cnpj-dv/README.md` |
| Domain aggregator | `packages/cnpj-utils/README.md` |
| Top-level aggregator | `packages/br-utils/README.md` |
