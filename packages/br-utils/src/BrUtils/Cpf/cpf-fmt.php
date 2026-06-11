<?php

declare(strict_types=1);

namespace Lacus\BrUtils\Cpf;

use Closure;
use Exception;
use InvalidArgumentException;

/**
 * Helper function to simplify the usage of the {@see CpfFormatter} class.
 *
 * Formats a CPF string according to the given options. With no options,
 * returns the traditional CPF format (e.g. `123.456.789-10`). Invalid input
 * length is handled by the configured `onFail` callback instead of throwing.
 *
 * @param string $cpfString
 * @param ?bool $escape
 * @param ?bool $hidden
 * @param ?string $hiddenKey
 * @param ?int $hiddenStart
 * @param ?int $hiddenEnd
 * @param ?string $dotKey
 * @param ?string $dashKey
 * @param ?Closure(mixed, Exception): string $onFail
 *
 * @throws InvalidArgumentException If any option has an invalid type.
 */
function cpf_fmt(
    string $cpfString,
    ?bool $escape = null,
    ?bool $hidden = null,
    ?string $hiddenKey = null,
    ?int $hiddenStart = null,
    ?int $hiddenEnd = null,
    ?string $dotKey = null,
    ?string $dashKey = null,
    ?Closure $onFail = null,
): string {
    return \Lacus\CpfFmt\cpf_fmt(
        $cpfString,
        $escape,
        $hidden,
        $hiddenKey,
        $hiddenStart,
        $hiddenEnd,
        $dotKey,
        $dashKey,
        $onFail,
    );
}
