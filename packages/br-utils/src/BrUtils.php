<?php

declare(strict_types=1);

namespace Lacus;

use Closure;
use InvalidArgumentException;
use Lacus\BrUtils\Cnpj\CnpjFormatterOptions;
use Lacus\BrUtils\Cnpj\CnpjGeneratorOptions;
use Lacus\BrUtils\Cnpj\CnpjUtils;
use Lacus\BrUtils\Cnpj\CnpjValidatorOptions;
use Lacus\BrUtils\Cnpj\Enums\CnpjType;
use Lacus\BrUtils\Cnpj\Enums\CnpjValidationType;
use Lacus\BrUtils\Cnpj\Exceptions\CnpjFormatterException;
use Lacus\BrUtils\Cpf\CpfFormatterOptions;
use Lacus\BrUtils\Cpf\CpfGeneratorOptions;
use Lacus\BrUtils\Cpf\CpfUtils;

/**
 * Utility class for Brazilian-related data, like CPF (Cadastro de Pessoa
 * Física) and CNPJ (Cadastro Nacional da Pessoa Jurídica). Provides a unified
 * interface for formatting, generating, and validating data.
 *
 * @property-read CpfUtils $cpf
 * @property-read CnpjUtils $cnpj
 */
class BrUtils
{
    private CpfUtils $cpfUtils;
    private CnpjUtils $cnpjUtils;

    /**
     * Creates a new instance with configurable CPF and CNPJ utilities.
     *
     * Each `$cpf` / `$cnpj` argument accepts either a pre-built utils instance
     * or a configuration array spread into the corresponding utils
     * constructor. Within that array, each resource key (`formatter`,
     * `generator` and `validator` for CNPJ) accepts either an options object
     * or an associative array of option values.
     *
     * @param CpfUtils|array{
     *     formatter?: CpfFormatterOptions|array{
     *         escape?: bool|null,
     *         hidden?: bool|null,
     *         hiddenKey?: string|null,
     *         hiddenStart?: int|null,
     *         hiddenEnd?: int|null,
     *         dotKey?: string|null,
     *         dashKey?: string|null,
     *         onFail?: Closure|null,
     *     },
     *     generator?: CpfGeneratorOptions|array{
     *         format?: bool|null,
     *         prefix?: string|null,
     *     },
     * } $cpf
     * @param CnpjUtils|array{
     *     formatter?: CnpjFormatterOptions|array{
     *         hidden?: bool|null,
     *         hiddenKey?: string|null,
     *         hiddenStart?: int|null,
     *         hiddenEnd?: int|null,
     *         dotKey?: string|null,
     *         slashKey?: string|null,
     *         dashKey?: string|null,
     *         escape?: bool|null,
     *         encode?: bool|null,
     *         onFail?: (Closure(mixed, CnpjFormatterException): string|null),
     *     },
     *     generator?: CnpjGeneratorOptions|array{
     *         format?: bool|null,
     *         prefix?: string|null,
     *         type?: CnpjType|null,
     *     },
     *     validator?: CnpjValidatorOptions|array{
     *         caseSensitive?: bool|null,
     *         type?: CnpjValidationType|'alphanumeric'|'numeric'|null,
     *     },
     * } $cnpj
     */
    public function __construct(
        CpfUtils|array $cpf = [],
        CnpjUtils|array $cnpj = [],
    ) {
        $this->cpfUtils = $cpf instanceof CpfUtils
            ? $cpf
            : new CpfUtils(...$cpf);
        $this->cnpjUtils = $cnpj instanceof CnpjUtils
            ? $cnpj
            : new CnpjUtils(...$cnpj);
    }

    /**
     * Property-style access to the data-related utils.
     */
    public function __get(string $name): mixed
    {
        return match ($name) {
            'cpf'   => $this->getCpfUtils(),
            'cnpj'  => $this->getCnpjUtils(),
            default => throw new InvalidArgumentException("Unknown property: {$name}"),
        };
    }

    /**
     * Returns the CPF utilities instance.
     */
    public function getCpfUtils(): CpfUtils
    {
        return $this->cpfUtils;
    }

    /**
     * Returns the CNPJ utilities instance.
     */
    public function getCnpjUtils(): CnpjUtils
    {
        return $this->cnpjUtils;
    }
}
