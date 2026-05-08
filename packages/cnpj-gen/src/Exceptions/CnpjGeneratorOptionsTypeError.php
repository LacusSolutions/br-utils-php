<?php

declare(strict_types=1);

namespace Lacus\BrUtils\Cnpj\Exceptions;

use Lacus\Utils\TypeDescriber;

/**
 * Error raised when a specific option in the generator configuration has an
 * invalid type. The error message includes the option name, the actual input
 * type and the expected type.
 */
class CnpjGeneratorOptionsTypeError extends CnpjGeneratorTypeError
{
    public readonly string $optionName;

    /**
     * @param 'format'|'prefix'|'type' $optionName
     */
    public function __construct(string $optionName, mixed $actualInput, string $expectedType)
    {
        $actualInputType = TypeDescriber::describe($actualInput);

        parent::__construct(
            $actualInput,
            $actualInputType,
            $expectedType,
            "CNPJ generator option \"{$optionName}\" must be of type {$expectedType}. Got {$actualInputType}.",
        );
        $this->optionName = $optionName;
    }
}
