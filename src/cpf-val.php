<?php

declare(strict_types=1);

namespace Lacus\CpfVal;

const CPF_LENGTH = 11;

function cpf_val(string $cpfString): bool
{
    $formatter = new CpfValidator();

    return $formatter->isValid($cpfString);
}
