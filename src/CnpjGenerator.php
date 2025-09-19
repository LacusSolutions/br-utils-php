<?php

declare(strict_types=1);

namespace Lacus\CnpjGen;

class CnpjGenerator
{
    private CnpjGeneratorVerifierDigit $verifierDigit;
    private CnpjGeneratorOptions $options;

    public function __construct(
        ?bool $format = null,
        ?string $prefix = null,
    ) {
        $this->verifierDigit = new CnpjGeneratorVerifierDigit();
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

        $prefixArray = str_split($actualOptions->getPrefix());
        $prefixNumbers = array_map('intval', $prefixArray);

        $businessId = $this->generateBusinessId($prefixNumbers);
        $branchId = $this->generateBranchId($prefixNumbers);
        $cnpjSequence = array_merge($businessId, $branchId);
        $cnpjSequence[] = $this->verifierDigit->calculate($cnpjSequence);
        $cnpjSequence[] = $this->verifierDigit->calculate($cnpjSequence);
        $cnpjGenerated = implode('', $cnpjSequence);

        if ($actualOptions->isFormatting()) {
            return $this->format($cnpjGenerated);
        }

        return $cnpjGenerated;
    }

    public function getOptions(): CnpjGeneratorOptions
    {
        return $this->options;
    }

    private function generateBusinessId(array $prefixNumbers): array
    {
        $businessIdLength = 8;

        $businessIdStart = array_slice($prefixNumbers, 0, $businessIdLength);
        $businessIdStartLength = count($businessIdStart);

        $businessIdEnd = array_fill(0, $businessIdLength - $businessIdStartLength, null);
        $businessIdEnd = array_map(fn () => rand(0, 9), $businessIdEnd);

        return array_merge($businessIdStart, $businessIdEnd);
    }

    private function generateBranchId(array $prefixNumbers): array
    {
        $branchIdLength = 4;

        $branchIdStart = array_slice($prefixNumbers, 8, $branchIdLength);
        $branchIdStartLength = count($branchIdStart);

        $branchIdEnd = array_fill(0, $branchIdLength - $branchIdStartLength, 0);
        $branchIdEndLength = count($branchIdEnd);

        if ($branchIdEndLength > 0) {
            $branchIdEnd[$branchIdEndLength - 1] = rand(1, 9);
        }

        return array_merge($branchIdStart, $branchIdEnd);
    }

    private function format(string $cnpjString): string
    {
        return substr($cnpjString, 0, 2) . '.' .
               substr($cnpjString, 2, 3) . '.' .
               substr($cnpjString, 5, 3) . '/' .
               substr($cnpjString, 8, 4) . '-' .
               substr($cnpjString, 12, 2);
    }
}
