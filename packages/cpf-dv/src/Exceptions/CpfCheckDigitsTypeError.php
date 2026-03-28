<?php

declare(strict_types=1);

namespace Lacus\BrUtils\Cpf\Exceptions;

use ReflectionClass;
use TypeError;

/**
 * Base error for all `cpf-dv` type-related errors.
 *
 * This abstract class extends the native `TypeError` and serves as the base for
 * all type validation errors in the `CpfCheckDigits`.
 */
abstract class CpfCheckDigitsTypeError extends TypeError
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
     * Get the name of the class instance name.
     */
    public function getName(): string
    {
        $thisReflection = new ReflectionClass($this);

        return $thisReflection->getShortName();
    }
}
