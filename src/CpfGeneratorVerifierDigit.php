<?php

declare(strict_types=1);

namespace Lacus\CpfGen;

class CpfGeneratorVerifierDigit
{
    public function calculate(array $cpfSequence): int
    {
        $min = CPF_LENGTH - 2;
        $max = CPF_LENGTH - 1;
        $sequenceLength = count($cpfSequence);

        if ($sequenceLength < $min || $sequenceLength > $max) {
            throw new \TypeError(
                'To calculate the verifier digit, the CPF sequence must be between '
                . $min . ' and '
                . $max . ' digits long, but got '
                . $sequenceLength . ' digits ("'
                . implode('', $cpfSequence) . '").',
            );
        }

        $factor = $sequenceLength + 1;
        $sum = array_reduce($cpfSequence, function ($acc, $num) use (&$factor): int {
            $result = $acc + ($num * $factor);
            $factor--;

            return $result;
        }, 0);
        $remainder = 11 - ($sum % 11);

        return $remainder > 9 ? 0 : $remainder;
    }
}
