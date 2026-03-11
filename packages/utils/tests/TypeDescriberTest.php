<?php

declare(strict_types=1);

namespace Lacus\Utils\Tests;

use Lacus\Utils\TypeDescriber;
use PHPUnit\Framework\TestCase;

final class TypeDescriberTest extends TestCase
{
    public function testWhenGivenNullReturnsNull(): void
    {
        $result = TypeDescriber::describe(null);

        $this->assertSame('null', $result);
    }

    public function testWhenGivenStringReturnsStringForNonEmpty(): void
    {
        $result = TypeDescriber::describe('hello');

        $this->assertSame('string', $result);
    }

    public function testWhenGivenStringReturnsStringForEmpty(): void
    {
        $result = TypeDescriber::describe('');

        $this->assertSame('string', $result);
    }

    public function testWhenGivenStringReturnsStringForWhitespace(): void
    {
        $result = TypeDescriber::describe('   ');

        $this->assertSame('string', $result);
    }

    public function testWhenGivenNumberReturnsIntegerNumberForPositiveInteger(): void
    {
        $result = TypeDescriber::describe(42);

        $this->assertSame('integer number', $result);
    }

    public function testWhenGivenNumberReturnsIntegerNumberForNegativeInteger(): void
    {
        $result = TypeDescriber::describe(-42);

        $this->assertSame('integer number', $result);
    }

    public function testWhenGivenNumberReturnsIntegerNumberForZero(): void
    {
        $result = TypeDescriber::describe(0);

        $this->assertSame('integer number', $result);
    }

    public function testWhenGivenNumberReturnsFloatNumberForPositiveFloat(): void
    {
        $result = TypeDescriber::describe(3.14);

        $this->assertSame('float number', $result);
    }

    public function testWhenGivenNumberReturnsFloatNumberForNegativeFloat(): void
    {
        $result = TypeDescriber::describe(-3.14);

        $this->assertSame('float number', $result);
    }

    public function testWhenGivenNumberReturnsNanForNan(): void
    {
        $result = TypeDescriber::describe(NAN);

        $this->assertSame('NaN', $result);
    }

    public function testWhenGivenNumberReturnsInfinityForPositiveInfinity(): void
    {
        $result = TypeDescriber::describe(INF);

        $this->assertSame('Infinity', $result);
    }

    public function testWhenGivenNumberReturnsInfinityForNegativeInfinity(): void
    {
        $result = TypeDescriber::describe(-INF);

        $this->assertSame('Infinity', $result);
    }

    public function testWhenGivenBooleanReturnsBooleanForTrue(): void
    {
        $result = TypeDescriber::describe(true);

        $this->assertSame('boolean', $result);
    }

    public function testWhenGivenBooleanReturnsBooleanForFalse(): void
    {
        $result = TypeDescriber::describe(false);

        $this->assertSame('boolean', $result);
    }

    public function testWhenGivenEmptyArrayReturnsArrayEmpty(): void
    {
        $result = TypeDescriber::describe([]);

        $this->assertSame('Array (empty)', $result);
    }

    public function testWhenGivenHomogeneousArrayReturnsStringArray(): void
    {
        $result = TypeDescriber::describe(['a', 'b', 'c']);

        $this->assertSame('string[]', $result);
    }

    public function testWhenGivenHomogeneousArrayReturnsNumberArray(): void
    {
        $result = TypeDescriber::describe([1, 2, 3]);

        $this->assertSame('number[]', $result);
    }

    public function testWhenGivenHomogeneousArrayReturnsBooleanArray(): void
    {
        $result = TypeDescriber::describe([true, false, true]);

        $this->assertSame('boolean[]', $result);
    }

    public function testWhenGivenHomogeneousArrayReturnsObjectArray(): void
    {
        $result = TypeDescriber::describe([(object) ([]), (object) (['a' => 1])]);

        $this->assertSame('object[]', $result);
    }

    public function testWhenGivenHeterogeneousArrayReturnsNumberOrStringArray(): void
    {
        $result = TypeDescriber::describe([1, 'a', 2, 'b']);

        $this->assertSame('(number | string)[]', $result);
    }

    public function testWhenGivenHeterogeneousArrayReturnsStringNumberBooleanArray(): void
    {
        $result = TypeDescriber::describe(['hello', 42, true]);

        $this->assertSame('(boolean | number | string)[]', $result);
    }

    public function testWhenGivenHeterogeneousArrayReturnsNumberOrObjectArray(): void
    {
        $result = TypeDescriber::describe([1, (object) ([]), 2, (object) (['a' => 1])]);

        $this->assertSame('(number | object)[]', $result);
    }

    public function testWhenGivenPlainObjectReturnsObject(): void
    {
        $result = TypeDescriber::describe((object) (['key' => 'value']));

        $this->assertSame('object', $result);
    }

    public function testWhenGivenEmptyObjectReturnsObject(): void
    {
        $result = TypeDescriber::describe((object) ([]));

        $this->assertSame('object', $result);
    }

    public function testWhenGivenHeterogeneousArrayWithNullReturnsObjectOrStringArray(): void
    {
        $result = TypeDescriber::describe(['a', null, 'b']);

        $this->assertSame('(object | string)[]', $result);
    }

    public function testWhenGivenResourceReturnsResource(): void
    {
        $handle = fopen('php://memory', 'r');

        try {
            $this->assertSame('resource', TypeDescriber::describe($handle));
        } finally {
            fclose($handle);
        }
    }
}
