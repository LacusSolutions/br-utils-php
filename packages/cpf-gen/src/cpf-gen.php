<?php

declare(strict_types=1);

namespace Lacus\CpfGen;

const CPF_LENGTH = 11;

function cpf_gen(
    ?bool $format = null,
    ?string $prefix = null,
): string {
    $formatter = new CpfGenerator(
        $format,
        $prefix,
    );

    return $formatter->generate();
}
