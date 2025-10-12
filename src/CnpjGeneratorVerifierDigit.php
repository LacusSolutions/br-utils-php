<?php

declare(strict_types=1);

namespace Lacus\CnpjGen;

use InvalidArgumentException;

class CnpjGeneratorVerifierDigit
{
    /**
     * @param array<int> $cnpjSequence
     */
    public function calculate(array $cnpjSequence): int
    {
        $min = CNPJ_LENGTH - 2;
        $max = CNPJ_LENGTH - 1;
        $sequenceLength = count($cnpjSequence);

        if ($sequenceLength < $min || $sequenceLength > $max) {
            throw new InvalidArgumentException(
                "To calculate the verifier digit, the CNPJ sequence must be between {$min} and {$max} digits long, but got {$sequenceLength} digits (\""
                . implode('', $cnpjSequence)
                . "\").",
            );
        }

        $factor = 2;
        $reversedSequence = array_reverse($cnpjSequence);
        $sum = array_reduce($reversedSequence, function ($acc, $num) use (&$factor): int {
            $result = ($num * $factor) + $acc;
            $factor = $factor === 9 ? 2 : $factor + 1;

            return $result;
        }, 0);
        $remainder = $sum % 11;

        return $remainder < 2 ? 0 : 11 - $remainder;
    }
}
