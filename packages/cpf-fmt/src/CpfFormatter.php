<?php

declare(strict_types=1);

namespace Lacus\Formatters\Cpf;

/**
 * Formatador de CPF brasileiro
 */
class CpfFormatter
{
    /**
     * Formata um CPF com máscara (XXX.XXX.XXX-XX)
     */
    public function format(string $cpf): string
    {
        $cpf = $this->clean($cpf);

        if (strlen($cpf) !== 11) {
            throw new \InvalidArgumentException('CPF deve ter 11 dígitos');
        }

        return substr($cpf, 0, 3) . '.' .
               substr($cpf, 3, 3) . '.' .
               substr($cpf, 6, 3) . '-' .
               substr($cpf, 9, 2);
    }

    /**
     * Remove formatação de um CPF
     */
    public function clean(string $cpf): string
    {
        return preg_replace('/[^0-9]/', '', $cpf);
    }

    /**
     * Verifica se um CPF está formatado
     */
    public function isFormatted(string $cpf): bool
    {
        return preg_match('/^\d{3}\.\d{3}\.\d{3}-\d{2}$/', $cpf) === 1;
    }
}
