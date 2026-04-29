<?php

declare(strict_types=1);

namespace Lacus\BrUtils\Cnpj\Exceptions;

/**
 * Exception raised when the CNPJ option `prefix` is invalid. This is a business
 * logic exception and it is highly recommended that users of the library catch
 * it and handle it appropriately.
 */
class CnpjGeneratorOptionPrefixInvalidException extends CnpjGeneratorException
{
    public readonly string $actualInput;
    public readonly string $reason;

    public function __construct(string $actualInput, string $reason)
    {
        parent::__construct("CNPJ generator option \"prefix\" with value \"{$actualInput}\" is invalid. {$reason}");
        $this->actualInput = $actualInput;
        $this->reason = $reason;
    }
}
