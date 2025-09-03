<?php

declare(strict_types=1);

namespace Lacus\BrUtils;

/**
 * Utilitários brasileiros - CPF, CNPJ e ferramentas relacionadas
 */
class BrUtils
{
    /**
     * Valida se um documento é um CPF válido
     */
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

    /**
     * Valida se um documento é um CNPJ válido
     */
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

    /**
     * Formata um CPF
     */
    public function formatCpf(string $cpf): string
    {
        $cpf = $this->cleanCpf($cpf);

        if (strlen($cpf) !== 11) {
            throw new \InvalidArgumentException('CPF deve ter 11 dígitos');
        }

        return substr($cpf, 0, 3) . '.' .
               substr($cpf, 3, 3) . '.' .
               substr($cpf, 6, 3) . '-' .
               substr($cpf, 9, 2);
    }

    /**
     * Formata um CNPJ
     */
    public function formatCnpj(string $cnpj): string
    {
        $cnpj = $this->cleanCnpj($cnpj);

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
     * Remove formatação de um CPF
     */
    private function cleanCpf(string $cpf): string
    {
        return preg_replace('/[^0-9]/', '', $cpf);
    }

    /**
     * Remove formatação de um CNPJ
     */
    private function cleanCnpj(string $cnpj): string
    {
        return preg_replace('/[^0-9]/', '', $cnpj);
    }

    /**
     * Verifica se todos os dígitos são iguais
     */
    private function hasRepeatedDigits(string $document): bool
    {
        $length = strlen($document);
        return preg_match('/^(\d)\1{' . ($length - 1) . '}$/', $document) === 1;
    }

    /**
     * Valida os dígitos verificadores do CPF
     */
    private function validateCpfDigits(string $cpf): bool
    {
        $firstDigit = $this->calculateCpfFirstDigit(substr($cpf, 0, 9));
        $secondDigit = $this->calculateCpfSecondDigit(substr($cpf, 0, 10));

        return $cpf[9] === $firstDigit && $cpf[10] === $secondDigit;
    }

    /**
     * Valida os dígitos verificadores do CNPJ
     */
    private function validateCnpjDigits(string $cnpj): bool
    {
        $firstDigit = $this->calculateCnpjFirstDigit(substr($cnpj, 0, 12));
        $secondDigit = $this->calculateCnpjSecondDigit(substr($cnpj, 0, 13));

        return $cnpj[12] === $firstDigit && $cnpj[13] === $secondDigit;
    }

    /**
     * Calcula o primeiro dígito verificador do CPF
     */
    private function calculateCpfFirstDigit(string $cpf): string
    {
        $sum = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum += (int) $cpf[$i] * (10 - $i);
        }

        $remainder = $sum % 11;
        return $remainder < 2 ? '0' : (string) (11 - $remainder);
    }

    /**
     * Calcula o segundo dígito verificador do CPF
     */
    private function calculateCpfSecondDigit(string $cpf): string
    {
        $sum = 0;
        for ($i = 0; $i < 10; $i++) {
            $sum += (int) $cpf[$i] * (11 - $i);
        }

        $remainder = $sum % 11;
        return $remainder < 2 ? '0' : (string) (11 - $remainder);
    }

    /**
     * Calcula o primeiro dígito verificador do CNPJ
     */
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

    /**
     * Calcula o segundo dígito verificador do CNPJ
     */
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
