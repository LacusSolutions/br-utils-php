---
id: changelogs
title: Changelog entries
scope: packages/*/CHANGELOG.md
triggers:
  - creating or editing a CHANGELOG.md entry
  - deciding whether a change needs a changelog entry
  - choosing a SemVer bump level
  - reviewing changelog entries before release
---

# changelogs

Maintain `packages/<pkg>/CHANGELOG.md` files following the rules below. All `git tag` and SemVer lookups must run from inside the PHP repo.

## Repository constraints

- `packages/<pkg>/CHANGELOG.md` files are the **only** files agents create or edit in this workflow.
- Do **not** run `php run release`, `composer run release`, create GitHub Releases, or push git tags. Only the developer does that.
- Do **not** edit released sections — once tagged, history is immutable.
- Do **not** edit the top-level `# lacus/<pkg>` heading.

## Step 1 — Determine the latest released version

```bash
cd php && git tag -l 'lacus/<pkg>@*' | sort -V | tail -n 3
```

The last line is the latest released version. Strip the `lacus/<pkg>@` prefix to get the bare SemVer (e.g. `2.0.0`).

If no tag exists yet, the package has never been released — the first proposed version is `1.0.0`.

## Step 2 — Inspect the top of `CHANGELOG.md`

The file always starts with `# lacus/<pkg>` followed by version blocks. Look at the **top-most** `## x.y.z` heading:

- If it **equals** the latest released tag → the section is released. **Prepend a new section** with a freshly proposed version above it.
- If it is **greater** than the latest released tag → the section is the current in-progress version. **Append or refine** that section instead of creating a new one. If the new change is more severe than the proposed bump (e.g. a breaking change arrives when the section was a patch), **promote** the heading (`## 1.0.1` → `## 2.0.0`) and reorganize the bullets.

## Step 3 — Skip dev-only changes

The changelog is for **end users** of `lacus/<pkg>` on Packagist. If every in-scope change is purely internal and invisible to consumers, do **not** add an entry.

### Dev-only (skip):

- Tests, fixtures, test helpers — anything under `tests/`.
- Benchmarks, profiling, coverage tooling.
- Linter / formatter / static-analysis configs — `.php-cs-fixer.config.php`, `.php-stan.config.neon`, `.captainhook.config.json`, `phpunit.xml`, `.pest.config.xml`.
- CI workflows under `.github/`.
- `composer.json` edits that touch only `"require-dev"`, `"scripts"`, or `"autoload-dev"`.
- Lock-file regeneration — `composer.lock`, `vendor/`.
- Repo hygiene — `.gitignore`, `.gitattributes`, `.editorconfig`.

### User-facing (entry needed):

If even one in-scope change is user-facing, add an entry documenting **only** the user-facing parts:

- Anything under `src/`.
- `composer.json` runtime `"require"`, `"name"`, or `"autoload"` changes.
- A public `README.md` correction.

## Step 4 — Choose the next version using SemVer

Based on all user-facing changes since the latest released tag (cumulative — not just this turn):

| Level | When to use |
|-------|-------------|
| **major** | Removal or rename of a public class/method; namespace move (e.g. `Lacus\CpfVal\` → `Lacus\BrUtils\Cpf\`); signature change that breaks callers; raising minimum PHP version; behavior change that breaks existing usage |
| **minor** | New public class, new method, new option in `*Options`, new exception type, new feature behind an existing entry point |
| **patch** | Bug fix in `src/`; runtime dependency bump (`"require"`); user-visible `README.md` fix |

If the in-progress section already proposes a higher bump, **do not downgrade** it.

## Step 5 — Format

Match the style already used in this repo. Top-level template:

```markdown
# lacus/<pkg>

## <next-version>

### <Section heading>

- **<Topic>** — one-sentence description.

## <previous-version>

...
```

Common section headings, in this order when present:

- `### 🎉 v<N> at a glance 🎊` — only for new major releases; curated highlight reel of 3–6 bullets.
- `### BREAKING CHANGES` — required when bumping major. One bullet per break.
- `### New features` (or `### New Features`)
- `### Improvements`
- `### Bug fixes`
- `### Patch Changes` — one bullet per change.

## Step 6 — Conciseness rules (strict)

- **One sentence per bullet.** Two short sentences only when the second is a brief migration tip ("To restore the old behavior, …").
- **Lead with a bold topic** (`**`-wrapped) of 1–4 words, then an em-dash or colon, then the description.
- **No expository prose.** Don't explain motivation, internals, or test details. Don't recap what is now in the docs — link to them.
- **Use backticks** for every class, method, namespace, option, exception, file path, and CLI flag mentioned.
- **Prefer the smallest accurate description.** "Fix off-by-one in `cnpj_fmt` array input." beats "Resolves an off-by-one error that was occurring when callers supplied an array of strings to the `cnpj_fmt` helper function…"
- **Limit each version section to ≤ 8 bullets total** across all sub-headings; if you need more, the change is documented at too low a level.

## Examples

Minimal patch:

```markdown
# lacus/cnpj-fmt

## 2.0.1

### Bug fixes

- **Array input** — Fix off-by-one in `CnpjFormatter::format()` when input is a `list<string>`.
```

Minor addition:

```markdown
## 2.1.0

### New features

- **`strict` option** — `CnpjValidator` now accepts a `strict` option that rejects numeric-only CNPJs when `type` is `'alphanumeric'`.
```

Major with migration tip:

```markdown
## 3.0.0

### BREAKING CHANGES

- **Namespace** — Public API moved from `Lacus\CnpjFmt\` to `Lacus\BrUtils\Cnpj\`; update all `use` statements.
- **`onFail` default** — Default callback now returns `''`; previously returned the original input. Pass `onFail: fn ($v) => $v` to restore old behavior.
```

## Package-level overrides

Before applying this harness, check whether the target package defines `packages/<pkg>/AGENTS.md` or `packages/<pkg>/context/`. If either exists and contradicts this file on the same topic, **follow the package-level instruction** (see [`context/README.md`](README.md#instruction-precedence)).

## Reference

| Concern | Path |
|---------|------|
| Format reference | `packages/cnpj-fmt/CHANGELOG.md` |
| DV format reference | `packages/cpf-dv/CHANGELOG.md` |
| Tag lookup | `git tag -l 'lacus/<pkg>@*' \| sort -V` |
