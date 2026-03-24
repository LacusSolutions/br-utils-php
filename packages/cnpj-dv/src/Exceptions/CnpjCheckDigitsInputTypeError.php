<?php

declare(strict_types=1);

namespace Lacus\BrUtils\Cnpj\Exceptions;

use Lacus\Utils\TypeDescriber;

/**
 * Error raised when the input provided to `CnpjCheckDigits` is not of the
 * expected type (string or string[]). The error message includes both the
 * actual type of the input and the expected type.
 */
class CnpjCheckDigitsInputTypeError extends CnpjCheckDigitsTypeError
{
    public function __construct(mixed $actualInput, string $expectedType)
    {
        $actualType = TypeDescriber::describe($actualInput);

        parent::__construct(
            $actualInput,
            $actualType,
            $expectedType,
            "CNPJ input must be of type {$expectedType}. Got {$actualType}.",
        );
    }
}
