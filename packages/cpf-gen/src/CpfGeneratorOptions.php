<?php

declare(strict_types=1);

namespace Lacus\CpfGen;

const CPF_LENGTH = 11;

class CpfGeneratorOptions
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
        $min = 0;
        $max = CPF_LENGTH - 2;
        $digitsOnly = preg_replace('/[^0-9]/', '', $value);
        $prefixLength = strlen($digitsOnly);

        if ($prefixLength > $max) {
            throw new \TypeError(
                'Option "prefix" must be a string containing between '
                . $min . ' and '
                . $max . ' digits.',
            );
        }

        $this->options['prefix'] = $digitsOnly;
    }

    public function getPrefix(): string
    {
        return $this->options['prefix'];
    }
}
