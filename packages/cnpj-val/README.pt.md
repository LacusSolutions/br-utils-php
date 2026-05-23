![cnpj-val para PHP](https://br-utils.vercel.app/img/cover_cnpj-val.jpg)

> 🚀 **Suporte total ao [novo formato alfanumérico de CNPJ](https://github.com/user-attachments/files/23937961/calculodvcnpjalfanaumerico.pdf).**

> 🌎 [Access documentation in English](https://github.com/LacusSolutions/br-utils-php/blob/main/packages/cnpj-val/README.md)

Utilitário em PHP para validar CNPJs (Cadastro Nacional da Pessoa Jurídica).

## Recursos

- ✅ **CNPJ alfanumérico**: Valida CNPJ de 14 caracteres no formato numérico ou alfanumérico
- ✅ **Entrada flexível**: Aceita `string` ou `list<string>`; elementos do array são concatenados na ordem
- ✅ **Agnóstico ao formato**: Remove caracteres não alfanuméricos (ou não numéricos quando `type` é `numeric`) e opcionalmente converte letras para maiúsculas
- ✅ **Sensibilidade a maiúsculas opcional**: Com `caseSensitive` em `false`, letras minúsculas são aceitas para CNPJ alfanumérico
- ✅ **Sobrescrita por chamada**: Os padrões da instância podem ser sobrescritos em uma única chamada de `isValid()`
- ✅ **Validação tipada de opções**: Subclasses específicas de `TypeError` / `Exception` para uso inválido de opções ou entrada
- ✅ **Duas formas de uso**: Orientada a objeto (`CnpjValidator`) e funcional (`cnpj_val()`)

## Instalação

```bash
# usando Composer
$ composer require lacus/cnpj-val
```

## Importação

```php
<?php

use Lacus\BrUtils\Cnpj\CnpjValidator;
use Lacus\BrUtils\Cnpj\CnpjValidatorOptions;
use Lacus\BrUtils\Cnpj\Enums\CnpjValidationType;

use function Lacus\BrUtils\Cnpj\cnpj_val;
```

## Início rápido

```php
<?php

use Lacus\BrUtils\Cnpj\CnpjValidator;

$validator = new CnpjValidator();

$validator->isValid('98765432000198');      // true
$validator->isValid('98.765.432/0001-98');  // true
$validator->isValid('98765432000199');      // false

$validator->isValid('1QB5UKALPYFP59');      // true (alfanumérico)
$validator->isValid('1QB5UKALpyfp59');      // false (padrão é case-sensitive)
$validator->isValid('1QB5UKALpyfp59', caseSensitive: false);  // true

$validator->isValid('96206256120884');       // true (numérico)
$validator->isValid('1QB5UKALPYFP59', type: CnpjValidationType::Numeric);  // false
```

Helper funcional:

```php
cnpj_val('98765432000198');      // true
cnpj_val('98.765.432/0001-98');  // true
cnpj_val('98765432000199');      // false
```

## Utilização

Os principais recursos são a classe `CnpjValidator`, o objeto de valor `CnpjValidatorOptions`, o enum `CnpjValidationType` e o helper `cnpj_val()`.

### `CnpjValidator`

- **`__construct`**: `new CnpjValidator(?CnpjValidatorOptions $options = null, $type = null, $caseSensitive = null)`

  Se `$options` for uma instância de `CnpjValidatorOptions`, essa mesma instância é armazenada internamente (mutações posteriores afetam futuras chamadas de `isValid()` sem sobrescrita por chamada). Caso contrário, um novo objeto de opções é construído a partir dos valores nomeados.

- **`getOptions()`**: Retorna a instância interna de `CnpjValidatorOptions`.
- **`isValid`**: `isValid(string|list<string> $cnpjInput, ?CnpjValidatorOptions $options = null, $type = null, $caseSensitive = null): bool`

  As opções por chamada são mescladas sobre os padrões da instância apenas naquela chamada. Retorna `true` quando a entrada sanitizada tem exatamente **14** caracteres, os dois últimos são dígitos e os dígitos verificadores conferem (`CnpjCheckDigits` de **`lacus/cnpj-dv`**). Caso contrário retorna `false` (CNPJ inválido, tamanho incorreto, base/filial inelegíveis, etc.) sem lançar exceção.

  Se a entrada não for `string` nem `list` de strings, é lançada **`CnpjValidatorInputTypeError`**.

```php
<?php

use Lacus\BrUtils\Cnpj\CnpjValidator;
use Lacus\BrUtils\Cnpj\Enums\CnpjValidationType;

$validator = new CnpjValidator(type: CnpjValidationType::Numeric);

$validator->isValid('98.765.432/0001-98');   // true
$validator->isValid('1QB5UKALPYFP59');       // false (letras removidas → tamanho ≠ 14)
$validator->isValid('1QB5UKALpyfp59', type: CnpjValidationType::Alphanumeric, caseSensitive: false);  // true
```

Opções padrão na instância; sobrescrita por chamada:

```php
$validator = new CnpjValidator(caseSensitive: false);

$validator->isValid('1qb5ukalpyfp59');                       // true (padrões da instância)
$validator->isValid('1qb5ukalpyfp59', caseSensitive: true);  // só nesta chamada: false
$validator->isValid('1qb5ukalpyfp59');                       // true de novo
```

### `CnpjValidatorOptions`

Armazena as configurações do validador (`type`, `caseSensitive`). Construa com parâmetros nomeados e `overrides` opcional (lista de arrays e/ou outras instâncias de `CnpjValidatorOptions`, mescladas em ordem). Expõe propriedades via `__get` / `__set` mágicos.

- **`getAll()`**: Retorna um array superficial com todas as opções.
- **`set(...)`**: Atualiza vários campos de uma vez; retorna `$this`.

### `CnpjValidationType`

Enum com valor de apoio para a opção `type`:

- `CnpjValidationType::Alphanumeric` (`"alphanumeric"`) — padrão; mantém `0`–`9` e `A`–`Z` após sanitização.
- `CnpjValidationType::Numeric` (`"numeric"`) — CNPJ numérico legado; remove tudo exceto `0`–`9`.

Métodos auxiliares:

- `CnpjValidationType::values(): list<string>`
- `toSequenceType(): SequenceType` (de **`lacus/utils`**)

Os literais `'alphanumeric'` e `'numeric'` também são aceitos onde `type` é documentado.

### Helper funcional

`cnpj_val()` cria um novo `CnpjValidator` com os mesmos argumentos do construtor e chama `isValid($cnpjInput)` uma vez. Use argumentos nomeados para opções:

```php
cnpj_val('98765432000198');                              // true
cnpj_val('1QB5UKALpyfp59', caseSensitive: false);        // true
cnpj_val('1QB5UKALPYFP59', type: CnpjValidationType::Numeric);  // false
```

Para passar um objeto de opções completo como segundo argumento: `cnpj_val($cnpj, new CnpjValidatorOptions(type: CnpjValidationType::Numeric))`.

### Formatos de entrada

**String:** Dígitos e/ou letras brutos, ou CNPJ já formatado (ex.: `98.765.432/0001-98`, `1Q.B5U.KAL/PYFP-59`). Caracteres são removidos conforme `type`; com `caseSensitive` em `false`, letras são convertidas para maiúsculas antes da validação alfanumérica.

**Array de strings:** Cada elemento deve ser string; os valores são concatenados (ex.: por dígito, segmentos agrupados ou misturados com pontuação). Elementos não string lançam **`CnpjValidatorInputTypeError`**.

### Opções de validação

| Parâmetro | Tipo | Padrão | Descrição |
|-----------|------|---------|-------------|
| `type` | `CnpjValidationType\|'alphanumeric'\|'numeric'\|null` | `CnpjValidationType::Alphanumeric` | Conjunto de caracteres após sanitização: alfanumérico (`0`–`9`, `A`–`Z`) ou somente numérico (`0`–`9`) |
| `caseSensitive` | `?bool` | `true` | Se `false`, letras minúsculas são convertidas para maiúsculas antes da validação alfanumérica |

CNPJ inválido (tamanho incorreto após sanitização, dígitos verificadores inválidos, base/filial inelegíveis `00000000` / `0000`, dígitos repetidos, verificadores não numéricos) retorna **`false`** — falhas de validação não lançam exceção.

### Erros e exceções

O pacote usa **TypeError** para tipos de opção/entrada inválidos e **Exception** para valores de opção inválidos. Falhas de validação retornam `false`.

- **Tipo de entrada incorreto** (não `string` nem `list<string>`): **`CnpjValidatorInputTypeError`** — estende **`CnpjValidatorTypeError`** (estende `TypeError` do PHP).
- **Tipos de opção inválidos** ao construir ou mesclar opções: **`CnpjValidatorOptionsTypeError`**.
- **Valor de `type` inválido** (não `alphanumeric` / `numeric`): **`CnpjValidatorOptionTypeInvalidException`** — estende **`CnpjValidatorException`**.

```php
<?php

use Lacus\BrUtils\Cnpj\CnpjValidator;
use Lacus\BrUtils\Cnpj\Exceptions\CnpjValidatorInputTypeError;
use Lacus\BrUtils\Cnpj\Exceptions\CnpjValidatorOptionTypeInvalidException;

try {
    (new CnpjValidator())->isValid(12345678000198);
} catch (CnpjValidatorInputTypeError $e) {
    echo $e->getMessage();
}

try {
    new CnpjValidator(type: 'invalid');
} catch (CnpjValidatorOptionTypeInvalidException $e) {
    echo $e->getMessage();
}
```

### Outros recursos disponíveis

- **`CnpjValidatorOptions::CNPJ_LENGTH`**: `14` — comprimento padrão do CNPJ após sanitização.

## Contribuição e suporte

Contribuições são bem-vindas! Consulte as [Diretrizes de contribuição](https://github.com/LacusSolutions/br-utils-php/blob/main/CONTRIBUTING.md). Se o projeto for útil para você, considere:

- ⭐ Dar uma estrela no repositório
- 🤝 Contribuir com código
- 💡 [Sugerir novas funcionalidades](https://github.com/LacusSolutions/br-utils-php/issues)
- 🐛 [Reportar bugs](https://github.com/LacusSolutions/br-utils-php/issues)

## Licença

Este projeto está sob a licença MIT — veja o arquivo [LICENSE](https://github.com/LacusSolutions/br-utils-php/blob/main/LICENSE).

## Changelog

Veja o [CHANGELOG](https://github.com/LacusSolutions/br-utils-php/blob/main/packages/cnpj-val/CHANGELOG.md) para alterações e histórico de versões.

---

Feito com ❤️ por [Lacus Solutions](https://github.com/LacusSolutions)
