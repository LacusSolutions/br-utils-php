<?php

declare(strict_types=1);

namespace Lacus\CpfUtils;

function cpf_fmt(
    string $cpfString,
    ?bool $escape = null,
    ?bool $hidden = null,
    ?string $hiddenKey = null,
    ?int $hiddenStart = null,
    ?int $hiddenEnd = null,
    ?string $dotKey = null,
    ?string $dashKey = null,
    ?callable $onFail = null,
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

function cpf_gen(
    ?bool $format = null,
    ?string $prefix = null,
): string {
    return \Lacus\CpfGen\cpf_gen(
        $format,
        $prefix,
    );
}

function cpf_val(string $cpfString): bool
{
    return \Lacus\CpfVal\cpf_val($cpfString);
}
