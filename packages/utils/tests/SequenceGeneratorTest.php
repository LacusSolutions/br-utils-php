<?php

declare(strict_types=1);

namespace Lacus\Utils\Tests;

use Lacus\Utils\SequenceGenerator;
use Lacus\Utils\SequenceType;
use PHPUnit\Framework\TestCase;

final class SequenceGeneratorTest extends TestCase
{
    public function testNumericGeneratesCorrectLength(): void
    {
        for ($i = 0; $i < 20; $i++) {
            $result = SequenceGenerator::generate(32, SequenceType::Numeric);

            $this->assertSame(32, strlen($result), "Iteration #{$i}");
        }
    }

    public function testNumericContainsOnlyDigits(): void
    {
        for ($i = 0; $i < 50; $i++) {
            $result = SequenceGenerator::generate(100, SequenceType::Numeric);

            $this->assertMatchesRegularExpression('/^\d+$/', $result, "Iteration #{$i}");
        }
    }

    public function testAlphabeticGeneratesCorrectLength(): void
    {
        for ($i = 0; $i < 20; $i++) {
            $result = SequenceGenerator::generate(32, SequenceType::Alphabetic);

            $this->assertSame(32, strlen($result), "Iteration #{$i}");
        }
    }

    public function testAlphabeticContainsOnlyUppercaseLetters(): void
    {
        for ($i = 0; $i < 50; $i++) {
            $result = SequenceGenerator::generate(100, SequenceType::Alphabetic);

            $this->assertMatchesRegularExpression('/^[A-Z]+$/', $result, "Iteration #{$i}");
        }
    }

    public function testAlphabeticDoesNotContainNumbers(): void
    {
        for ($i = 0; $i < 50; $i++) {
            $result = SequenceGenerator::generate(100, SequenceType::Alphabetic);

            $this->assertDoesNotMatchRegularExpression('/\d/', $result, "Iteration #{$i}");
        }
    }

    public function testAlphanumericGeneratesCorrectLength(): void
    {
        for ($i = 0; $i < 20; $i++) {
            $result = SequenceGenerator::generate(32, SequenceType::Alphanumeric);

            $this->assertSame(32, strlen($result), "Iteration #{$i}");
        }
    }

    public function testAlphanumericContainsOnlyDigitsAndUppercaseLetters(): void
    {
        for ($i = 0; $i < 50; $i++) {
            $result = SequenceGenerator::generate(100, SequenceType::Alphanumeric);

            $this->assertMatchesRegularExpression('/^[0-9A-Z]+$/', $result, "Iteration #{$i}");
        }
    }

    public function testAlphanumericDoesNotContainLowercaseLetters(): void
    {
        for ($i = 0; $i < 50; $i++) {
            $result = SequenceGenerator::generate(100, SequenceType::Alphanumeric);

            $this->assertDoesNotMatchRegularExpression('/[a-z]/', $result, "Iteration #{$i}");
        }
    }

    public function testGenerateWithSizeZeroReturnsEmptyString(): void
    {
        $result = SequenceGenerator::generate(0, SequenceType::Numeric);

        $this->assertSame('', $result);
    }
}
