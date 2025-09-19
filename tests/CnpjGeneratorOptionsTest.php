<?php

declare(strict_types=1);

namespace Lacus\CnpjGen\Tests;

use Lacus\CnpjGen\CnpjGeneratorOptions;
use PHPUnit\Framework\TestCase;

class CnpjGeneratorOptionsTest extends TestCase
{
    public function testConstructorWithNoParams(): void
    {
        $options = new CnpjGeneratorOptions();

        $this->assertFalse($options->isFormatting());
        $this->assertEquals('', $options->getPrefix());
    }
    public function testConstructorWithAllNullParams(): void
    {
        $options = new CnpjGeneratorOptions(null, null);

        $this->assertFalse($options->isFormatting());
        $this->assertEquals('', $options->getPrefix());
    }

    public function testConstructorWithAllParams(): void
    {
        $options = new CnpjGeneratorOptions(
            true,
            '12345678',
        );

        $this->assertTrue($options->isFormatting());
        $this->assertEquals('12345678', $options->getPrefix());
    }

    public function testConstructorWithMixedNullAndValidValues(): void
    {
        $options = new CnpjGeneratorOptions(
            true,
            null,   // should default to ''
        );

        $this->assertTrue($options->isFormatting());
        $this->assertEquals('', $options->getPrefix());
    }

    public function testMergeReturnsNewInstance(): void
    {
        $originalOptions = new CnpjGeneratorOptions();
        $mergedOptions = $originalOptions->merge();

        $this->assertNotSame($originalOptions, $mergedOptions);
        $this->assertInstanceOf(CnpjGeneratorOptions::class, $mergedOptions);
    }

    public function testMergeWithAllNullsPreservesOriginalValues(): void
    {
        $originalOptions = new CnpjGeneratorOptions(
            true,
            '333666',
        );

        $mergedOptions = $originalOptions->merge(null, null);

        $this->assertTrue($mergedOptions->isFormatting());
        $this->assertEquals('333666', $mergedOptions->getPrefix());
    }

    public function testMergeWithPartialOverrides(): void
    {
        $originalOptions = new CnpjGeneratorOptions(
            true,
            '1234',
        );

        $mergedOptions = $originalOptions->merge(
            null,          // keep original
            '111222333',   // override
        );

        $this->assertTrue($mergedOptions->isFormatting());
        $this->assertEquals('111222333', $mergedOptions->getPrefix());
    }

    public function testSetFormat(): void
    {
        $options = new CnpjGeneratorOptions();

        $options->setFormat(true);
        $this->assertTrue($options->isFormatting());

        $options->setFormat(false);
        $this->assertFalse($options->isFormatting());
    }

    public function testSetPrefixWithFewDigits(): void
    {
        $options = new CnpjGeneratorOptions();

        $options->setPrefix('12345');
        $this->assertEquals('12345', $options->getPrefix());

        $options->setPrefix('8888');
        $this->assertEquals('8888', $options->getPrefix());
    }

    public function testSetPrefixWithNonNumericChars(): void
    {
        $options = new CnpjGeneratorOptions();

        $options->setPrefix('123acb');
        $this->assertEquals('123', $options->getPrefix());

        $options->setPrefix('This is a test');
        $this->assertEquals('', $options->getPrefix());
    }

    public function testSetPrefixThrowsErrorWithTooManyDigits(): void
    {
        $options = new CnpjGeneratorOptions();

        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage('Option "prefix" must be a string containing between 0 and 12 digits.');
        $options->setPrefix('12345678000910');
    }

    public function testSetPrefixThrowsErrorWithInvalidBranchID(): void
    {
        $options = new CnpjGeneratorOptions();

        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage('The branch ID (characters 8 to 11) cannot be "0000".');
        $options->setPrefix('123456780000');
    }
}
