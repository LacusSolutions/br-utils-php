<?php

declare(strict_types=1);

namespace Lacus\CnpjVal\Tests;

use Lacus\CnpjVal\CnpjValidator;
use PHPUnit\Framework\TestCase;

class CnpjValidatorTest extends TestCase
{
    private CnpjValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new CnpjValidator();
    }

    public function testCnpjStringWithDotsAndSlashIsValid(): void
    {
        $this->assertTrue($this->validator->isValid('22.250.620/0001-11'));
    }

    public function testCnpjStringWithDotsAndDotIsValid(): void
    {
        $this->assertTrue($this->validator->isValid('53.975.985/0001.37'));
    }

    public function testCnpjStringWithUnderscoresAndPipeIsValid(): void
    {
        $this->assertTrue($this->validator->isValid('31_592_118|0001_80'));
    }

    public function testCnpjStringWithDashIsValid(): void
    {
        $this->assertTrue($this->validator->isValid('188549330001-01'));
    }

    public function testCnpjStringWithoutFormattingIsValid(): void
    {
        $this->assertTrue($this->validator->isValid('19593887000105'));
    }

    public function testAnotherCnpjStringWithoutFormattingIsValid(): void
    {
        $this->assertTrue($this->validator->isValid('99042801000187'));
    }

    public function testThirdCnpjStringWithoutFormattingIsValid(): void
    {
        $this->assertTrue($this->validator->isValid('27728000000169'));
    }

    public function testFourthCnpjStringWithoutFormattingIsValid(): void
    {
        $this->assertTrue($this->validator->isValid('72199088000123'));
    }

    public function testFifthCnpjStringWithoutFormattingIsValid(): void
    {
        $this->assertTrue($this->validator->isValid('00113719000139'));
    }

    public function testSixthCnpjStringWithoutFormattingIsValid(): void
    {
        $this->assertTrue($this->validator->isValid('50096743000185'));
    }

    public function testCnpjStringWithDotsAndSlashIsNotValid(): void
    {
        $this->assertFalse($this->validator->isValid('68.224.994/0001-62'));
    }

    public function testCnpjStringWithDotsAndPipeIsNotValid(): void
    {
        $this->assertFalse($this->validator->isValid('41.406.219|0001.73'));
    }

    public function testCnpjStringWithUnderscoresAndHashIsNotValid(): void
    {
        $this->assertFalse($this->validator->isValid('46_063_859#0001_41'));
    }

    public function testCnpjStringWithSlashIsNotValid(): void
    {
        $this->assertFalse($this->validator->isValid('54964126/000106'));
    }

    public function testCnpjStringWithoutFormattingIsNotValid(): void
    {
        $this->assertFalse($this->validator->isValid('03783943000127'));
    }

    /*
     * Other random values are invalid
     */

    public function testValue123IsNotValid(): void
    {
        $this->assertFalse($this->validator->isValid(123));
    }

    public function testValue123456IsNotValid(): void
    {
        $this->assertFalse($this->validator->isValid(123456));
    }

    public function testValue123456789IsNotValid(): void
    {
        $this->assertFalse($this->validator->isValid(123456789));
    }

    public function testValueAbcIsNotValid(): void
    {
        $this->assertFalse($this->validator->isValid('abc'));
    }

    public function testValueAbc123IsNotValid(): void
    {
        $this->assertFalse($this->validator->isValid('abc123'));
    }

    public function testValueTrueIsNotValid(): void
    {
        $this->assertFalse($this->validator->isValid(true));
    }

    public function testValueFalseIsNotValid(): void
    {
        $this->assertFalse($this->validator->isValid(false));
    }

    public function testValueNullIsNotValid(): void
    {
        $this->assertFalse($this->validator->isValid(null));
    }

    public function testValueInfinityIsNotValid(): void
    {
        $this->assertFalse($this->validator->isValid(INF));
    }

    public function testValueNegativeInfinityIsNotValid(): void
    {
        $this->assertFalse($this->validator->isValid(-INF));
    }

    public function testArrayIsNotValid(): void
    {
        $this->assertFalse($this->validator->isValid([1, 2, 3]));
    }

    public function testObjectIsNotValid(): void
    {
        $this->assertFalse($this->validator->isValid((object)['a' => 1, 'b' => 2, 'c' => 3]));
    }

    public function testEmptyStringIsNotValid(): void
    {
        $this->assertFalse($this->validator->isValid(''));
    }

    public function testStringWithLessThan14DigitsIsNotValid(): void
    {
        $this->assertFalse($this->validator->isValid('1234567890123'));
    }

    public function testStringWithMoreThan14DigitsIsNotValid(): void
    {
        $this->assertFalse($this->validator->isValid('123456789012345'));
    }
}
