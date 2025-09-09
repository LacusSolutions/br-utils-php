<?php

declare(strict_types=1);

namespace Lacus\Formatters\Cnpj;

class CnpjFormatter
{
    public function format(string $cnpj): string
    {
        $cnpj = $this->clean($cnpj);

        if (strlen($cnpj) !== 14) {
            throw new \InvalidArgumentException('CNPJ deve ter 14 dígitos');
        }

        return substr($cnpj, 0, 2) . '.' .
               substr($cnpj, 2, 3) . '.' .
               substr($cnpj, 5, 3) . '/' .
               substr($cnpj, 8, 4) . '-' .
               substr($cnpj, 12, 2);
    }
}
