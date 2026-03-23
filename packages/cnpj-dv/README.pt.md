![cnpj-dv para PHP](https://br-utils.vercel.app/img/cover_cnpj-dv.jpg)

> 🚀 **Suporte total ao [novo formato alfanumérico de CNPJ](https://github.com/user-attachments/files/23937961/calculodvcnpjalfanaumerico.pdf).**

> 🌎 [Access documentation in English](https://github.com/LacusSolutions/br-utils-php/blob/main/packages/cnpj-dv/README.md)

Utilitário em PHP para calcular os dígitos verificadores de CNPJ (Cadastro Nacional da Pessoa Jurídica).

## Recursos

- ✅ **CNPJ alfanumérico**: Suporte completo ao novo formato alfanumérico de CNPJ (a partir de 2026)
- ✅ **Entrada flexível**: Aceita `string` ou `array` de strings
- ✅ **Agnóstico ao formato**: Remove caracteres não alfanuméricos da entrada em string e converte letras para maiúsculas
- ✅ **Junção em array**: Strings com vários caracteres em arrays são concatenadas e interpretadas como uma única sequência
- ✅ **Validação de entrada**: Rejeita CNPJs inelegíveis (base toda zero `00000000`, filial `0000`, ou 12 dígitos numéricos repetidos)
- ✅ **Avaliação lazy**: Dígitos verificadores são calculados apenas quando acessados (via propriedades)
- ✅ **Cache**: Valores calculados são armazenados em cache para acessos subsequentes
- ✅ **API estilo propriedades**: `first`, `second`, `both`, `cnpj` (via `__get` mágico)
- ✅ **Dependências mínimas**: Apenas [`lacus/utils`](https://packagist.org/packages/lacus/utils)
- ✅ **Tratamento de erros**: Tipos específicos para tipo, tamanho e CNPJ inválido (semântica `TypeError` vs `Exception`)

## Instalação

```bash
# usando Composer
$ composer require lacus/cnpj-dv
```

## Início rápido

```php
<?php

use Lacus\BrUtils\Cnpj\CnpjCheckDigits;

$checkDigits = new CnpjCheckDigits('914157320007');

$checkDigits->first;   // '9'
$checkDigits->second;  // '3'
$checkDigits->both;    // '93'
$checkDigits->cnpj;    // '91415732000793'
```

## Utilização

O principal recurso deste pacote é a classe `CnpjCheckDigits`. Por meio da instância, você acessa as informações dos dígitos verificadores do CNPJ:

- **`__construct`**: `new CnpjCheckDigits(string|array $cnpjInput)` — 12–14 caracteres alfanuméricos após a sanitização (formatação removida em strings; letras em maiúsculas). Apenas os **primeiros 12** caracteres entram como base; com 13 ou 14 caracteres (ex.: CNPJ completo com DV anteriores), os caracteres 13 e 14 são **ignorados** e os dígitos são recalculados.
- **`first`**: Primeiro dígito verificador (13º caractere do CNPJ completo). Lazy, em cache.
- **`second`**: Segundo dígito verificador (14º caractere do CNPJ completo). Lazy, em cache.
- **`both`**: Ambos os dígitos verificadores concatenados em uma string.
- **`cnpj`**: O CNPJ completo como string de 14 caracteres (12 da base + 2 dígitos verificadores).

### Formatos de entrada

A classe `CnpjCheckDigits` aceita múltiplos formatos de entrada:

**String:** dígitos e/ou letras crus, ou CNPJ formatado (ex.: `91.415.732/0007-93`, `MG.KGM.J9X/0001-68`). Caracteres não alfanuméricos são removidos; letras minúsculas viram maiúsculas.

**Array de strings:** cada elemento deve ser string; os valores são concatenados e interpretados como uma única string (ex.: `['9','1','4',…]`, `['9141','5732','0007']`, `['MG','KGM','J9X','0001']`). Elementos que não são string não são permitidos.

### Erros e exceções

Este pacote usa a distinção **TypeError vs Exception**: *erros de tipo* indicam uso incorreto da API (ex.: tipo errado); *exceções* indicam dados inválidos ou inelegíveis (ex.: tamanho ou regras de negócio). Você pode capturar classes específicas ou as bases abstratas.

- **CnpjCheckDigitsTypeError** (_abstract_) — base para erros de tipo; estende o `TypeError` do PHP
- **CnpjCheckDigitsInputTypeError** — entrada não é `string` nem `array` de strings (ou o array contém elemento que não é string)
- **CnpjCheckDigitsException** (_abstract_) — base para exceções de dados/fluxo; estende `Exception`
- **CnpjCheckDigitsInputLengthException** — tamanho após sanitização não é 12–14
- **CnpjCheckDigitsInputInvalidException** — base `00000000`, filial `0000`, ou 12 dígitos numéricos idênticos (padrão de repetição)

```php
<?php

use Lacus\BrUtils\Cnpj\CnpjCheckDigits;
use Lacus\BrUtils\Cnpj\Exceptions\CnpjCheckDigitsException;
use Lacus\BrUtils\Cnpj\Exceptions\CnpjCheckDigitsInputInvalidException;
use Lacus\BrUtils\Cnpj\Exceptions\CnpjCheckDigitsInputLengthException;
use Lacus\BrUtils\Cnpj\Exceptions\CnpjCheckDigitsInputTypeError;

// Tipo de entrada (ex.: inteiro não permitido)
try {
    new CnpjCheckDigits(12345);
} catch (CnpjCheckDigitsInputTypeError $e) {
    echo $e->getMessage();
}

// Tamanho (deve ser 12–14 caracteres alfanuméricos após sanitização)
try {
    new CnpjCheckDigits('12345678901');
} catch (CnpjCheckDigitsInputLengthException $e) {
    echo $e->getMessage();
}

// Inválido (ex.: base ou filial zeradas, ou dígitos numéricos repetidos)
try {
    new CnpjCheckDigits('000000000001');
} catch (CnpjCheckDigitsInputInvalidException $e) {
    echo $e->getMessage();
}

// Qualquer exceção de dados do pacote
try {
    // código arriscado
} catch (CnpjCheckDigitsException $e) {
    // tratar
}
```

### Outros recursos disponíveis

- **`CNPJ_MIN_LENGTH`**: `12` — constante de classe `CnpjCheckDigits::CNPJ_MIN_LENGTH`, e constante global `Lacus\BrUtils\Cnpj\CNPJ_MIN_LENGTH` quando `cnpj-dv.php` é carregado pelo autoload do Composer.
- **`CNPJ_MAX_LENGTH`**: `14` — constante de classe `CnpjCheckDigits::CNPJ_MAX_LENGTH`, e constante global `Lacus\BrUtils\Cnpj\CNPJ_MAX_LENGTH` quando `cnpj-dv.php` é carregado pelo autoload do Composer.

## Algoritmo de cálculo

O pacote calcula os dígitos verificadores com as regras oficiais brasileiras de módulo 11 estendidas a caracteres alfanuméricos:

1. **Valor do caractere:** cada caractere contribui com `ord(caractere) − 48` (assim `0`–`9` permanecem 0–9; letras usam o deslocamento ASCII em relação a `0`).
2. **Pesos:** da **direita para a esquerda**, multiplicar pelos pesos que ciclam **2, 3, 4, 5, 6, 7, 8, 9** e voltam a 2.
3. **Primeiro dígito verificador (13ª posição):** aplicar os itens 1–2 aos **primeiros 12** caracteres da base; seja `r = soma % 11`. O dígito é `0` se `r < 2`, senão `11 − r`.
4. **Segundo dígito verificador (14ª posição):** aplicar os itens 1–2 aos 12 primeiros caracteres **mais** o primeiro dígito verificador; mesma fórmula para `r`.

## Contribuição e suporte

Contribuições são bem-vindas! Consulte as [Diretrizes de contribuição](https://github.com/LacusSolutions/br-utils-php/blob/main/CONTRIBUTING.md). Se o projeto for útil para você, considere:

- ⭐ Dar uma estrela no repositório
- 🤝 Contribuir com código
- 💡 [Sugerir novas funcionalidades](https://github.com/LacusSolutions/br-utils-php/issues)
- 🐛 [Reportar bugs](https://github.com/LacusSolutions/br-utils-php/issues)

## Licença

Este projeto está sob a licença MIT — veja o arquivo [LICENSE](https://github.com/LacusSolutions/br-utils-php/blob/main/LICENSE).

## Changelog

Veja o [CHANGELOG](https://github.com/LacusSolutions/br-utils-php/blob/main/packages/cnpj-dv/CHANGELOG.md) para alterações e histórico de versões.

---

Feito com ❤️ por [Lacus Solutions](https://github.com/LacusSolutions)
