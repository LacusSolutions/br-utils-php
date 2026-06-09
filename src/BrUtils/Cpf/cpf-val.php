<?php

declare(strict_types=1);

namespace Lacus\BrUtils\Cpf;

/**
 * Helper function to simplify the usage of the {@link CpfValidator} class.
 *
 * @param string $cpfString
 */
function cpf_val(string $cpfString): bool
{
    return \Lacus\CpfVal\cpf_val($cpfString);
}
