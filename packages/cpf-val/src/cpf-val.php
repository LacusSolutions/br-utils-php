<?php

declare(strict_types=1);

namespace Lacus\CpfVal;

function cpf_val(string $cpfString): bool
{
    $formatter = new CpfValidator();

    return $formatter->isValid($cpfString);
}
