<?php

declare(strict_types=1);

namespace Lacus\BrUtils\Cnpj\Exceptions;

use ReflectionClass;
use TypeError;

/**
 * Base error for all `cnpj-gen` type-related errors.
 *
 * This abstract class extends the native `TypeError` and serves as the base for
 * all type validation errors in the CNPJ generator.
 */
abstract class CnpjGeneratorTypeError extends TypeError
{
    public readonly mixed $actualInput;
    public readonly string $actualType;
    public readonly string $expectedType;

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
        $thisReflection = new ReflectionClass($this);

        return $thisReflection->getShortName();
    }
}
