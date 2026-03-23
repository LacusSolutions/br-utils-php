<?php

declare(strict_types=1);

namespace Lacus\BrUtils\Cnpj\Exceptions;

/**
 * Exception raised when the CNPJ input contains invalid character sequences,
 * like all digits are repeated. This is a business logic exception and it is
 * highly recommended that users of the library catch it and handle it
 * appropriately.
 */
class CnpjCheckDigitsInputInvalidException extends CnpjCheckDigitsException
{
    /** @var string|list<string> */
    public string|array $actualInput;
    public string $reason;

    /** @param string|list<string> $actualInput */
    public function __construct(string|array $actualInput, string $reason)
    {
        $fmtActual = is_string($actualInput)
            ? "\"{$actualInput}\""
            : json_encode($actualInput, JSON_THROW_ON_ERROR);

        parent::__construct("CNPJ input {$fmtActual} is invalid. {$reason}");
        $this->actualInput = $actualInput;
        $this->reason = $reason;
    }
}
