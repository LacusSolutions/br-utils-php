<?php

declare(strict_types=1);

namespace Lacus\Generators\Cnpj\Tests;

use Lacus\Generators\Cnpj\CnpjGenerator;
use PHPUnit\Framework\TestCase;

class CnpjGeneratorTest extends TestCase
{
    private CnpjGenerator $generator;

    protected function setUp(): void
    {
        $this->generator = new CnpjGenerator();
    }

    public function testResultLengthEqualsTo14WithoutFormatting(): void
    {
        for ($i = 0; $i < 25; $i++) {
            $cnpj = $this->generator->generate();

            $this->assertEquals(14, strlen($cnpj));
        }
    }

    public function testResultLengthEqualsTo18WithFormatting(): void
    {
        for ($i = 0; $i < 25; $i++) {
            $cnpj = $this->generator->generate(['format' => true]);

            $this->assertEquals(18, strlen($cnpj));
        }
    }

    public function testGeneratedCnpjIsValidWithoutFormatting(): void
    {
        for ($i = 0; $i < 25; $i++) {
            $cnpj = $this->generator->generate();

            $this->assertTrue($this->isValidCnpj($cnpj));
        }
    }

    public function testGeneratedFormattedCnpjIsValidWithFormatting(): void
    {
        for ($i = 0; $i < 25; $i++) {
            $cnpj = $this->generator->generate(['format' => true]);

            $this->assertTrue($this->isValidCnpj($cnpj));
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
            $cnpj = $this->generator->generate(['prefix' => $prefix]);

            $this->assertTrue($this->isValidCnpj($cnpj));
        }
    }

    public function testFormattedCnpjMatchesPattern(): void
    {
        for ($i = 0; $i < 25; $i++) {
            $cnpj = $this->generator->generate(['format' => true]);

            $this->assertMatchesRegularExpression('/(\d{2}).(\d{3}).(\d{3})\/(\d{4})-(\d{2})/', $cnpj);
        }
    }

    public function testPrefixedValueCannotAcceptStringWithMoreThan12Digits(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->generator->generate(['prefix' => '1234567890123']);
    }

    public function testPrefixedValueCannotAcceptFormattedStringWithMoreThan12Digits(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->generator->generate(['prefix' => '12.345.678/0001-2']);
    }

    private function isValidCnpj(string $cnpj): bool
    {
        // Remove formatting
        $cnpj = preg_replace('/[^0-9]/', '', $cnpj);

        // Check length
        if (strlen($cnpj) !== 14) {
            return false;
        }

        // Check for repeated digits
        if (preg_match('/(\d)\1{13}/', $cnpj)) {
            return false;
        }

        // Calculate first check digit
        $sum = 0;
        $weights = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        for ($i = 0; $i < 12; $i++) {
            $sum += (int)$cnpj[$i] * $weights[$i];
        }
        $remainder = $sum % 11;
        $firstDigit = ($remainder < 2) ? 0 : 11 - $remainder;

        if ((int)$cnpj[12] !== $firstDigit) {
            return false;
        }

        // Calculate second check digit
        $sum = 0;
        $weights = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        for ($i = 0; $i < 13; $i++) {
            $sum += (int)$cnpj[$i] * $weights[$i];
        }
        $remainder = $sum % 11;
        $secondDigit = ($remainder < 2) ? 0 : 11 - $remainder;

        return (int)$cnpj[13] === $secondDigit;
    }
}
