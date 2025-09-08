<?php

declare(strict_types=1);

namespace Lacus\Cnpj\Val;

/**
 * Validador de CNPJ brasileiro
 */
class CnpjValidator
{
    /**
     * Valida se um CNPJ é válido
     */
    public function isValid(string $cnpj): bool
    {
        $cnpj = $this->clean($cnpj);

        if (strlen($cnpj) !== 14) {
            return false;
        }

        if ($this->hasRepeatedDigits($cnpj)) {
            return false;
        }

        return $this->validateDigits($cnpj);
    }

    /**
     * Remove formatação de um CNPJ
     */
    public function clean(string $cnpj): string
    {
        return preg_replace('/[^0-9]/', '', $cnpj);
    }

    /**
     * Verifica se todos os dígitos são iguais
     */
    private function hasRepeatedDigits(string $cnpj): bool
    {
        return preg_match('/^(\d)\1{13}$/', $cnpj) === 1;
    }

    /**
     * Valida os dígitos verificadores
     */
    private function validateDigits(string $cnpj): bool
    {
        $firstDigit = $this->calculateFirstDigit(substr($cnpj, 0, 12));
        $secondDigit = $this->calculateSecondDigit(substr($cnpj, 0, 13));

        return $cnpj[12] === $firstDigit && $cnpj[13] === $secondDigit;
    }

    /**
     * Calcula o primeiro dígito verificador
     */
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

    /**
     * Calcula o segundo dígito verificador
     */
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
}
