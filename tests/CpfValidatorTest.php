<?php

declare(strict_types=1);

namespace Lacus\Validators\Cpf\Tests;

use Lacus\Validators\Cpf\CpfValidator;
use PHPUnit\Framework\TestCase;

class CpfValidatorTest extends TestCase
{
    private CpfValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new CpfValidator();
    }

    public function testCpfStringWithDotsIsValid(): void
    {
        $this->assertTrue($this->validator->isValid('499.784.420-90'));
    }

    public function testCpfStringWithDotsAndExtraDotIsValid(): void
    {
        $this->assertTrue($this->validator->isValid('028.062.110.85'));
    }

    public function testCpfStringWithUnderscoresIsValid(): void
    {
        $this->assertTrue($this->validator->isValid('011_258_960_00'));
    }

    public function testCpfStringWithDashIsValid(): void
    {
        $this->assertTrue($this->validator->isValid('779953010-30'));
    }

    public function testCpfStringWithoutFormattingIsValid(): void
    {
        $this->assertTrue($this->validator->isValid('86244870050'));
    }

    public function testAnotherCpfStringWithoutFormattingIsValid(): void
    {
        $this->assertTrue($this->validator->isValid('22312659077'));
    }

    public function testThirdCpfStringWithoutFormattingIsValid(): void
    {
        $this->assertTrue($this->validator->isValid('96215666068'));
    }

    public function testFourthCpfStringWithoutFormattingIsValid(): void
    {
        $this->assertTrue($this->validator->isValid('67107095072'));
    }

    public function testFifthCpfStringWithoutFormattingIsValid(): void
    {
        $this->assertTrue($this->validator->isValid('48039958008'));
    }

    public function testSixthCpfStringWithoutFormattingIsValid(): void
    {
        $this->assertTrue($this->validator->isValid('20954431014'));
    }

    public function testCpfStringWithDotsIsNotValid(): void
    {
        $this->assertFalse($this->validator->isValid('090.871.219-71'));
    }

    public function testCpfStringWithDotsAndExtraDotIsNotValid(): void
    {
        $this->assertFalse($this->validator->isValid('081.465.729.10'));
    }

    public function testCpfStringWithUnderscoresIsNotValid(): void
    {
        $this->assertFalse($this->validator->isValid('011_258_960_99'));
    }

    public function testCpfStringWithDashIsNotValid(): void
    {
        $this->assertFalse($this->validator->isValid('499784420-75'));
    }

    public function testCpfStringWithoutFormattingIsNotValid(): void
    {
        $this->assertFalse($this->validator->isValid('86244870011'));
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

    public function testStringWithLessThan11DigitsIsNotValid(): void
    {
        $this->assertFalse($this->validator->isValid('1234567890'));
    }

    public function testStringWithMoreThan11DigitsIsNotValid(): void
    {
        $this->assertFalse($this->validator->isValid('123456789012'));
    }
}
