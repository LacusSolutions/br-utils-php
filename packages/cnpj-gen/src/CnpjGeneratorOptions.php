<?php

declare(strict_types=1);

namespace Lacus\CnpjGen;

const CNPJ_LENGTH = 14;

class CnpjGeneratorOptions
{
    private array $options;

    public function __construct(
        ?bool $format = null,
        ?string $prefix = null,
    ) {
        $this->setFormat($format ?? false);
        $this->setPrefix($prefix ?? '');
    }

    public function merge(
        ?bool $format = null,
        ?string $prefix = null,
    ): self
    {
        return new self(
            $format ?? $this->isFormatting(),
            $prefix ?? $this->getPrefix(),
        );
    }

    public function setFormat(bool $value): void
    {
        $this->options['format'] = $value;
    }

    public function isFormatting(): bool
    {
        return $this->options['format'];
    }

    public function setPrefix(string $value): void
    {
        $this->options['prefix'] = $value;
    }

    public function getPrefix(): string
    {
        return $this->options['prefix'];
    }
}
