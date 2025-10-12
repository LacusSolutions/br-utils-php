<?php

declare(strict_types=1);

namespace Lacus\CnpjGen\Tests;

use InvalidArgumentException;
use Lacus\CnpjGen\Tests\Utils\ExternalCnpjValidator;

trait CnpjGeneratorTestCases
{
    use ExternalCnpjValidator;

    abstract protected function generate(
        ?bool $format = null,
        ?string $prefix = null,
    ): string;

    public function testResultLengthEqualsTo14NoFormatting(): void
    {
        for ($i = 0; $i < 25; $i++) {
            $cnpj = $this->generate();
            $cnpjSize = strlen($cnpj);

            $this->assertEquals(
                14,
                $cnpjSize,
                "Input: {$cnpj}, Expected: 14, Result: {$cnpjSize}"
            );
        }
    }

    public function testResultLengthEqualsTo18WithFormatting(): void
    {
        for ($i = 0; $i < 25; $i++) {
            $cnpj = $this->generate(true);
            $cnpjSize = strlen($cnpj);

            $this->assertEquals(
                18,
                $cnpjSize,
                "Input: {$cnpj}, Expected: 18, Result: {$cnpjSize}"
            );
        }
    }

    public function testGeneratedCnpjIsValidNoFormatting(): void
    {
        for ($i = 0; $i < 25; $i++) {
            $cnpj = $this->generate();
            $isValid = $this->isValid($cnpj);

            $this->assertTrue($isValid, "Input: {$cnpj}, Expected: true");
        }
    }

    public function testGeneratedFormattedCnpjIsValidWithFormatting(): void
    {
        for ($i = 0; $i < 25; $i++) {
            $cnpj = $this->generate(true);
            $isValid = $this->isValid($cnpj);

            $this->assertTrue($isValid, "Input: {$cnpj}, Expected: true");
        }
    }

    public function testGeneratedCnpjIsValidWithPrefix(): void
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
            '1234567890',
            '12345678900',
            '123456789000',
            '123456780009',
            '12.345.678/0009',
        ];

        foreach ($prefixes as $prefix) {
            $cnpj = $this->generate(false, $prefix);
            $isValid = $this->isValid($cnpj);

            $this->assertTrue($isValid, "Input: {$cnpj}, Expected: true");
        }
    }

    public function testFormattedCnpjMatchesPattern(): void
    {
        for ($i = 0; $i < 25; $i++) {
            $cnpj = $this->generate(true);

            $this->assertMatchesRegularExpression(
                '/(\d{2}).(\d{3}).(\d{3})\/(\d{4})-(\d{2})/',
                $cnpj,
                "Input: {$cnpj}, Expected: ##.###.###/####-##",
            );
        }
    }

    public function testPrefixedValueCannotAcceptStringWithMoreThan12Digits(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->generate(false, '12.345.678/0000-99');

        $this->expectException(InvalidArgumentException::class);
        $this->generate(false, '12345678000099');
    }
}
