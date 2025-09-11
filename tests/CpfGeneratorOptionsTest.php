<?php

declare(strict_types=1);

namespace Lacus\CpfGen\Tests;

use Lacus\CpfGen\CpfGeneratorOptions;
use PHPUnit\Framework\TestCase;

class CpfGeneratorOptionsTest extends TestCase
{
    public function testConstructorWithNoParams(): void
    {
        $options = new CpfGeneratorOptions();

        $this->assertFalse($options->isFormatting());
        $this->assertEquals('', $options->getPrefix());
    }
    public function testConstructorWithAllNullParams(): void
    {
        $options = new CpfGeneratorOptions(null, null);

        $this->assertFalse($options->isFormatting());
        $this->assertEquals('', $options->getPrefix());
    }

    public function testConstructorWithAllParams(): void
    {
        $options = new CpfGeneratorOptions(
            true,
            '12345678',
        );

        $this->assertTrue($options->isFormatting());
        $this->assertEquals('12345678', $options->getPrefix());
    }

    public function testConstructorWithMixedNullAndValidValues(): void
    {
        $options = new CpfGeneratorOptions(
            true,
            null,   // should default to ''
        );

        $this->assertTrue($options->isFormatting());
        $this->assertEquals('', $options->getPrefix());
    }

    public function testMergeReturnsNewInstance(): void
    {
        $originalOptions = new CpfGeneratorOptions();
        $mergedOptions = $originalOptions->merge();

        $this->assertNotSame($originalOptions, $mergedOptions);
        $this->assertInstanceOf(CpfGeneratorOptions::class, $mergedOptions);
    }

    public function testMergeWithAllNullsPreservesOriginalValues(): void
    {
        $originalOptions = new CpfGeneratorOptions(
            true,
            '333666',
        );

        $mergedOptions = $originalOptions->merge(null, null);

        $this->assertTrue($mergedOptions->isFormatting());
        $this->assertEquals('333666', $mergedOptions->getPrefix());
    }

    public function testMergeWithPartialOverrides(): void
    {
        $originalOptions = new CpfGeneratorOptions(
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
        $options = new CpfGeneratorOptions();

        $options->setFormat(true);
        $this->assertTrue($options->isFormatting());

        $options->setFormat(false);
        $this->assertFalse($options->isFormatting());
    }

    public function testSetPrefix(): void
    {
        $options = new CpfGeneratorOptions();

        $options->setPrefix('12345');
        $this->assertEquals('12345', $options->getPrefix());

        $options->setPrefix('8888');
        $this->assertEquals('8888', $options->getPrefix());
    }
}
