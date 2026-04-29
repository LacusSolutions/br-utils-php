<?php

declare(strict_types=1);

namespace Lacus\BrUtils\Cnpj\Exceptions;

use Exception;
use ReflectionClass;

/**
 * Base exception for all `cnpj-gen` rules-related errors.
 *
 * This abstract class extends the native `Exception` and serves as the base for
 * all non-type-related errors in the `CnpjGenerator` and its dependencies. It is
 * suitable for validation errors, range errors, and other business logic
 * exceptions that are not strictly type-related.
 */
abstract class CnpjGeneratorException extends Exception
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }

    /**
     * Get the short class name of the exception instance.
     */
    public function getName(): string
    {
        $thisReflection = new ReflectionClass($this);

        return $thisReflection->getShortName();
    }
}
