<?php

declare(strict_types=1);

namespace Lacus\CnpjUtils;

use Closure;

function cnpj_fmt(
    string $cnpjString,
    ?bool $escape = null,
    ?bool $hidden = null,
    ?string $hiddenKey = null,
    ?int $hiddenStart = null,
    ?int $hiddenEnd = null,
    ?string $dotKey = null,
    ?string $slashKey = null,
    ?string $dashKey = null,
    ?Closure $onFail = null,
): string {
    return \Lacus\CnpjFmt\cnpj_fmt(
        $cnpjString,
        $escape,
        $hidden,
        $hiddenKey,
        $hiddenStart,
        $hiddenEnd,
        $dotKey,
        $slashKey,
        $dashKey,
        $onFail,
    );
}

function cnpj_gen(
    ?bool $format = null,
    ?string $prefix = null,
): string {
    return \Lacus\CnpjGen\cnpj_gen(
        $format,
        $prefix,
    );
}

function cnpj_val(string $cnpjString): bool
{
    return \Lacus\CnpjVal\cnpj_val($cnpjString);
}
