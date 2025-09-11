<?php

declare(strict_types=1);

namespace Lacus\CpfVal;

class CpfValidator
{
    public function isValid(string $cpfString): bool
    {
        // TODO: Implement the logic to isValid a valid CPF
        return $cpfString !== '';
    }
}
