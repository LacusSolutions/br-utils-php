<?php

declare(strict_types=1);

namespace Lacus\CnpjVal;

function cnpj_val(string $cnpjString): bool
{
    $formatter = new CnpjValidator();

    return $formatter->isValid($cnpjString);
}
