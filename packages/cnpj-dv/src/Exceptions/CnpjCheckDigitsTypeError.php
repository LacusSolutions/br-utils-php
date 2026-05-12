<?php

declare(strict_types=1);

namespace Lacus\BrUtils\Cnpj\Exceptions;

use TypeError;

/**
 * Base error for all `cnpj-dv` type-related errors.
 *
 * This abstract class extends the native `TypeError` and serves as the base for
 * all type validation errors in the `CnpjCheckDigits`.
 */
abstract class CnpjCheckDigitsTypeError extends TypeError
{
    public mixed $actualInput;
    public string $actualType;
    public string $expectedType;

    public function __construct(
        mixed $actualInput,
        string $actualType,
        string $expectedType,
        string $message,
    ) {
        parent::__construct($message);
        $this->actualInput = $actualInput;
        $this->actualType = $actualType;
        $this->expectedType = $expectedType;
    }

    /**
     * Get the short class name of the error instance.
     */
    public function getName(): string
    {
        $className = static::class;
        $lastBackslashIndex = strrpos($className, '\\');

        if ($lastBackslashIndex === false) {
            return $className;
        }

        return substr($className, $lastBackslashIndex + 1);
    }
}
