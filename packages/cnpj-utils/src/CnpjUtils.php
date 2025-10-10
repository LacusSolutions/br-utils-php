<?php

declare(strict_types=1);

namespace Lacus\CnpjUtils;

class CnpjUtils
{
    private CnpjFormatter $formatter;
    private CnpjGenerator $generator;
    private CnpjValidator $validator;

    public function __construct(
        array $formatter = [],
        array $generator = [],
    ) {
        $this->formatter = new CnpjFormatter(...$formatter);
        $this->generator = new CnpjGenerator(...$generator);
        $this->validator = new CnpjValidator();
    }

    public function format(
        string $cnpjString,
        ?bool $escape = null,
        ?bool $hidden = null,
        ?string $hiddenKey = null,
        ?int $hiddenStart = null,
        ?int $hiddenEnd = null,
        ?string $dotKey = null,
        ?string $slashKey = null,
        ?string $dashKey = null,
        ?callable $onFail = null,
    ): string {
        return $this->formatter->format(
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

    public function generate(
        ?bool $format = null,
        ?string $prefix = null,
    ): string {
        return $this->generator->generate(
            $format,
            $prefix,
        );
    }

    public function isValid(string $cnpjString): bool
    {
        return $this->validator->isValid($cnpjString);
    }

    public function getFormatter(): CnpjFormatter
    {
        return $this->formatter;
    }

    public function getGenerator(): CnpjGenerator
    {
        return $this->generator;
    }

    public function getValidator(): CnpjValidator
    {
        return $this->validator;
    }
}
