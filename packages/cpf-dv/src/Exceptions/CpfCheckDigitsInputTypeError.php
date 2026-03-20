<?php

declare(strict_types=1);

namespace Lacus\BrUtils\Cpf\Exceptions;

use Lacus\Utils\TypeDescriber;

/**
 * Error raised when the input provided to `CpfCheckDigits` is not of the
 * expected type (string or string[]). The error message includes both the
 * actual type of the input and the expected type.
 */
class CpfCheckDigitsInputTypeError extends CpfCheckDigitsTypeError
{
    public function __construct(mixed $actualInput, string $expectedType)
    {
        $actualType = TypeDescriber::describe($actualInput);

        parent::__construct(
            $actualInput,
            $actualType,
            $expectedType,
            "CPF input must be of type {$expectedType}. Got {$actualType}.",
        );
    }
}
