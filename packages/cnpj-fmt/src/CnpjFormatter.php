<?php

declare(strict_types=1);

namespace Lacus\Cnpj\Fmt;

/**
 * Formatador de CNPJ brasileiro
 */
class CnpjFormatter
{
    /**
     * Formata um CNPJ com máscara (XX.XXX.XXX/XXXX-XX)
     */
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

    /**
     * Remove formatação de um CNPJ
     */
    public function clean(string $cnpj): string
    {
        return preg_replace('/[^0-9]/', '', $cnpj);
    }

    /**
     * Verifica se um CNPJ está formatado
     */
    public function isFormatted(string $cnpj): bool
    {
        return preg_match('/^\d{2}\.\d{3}\.\d{3}\/\d{4}-\d{2}$/', $cnpj) === 1;
    }
}
