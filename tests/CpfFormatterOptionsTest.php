<?php

declare(strict_types=1);

namespace Lacus\CpfFmt\Tests;

use Lacus\CpfFmt\CpfFormatterOptions;
use PHPUnit\Framework\TestCase;

class CpfFormatterOptionsTest extends TestCase
{
    public function testConstructorWithAllNullParameters(): void
    {
        $options = new CpfFormatterOptions(
            null, // escape
            null, // hidden
            null, // hiddenKey
            null, // hiddenStart
            null, // hiddenEnd
            null, // dotKey
            null, // dashKey
            null  // onFail
        );

        $this->assertFalse($options->isEscaped());
        $this->assertFalse($options->isHidden());
        $this->assertEquals('*', $options->getHiddenKey());
        $this->assertEquals(3, $options->getHiddenStart());
        $this->assertEquals(10, $options->getHiddenEnd());
        $this->assertEquals('.', $options->getDotKey());
        $this->assertEquals('-', $options->getDashKey());
        $this->assertIsCallable($options->getOnFail());
    }

    public function testConstructorWithAllParameters(): void
    {
        $onFailCallback = function (string $value): string {
            return 'ERROR: ' . $value;
        };

        $options = new CpfFormatterOptions(
            true,  // escape
            true,  // hidden
            '#',   // hiddenKey
            1,     // hiddenStart
            8,     // hiddenEnd
            '|',   // dotKey
            '~',   // dashKey
            $onFailCallback
        );

        $this->assertTrue($options->isEscaped());
        $this->assertTrue($options->isHidden());
        $this->assertEquals('#', $options->getHiddenKey());
        $this->assertEquals(1, $options->getHiddenStart());
        $this->assertEquals(8, $options->getHiddenEnd());
        $this->assertEquals('|', $options->getDotKey());
        $this->assertEquals('~', $options->getDashKey());
        $this->assertSame($onFailCallback, $options->getOnFail());
    }

    public function testMergeWithPartialOverrides(): void
    {
        $originalOptions = new CpfFormatterOptions(
            false, // escape
            false, // hidden
            '*',   // hiddenKey
            3,     // hiddenStart
            10,    // hiddenEnd
            '.',   // dotKey
            '-',   // dashKey
            null   // onFail
        );

        $mergedOptions = $originalOptions->merge(
            true,  // escape (override)
            null,  // hidden (keep original)
            '#',   // hiddenKey (override)
            null,  // hiddenStart (keep original)
            null,  // hiddenEnd (keep original)
            '|',   // dotKey (override)
            null,  // dashKey (keep original)
            null   // onFail (keep original)
        );

        $this->assertTrue($mergedOptions->isEscaped());
        $this->assertFalse($mergedOptions->isHidden());
        $this->assertEquals('#', $mergedOptions->getHiddenKey());
        $this->assertEquals(3, $mergedOptions->getHiddenStart());
        $this->assertEquals(10, $mergedOptions->getHiddenEnd());
        $this->assertEquals('|', $mergedOptions->getDotKey());
        $this->assertEquals('-', $mergedOptions->getDashKey());
    }

    public function testSetEscape(): void
    {
        $options = new CpfFormatterOptions(null, null, null, null, null, null, null, null);

        $options->setEscape(true);
        $this->assertTrue($options->isEscaped());

        $options->setEscape(false);
        $this->assertFalse($options->isEscaped());
    }

    public function testSetHide(): void
    {
        $options = new CpfFormatterOptions(null, null, null, null, null, null, null, null);

        $options->setHide(true);
        $this->assertTrue($options->isHidden());

        $options->setHide(false);
        $this->assertFalse($options->isHidden());
    }

    public function testSetHiddenKey(): void
    {
        $options = new CpfFormatterOptions(null, null, null, null, null, null, null, null);

        $options->setHiddenKey('X');
        $this->assertEquals('X', $options->getHiddenKey());

        $options->setHiddenKey('?');
        $this->assertEquals('?', $options->getHiddenKey());
    }

    public function testSetHiddenRangeWithValidValues(): void
    {
        $options = new CpfFormatterOptions(null, null, null, null, null, null, null, null);

        $options->setHiddenRange(0, 10);
        $this->assertEquals(0, $options->getHiddenStart());
        $this->assertEquals(10, $options->getHiddenEnd());

        $options->setHiddenRange(5, 7);
        $this->assertEquals(5, $options->getHiddenStart());
        $this->assertEquals(7, $options->getHiddenEnd());
    }

    public function testSetHiddenRangeWithSwappedValues(): void
    {
        $options = new CpfFormatterOptions(null, null, null, null, null, null, null, null);

        // Test that start > end gets swapped
        $options->setHiddenRange(8, 2);
        $this->assertEquals(2, $options->getHiddenStart());
        $this->assertEquals(8, $options->getHiddenEnd());
    }

    public function testSetHiddenRangeWithInvalidStart(): void
    {
        $options = new CpfFormatterOptions(null, null, null, null, null, null, null, null);

        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage('Option "hiddenStart" must be an integer between 0 and 10.');
        $options->setHiddenRange(-1, 5);
    }

    public function testSetHiddenRangeWithInvalidEnd(): void
    {
        $options = new CpfFormatterOptions(null, null, null, null, null, null, null, null);

        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage('Option "hiddenRange.end" must be an integer between 0 and 10.');
        $options->setHiddenRange(5, 11);
    }

    public function testSetHiddenRangeWithStartTooHigh(): void
    {
        $options = new CpfFormatterOptions(null, null, null, null, null, null, null, null);

        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage('Option "hiddenStart" must be an integer between 0 and 10.');
        $options->setHiddenRange(11, 5);
    }

    public function testSetDotKey(): void
    {
        $options = new CpfFormatterOptions(null, null, null, null, null, null, null, null);

        $options->setDotKey('|');
        $this->assertEquals('|', $options->getDotKey());

        $options->setDotKey(' ');
        $this->assertEquals(' ', $options->getDotKey());
    }

    public function testSetDashKey(): void
    {
        $options = new CpfFormatterOptions(null, null, null, null, null, null, null, null);

        $options->setDashKey('~');
        $this->assertEquals('~', $options->getDashKey());

        $options->setDashKey('_');
        $this->assertEquals('_', $options->getDashKey());
    }

    public function testSetOnFailWithValidCallback(): void
    {
        $options = new CpfFormatterOptions(null, null, null, null, null, null, null, null);

        $callback = function (string $value): string {
            return 'ERROR: ' . $value;
        };

        $options->setOnFail($callback);
        $this->assertSame($callback, $options->getOnFail());
    }

    public function testSetOnFailWithInvalidCallback(): void
    {
        $options = new CpfFormatterOptions(null, null, null, null, null, null, null, null);

        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage('must be of type callable, string given');
        $options->setOnFail('not a callback');
    }

    public function testSetOnFailWithArray(): void
    {
        $options = new CpfFormatterOptions(null, null, null, null, null, null, null, null);

        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage('must be of type callable, array given');
        $options->setOnFail(['not', 'callable']);
    }

    public function testSetOnFailWithNull(): void
    {
        $options = new CpfFormatterOptions(null, null, null, null, null, null, null, null);

        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage('must be of type callable, null given');
        /** @phpstan-ignore-next-line */
        $options->setOnFail(null);
    }

    public function testSetOnFailWithInt(): void
    {
        $options = new CpfFormatterOptions(null, null, null, null, null, null, null, null);

        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage('must be of type callable, int given');
        /** @phpstan-ignore-next-line */
        $options->setOnFail(123);
    }

    public function testBoundaryValuesForHiddenRange(): void
    {
        $options = new CpfFormatterOptions(null, null, null, null, null, null, null, null);

        // Test minimum values
        $options->setHiddenRange(0, 0);
        $this->assertEquals(0, $options->getHiddenStart());
        $this->assertEquals(0, $options->getHiddenEnd());

        // Test maximum values
        $options->setHiddenRange(10, 10);
        $this->assertEquals(10, $options->getHiddenStart());
        $this->assertEquals(10, $options->getHiddenEnd());
    }

    public function testDefaultOnFailCallbackBehavior(): void
    {
        $options = new CpfFormatterOptions(null, null, null, null, null, null, null, null);

        $callback = $options->getOnFail();
        $result = $callback('test input');
        $this->assertEquals('test input', $result);
    }

    public function testMergeReturnsNewInstance(): void
    {
        $originalOptions = new CpfFormatterOptions(null, null, null, null, null, null, null, null);
        $mergedOptions = $originalOptions->merge(null, null, null, null, null, null, null, null);

        $this->assertNotSame($originalOptions, $mergedOptions);
        $this->assertInstanceOf(CpfFormatterOptions::class, $mergedOptions);
    }

    public function testMergeWithAllNullsPreservesOriginalValues(): void
    {
        $originalOptions = new CpfFormatterOptions(
            true,  // escape
            true,  // hidden
            '#',   // hiddenKey
            1,     // hiddenStart
            8,     // hiddenEnd
            '|',   // dotKey
            '~',   // dashKey
            function (string $value): string { return 'ERROR: ' . $value; }
        );

        $mergedOptions = $originalOptions->merge(null, null, null, null, null, null, null, null);

        $this->assertTrue($mergedOptions->isEscaped());
        $this->assertTrue($mergedOptions->isHidden());
        $this->assertEquals('#', $mergedOptions->getHiddenKey());
        $this->assertEquals(1, $mergedOptions->getHiddenStart());
        $this->assertEquals(8, $mergedOptions->getHiddenEnd());
        $this->assertEquals('|', $mergedOptions->getDotKey());
        $this->assertEquals('~', $mergedOptions->getDashKey());
    }

    public function testConstructorWithMixedNullAndValidValues(): void
    {
        $onFailCallback = function (string $value): string {
            return 'CUSTOM: ' . $value;
        };

        $options = new CpfFormatterOptions(
            true,  // escape
            null,  // hidden (should default to false)
            null,  // hiddenKey (should default to '*')
            5,     // hiddenStart
            null,  // hiddenEnd (should default to 10)
            null,  // dotKey (should default to '.')
            '~',   // dashKey
            $onFailCallback
        );

        $this->assertTrue($options->isEscaped());
        $this->assertFalse($options->isHidden());
        $this->assertEquals('*', $options->getHiddenKey());
        $this->assertEquals(5, $options->getHiddenStart());
        $this->assertEquals(10, $options->getHiddenEnd());
        $this->assertEquals('.', $options->getDotKey());
        $this->assertEquals('~', $options->getDashKey());
        $this->assertSame($onFailCallback, $options->getOnFail());
    }
}
