<?php

declare(strict_types=1);

namespace Lacus\BrUtils\Cnpj;

use Lacus\BrUtils\Cnpj\Enums\CnpjValidationType;
use Lacus\BrUtils\Cnpj\Exceptions\CnpjValidatorInputTypeError;
use Lacus\BrUtils\Cnpj\Exceptions\CnpjValidatorOptionsTypeError;
use Lacus\BrUtils\Cnpj\Exceptions\CnpjValidatorOptionTypeInvalidException;

/**
 * Helper function to simplify the usage of the {@link CnpjValidator} class.
 *
 * If no options are provided, it validates a CNPJ string or array of strings
 * using default settings. If options are provided, they control case
 * sensitivity and the type of characters to be validated.
 *
 * @param string|list<string> $cnpjInput
 * @param ?CnpjValidatorOptions $options
 * @param ?(CnpjValidationType|'alphanumeric'|'numeric') $type
 * @param ?bool $caseSensitive
 *
 * @throws CnpjValidatorInputTypeError If the input is not a string or an
 *     array of strings.
 * @throws CnpjValidatorOptionsTypeError If any option has an invalid type.
 * @throws CnpjValidatorOptionTypeInvalidException If the `type` option is not one of the allowed values.
 */
function cnpj_val(
    $cnpjInput,
    $options = null,
    $type = null,
    $caseSensitive = null,
): bool {
    static $defaultValidator = null;

    if (!$defaultValidator instanceof CnpjValidator) {
        $defaultValidator = new CnpjValidator();
    }

    return $defaultValidator->isValid($cnpjInput, $options, $type, $caseSensitive);
}
