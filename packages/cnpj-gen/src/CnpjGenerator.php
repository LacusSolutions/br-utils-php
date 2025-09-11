<?php

declare(strict_types=1);

namespace Lacus\CnpjGen;

class CnpjGenerator
{
    private CnpjGeneratorOptions $options;

    public function __construct(
        ?bool $format = null,
        ?string $prefix = null,
    ) {
        $this->options = new CnpjGeneratorOptions(
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

        // TODO: Implement the logic to generate a valid CNPJ
        $generatedCnpj = '';

        return $generatedCnpj;
    }

    public function getOptions(): CnpjGeneratorOptions
    {
        return $this->options;
    }
}
