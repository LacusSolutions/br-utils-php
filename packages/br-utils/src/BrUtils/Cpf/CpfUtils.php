<?php

declare(strict_types=1);

namespace Lacus\BrUtils\Cpf;

use Closure;
use InvalidArgumentException;
use Lacus\CpfUtils\CpfUtils as BaseCpfUtils;

class CpfUtils extends BaseCpfUtils
{
    /**
     * @param CpfFormatterOptions|array{
     *     escape?: bool|null,
     *     hidden?: bool|null,
     *     hiddenKey?: string|null,
     *     hiddenStart?: int|null,
     *     hiddenEnd?: int|null,
     *     dotKey?: string|null,
     *     dashKey?: string|null,
     *     onFail?: Closure|null,
     * } $formatter
     * @param CpfGeneratorOptions|array{
     *     format?: bool|null,
     *     prefix?: string|null,
     * } $generator
     *
     * @throws InvalidArgumentException If any option has an invalid value.
     */
    public function __construct(
        CpfFormatterOptions|array $formatter = [],
        CpfGeneratorOptions|array $generator = [],
    ) {
        $formatterOptions = $formatter instanceof CpfFormatterOptions
            ? $formatter
            : new CpfFormatterOptions(...$formatter);

        $generatorOptions = $generator instanceof CpfGeneratorOptions
            ? $generator
            : new CpfGeneratorOptions(...$generator);

        parent::__construct(
            formatter: [
                'escape' => $formatterOptions->isEscaped(),
                'hidden' => $formatterOptions->isHidden(),
                'hiddenKey' => $formatterOptions->getHiddenKey(),
                'hiddenStart' => $formatterOptions->getHiddenStart(),
                'hiddenEnd' => $formatterOptions->getHiddenEnd(),
                'dotKey' => $formatterOptions->getDotKey(),
                'dashKey' => $formatterOptions->getDashKey(),
                'onFail' => $formatterOptions->getOnFail(),
            ],
            generator: [
                'format' => $generatorOptions->isFormatting(),
                'prefix' => $generatorOptions->getPrefix(),
            ],
        );
    }
}
