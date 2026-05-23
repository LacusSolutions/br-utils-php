<?php

declare(strict_types=1);

namespace Lacus\BrUtils\Cnpj\Exceptions;

/**
 * Exception raised when the CNPJ option `type` is given a value that is not
 * one of the allowed values. The option must be one of the enumerated values
 * of CnpjType. This is a business logic exception and it is highly recommended
 * that users of the library catch it and handle it appropriately.
 */
class CnpjValidatorOptionTypeInvalidException extends CnpjValidatorException
{
    public readonly string $actualInput;

    /** @var list<string> */
    public readonly array $expectedValues;

    /**
     * @param list<string> $expectedValues
     */
    public function __construct(string $actualInput, array $expectedValues)
    {
        $expectedValuesString = implode('", "', $expectedValues);

        parent::__construct("CNPJ validator option \"type\" accepts only the following values: \"{$expectedValuesString}\". Got \"{$actualInput}\".");
        $this->actualInput = $actualInput;
        $this->expectedValues = $expectedValues;
    }
}
