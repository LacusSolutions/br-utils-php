<?php

declare(strict_types=1);

namespace Lacus\BrUtils\Cpf;

use InvalidArgumentException;

/**
 * Helper function to simplify the usage of the {@see CpfGenerator} class.
 *
 * Generates a valid 11-digits CPF (prefix, random body and computed check
 * digits). With default options the result is unformatted numeric; pass
 * `format: true` for `000.000.000-00` style output.
 *
 * @param ?bool $format
 * @param ?string $prefix
 *
 * @throws InvalidArgumentException If any option has an invalid type.
 */
function cpf_gen(
    ?bool $format = null,
    ?string $prefix = null,
): string {
    return \Lacus\CpfGen\cpf_gen(
        $format,
        $prefix,
    );
}
