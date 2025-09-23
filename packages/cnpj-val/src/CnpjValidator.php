<?php

declare(strict_types=1);

namespace Lacus\CnpjVal;

use Lacus\CnpjGen\CnpjGeneratorVerifierDigit;

class CnpjValidator
{
    private CnpjGeneratorVerifierDigit $verifierDigit;

    public function __construct()
    {
        $this->verifierDigit = new CnpjGeneratorVerifierDigit();
    }

    public function isValid(string $cnpjString): bool
    {
        $cnpjNumbersString = preg_replace('/[^0-9]/', '', $cnpjString);
        $cnpjNumbersStringArray = str_split($cnpjNumbersString);
        $cnpjNumbersArray = array_map('intval', $cnpjNumbersStringArray);

        if (count($cnpjNumbersArray) !== CNPJ_LENGTH) {
            return false;
        }


        $firstVerifierDigitIndex = CNPJ_LENGTH - 2;
        $providedFirstVerifierDigit = $cnpjNumbersArray[$firstVerifierDigitIndex];
        $baseFirstVerifierDigitCalculation = array_slice($cnpjNumbersArray, 0, $firstVerifierDigitIndex);
        $calculatedFirstVerifierDigit = $this->verifierDigit->calculate($baseFirstVerifierDigitCalculation);

        if ($providedFirstVerifierDigit !== $calculatedFirstVerifierDigit) {
            return false;
        }

        $secondVerifierDigitIndex = CNPJ_LENGTH - 1;
        $baseSecondVerifierDigitCalculation = array_slice($cnpjNumbersArray, 0, $secondVerifierDigitIndex);
        $providedSecondVerifierDigit = $cnpjNumbersArray[$secondVerifierDigitIndex];
        $calculatedSecondVerifierDigit = $this->verifierDigit->calculate($baseSecondVerifierDigitCalculation);

        if ($providedSecondVerifierDigit !== $calculatedSecondVerifierDigit) {
            return false;
        }

        return true;
    }
}
