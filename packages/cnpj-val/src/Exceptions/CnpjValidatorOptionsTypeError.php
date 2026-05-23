<?php

declare(strict_types=1);

namespace Lacus\BrUtils\Cnpj\Exceptions;

use Lacus\Utils\TypeDescriber;

/**
 * Error raised when a specific option in the validator configuration has an
 * invalid type. The error message includes the option name, the actual input
 * type and the expected type.
 */
class CnpjValidatorOptionsTypeError extends CnpjValidatorTypeError
{
    public readonly string $optionName;

    /**
     * @param 'caseSensitive'|'type' $optionName
     */
    public function __construct(string $optionName, mixed $actualInput, string $expectedType)
    {
        $actualInputType = TypeDescriber::describe($actualInput);

        parent::__construct(
            $actualInput,
            $actualInputType,
            $expectedType,
            "CNPJ validator option \"{$optionName}\" must be of type {$expectedType}. Got {$actualInputType}.",
        );
        $this->optionName = $optionName;
    }
}
