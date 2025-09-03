<?php

declare(strict_types=1);

namespace Lacus\CpfGen;

/**
 * Gerador de CPF brasileiro válido
 */
class CpfGenerator
{
    /**
     * Gera um CPF válido
     */
    public function generate(): string
    {
        $cpf = $this->generateRandomDigits(9);
        $cpf .= $this->calculateFirstDigit($cpf);
        $cpf .= $this->calculateSecondDigit($cpf);

        return $cpf;
    }

    /**
     * Gera um CPF válido formatado
     */
    public function generateFormatted(): string
    {
        $cpf = $this->generate();
        return $this->format($cpf);
    }

    /**
     * Gera múltiplos CPFs válidos
     */
    public function generateMultiple(int $count): array
    {
        $cpfs = [];
        for ($i = 0; $i < $count; $i++) {
            $cpfs[] = $this->generate();
        }
        return $cpfs;
    }

    private function generateRandomDigits(int $length): string
    {
        $digits = '';
        for ($i = 0; $i < $length; $i++) {
            $digits .= random_int(0, 9);
        }
        return $digits;
    }

    private function calculateFirstDigit(string $cpf): string
    {
        $sum = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum += (int) $cpf[$i] * (10 - $i);
        }

        $remainder = $sum % 11;
        return $remainder < 2 ? '0' : (string) (11 - $remainder);
    }

    private function calculateSecondDigit(string $cpf): string
    {
        $sum = 0;
        for ($i = 0; $i < 10; $i++) {
            $sum += (int) $cpf[$i] * (11 - $i);
        }

        $remainder = $sum % 11;
        return $remainder < 2 ? '0' : (string) (11 - $remainder);
    }

    private function format(string $cpf): string
    {
        return substr($cpf, 0, 3) . '.' .
               substr($cpf, 3, 3) . '.' .
               substr($cpf, 6, 3) . '-' .
               substr($cpf, 9, 2);
    }
}
