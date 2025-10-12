<?php

declare(strict_types=1);

namespace Lacus\CpfGen\Tests;

use InvalidArgumentException;
use Lacus\CpfGen\Tests\Utils\ExternalCpfValidator;

trait CpfGeneratorTestCases
{
    use ExternalCpfValidator;

    abstract protected function generate(
        ?bool $format = null,
        ?string $prefix = null,
    ): string;

    public function testResultLengthEqualsTo11NoFormatting(): void
    {
        for ($i = 0; $i < 25; $i++) {
            $cpf = $this->generate();
            $cpfSize = strlen($cpf);

            $this->assertEquals(
                11,
                $cpfSize,
                "Input: {$cpf}, Expected: 11, Result: {$cpfSize}"
            );
        }
    }

    public function testResultLengthEqualsTo14WithFormatting(): void
    {
        for ($i = 0; $i < 25; $i++) {
            $cpf = $this->generate(true);
            $cpfSize = strlen($cpf);

            $this->assertEquals(
                14,
                $cpfSize,
                "Input: {$cpf}, Expected: 14, Result: {$cpfSize}"
            );
        }
    }

    public function testGeneratedCpfIsValidNoFormatting(): void
    {
        for ($i = 0; $i < 25; $i++) {
            $cpf = $this->generate();
            $isValid = $this->isValid($cpf);

            $this->assertTrue($isValid, "Input: {$cpf}, Expected: true");
        }
    }

    public function testGeneratedFormattedCpfIsValidWithFormatting(): void
    {
        for ($i = 0; $i < 25; $i++) {
            $cpf = $this->generate(true);
            $isValid = $this->isValid($cpf);

            $this->assertTrue($isValid, "Input: {$cpf}, Expected: true");
        }
    }

    public function testGeneratedCpfIsValidWithPrefix(): void
    {
        $prefixes = [
            '1',
            '12',
            '123',
            '1234',
            '12345',
            '123456',
            '1234567',
            '12345678',
            '123456789',
            '123.456.789',
        ];

        foreach ($prefixes as $prefix) {
            $cpf = $this->generate(false, $prefix);
            $isValid = $this->isValid($cpf);

            $this->assertTrue($isValid, "Input: {$cpf}, Expected: true");
        }
    }

    public function testFormattedCpfMatchesPattern(): void
    {
        for ($i = 0; $i < 25; $i++) {
            $cpf = $this->generate(true);

            $this->assertMatchesRegularExpression(
                '/(\d{3}).(\d{3}).(\d{3})-(\d{2})/',
                $cpf,
                "Input: {$cpf}, Expected: ###.###.###-##",
            );
        }
    }

    public function testPrefixedValueCannotAcceptStringWithMoreThan9Digits(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->generate(false, '1234567890');

        $this->expectException(InvalidArgumentException::class);
        $this->generate(false, '123.456.789-0');
    }
}
