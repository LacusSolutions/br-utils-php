![cnpj-gen para PHP](https://br-utils.vercel.app/img/cover_cnpj-gen.jpg)

> 🚀 **Suporte total ao [novo formato alfanumérico de CNPJ](https://github.com/user-attachments/files/23937961/calculodvcnpjalfanaumerico.pdf).**

> 🌎 [Access documentation in English](https://github.com/LacusSolutions/br-utils-php/blob/main/packages/cnpj-gen/README.md)

Utilitário em PHP para gerar CNPJ (Cadastro Nacional da Pessoa Jurídica) válido.

## Recursos

- ✅ **Geração de CNPJ alfanumérico**: Suporta caracteres de base `0-9` e `A-Z`, com dígitos verificadores numéricos
- ✅ **API de opções flexível**: Use argumentos nomeados ou uma instância de `CnpjGeneratorOptions`
- ✅ **Prefixo base configurável**: Informe até 12 caracteres base e gere apenas as posições faltantes
- ✅ **Alternância de formatação**: Retorna saída compacta (`14` chars) ou formatada (`18` chars)
- ✅ **Sobrescrita por chamada**: Os padrões da instância podem ser sobrescritos em uma única chamada de `generate()`
- ✅ **Validação tipada de opções**: Subclasses específicas de `TypeError`/`Exception` para uso inválido de opções
- ✅ **Duas formas de uso**: Orientada a objeto (`CnpjGenerator`) e funcional (`cnpj_gen()`)

## Instalação

```bash
# usando Composer
$ composer require lacus/cnpj-gen
```

## Importação

```php
<?php

use Lacus\BrUtils\Cnpj\CnpjGenerator;
use Lacus\BrUtils\Cnpj\CnpjGeneratorOptions;
use Lacus\BrUtils\Cnpj\Enums\CnpjType;

use function Lacus\BrUtils\Cnpj\cnpj_gen;
```

## Início rápido

```php
<?php

use Lacus\BrUtils\Cnpj\CnpjGenerator;

$generator = new CnpjGenerator();

$generator->generate();                // ex.: "AB123CDE000196"
$generator->generate(format: true);    // ex.: "AB.123.CDE/0001-96"
```

## Utilização

Os principais recursos são a classe `CnpjGenerator`, o objeto de opções `CnpjGeneratorOptions`, o enum `CnpjType` e o helper `cnpj_gen()`.

### `CnpjGenerator`

- **`__construct`**: `new CnpjGenerator(?CnpjGeneratorOptions $options = null, $format = null, $prefix = null, $type = null)`

  Se `$options` for uma instância de `CnpjGeneratorOptions`, essa mesma instância é armazenada internamente (mutações posteriores afetam futuras chamadas de `generate()` sem sobrescrita por chamada). Caso contrário, um novo objeto de opções é construído a partir dos valores nomeados.

- **`getOptions()`**: Retorna a instância interna de `CnpjGeneratorOptions`.
- **`generate`**: `generate(?CnpjGeneratorOptions $options = null, $format = null, $prefix = null, $type = null): string`

  As opções por chamada são mescladas sobre os padrões da instância apenas naquela chamada. O valor retornado é:

  - `14` caracteres quando `format = false` (padrão)
  - `18` caracteres com separadores quando `format = true` (`XX.XXX.XXX/XXXX-XX`)

```php
<?php

use Lacus\BrUtils\Cnpj\CnpjGenerator;
use Lacus\BrUtils\Cnpj\Enums\CnpjType;

$generator = new CnpjGenerator(type: CnpjType::Numeric);

$generator->generate();                                 // ex.: "12345678000195"
$generator->generate(format: true);                     // ex.: "12.345.678/0001-95"
$generator->generate(prefix: 'AB123CDE');               // ex.: "AB123CDE000196"
$generator->generate(prefix: 'AB123CDE', format: true); // ex.: "AB.123.CDE/0001-96"
```

Opções padrão na instância; sobrescritas por chamada:

```php
$generator = new CnpjGenerator(format: true, type: CnpjType::Numeric);

$generator->generate();                // CNPJ numérico formatado
$generator->generate(format: false);   // somente nesta chamada: sem formato
$generator->generate();                // volta ao padrão da instância
```

### `CnpjGeneratorOptions`

`CnpjGeneratorOptions` encapsula a configuração de geração (`format`, `prefix`, `type`), suporta acesso por propriedades mágicas (`__get`/`__set`) e permite mesclar valores em camadas via `overrides`.

- **Construtor**: `new CnpjGeneratorOptions($format = null, $prefix = null, $type = null, ?array $overrides = [])`
  - `overrides` aceita uma lista de arrays e/ou outras instâncias de `CnpjGeneratorOptions`
  - a ordem de mesclagem é da esquerda para a direita (a última sobrescrita prevalece)
- **`set(...)`**: Atualiza um ou mais valores de opção e retorna `$this`
- **`getAll()`**: Retorna um snapshot superficial (`format`, `prefix`, `type`)

### Opções de geração

| Parâmetro | Tipo | Padrão | Descrição |
|-----------|------|--------|-----------|
| `format` | `?bool` | `false` | Se `true`, retorna CNPJ formatado (`XX.XXX.XXX/XXXX-XX`); caso contrário, retorna saída compacta de 14 caracteres |
| `prefix` | `?string` | `''` | Base inicial para geração. Remove caracteres não alfanuméricos, converte letras para maiúsculas e usa apenas os 12 primeiros caracteres (índices `0`-`11`); caracteres no índice `12+` são ignorados |
| `type` | `CnpjType\|'alphanumeric'\|'alphabetic'\|'numeric'\|null` | `CnpjType::Alphanumeric` | Família de caracteres usada nas posições de base geradas (`0-9`, `A-Z` ou ambos) |

Regras de validação do `prefix`:

- base `00000000` é rejeitada (quando os 8 primeiros caracteres estão presentes)
- filial `0000` é rejeitada (quando os caracteres 9-12 estão presentes)
- 12 dígitos numéricos repetidos são rejeitados (ex.: `111111111111`)

### `CnpjType`

Casos disponíveis do enum:

- `CnpjType::Alphanumeric`
- `CnpjType::Alphabetic`
- `CnpjType::Numeric`

Métodos auxiliares:

- `CnpjType::values(): list<string>`
- `CnpjType::toSequenceType(): SequenceType`

### Helper funcional

`cnpj_gen()` é um atalho de conveniência:

- instancia um novo `CnpjGenerator` com os mesmos argumentos do construtor
- chama `generate()` uma vez

```php
$cnpj = cnpj_gen();               // ex.: "AB123CDE000196"
$cnpj = cnpj_gen(format: true);   // ex.: "AB.123.CDE/0001-96"
$cnpj = cnpj_gen(prefix: '12345678', type: CnpjType::Numeric);
```

### Erros e exceções

Este pacote usa a distinção **TypeError vs Exception**:

- **Erros de tipo** indicam tipo inválido na API/opções
- **Exceções** indicam valor inválido de opção ou violação de regra de negócio

Classes relevantes:

- `CnpjGeneratorTypeError` (abstrata, estende `TypeError` do PHP)
- `CnpjGeneratorOptionsTypeError`
- `CnpjGeneratorException` (abstrata, estende `Exception`)
- `CnpjGeneratorOptionPrefixInvalidException`
- `CnpjGeneratorOptionTypeInvalidException`

```php
<?php

use Lacus\BrUtils\Cnpj\CnpjGenerator;
use Lacus\BrUtils\Cnpj\Exceptions\CnpjGeneratorOptionPrefixInvalidException;
use Lacus\BrUtils\Cnpj\Exceptions\CnpjGeneratorOptionTypeInvalidException;
use Lacus\BrUtils\Cnpj\Exceptions\CnpjGeneratorOptionsTypeError;

try {
    $generator = new CnpjGenerator(prefix: '00000000');
    $generator->generate();
} catch (CnpjGeneratorOptionPrefixInvalidException $e) {
    echo $e->getMessage();
}

try {
    new CnpjGenerator(type: 'invalid');
} catch (CnpjGeneratorOptionTypeInvalidException $e) {
    echo $e->getMessage();
}

try {
    new CnpjGenerator(prefix: 123);
} catch (CnpjGeneratorOptionsTypeError $e) {
    echo $e->getMessage();
}
```

### Outros recursos disponíveis

- `CnpjGeneratorOptions::CNPJ_LENGTH` (`14`)
- `CnpjGeneratorOptions::CNPJ_PREFIX_MAX_LENGTH` (`12`)

## Contribuição e suporte

Contribuições são bem-vindas! Consulte as [Diretrizes de contribuição](https://github.com/LacusSolutions/br-utils-php/blob/main/CONTRIBUTING.md). Se o projeto for útil para você, considere:

- ⭐ Dar uma estrela no repositório
- 🤝 Contribuir com código
- 💡 [Sugerir novas funcionalidades](https://github.com/LacusSolutions/br-utils-php/issues)
- 🐛 [Reportar bugs](https://github.com/LacusSolutions/br-utils-php/issues)

## Licença

Este projeto está sob a licença MIT — veja o arquivo [LICENSE](https://github.com/LacusSolutions/br-utils-php/blob/main/LICENSE).

## Changelog

Veja o [CHANGELOG](https://github.com/LacusSolutions/br-utils-php/blob/main/packages/cnpj-gen/CHANGELOG.md) para alterações e histórico de versões.

---

Feito com ❤️ por [Lacus Solutions](https://github.com/LacusSolutions)
