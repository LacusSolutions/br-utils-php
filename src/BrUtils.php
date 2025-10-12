<?php

declare(strict_types=1);

namespace Lacus\BrUtils;

use InvalidArgumentException;

class BrUtils
{
    public function isValidCpf(string $cpf): bool
    {
        $cpf = $this->cleanCpf($cpf);

        if (strlen($cpf) !== 11) {
            return false;
        }

        if ($this->hasRepeatedDigits($cpf)) {
            return false;
        }

        return $this->validateCpfDigits($cpf);
    }

    public function isValidCnpj(string $cnpj): bool
    {
        $cnpj = $this->cleanCnpj($cnpj);

        if (strlen($cnpj) !== 14) {
            return false;
        }

        if ($this->hasRepeatedDigits($cnpj)) {
            return false;
        }

        return $this->validateCnpjDigits($cnpj);
    }

    public function formatCpf(string $cpf): string
    {
        $cpf = $this->cleanCpf($cpf);

        if (strlen($cpf) !== 11) {
            throw new InvalidArgumentException('CPF deve ter 11 dígitos');
        }

        return substr($cpf, 0, 3) . '.' .
               substr($cpf, 3, 3) . '.' .
               substr($cpf, 6, 3) . '-' .
               substr($cpf, 9, 2);
    }

    public function formatCnpj(string $cnpj): string
    {
        $cnpj = $this->cleanCnpj($cnpj);

        if (strlen($cnpj) !== 14) {
            throw new InvalidArgumentException('CNPJ deve ter 14 dígitos');
        }

        return substr($cnpj, 0, 2) . '.' .
               substr($cnpj, 2, 3) . '.' .
               substr($cnpj, 5, 3) . '/' .
               substr($cnpj, 8, 4) . '-' .
               substr($cnpj, 12, 2);
    }

    private function cleanCpf(string $cpf): string
    {
        return preg_replace('/[^0-9]/', '', $cpf);
    }

    private function cleanCnpj(string $cnpj): string
    {
        return preg_replace('/[^0-9]/', '', $cnpj);
    }

    private function hasRepeatedDigits(string $document): bool
    {
        $length = strlen($document);
        return preg_match('/^(\d)\1{' . ($length - 1) . '}$/', $document) === 1;
    }

    private function validateCpfDigits(string $cpf): bool
    {
        $firstDigit = $this->calculateCpfFirstDigit(substr($cpf, 0, 9));
        $secondDigit = $this->calculateCpfSecondDigit(substr($cpf, 0, 10));

        return $cpf[9] === $firstDigit && $cpf[10] === $secondDigit;
    }

    private function validateCnpjDigits(string $cnpj): bool
    {
        $firstDigit = $this->calculateCnpjFirstDigit(substr($cnpj, 0, 12));
        $secondDigit = $this->calculateCnpjSecondDigit(substr($cnpj, 0, 13));

        return $cnpj[12] === $firstDigit && $cnpj[13] === $secondDigit;
    }

    private function calculateCpfFirstDigit(string $cpf): string
    {
        $sum = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum += (int) $cpf[$i] * (10 - $i);
        }

        $remainder = $sum % 11;
        return $remainder < 2 ? '0' : (string) (11 - $remainder);
    }

    private function calculateCpfSecondDigit(string $cpf): string
    {
        $sum = 0;
        for ($i = 0; $i < 10; $i++) {
            $sum += (int) $cpf[$i] * (11 - $i);
        }

        $remainder = $sum % 11;
        return $remainder < 2 ? '0' : (string) (11 - $remainder);
    }

    private function calculateCnpjFirstDigit(string $cnpj): string
    {
        $weights = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        $sum = 0;

        for ($i = 0; $i < 12; $i++) {
            $sum += (int) $cnpj[$i] * $weights[$i];
        }

        $remainder = $sum % 11;
        return $remainder < 2 ? '0' : (string) (11 - $remainder);
    }

    private function calculateCnpjSecondDigit(string $cnpj): string
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
