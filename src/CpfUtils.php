<?php

declare(strict_types=1);

namespace Lacus\CpfUtils;

class CpfUtils
{
    private CpfFormatter $formatter;
    private CpfGenerator $generator;
    private CpfValidator $validator;

    /**
     * @param array<mixed> $formatter
     * @param array<mixed> $generator
     */
    public function __construct(
        array $formatter = [],
        array $generator = [],
    ) {
        $this->formatter = new CpfFormatter(...$formatter);
        $this->generator = new CpfGenerator(...$generator);
        $this->validator = new CpfValidator();
    }

    public function format(
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
        return $this->formatter->format(
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

    public function generate(
        ?bool $format = null,
        ?string $prefix = null,
    ): string {
        return $this->generator->generate(
            $format,
            $prefix,
        );
    }

    public function isValid(string $cpfString): bool
    {
        return $this->validator->isValid($cpfString);
    }

    public function getFormatter(): CpfFormatter
    {
        return $this->formatter;
    }

    public function getGenerator(): CpfGenerator
    {
        return $this->generator;
    }

    public function getValidator(): CpfValidator
    {
        return $this->validator;
    }
}
