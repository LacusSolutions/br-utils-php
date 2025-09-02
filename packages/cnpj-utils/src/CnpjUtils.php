<?php

declare(strict_types=1);

namespace Lacus\CnpjUtils;

/**
 * Utilitários para CNPJ brasileiro
 */
class CnpjUtils
{
    /**
     * Extrai informações de um CNPJ
     */
    public function extractInfo(string $cnpj): array
    {
        $cnpj = $this->clean($cnpj);

        return [
            'cnpj' => $cnpj,
            'formatted' => $this->format($cnpj),
            'isValid' => $this->isValid($cnpj),
            'type' => $this->getType($cnpj),
        ];
    }

    /**
     * Obtém o tipo de empresa baseado no CNPJ
     */
    public function getType(string $cnpj): string
    {
        $cnpj = $this->clean($cnpj);
        $typeCode = (int) substr($cnpj, 8, 1);

        $types = [
            0 => 'Matriz',
            1 => 'Filial',
        ];

        return $types[$typeCode] ?? 'Desconhecido';
    }

    /**
     * Remove formatação de um CNPJ
     */
    private function clean(string $cnpj): string
    {
        return preg_replace('/[^0-9]/', '', $cnpj);
    }

    /**
     * Formata um CNPJ
     */
    private function format(string $cnpj): string
    {
        return substr($cnpj, 0, 2) . '.' .
               substr($cnpj, 2, 3) . '.' .
               substr($cnpj, 5, 3) . '/' .
               substr($cnpj, 8, 4) . '-' .
               substr($cnpj, 12, 2);
    }

    /**
     * Valida um CNPJ
     */
    private function isValid(string $cnpj): bool
    {
        if (strlen($cnpj) !== 14) {
            return false;
        }

        if (preg_match('/^(\d)\1{13}$/', $cnpj) === 1) {
            return false;
        }

        return $this->validateDigits($cnpj);
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
