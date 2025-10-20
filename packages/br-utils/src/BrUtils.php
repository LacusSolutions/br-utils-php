<?php

declare(strict_types=1);

namespace Lacus\BrUtils;

use InvalidArgumentException;

/**
 * @property-read CpfUtils $cpf
 * @property-read CnpjUtils $cnpj
 */
class BrUtils
{
    private CnpjUtils $cnpjUtils;
    private CpfUtils $cpfUtils;

    /**
     * @param array{
     *   formatter?: array{
     *     escape?: bool,
     *     hidden?: bool,
     *     hiddenKey?: string,
     *     hiddenStart?: int,
     *     hiddenEnd?: int,
     *     dotKey?: string,
     *     slashKey?: string,
     *     dashKey?: string,
     *     onFail?: Closure,
     *   },
     *   generator?: array{
     *     format?: bool,
     *     prefix?: string,
     *   },
     * } $cnpj
     * @param array{
     *     formatter?: array{
     *       escape?: bool,
     *       hidden?: bool,
     *       hiddenKey?: string,
     *       hiddenStart?: int,
     *       hiddenEnd?: int,
     *       dotKey?: string,
     *       dashKey?: string,
     *       onFail?: Closure,
     *     },
     *     generator?: array{
     *       format?: bool,
     *       prefix?: string,
     *     },
     * } $cpf
     */
    public function __construct(
        array $cnpj = [],
        array $cpf = [],
    ) {
        $this->cpfUtils = new CpfUtils(...$cpf);
        $this->cnpjUtils = new CnpjUtils(...$cnpj);
    }

    public function __get(string $name): mixed
    {
        return match ($name) {
            'cpf' => $this->getCpfUtils(),
            'cnpj' => $this->getCnpjUtils(),
            default => throw new InvalidArgumentException("Property {$name} not found"),
        };
    }

    public function getCpfUtils(): CpfUtils
    {
        return $this->cpfUtils;
    }

    public function getCnpjUtils(): CnpjUtils
    {
        return $this->cnpjUtils;
    }
}
