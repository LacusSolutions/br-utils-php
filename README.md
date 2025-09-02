# Lacus BR Utils Monorepo

Monorepo para utilitários brasileiros - CPF, CNPJ e ferramentas relacionadas.

## Estrutura

```
php/
├── packages/           # Pacotes individuais
│   ├── cpf-fmt/       # Formatação de CPF
│   ├── cpf-gen/       # Geração de CPF
│   ├── cpf-val/       # Validação de CPF
│   ├── cpf-utils/     # Utilitários de CPF
│   ├── cnpj-fmt/      # Formatação de CNPJ
│   ├── cnpj-gen/      # Geração de CNPJ
│   ├── cnpj-val/      # Validação de CNPJ
│   ├── cnpj-utils/    # Utilitários de CNPJ
│   └── br-utils/      # Pacote principal com todas as funcionalidades
├── src/                # Código compartilhado
├── tests/              # Testes compartilhados
├── composer.json       # Configuração principal
├── phpunit.xml.dist    # Configuração de testes
├── phpstan.neon       # Configuração de análise estática
├── .php-cs-fixer.dist.php # Configuração de formatação
└── infection.json.dist # Configuração de mutação testing
```

## Pacotes Disponíveis

### CPF
- **lacus/cpf-fmt**: Formatação de CPF brasileiro
- **lacus/cpf-gen**: Geração de CPF válido
- **lacus/cpf-val**: Validação de CPF brasileiro
- **lacus/cpf-utils**: Utilitários para CPF

### CNPJ
- **lacus/cnpj-fmt**: Formatação de CNPJ brasileiro
- **lacus/cnpj-gen**: Geração de CNPJ válido
- **lacus/cnpj-val**: Validação de CNPJ brasileiro
- **lacus/cnpj-utils**: Utilitários para CNPJ

### Geral
- **lacus/br-utils**: Pacote principal com todas as funcionalidades

## Instalação

```bash
# Instalar dependências do monorepo
composer install

# Instalar um pacote específico
composer require lacus/cpf-fmt
composer require lacus/cnpj-val
composer require lacus/br-utils
```

## Desenvolvimento

```bash
# Executar testes
composer test

# Análise estática
composer analyse

# Formatação de código
composer fix

# Verificar formatação
composer check

# Mutação testing
composer infection
```

## Requisitos

- PHP 8.1 ou superior
- Composer

## Licença

MIT
