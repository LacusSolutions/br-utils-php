---
id: domain-parity
title: CPF ↔ CNPJ domain parity
scope: packages/cpf-*/src/, packages/cnpj-*/src/
triggers:
  - porting a CPF feature to CNPJ (or vice versa)
  - reviewing a PR that touches cpf-* and cnpj-* symmetrically
  - checking whether a CNPJ counterpart exists for a CPF change
  - deciding whether a divergence is intentional
---

# domain-parity

Use this harness when a change touches CPF packages and you need to determine whether the symmetric CNPJ package requires the same change, or when reviewing whether two domains stay in sync. All paths are relative to the **php/** subrepo root.

## Repository constraints

- **Always check the counterpart.** When you change a `cpf-*` package, verify that the `cnpj-*` counterpart exists and whether parity applies or divergence is intentional.
- Do **not** silently skip the counterpart — either apply the symmetric change or document why it doesn't apply.
- Intentional divergences are cataloged below; they are not bugs.

## Package pairing

| CPF package | CNPJ counterpart |
|-------------|------------------|
| `cpf-dv` | `cnpj-dv` |
| `cpf-fmt` | `cnpj-fmt` |
| `cpf-gen` | `cnpj-gen` |
| `cpf-val` | `cnpj-val` |
| `cpf-utils` | `cnpj-utils` |

`utils` and `br-utils` are shared/aggregate — no counterpart check needed.

## Migration asymmetry (current state — important)

CPF and CNPJ are **not** at the same generation. This is a known, temporary divergence being resolved by active migration work:

| Aspect | CPF (current) | CNPJ (current) |
|--------|---------------|----------------|
| **Generation** | v1 (mostly) | v2 |
| **Namespace** | `Lacus\CpfFmt\`, `Lacus\CpfGen\`, etc.; `cpf-dv` → `Lacus\BrUtils\Cpf\` | `Lacus\BrUtils\Cnpj\` |
| **PHP min** | `>=8.1` (v1); `^8.2` (`cpf-dv`) | `^8.2` |
| **Test runner** | PHPUnit 10 (except `cpf-dv` → Pest) | Pest 3 |
| **Exceptions** | Minimal | Full `TypeError` / `RuntimeException` hierarchies |
| **Options class** | None or minimal | Full `*Options` with `onFail`, validation, `DISALLOWED_KEY_CHARACTERS`, etc. |

When **porting a CPF package to v2**, follow the corresponding CNPJ package as the reference implementation and check the branch `feat/cpf-packages-v2`.

## Intentional divergences (not bugs)

| Area | CPF | CNPJ |
|------|-----|------|
| **Input character set** | Digits only (0–9) | Alphanumeric (digits + uppercase A–Z) |
| **Identifier length** | 11 characters | 14 characters |
| **Formatter mask pattern** | `###.###.###-##` | `##.###.###/####-##` |
| **Formatter `slashKey` option** | Not present | Present (separates prefix and branch suffix) |
| **Validator options** | None beyond `onFail` (v1); `onFail` only (cpf-dv) | `type` (`'numeric'` \| `'alphanumeric'`) and `caseSensitive` |
| **Generator options** | `format`, `prefix` | `format`, `prefix`, `type` (`'numeric'` \| `'alphabetic'` \| `'alphanumeric'`) |
| **DV algorithm** | Numeric modulo-11 on each digit | Weighted sum using char-code values; branch ID validation |
| **Default hidden range** | v1: positions 3–10; v2 target: 3–9 | Positions 5–13 |
| **Alphanumeric support** | Not applicable | Full support (introduced in 2026) |

Do not "fix" these toward CPF behavior without explicit product intent.

## Parity workflow

When changing a `cpf-*` package:

1. Identify the CNPJ counterpart from the table above.
2. Check if the same issue or feature applies to the CNPJ package (same archetype, same `src/` structure per [`agents/package-arch.md`](package-arch.md)).
3. If parity applies → open or note a corresponding change for `cnpj-*`.
4. If divergence is intentional (table above) → no action needed; note it in the CHANGELOG body if user-visible.
5. If unsure → ask the developer.

## Symmetry checklist

When a feature or fix is applied to one domain, verify the following in the counterpart:

- [ ] Same change in the main class if logic is symmetric
- [ ] Same change in the options class if a new option is added
- [ ] Same new exception class in `Exceptions/` if a new error case is introduced
- [ ] Same `@throws` annotation in PHPDoc per [`agents/phpdoc.md`](phpdoc.md)
- [ ] Same test cases per [`agents/unit-tests.md`](unit-tests.md)
- [ ] Both packages included in CHANGELOG entries if user-facing per [`agents/changelogs.md`](changelogs.md)
- [ ] Both READMEs updated per [`agents/readme-docs.md`](readme-docs.md) if options or defaults change

## Key files for comparison

| Concern | CPF | CNPJ |
|---------|-----|------|
| DV algorithm | `packages/cpf-dv/src/CpfCheckDigits.php` | `packages/cnpj-dv/src/CnpjCheckDigits.php` |
| Formatter | `packages/cpf-fmt/src/CpfFormatter.php` | `packages/cnpj-fmt/src/CnpjFormatter.php` |
| Formatter options | `packages/cpf-fmt/src/CpfFormatterOptions.php` | `packages/cnpj-fmt/src/CnpjFormatterOptions.php` |
| Validator options | — (v1, no options class) | `packages/cnpj-val/src/CnpjValidatorOptions.php` |
| Generator options | `packages/cpf-gen/src/CpfGeneratorOptions.php` (v1) | `packages/cnpj-gen/src/CnpjGeneratorOptions.php` |
| Aggregator class | `packages/cpf-utils/src/CpfUtils.php` | `packages/cnpj-utils/src/CnpjUtils.php` |

## Package-level overrides

Before applying this harness, check whether the target package defines `packages/<pkg>/AGENTS.md` or `packages/<pkg>/agents/`. If either exists and contradicts this file on the same topic, **follow the package-level instruction** (see [`agents/README.md`](README.md#instruction-precedence)).
