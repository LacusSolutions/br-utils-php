<?php

declare(strict_types=1);

namespace Lacus\CpfGen\Tests;

use Lacus\CpfGen\CpfGenerator;
use PHPUnit\Framework\TestCase;

class CpfGeneratorTest extends TestCase
{
    private CpfGenerator $generator;

    protected function setUp(): void
    {
        $this->generator = new CpfGenerator();
    }

    public function testResultLengthEqualsTo11WithoutFormatting(): void
    {
        for ($i = 0; $i < 25; $i++) {
            $cpf = $this->generator->generate();

            $this->assertEquals(11, strlen($cpf));
        }
    }

    public function testResultLengthEqualsTo14WithFormatting(): void
    {
        for ($i = 0; $i < 25; $i++) {
            $cpf = $this->generator->generate(['format' => true]);

            $this->assertEquals(14, strlen($cpf));
        }
    }

    public function testGeneratedCpfIsValidWithoutFormatting(): void
    {
        for ($i = 0; $i < 25; $i++) {
            $cpf = $this->generator->generate();

            $this->assertTrue($this->isValidCpf($cpf));
        }
    }

    public function testGeneratedFormattedCpfIsValidWithFormatting(): void
    {
        for ($i = 0; $i < 25; $i++) {
            $cpf = $this->generator->generate(['format' => true]);

            $this->assertTrue($this->isValidCpf($cpf));
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
            $cpf = $this->generator->generate(['prefix' => $prefix]);

            $this->assertTrue($this->isValidCpf($cpf));
        }
    }

    public function testFormattedCpfMatchesPattern(): void
    {
        for ($i = 0; $i < 25; $i++) {
            $cpf = $this->generator->generate(['format' => true]);

            $this->assertMatchesRegularExpression('/(\d{3}).(\d{3}).(\d{3})-(\d{2})/', $cpf);
        }
    }

    public function testPrefixedValueCannotAcceptStringWithMoreThan9Digits(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->generator->generate(['prefix' => '1234567890']);
    }

    public function testPrefixedValueCannotAcceptFormattedStringWithMoreThan9Digits(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->generator->generate(['prefix' => '123.456.789-0']);
    }

    private function isValidCpf(string $cpf): bool
    {
        // Remove formatting
        $cpf = preg_replace('/[^0-9]/', '', $cpf);

        // Check length
        if (strlen($cpf) !== 11) {
            return false;
        }

        // Check for repeated digits
        if (preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }

        // Calculate first check digit
        $sum = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum += (int)$cpf[$i] * (10 - $i);
        }
        $remainder = $sum % 11;
        $firstDigit = ($remainder < 2) ? 0 : 11 - $remainder;

        if ((int)$cpf[9] !== $firstDigit) {
            return false;
        }

        // Calculate second check digit
        $sum = 0;
        for ($i = 0; $i < 10; $i++) {
            $sum += (int)$cpf[$i] * (11 - $i);
        }
        $remainder = $sum % 11;
        $secondDigit = ($remainder < 2) ? 0 : 11 - $remainder;

        return (int)$cpf[10] === $secondDigit;
    }
}
