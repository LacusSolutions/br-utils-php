<?php

declare(strict_types=1);

namespace Lacus\CnpjVal;

class CnpjValidator
{
    public function isValid(string $cnpjString): bool
    {
        // TODO: Implement the logic to isValid a valid CNPJ
        return $cnpjString !== '';
    }
}
