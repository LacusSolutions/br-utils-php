<?php

declare(strict_types=1);

namespace Lacus\BrUtils\Tests\Cpf;

use Lacus\BrUtils\Cpf\CpfGenerator;
use Lacus\CpfGen\Tests\CpfGeneratorTestCases;
use PHPUnit\Framework\TestCase;

class CpfGeneratorClassTest extends TestCase
{
    use CpfGeneratorTestCases;

    private CpfGenerator $generator;

    protected function setUp(): void
    {
        $this->generator = new CpfGenerator();
    }

    protected function generate(
        ?bool $format = null,
        ?string $prefix = null,
    ): string {
        return $this->generator->generate(
            $format,
            $prefix,
        );
    }
}
