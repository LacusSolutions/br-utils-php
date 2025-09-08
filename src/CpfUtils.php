<?php

declare(strict_types=1);

namespace Lacus\Cpf\Utils;

/**
 * Utilitários para CPF brasileiro
 */
class CpfUtils
{
    /**
     * Extrai informações de um CPF
     */
    public function extractInfo(string $cpf): array
    {
        $cpf = $this->clean($cpf);

        return [
            'cpf' => $cpf,
            'formatted' => $this->format($cpf),
            'region' => $this->getRegion($cpf),
            'isValid' => $this->isValid($cpf),
        ];
    }

    /**
     * Obtém a região de emissão do CPF
     */
    public function getRegion(string $cpf): string
    {
        $cpf = $this->clean($cpf);
        $regionCode = (int) substr($cpf, 8, 1);

        $regions = [
            0 => 'RS',
            1 => 'DF, GO, MS, MT, TO',
            2 => 'AC, AM, AP, PA, RO, RR',
            3 => 'CE, MA, PI',
            4 => 'AL, PB, PE, RN',
            5 => 'BA, SE',
            6 => 'MG',
            7 => 'ES, RJ',
            8 => 'SP',
            9 => 'PR, SC',
        ];

        return $regions[$regionCode] ?? 'Desconhecida';
    }

    /**
     * Remove formatação de um CPF
     */
    private function clean(string $cpf): string
    {
        return preg_replace('/[^0-9]/', '', $cpf);
    }

    /**
     * Formata um CPF
     */
    private function format(string $cpf): string
    {
        return substr($cpf, 0, 3) . '.' .
               substr($cpf, 3, 3) . '.' .
               substr($cpf, 6, 3) . '-' .
               substr($cpf, 9, 2);
    }

    /**
     * Valida um CPF
     */
    private function isValid(string $cpf): bool
    {
        if (strlen($cpf) !== 11) {
            return false;
        }

        if (preg_match('/^(\d)\1{10}$/', $cpf) === 1) {
            return false;
        }

        return $this->validateDigits($cpf);
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
