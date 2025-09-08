<?php

declare(strict_types=1);

namespace Lacus\Cnpj\Gen;

/**
 * Gerador de CNPJ brasileiro válido
 */
class CnpjGenerator
{
    /**
     * Gera um CNPJ válido
     */
    public function generate(): string
    {
        $cnpj = $this->generateRandomDigits(12);
        $cnpj .= $this->calculateFirstDigit($cnpj);
        $cnpj .= $this->calculateSecondDigit($cnpj);

        return $cnpj;
    }

    /**
     * Gera um CNPJ válido formatado
     */
    public function generateFormatted(): string
    {
        $cnpj = $this->generate();
        return $this->format($cnpj);
    }

    /**
     * Gera múltiplos CNPJs válidos
     */
    public function generateMultiple(int $count): array
    {
        $cnpjs = [];
        for ($i = 0; $i < $count; $i++) {
            $cnpjs[] = $this->generate();
        }
        return $cnpjs;
    }

    private function generateRandomDigits(int $length): string
    {
        $digits = '';
        for ($i = 0; $i < $length; $i++) {
            $digits .= random_int(0, 9);
        }
        return $digits;
    }

    private function calculateFirstDigit(string $cnpj): string
    {
        $weights = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        $sum = 0;

        for ($i = 0; $i < 12; $i++) {
            $sum += (int) $cnpj[$i] * $weights[$i];
        }

        $remainder = $sum % 11;
        return $remainder < 2 ? '0' : (string) (11 - $remainder);
    }

    private function calculateSecondDigit(string $cnpj): string
    {
        $weights = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        $sum = 0;

        for ($i = 0; $i < 13; $i++) {
            $sum += (int) $cnpj[$i] * $weights[$i];
        }

        $remainder = $sum % 11;
        return $remainder < 2 ? '0' : (string) (11 - $remainder);
    }

    private function format(string $cnpj): string
    {
        return substr($cnpj, 0, 2) . '.' .
               substr($cnpj, 2, 3) . '.' .
               substr($cnpj, 5, 3) . '/' .
               substr($cnpj, 8, 4) . '-' .
               substr($cnpj, 12, 2);
    }
}
