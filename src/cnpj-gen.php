<?php

declare(strict_types=1);

namespace Lacus\BrUtils\Cnpj;

use Lacus\BrUtils\Cnpj\Enums\CnpjType;
use Lacus\BrUtils\Cnpj\Exceptions\CnpjGeneratorOptionPrefixInvalidException;
use Lacus\BrUtils\Cnpj\Exceptions\CnpjGeneratorOptionsTypeError;
use Lacus\BrUtils\Cnpj\Exceptions\CnpjGeneratorOptionTypeInvalidException;

/**
 * Helper function to simplify the usage of the {@see CnpjGenerator} class.
 *
 * Generates a valid 14-character CNPJ (prefix, random body for the chosen
 * {@see CnpjType}, and computed check digits). With default options the result
 * is unformatted alphanumeric; pass `format: true` for `00.000.000/0000-00`
 * style output.
 *
 * @param ?CnpjGeneratorOptions $options
 * @param ?bool $format
 * @param ?string $prefix
 * @param ?(CnpjType|'alphanumeric'|'alphabetic'|'numeric') $type
 *
 * @throws CnpjGeneratorOptionsTypeError If any option has an invalid type.
 * @throws CnpjGeneratorOptionPrefixInvalidException If the `prefix` option
 *   contains an invalid combination of characters.
 * @throws CnpjGeneratorOptionTypeInvalidException If the `type` option is
 *   not one of the allowed values.
 */
function cnpj_gen(
    $options = null,
    $format = null,
    $prefix = null,
    $type = null,
): string {
    $generator = new CnpjGenerator(
        $options,
        $format,
        $prefix,
        $type,
    );

    return $generator->generate();
}
