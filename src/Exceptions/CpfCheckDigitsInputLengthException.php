<?php

declare(strict_types=1);

namespace Lacus\BrUtils\Cpf\Exceptions;

/**
 * Error raised when the input (after optional processing) does not have the
 * required length to calculate the check digits. A valid CPF input must
 * contain between 9 and 11 numeric characters. The error message
 * distinguishes between the original input and the evaluated one (which strips
 * punctuation characters).
 */
class CpfCheckDigitsInputLengthException extends CpfCheckDigitsException
{
    /** @var string|list<string> */
    public string|array $actualInput;
    public string $evaluatedInput;
    public int $minExpectedLength;
    public int $maxExpectedLength;

    /** @param string|list<string> $actualInput */
    public function __construct(
        string|array $actualInput,
        string $evaluatedInput,
        int $minExpectedLength,
        int $maxExpectedLength,
    ) {
        $fmtActual = is_string($actualInput)
          ? "\"{$actualInput}\""
          : json_encode($actualInput, JSON_THROW_ON_ERROR);
        $fmtEvaluated = $actualInput === $evaluatedInput
            ? (string) strlen($evaluatedInput)
            : strlen($evaluatedInput) . ' in "' . $evaluatedInput . '"';

        parent::__construct("CPF input {$fmtActual} does not contain {$minExpectedLength} to {$maxExpectedLength} digits. Got {$fmtEvaluated}.");
        $this->actualInput = $actualInput;
        $this->evaluatedInput = $evaluatedInput;
        $this->minExpectedLength = $minExpectedLength;
        $this->maxExpectedLength = $maxExpectedLength;
    }
}
