<?php

declare(strict_types=1);

namespace Lacus\CpfVal;

use Lacus\CpfGen\CpfGeneratorVerifierDigit;

class CpfValidator
{
    private CpfGeneratorVerifierDigit $verifierDigit;

    public function __construct()
    {
        $this->verifierDigit = new CpfGeneratorVerifierDigit();
    }

    public function isValid(string $cpfString): bool
    {
        $cpfNumbersString = preg_replace('/[^0-9]/', '', $cpfString);
        $cpfNumbersStringArray = str_split($cpfNumbersString);
        $cpfNumbersArray = array_map('intval', $cpfNumbersStringArray);

        if (count($cpfNumbersArray) !== CPF_LENGTH) {
            return false;
        }

        $firstVerifierDigitIndex = CPF_LENGTH - 2;
        $providedFirstVerifierDigit = $cpfNumbersArray[$firstVerifierDigitIndex];
        $baseFirstVerifierDigitCalculation = array_slice($cpfNumbersArray, 0, $firstVerifierDigitIndex);
        $calculatedFirstVerifierDigit = $this->verifierDigit->calculate($baseFirstVerifierDigitCalculation);

        if ($providedFirstVerifierDigit !== $calculatedFirstVerifierDigit) {
            return false;
        }

        $secondVerifierDigitIndex = CPF_LENGTH - 1;
        $baseSecondVerifierDigitCalculation = array_slice($cpfNumbersArray, 0, $secondVerifierDigitIndex);
        $providedSecondVerifierDigit = $cpfNumbersArray[$secondVerifierDigitIndex];
        $calculatedSecondVerifierDigit = $this->verifierDigit->calculate($baseSecondVerifierDigitCalculation);

        if ($providedSecondVerifierDigit !== $calculatedSecondVerifierDigit) {
            return false;
        }

        return true;
    }
}
