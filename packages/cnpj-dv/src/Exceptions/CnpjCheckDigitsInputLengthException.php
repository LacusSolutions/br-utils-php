<?php

declare(strict_types=1);

namespace Lacus\BrUtils\Cnpj\Exceptions;

/**
 * Error raised when the input (after optional processing) does not have the
 * required length to calculate the check digits. A valid CNPJ input must
 * contain between 12 and 14 alphanumeric characters. The error message
 * distinguishes between the original input and the evaluated one (which strips
 * non-alphanumeric characters).
 */
class CnpjCheckDigitsInputLengthException extends CnpjCheckDigitsException
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

        parent::__construct("CNPJ input {$fmtActual} does not contain {$minExpectedLength} to {$maxExpectedLength} digits. Got {$fmtEvaluated}.");
        $this->actualInput = $actualInput;
        $this->evaluatedInput = $evaluatedInput;
        $this->minExpectedLength = $minExpectedLength;
        $this->maxExpectedLength = $maxExpectedLength;
    }
}
