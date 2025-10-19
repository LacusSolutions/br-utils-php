<?php

declare(strict_types=1);

namespace Lacus\BrUtils\Cpf;

use Closure;

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
    return \Lacus\CpfUtils\cpf_fmt(
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

function cpf_gen(
    ?bool $format = null,
    ?string $prefix = null,
): string {
    return \Lacus\CpfUtils\cpf_gen(
        $format,
        $prefix,
    );
}

function cpf_val(string $cpfString): bool
{
    return \Lacus\CpfUtils\cpf_val($cpfString);
}
