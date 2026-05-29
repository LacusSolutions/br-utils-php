![cnpj-utils para PHP](https://br-utils.vercel.app/img/cover_cnpj-utils.jpg)

> 🚀 **Suporte total ao [novo formato alfanumérico de CNPJ](https://github.com/user-attachments/files/23937961/calculodvcnpjalfanaumerico.pdf).**

> 🌎 [Access documentation in English](https://github.com/LacusSolutions/br-utils-php/blob/main/packages/cnpj-utils/README.md)

Utilitário em PHP para formatar, gerar e validar CNPJ (Cadastro Nacional da Pessoa Jurídica). Oferece uma classe wrapper para [`lacus/cnpj-fmt`](https://packagist.org/packages/lacus/cnpj-fmt), [`lacus/cnpj-gen`](https://packagist.org/packages/lacus/cnpj-gen) e [`lacus/cnpj-val`](https://packagist.org/packages/lacus/cnpj-val), além dos recursos fornecidos por esses pacotes.

## Recursos

- ✅ **API unificada**: Uma classe `CnpjUtils` para formatação, geração e validação
- ✅ **Componentes empacotados**: Todas as classes de formatador, gerador e validador, objetos de opções, enums e helpers dos pacotes relacionados estão disponíveis em `Lacus\BrUtils\Cnpj\`
- ✅ **CNPJ alfanumérico**: Suporte completo ao novo formato alfanumérico de CNPJ (a partir de 2026)
- ✅ **Entrada flexível**: `format()` e `isValid()` aceitam `string` ou `list<string>`
- ✅ **Padrões configuráveis**: Defina opções de formatador, gerador e validador na instância
- ✅ **Sobrescrita por chamada**: Sobrescreva qualquer opção de componente em uma única chamada de método
- ✅ **Opções de validação**: Configure `type` e `caseSensitive` (novo na v2; a v1 não tinha configurações de validador)
- ✅ **Duas formas de uso**: Fachada unificada (`CnpjUtils`) ou componentes isolados (`CnpjFormatter`, `CnpjGenerator`, `CnpjValidator`) e helpers funcionais (`cnpj_fmt()`, `cnpj_gen()`, `cnpj_val()`)
- ✅ **Tratamento de erros tipado**: Hierarquias dedicadas de `TypeError` / `Exception` dos pacotes empacotados

## Instalação

```bash
# usando Composer
$ composer require lacus/cnpj-utils
```

Isso instala **`lacus/cnpj-utils`** junto com [`lacus/cnpj-fmt`](https://packagist.org/packages/lacus/cnpj-fmt), [`lacus/cnpj-gen`](https://packagist.org/packages/lacus/cnpj-gen) e [`lacus/cnpj-val`](https://packagist.org/packages/lacus/cnpj-val). Não é necessário executar `composer require` separado para os pacotes de componentes ao usar **`lacus/cnpj-utils`**.

## Importação

Escolha a API que melhor se adapta ao seu caso. Todos os símbolos abaixo compartilham o namespace **`Lacus\BrUtils\Cnpj\`** e ficam disponíveis após instalar **`lacus/cnpj-utils`**.

**Fachada unificada:**

```php
<?php

use Lacus\BrUtils\Cnpj\CnpjUtils;
```

**Componentes isolados (orientados a objeto):**

```php
<?php

use Lacus\BrUtils\Cnpj\CnpjFormatter;
use Lacus\BrUtils\Cnpj\CnpjFormatterOptions;
use Lacus\BrUtils\Cnpj\CnpjGenerator;
use Lacus\BrUtils\Cnpj\CnpjGeneratorOptions;
use Lacus\BrUtils\Cnpj\CnpjValidator;
use Lacus\BrUtils\Cnpj\CnpjValidatorOptions;
use Lacus\BrUtils\Cnpj\Enums\CnpjType as CnpjGenerationType;
use Lacus\BrUtils\Cnpj\Enums\CnpjValidationType;
```

**Helpers funcionais** (autoload dos pacotes de componentes empacotados):

```php
<?php

use function Lacus\BrUtils\Cnpj\cnpj_fmt;
use function Lacus\BrUtils\Cnpj\cnpj_gen;
use function Lacus\BrUtils\Cnpj\cnpj_val;
```

## Início rápido

**Com `CnpjUtils` (tudo em um):**

```php
<?php

use Lacus\BrUtils\Cnpj\CnpjUtils;

$utils = new CnpjUtils();
$cnpj = '03603568000195';

$utils->format($cnpj);    // '03.603.568/0001-95'
$utils->isValid($cnpj);   // true
$utils->generate();       // ex.: 'AB123CDE000196'
```

**Com componentes isolados:**

```php
<?php

use Lacus\BrUtils\Cnpj\CnpjFormatter;
use Lacus\BrUtils\Cnpj\CnpjGenerator;
use Lacus\BrUtils\Cnpj\CnpjValidator;

$cnpj = '03603568000195';

(new CnpjFormatter())->format($cnpj);   // '03.603.568/0001-95'
(new CnpjValidator())->isValid($cnpj);  // true
(new CnpjGenerator())->generate();        // ex.: 'AB123CDE000196'
```

**Com helpers funcionais:**

```php
<?php

use function Lacus\BrUtils\Cnpj\cnpj_fmt;
use function Lacus\BrUtils\Cnpj\cnpj_gen;
use function Lacus\BrUtils\Cnpj\cnpj_val;

$cnpj = '03603568000195';

cnpj_fmt($cnpj);   // '03.603.568/0001-95'
cnpj_val($cnpj);   // true
cnpj_gen();        // ex.: 'AB123CDE000196'
```

## Utilização

Há três formas equivalentes de uso:

1. **`CnpjUtils`** — instância única com padrões compartilhados entre formatar, gerar e validar.
2. **Classes de componente** — `CnpjFormatter`, `CnpjGenerator` e `CnpjValidator` diretamente (as mesmas classes usadas internamente por `CnpjUtils`).
3. **Helpers funcionais** — `cnpj_fmt()`, `cnpj_gen()` e `cnpj_val()` para chamadas pontuais sem gerenciar instâncias.

As três abordagens expõem as mesmas opções e comportamento. Para tabelas completas de opções e detalhes específicos de cada componente, consulte o README de cada [pacote empacotado](#pacotes-empacotados).

### `CnpjUtils`

- **`__construct`**: `new CnpjUtils($formatter = [], $generator = [], $validator = [])`

  Cada argumento pode ser um array de opções (espalhado no construtor `*Options` do componente), uma instância de `CnpjFormatterOptions` / `CnpjGeneratorOptions` / `CnpjValidatorOptions` (armazenada por referência — alterações posteriores afetam chamadas sem sobrescrita por chamada) ou omitido para usar os padrões.

  Exemplo: `new CnpjUtils(formatter: ['hidden' => true], generator: ['format' => true], validator: ['type' => CnpjValidationType::Numeric])`.

- **`format`**: `format(string|list<string> $cnpjInput, ?CnpjFormatterOptions $options = null, …opções nomeadas do formatador…): string`

  Delega para `CnpjFormatter::format()`. Opções por chamada são mescladas sobre os padrões da instância apenas naquela chamada.

- **`generate`**: `generate(?CnpjGeneratorOptions $options = null, $format = null, $prefix = null, $type = null): string`

  Delega para `CnpjGenerator::generate()`. Opções por chamada são mescladas sobre os padrões da instância apenas naquela chamada.

- **`isValid`**: `isValid(string|list<string> $cnpjInput, ?CnpjValidatorOptions $options = null, $type = null, $caseSensitive = null): bool`

  Delega para `CnpjValidator::isValid()`. Opções por chamada são mescladas sobre os padrões da instância apenas naquela chamada.

- **`getFormatter()`**, **`getGenerator()`**, **`getValidator()`**: Retornam as instâncias internas dos componentes para uso direto.

```php
<?php

use Lacus\BrUtils\Cnpj\CnpjUtils;
use Lacus\BrUtils\Cnpj\Enums\CnpjType as CnpjGenerationType;
use Lacus\BrUtils\Cnpj\Enums\CnpjValidationType;

$utils = new CnpjUtils();

$utils->format('03603568000195');              // '03.603.568/0001-95'
$utils->format('12ABC34500DE99');              // '12.ABC.345/00DE-99'
$utils->isValid('98.765.432/0001-98');         // true
$utils->isValid('1QB5UKALPYFP59');             // true (alfanumérico)
$utils->generate(format: true);                // ex.: 'AB.123.CDE/0001-96'
$utils->generate(type: CnpjGenerationType::Numeric);  // ex.: '12345678000195'
```

### Padrões da instância e sobrescrita por chamada

```php
$utils = new CnpjUtils(
    formatter: ['hidden' => true, 'hiddenKey' => '#'],
    generator: ['format' => true],
    validator: ['type' => CnpjValidationType::Numeric],
);

$cnpj = '03603568000195';

$utils->format($cnpj);                  // mascarado (padrões do formatador da instância)
$utils->format($cnpj, hidden: false);    // só nesta chamada: sem máscara
$utils->generate(format: false);        // só nesta chamada: saída compacta
$utils->isValid('1QB5UKALPYFP59');      // false (validador da instância é só numérico)
$utils->isValid('1QB5UKALPYFP59', type: CnpjValidationType::Alphanumeric);  // true nesta chamada
```

### Formatação (`format`)

Suporta as mesmas opções de [`lacus/cnpj-fmt`](https://github.com/LacusSolutions/br-utils-php/blob/main/packages/cnpj-fmt/README.pt.md). Passe-as no construtor de `CnpjUtils` (argumento `formatter`), por chamada de `format()`, ou via `CnpjFormatter` / `cnpj_fmt()`.

| Parâmetro | Tipo | Padrão | Descrição |
|-----------|------|--------|-----------|
| `hidden` | `?bool` | `false` | Com `true`, substitui o intervalo inclusivo de índices `[hiddenStart, hiddenEnd]` na string normalizada de 14 caracteres antes de aplicar a pontuação |
| `hiddenKey` | `?string` | `'*'` | Substituição de cada posição oculta (pode ter vários caracteres ou ser vazia); não pode usar caracteres proibidos nas chaves |
| `hiddenStart` | `?int` | `5` | Índice inicial `0`–`13` (inclusivo) |
| `hiddenEnd` | `?int` | `13` | Índice final `0`–`13` (inclusivo); se `hiddenStart > hiddenEnd`, os valores são trocados |
| `dotKey` | `?string` | `'.'` | Separador entre os grupos `XX` / `XXX` / `XXX` |
| `slashKey` | `?string` | `'/'` | Separador antes do bloco da filial |
| `dashKey` | `?string` | `'-'` | Separador antes dos dois últimos caracteres |
| `escape` | `?bool` | `false` | Com `true`, aplica escape HTML na string final (`HtmlUtils::escape`) |
| `encode` | `?bool` | `false` | Com `true`, codifica a string final para URL (`UrlUtils::encodeUriComponent`) |
| `onFail` | `?\Closure` | veja abaixo | `Closure(mixed $value, CnpjFormatterException $e): string` — usado quando o comprimento após sanitização ≠ 14 |

O **`onFail`** padrão retorna string vazia. A exceção passada em falhas de comprimento é **`CnpjFormatterInputLengthException`**. Comprimento inválido **não** lança exceção em `format()`; tipos de entrada incorretos lançam **`CnpjFormatterInputTypeError`**.

```php
$cnpj = '03603568000195';

$utils->format($cnpj);                                        // '03.603.568/0001-95'
$utils->format($cnpj, hidden: true, hiddenKey: '#');          // '03.603.###/####-##'
$utils->format($cnpj, dotKey: '', slashKey: '|', dashKey: '_');  // '03603568|0001_95'
$utils->format($cnpj, encode: true);                          // saída codificada para URL
```

### Geração (`generate`)

Suporta as mesmas opções de [`lacus/cnpj-gen`](https://github.com/LacusSolutions/br-utils-php/blob/main/packages/cnpj-gen/README.pt.md). Passe-as no construtor de `CnpjUtils` (argumento `generator`), por chamada de `generate()`, ou via `CnpjGenerator` / `cnpj_gen()`.

| Parâmetro | Tipo | Padrão | Descrição |
|-----------|------|--------|-----------|
| `format` | `?bool` | `false` | Com `true`, retorna CNPJ formatado (`XX.XXX.XXX/XXXX-XX`); caso contrário, saída compacta de 14 caracteres |
| `prefix` | `?string` | `''` | Semente base para geração. Caracteres não alfanuméricos são removidos, letras viram maiúsculas, e apenas os 12 primeiros caracteres (índices `0`–`11`) são usados; caracteres no índice `12+` são ignorados |
| `type` | `CnpjGenerationType\|'alphanumeric'\|'alphabetic'\|'numeric'\|null` | `CnpjGenerationType::Alphanumeric` | Família de caracteres usada nas posições base geradas (`0`–`9`, `A`–`Z`, ou ambos) |

Regras de validação do `prefix`:

- base `00000000` é rejeitada (quando os 8 primeiros caracteres estão presentes)
- filial `0000` é rejeitada (quando os caracteres 9–12 estão presentes)
- 12 dígitos numéricos repetidos são rejeitados (ex.: `111111111111`)

```php
$utils->generate();                              // ex.: 'AB123CDE000196'
$utils->generate(format: true);                  // ex.: 'AB.123.CDE/0001-96'
$utils->generate(prefix: '12345678', type: CnpjGenerationType::Numeric);
```

### Validação (`isValid`)

Suporta as mesmas opções de [`lacus/cnpj-val`](https://github.com/LacusSolutions/br-utils-php/blob/main/packages/cnpj-val/README.pt.md). Diferente da v1, a validação agora é configurável por **`CnpjValidatorOptions`**. Passe-as no construtor de `CnpjUtils` (argumento `validator`), por chamada de `isValid()`, ou via `CnpjValidator` / `cnpj_val()`.

| Parâmetro | Tipo | Padrão | Descrição |
|-----------|------|--------|-----------|
| `type` | `CnpjValidationType\|'alphanumeric'\|'numeric'\|null` | `CnpjValidationType::Alphanumeric` | Conjunto de caracteres após sanitização |
| `caseSensitive` | `?bool` | `true` | Com `false`, letras minúsculas são convertidas para maiúsculas antes da validação alfanumérica |

```php
$utils = new CnpjUtils();

$utils->isValid('98765432000198');       // true
$utils->isValid('98765432000199');       // false
$utils->isValid('1QB5UKALPYFP59');       // true
$utils->isValid('1QB5UKALpyfp59');       // false (padrão é sensível a maiúsculas)
$utils->isValid('1QB5UKALpyfp59', caseSensitive: false);  // true

$utils->isValid('1QB5UKALPYFP59', type: CnpjValidationType::Numeric);  // false

// Validação só numérica legada como padrão da instância
$numericUtils = new CnpjUtils(validator: ['type' => CnpjValidationType::Numeric]);
$numericUtils->isValid('98.765.432/0001-98');   // true
$numericUtils->isValid('1QB5UKALPYFP59');       // false
```

CNPJ inválido retorna **`false`** sem lançar exceção. Tipos de entrada incorretos lançam **`CnpjValidatorInputTypeError`**.

### Componentes empacotados (uso isolado)

Instale **`lacus/cnpj-utils`** uma vez; importe e use qualquer recurso dos pacotes relacionados sem exigi-los separadamente.

#### `CnpjFormatter` e `cnpj_fmt()`

Mesma API de [`lacus/cnpj-fmt`](https://github.com/LacusSolutions/br-utils-php/blob/main/packages/cnpj-fmt/README.pt.md).

```php
<?php

use Lacus\BrUtils\Cnpj\CnpjFormatter;

use function Lacus\BrUtils\Cnpj\cnpj_fmt;

$cnpj = '03603568000195';

$formatter = new CnpjFormatter(hidden: true);
$formatter->format($cnpj);                    // mascarado com padrões da instância
$formatter->format($cnpj, hidden: false);    // sobrescrita por chamada

cnpj_fmt($cnpj);                              // '03.603.568/0001-95'
cnpj_fmt($cnpj, hidden: true, hiddenKey: '#'); // '03.603.###/####-##'
cnpj_fmt('12ABC34500DE99');                   // '12.ABC.345/00DE-99'
```

#### `CnpjGenerator` e `cnpj_gen()`

Mesma API de [`lacus/cnpj-gen`](https://github.com/LacusSolutions/br-utils-php/blob/main/packages/cnpj-gen/README.pt.md).

```php
<?php

use Lacus\BrUtils\Cnpj\CnpjGenerator;
use Lacus\BrUtils\Cnpj\Enums\CnpjType as CnpjGenerationType;

use function Lacus\BrUtils\Cnpj\cnpj_gen;

$generator = new CnpjGenerator(format: true, type: CnpjGenerationType::Numeric);

$generator->generate();                       // CNPJ numérico formatado
$generator->generate(format: false);         // por chamada: saída compacta

cnpj_gen();                                   // ex.: 'AB123CDE000196'
cnpj_gen(format: true);                       // ex.: 'AB.123.CDE/0001-96'
cnpj_gen(prefix: '12345678', type: CnpjGenerationType::Numeric);
```

#### `CnpjValidator` e `cnpj_val()`

Mesma API de [`lacus/cnpj-val`](https://github.com/LacusSolutions/br-utils-php/blob/main/packages/cnpj-val/README.pt.md).

```php
<?php

use Lacus\BrUtils\Cnpj\CnpjValidator;
use Lacus\BrUtils\Cnpj\Enums\CnpjValidationType;

use function Lacus\BrUtils\Cnpj\cnpj_val;

$validator = new CnpjValidator(type: CnpjValidationType::Numeric);

$validator->isValid('98.765.432/0001-98');    // true
$validator->isValid('1QB5UKALPYFP59');      // false (instância só numérica)

cnpj_val('98765432000198');                   // true
cnpj_val('1QB5UKALpyfp59', caseSensitive: false);  // true
cnpj_val('1QB5UKALPYFP59', type: CnpjValidationType::Numeric);  // false
```

#### Misturando estilos

Use `CnpjUtils` onde uma configuração compartilhada ajuda, e componentes isolados em outros pontos — são as mesmas classes subjacentes:

```php
<?php

use Lacus\BrUtils\Cnpj\CnpjFormatter;
use Lacus\BrUtils\Cnpj\CnpjUtils;
use Lacus\BrUtils\Cnpj\Enums\CnpjValidationType;

use function Lacus\BrUtils\Cnpj\cnpj_val;

$utils = new CnpjUtils(validator: ['type' => CnpjValidationType::Numeric]);

// Via fachada
$utils->format('03603568000195');

// Via componente retornado pela fachada (mesma instância do formatador)
$utils->getFormatter()->format('12ABC34500DE99');

// Via instância separada do componente
(new CnpjFormatter())->format('03603568000195');

// Via helper funcional
cnpj_val('98.765.432/0001-98');
```

### Acessando componentes a partir de `CnpjUtils`

```php
$utils = new CnpjUtils();

$formatter = $utils->getFormatter();
$generator = $utils->getGenerator();
$validator = $utils->getValidator();

$formatter->format('03603568000195', hidden: true);
$generator->generate(format: true);
$validator->isValid('03603568000195');
```

Use **`getFormatter()`**, **`getGenerator()`** e **`getValidator()`** quando já tiver uma instância de `CnpjUtils` e quiser o componente configurado sem criar um novo. As instâncias retornadas compartilham as mesmas opções passadas ao construtor de `CnpjUtils`.

### Erros e exceções

`CnpjUtils` não define tipos de exceção próprios; propaga erros dos pacotes empacotados:

- **Formatação**: `CnpjFormatterInputTypeError`, `CnpjFormatterOptionsTypeError`, `CnpjFormatterOptionsHiddenRangeInvalidException`, `CnpjFormatterOptionsForbiddenKeyCharacterException` e classes relacionadas.
- **Geração**: `CnpjGeneratorOptionsTypeError`, `CnpjGeneratorOptionPrefixInvalidException`, `CnpjGeneratorOptionTypeInvalidException` e classes relacionadas.
- **Validação**: `CnpjValidatorInputTypeError`, `CnpjValidatorOptionsTypeError`, `CnpjValidatorOptionTypeInvalidException` e classes relacionadas.

Tipos de opção inválidos são subclasses de **`TypeError`**; valores de opção inválidos são subclasses de **`Exception`**. Falha de validação retorna `false`; falha de comprimento na formatação é tratada por **`onFail`**.

```php
<?php

use Lacus\BrUtils\Cnpj\CnpjUtils;
use Lacus\BrUtils\Cnpj\Exceptions\CnpjFormatterInputTypeError;
use Lacus\BrUtils\Cnpj\Exceptions\CnpjValidatorInputTypeError;

try {
    (new CnpjUtils())->format(12345);
} catch (CnpjFormatterInputTypeError $e) {
    echo $e->getMessage();
}

try {
    (new CnpjUtils())->isValid(12345678000198);
} catch (CnpjValidatorInputTypeError $e) {
    echo $e->getMessage();
}
```

### Pacotes empacotados

| Pacote | Principais recursos | README |
|--------|---------------------|--------|
| [`lacus/cnpj-fmt`](https://packagist.org/packages/lacus/cnpj-fmt) | `CnpjFormatter`, `CnpjFormatterOptions`, `cnpj_fmt()` | [docs](https://github.com/LacusSolutions/br-utils-php/blob/main/packages/cnpj-fmt/README.pt.md) |
| [`lacus/cnpj-gen`](https://packagist.org/packages/lacus/cnpj-gen) | `CnpjGenerator`, `CnpjGeneratorOptions`, `CnpjType`, `cnpj_gen()` | [docs](https://github.com/LacusSolutions/br-utils-php/blob/main/packages/cnpj-gen/README.pt.md) |
| [`lacus/cnpj-val`](https://packagist.org/packages/lacus/cnpj-val) | `CnpjValidator`, `CnpjValidatorOptions`, `CnpjValidationType`, `cnpj_val()` | [docs](https://github.com/LacusSolutions/br-utils-php/blob/main/packages/cnpj-val/README.pt.md) |

Todos os itens acima são instalados como dependências de **`lacus/cnpj-utils`** e compartilham o namespace **`Lacus\BrUtils\Cnpj\`**. Para tabelas completas de opções, listas de exceções e comportamento em casos extremos, consulte o README de cada pacote.

## Contribuição e suporte

Contribuições são bem-vindas! Consulte as [Diretrizes de contribuição](https://github.com/LacusSolutions/br-utils-php/blob/main/CONTRIBUTING.md). Se o projeto for útil para você, considere:

- ⭐ Dar uma estrela no repositório
- 🤝 Contribuir com código
- 💡 [Sugerir novas funcionalidades](https://github.com/LacusSolutions/br-utils-php/issues)
- 🐛 [Reportar bugs](https://github.com/LacusSolutions/br-utils-php/issues)

## Licença

Este projeto está sob a licença MIT — veja o arquivo [LICENSE](https://github.com/LacusSolutions/br-utils-php/blob/main/LICENSE).

## Changelog

Veja o [CHANGELOG](https://github.com/LacusSolutions/br-utils-php/blob/main/packages/cnpj-utils/CHANGELOG.md) para alterações e histórico de versões.

---

Feito com ❤️ por [Lacus Solutions](https://github.com/LacusSolutions)
