<?php

declare(strict_types=1);

namespace Lacus\CpfGen;

class CpfGenerator
{
    private CpfGeneratorVerifierDigit $verifierDigit;
    private CpfGeneratorOptions $options;

    public function __construct(
        ?bool $format = null,
        ?string $prefix = null,
    ) {
        $this->verifierDigit = new CpfGeneratorVerifierDigit();
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

        $prefixArray = str_split($actualOptions->getPrefix());
        $prefixNumbers = array_map('intval', $prefixArray);

        $cpfSequence = $this->generatePersonalId($prefixNumbers);
        $cpfSequence[] = $this->verifierDigit->calculate($cpfSequence);
        $cpfSequence[] = $this->verifierDigit->calculate($cpfSequence);
        $cpfGenerated = implode('', $cpfSequence);

        if ($actualOptions->isFormatting()) {
            return $this->format($cpfGenerated);
        }

        return $cpfGenerated;
    }

    public function getOptions(): CpfGeneratorOptions
    {
        return $this->options;
    }

    /**
     * @param array<int> $prefixNumbers
     * @return array<int>
     */
    private function generatePersonalId(array $prefixNumbers): array
    {
        $personalIdLength = 9;

        $personalIdStart = array_slice($prefixNumbers, 0, $personalIdLength);
        $personalIdStartLength = count($personalIdStart);

        $personalIdEnd = array_fill(0, $personalIdLength - $personalIdStartLength, null);
        $personalIdEnd = array_map(fn () => rand(0, 9), $personalIdEnd);

        return array_merge($personalIdStart, $personalIdEnd);
    }

    private function format(string $cpfString): string
    {
        return substr($cpfString, 0, 3) . '.' .
               substr($cpfString, 3, 3) . '.' .
               substr($cpfString, 6, 3) . '-' .
               substr($cpfString, 9, 2);
    }
}
