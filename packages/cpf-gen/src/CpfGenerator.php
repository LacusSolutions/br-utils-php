<?php

declare(strict_types=1);

namespace Lacus\CpfGen;

class CpfGenerator
{
    private CpfGeneratorOptions $options;

    public function __construct(
        ?bool $format = null,
        ?string $prefix = null,
    ) {
        $this->options = new CpfGeneratorOptions(
            $format,
            $prefix,
        );
    }

    public function generate(
        ?bool $format = null,
        ?string $prefix = null,
    ): string {
        $actualOptions = $this->getOptions()->merge(
            $format,
            $prefix,
        );

        // TODO: Implement the logic to generate a valid CPF
        $generatedCpf = '';

        return $generatedCpf;
    }

    public function getOptions(): CpfGeneratorOptions
    {
        return $this->options;
    }
}
