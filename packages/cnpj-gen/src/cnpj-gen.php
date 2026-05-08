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
 * Formats a CNPJ string according to the given options. With no options,
 * returns the traditional CNPJ format (e.g. `12.345.678/0009-10`). Invalid
 * input length is handled by the configured `onFail` callback instead of
 * throwing.
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
