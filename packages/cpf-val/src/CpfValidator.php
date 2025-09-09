<?php

declare(strict_types=1);

namespace Lacus\Validators\Cpf;

/**
 * Validador de CPF brasileiro
 */
class CpfValidator
{
    /**
     * Valida se um CPF é válido
     */
    public function isValid(string $cpf): bool
    {
        $cpf = $this->clean($cpf);

        if (strlen($cpf) !== 11) {
            return false;
        }

        if ($this->hasRepeatedDigits($cpf)) {
            return false;
        }

        return $this->validateDigits($cpf);
    }

    /**
     * Remove formatação de um CPF
     */
    public function clean(string $cpf): string
    {
        return preg_replace('/[^0-9]/', '', $cpf);
    }

    /**
     * Verifica se todos os dígitos são iguais
     */
    private function hasRepeatedDigits(string $cpf): bool
    {
        return preg_match('/^(\d)\1{10}$/', $cpf) === 1;
    }

    /**
     * Valida os dígitos verificadores
     */
    private function validateDigits(string $cpf): bool
    {
        $firstDigit = $this->calculateFirstDigit(substr($cpf, 0, 9));
        $secondDigit = $this->calculateSecondDigit(substr($cpf, 0, 10));

        return $cpf[9] === $firstDigit && $cpf[10] === $secondDigit;
    }

    /**
     * Calcula o primeiro dígito verificador
     */
    private function calculateFirstDigit(string $cpf): string
    {
        $sum = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum += (int) $cpf[$i] * (10 - $i);
        }

        $remainder = $sum % 11;
        return $remainder < 2 ? '0' : (string) (11 - $remainder);
    }

    /**
     * Calcula o segundo dígito verificador
     */
    private function calculateSecondDigit(string $cpf): string
    {
        $sum = 0;
        for ($i = 0; $i < 10; $i++) {
            $sum += (int) $cpf[$i] * (11 - $i);
        }

        $remainder = $sum % 11;
        return $remainder < 2 ? '0' : (string) (11 - $remainder);
    }
}
