<?php

declare(strict_types=1);

namespace Lacus\CnpjGen;

use InvalidArgumentException;

class CnpjGeneratorOptions
{
    private bool $format;
    private string $prefix;

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
    ): self {
        return new self(
            $format ?? $this->isFormatting(),
            $prefix ?? $this->getPrefix(),
        );
    }

    public function setFormat(bool $value): void
    {
        $this->format = $value;
    }

    public function isFormatting(): bool
    {
        return $this->format;
    }

    public function setPrefix(string $value): void
    {
        $min = 0;
        $max = CNPJ_LENGTH - 2;
        $digitsOnly = preg_replace('/[^0-9]/', '', $value) ?? '';
        $prefixLength = strlen($digitsOnly);

        if ($prefixLength > CNPJ_LENGTH - 2) {
            throw new InvalidArgumentException(
                "Option \"prefix\" must be a string containing between {$min} and {$max} digits."
            );
        }

        if ($prefixLength > 8 && substr($digitsOnly, 8) === '0000') {
            throw new InvalidArgumentException(
                "The branch ID (characters 8 to 11) cannot be \"0000\"."
            );
        }

        $this->prefix = $digitsOnly;
    }

    public function getPrefix(): string
    {
        return $this->prefix;
    }
}
