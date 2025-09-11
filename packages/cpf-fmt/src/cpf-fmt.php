<?php

declare(strict_types=1);

namespace Lacus\CpfFmt;

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
    $formatter = new CpfFormatter(
        $escape,
        $hidden,
        $hiddenKey,
        $hiddenStart,
        $hiddenEnd,
        $dotKey,
        $dashKey,
        $onFail,
    );

    return $formatter->format($cpfString);
}
