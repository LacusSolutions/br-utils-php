![cpf-dv para PHP](https://br-utils.vercel.app/img/cover_cpf-dv.jpg)

> 🌎 [Access documentation in English](https://github.com/LacusSolutions/br-utils-php/blob/main/packages/cpf-dv/README.md)

Utilitário em PHP para calcular os dígitos verificadores de CPF (Cadastro de Pessoa Física).

## Recursos

- ✅ **Entrada flexível**: Aceita `string` ou `array` de strings
- ✅ **Agnóstico ao formato**: Remove automaticamente caracteres não numéricos da entrada em string
- ✅ **Auto-expansão**: Strings com vários caracteres em arrays são expandidas para dígitos individuais
- ✅ **Avaliação lazy**: Dígitos verificadores são calculados apenas quando acessados (via propriedades)
- ✅ **Cache**: Valores calculados são armazenados em cache para acessos subsequentes
- ✅ **API estilo propriedades**: `first`, `second`, `both`, `cpf` (via `__get` mágico)
- ✅ **Dependências mínimas**: Apenas [`lacus/utils`](https://packagist.org/packages/lacus/utils)
- ✅ **Tratamento de erros**: Tipos específicos para tipo, tamanho e CPF inválido (semântica `TypeError` vs `Exception`)

## Instalação

```bash
# usando Composer
$ composer require lacus/cpf-dv
```

## Início rápido

```php
<?php

use Lacus\BrUtils\Cpf\CpfCheckDigits;

$checkDigits = new CpfCheckDigits('054496519');

$checkDigits->first;   // '1'
$checkDigits->second;  // '0'
$checkDigits->both;    // '10'
$checkDigits->cpf;     // '05449651910'
```

## Utilização

O principal recurso deste pacote é a classe `CpfCheckDigits`. Por meio da instância, você acessa as informações dos dígitos verificadores do CPF:

- **`__construct`**: `new CpfCheckDigits(string|array $cpfInput)` — 9–11 dígitos (formatação removida de strings).
- **`first`**: Primeiro dígito verificador (10º dígito do CPF). Lazy, em cache.
- **`second`**: Segundo dígito verificador (11º dígito do CPF). Lazy, em cache.
- **`both`**: Ambos os dígitos verificadores concatenados em uma string.
- **`cpf`**: O CPF completo como string de 11 dígitos (9 da base + 2 dígitos verificadores).

### Formatos de entrada

A classe `CpfCheckDigits` aceita múltiplos formatos de entrada:

**String:** dígitos crus ou CPF formatado (ex.: `054.496.519-10`). Caracteres não numéricos são removidos automaticamente. Use 9 dígitos (apenas base) ou 11 dígitos (apenas os 9 primeiros são usados).

**Array de strings:** strings de um caractere ou de vários (expandidas para dígitos individuais), ex.: `['0','5','4','4','9','6','5','1','9']`, `['054496519']`, `['054','496','519']`.

### Erros e exceções

Este pacote usa a distinção **TypeError vs Exception**: *erros de tipo* indicam uso incorreto da API (ex.: tipo errado); *exceções* indicam dados inválidos ou ineligíveis (ex.: CPF inválido). Você pode capturar classes específicas ou as bases abstratas.

- **CpfCheckDigitsTypeError** (_abstract_) — base para erros de tipo; estende o `TypeError` do PHP
- **CpfCheckDigitsInputTypeError** — entrada não é `string` nem `string[]`
- **CpfCheckDigitsException** (_abstract_) — base para exceções de dados/fluxo; estende `Exception`
- **CpfCheckDigitsInputLengthException** — tamanho após sanitização não é 9–11
- **CpfCheckDigitsInputInvalidException** — entrada ineligível (ex.: dígitos repetidos como `111111111`)

```php
<?php

use Lacus\BrUtils\Cpf\CpfCheckDigits;
use Lacus\BrUtils\Cpf\Exceptions\CpfCheckDigitsException;
use Lacus\BrUtils\Cpf\Exceptions\CpfCheckDigitsInputInvalidException;
use Lacus\BrUtils\Cpf\Exceptions\CpfCheckDigitsInputLengthException;
use Lacus\BrUtils\Cpf\Exceptions\CpfCheckDigitsInputTypeError;

// Tipo de entrada (ex.: inteiro não permitido)
try {
    new CpfCheckDigits(12345678901);
} catch (CpfCheckDigitsInputTypeError $e) {
    echo $e->getMessage();
}

// Tamanho (deve ser 9–11 dígitos após sanitização)
try {
    new CpfCheckDigits('12345678');
} catch (CpfCheckDigitsInputLengthException $e) {
    echo $e->getMessage();
}

// Inválido (ex.: dígitos repetidos)
try {
    new CpfCheckDigits(['999', '999', '999']);
} catch (CpfCheckDigitsInputInvalidException $e) {
    echo $e->getMessage();
}

// Qualquer exceção de dados do pacote
try {
    // código arriscado
} catch (CpfCheckDigitsException $e) {
    // tratar
}
```

### Outros recursos disponíveis

- **`CPF_MIN_LENGTH`**: `9` — constante de classe `CpfCheckDigits::CPF_MIN_LENGTH`, e constante global `Lacus\BrUtils\Cpf\CPF_MIN_LENGTH` quando `cpf-dv.php` é carregado pelo autoload do Composer.
- **`CPF_MAX_LENGTH`**: `11` — constante de classe `CpfCheckDigits::CPF_MAX_LENGTH`, e constante global `Lacus\BrUtils\Cpf\CPF_MAX_LENGTH` quando `cpf-dv.php` é carregado pelo autoload do Composer.

## Algoritmo de cálculo

O pacote calcula os dígitos verificadores do CPF usando o algoritmo oficial brasileiro:

1. **Primeiro dígito (10ª posição):** dígitos 1–9 da base do CPF; pesos 10, 9, 8, 7, 6, 5, 4, 3, 2 (da esquerda para a direita); `resto = 11 - (soma(dígito × peso) % 11)`; resultado é `0` se resto > 9, caso contrário `resto`.
2. **Segundo dígito (11ª posição):** dígitos 1–9 + primeiro dígito verificador; pesos 11, 10, 9, 8, 7, 6, 5, 4, 3, 2 (da esquerda para a direita); mesma fórmula.

## Contribuição e suporte

Contribuições são bem-vindas! Consulte as [Diretrizes de contribuição](https://github.com/LacusSolutions/br-utils-php/blob/main/CONTRIBUTING.md). Se o projeto for útil para você, considere:

- ⭐ Dar uma estrela no repositório
- 🤝 Contribuir com código
- 💡 [Sugerir novas funcionalidades](https://github.com/LacusSolutions/br-utils-php/issues)
- 🐛 [Reportar bugs](https://github.com/LacusSolutions/br-utils-php/issues)

## Licença

Este projeto está sob a licença MIT — veja o arquivo [LICENSE](https://github.com/LacusSolutions/br-utils-php/blob/main/LICENSE).

## Changelog

Veja o [CHANGELOG](https://github.com/LacusSolutions/br-utils-php/blob/main/packages/cpf-dv/CHANGELOG.md) para alterações e histórico de versões.

---

Feito com ❤️ por [Lacus Solutions](https://github.com/LacusSolutions)
