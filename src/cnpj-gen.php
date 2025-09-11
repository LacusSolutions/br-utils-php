<?php

declare(strict_types=1);

namespace Lacus\CnpjGen;

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
