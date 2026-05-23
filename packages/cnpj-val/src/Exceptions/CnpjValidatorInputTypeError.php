<?php

declare(strict_types=1);

namespace Lacus\BrUtils\Cnpj\Exceptions;

use Lacus\Utils\TypeDescriber;

/**
 * Error raised when the input provided to the CNPJ validator is not of the
 * expected type. The error message includes both the actual input type and the
 * expected type.
 */
class CnpjValidatorInputTypeError extends CnpjValidatorTypeError
{
    public function __construct(mixed $actualInput, string $expectedType)
    {
        $actualInputType = TypeDescriber::describe($actualInput);

        parent::__construct(
            $actualInput,
            $actualInputType,
            $expectedType,
            "CNPJ input must be of type {$expectedType}. Got {$actualInputType}.",
        );
    }
}
