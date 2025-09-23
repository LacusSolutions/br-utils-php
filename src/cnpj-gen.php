<?php

declare(strict_types=1);

namespace Lacus\CnpjGen;

const CNPJ_LENGTH = 14;

function cnpj_gen(
    ?bool $format = null,
    ?string $prefix = null,
): string {
    $formatter = new CnpjGenerator(
        $format,
        $prefix,
    );

    return $formatter->generate();
}
