<?php

declare(strict_types=1);

namespace Lacus\BrUtils\Cnpj\Exceptions;

use Exception;

/**
 * Base exception for all `cnpj-val` rules-related errors.
 *
 * This abstract class extends the native `Exception` and serves as the base
 * for all non-type-related errors in the `CnpjValidator` and its dependencies.
 * It is suitable for validation errors, range errors, and other business logic
 * exceptions that are not strictly type-related.
 */
abstract class CnpjValidatorException extends Exception
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
        $className = static::class;
        $lastBackslashIndex = strrpos($className, '\\');

        if ($lastBackslashIndex === false) {
            return $className;
        }

        return substr($className, $lastBackslashIndex + 1);
    }
}
