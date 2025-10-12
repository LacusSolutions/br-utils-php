<?php

declare(strict_types=1);

namespace Lacus\CpfVal\Tests;

use InvalidArgumentException;
use TypeError;

trait CpfValidatorTestCases
{
    abstract protected function isValid(string $cpfString): bool;

    public function testCpfStringWithDotsAndDashIsValid(): void
    {
        $result = $this->isValid('499.784.420-90');

        $this->assertTrue($result);
    }

    public function testCpfStringWithDotsIsValid(): void
    {
        $result = $this->isValid('028.062.110.85');

        $this->assertTrue($result);
    }

    public function testCpfStringWithUnderscoresIsValid(): void
    {
        $result = $this->isValid('011_258_960_00');

        $this->assertTrue($result);
    }

    public function testCpfStringWithDashIsValid(): void
    {
        $result = $this->isValid('779953010-30');

        $this->assertTrue($result);
    }

    public function testCpfStringWithoutFormattingIsValid(): void
    {
        $result = $this->isValid('86244870050');

        $this->assertTrue($result);
    }

    public function testCpfString22312659077IsValid(): void
    {
        $result = $this->isValid('22312659077');

        $this->assertTrue($result);
    }

    public function testCpfString96215666068IsValid(): void
    {
        $result = $this->isValid('96215666068');

        $this->assertTrue($result);
    }

    public function testCpfString67107095072IsValid(): void
    {
        $result = $this->isValid('67107095072');

        $this->assertTrue($result);
    }

    public function testCpfString48039958008IsValid(): void
    {
        $result = $this->isValid('48039958008');

        $this->assertTrue($result);
    }

    public function testCpfString20954431014IsValid(): void
    {
        $result = $this->isValid('20954431014');

        $this->assertTrue($result);
    }

    // Invalid CPF test cases
    public function testCpfString09087121971IsNotValid(): void
    {
        $result = $this->isValid('090.871.219-71');

        $this->assertFalse($result);
    }

    public function testCpfString08146572910IsNotValid(): void
    {
        $result = $this->isValid('081.465.729.10');

        $this->assertFalse($result);
    }

    public function testCpfString01125896099IsNotValid(): void
    {
        $result = $this->isValid('011_258_960_99');

        $this->assertFalse($result);
    }

    public function testCpfString49978442075IsNotValid(): void
    {
        $result = $this->isValid('499784420-75');

        $this->assertFalse($result);
    }

    public function testCpfString86244870011IsNotValid(): void
    {
        $result = $this->isValid('86244870011');

        $this->assertFalse($result);
    }

    public function testValue123IsNotValid(): void
    {
        $this->expectException(TypeError::class);

        $this->isValid(123);
    }

    public function testValueAbcIsNotValid(): void
    {
        $result = $this->isValid('abc');

        $this->assertFalse($result);
    }

    public function testValueAbc123IsNotValid(): void
    {
        $result = $this->isValid('abc123');

        $this->assertFalse($result);
    }

    public function testValueTrueIsNotValid(): void
    {
        $this->expectException(TypeError::class);
        $this->isValid(true);
    }

    public function testValueFalseIsNotValid(): void
    {
        $this->expectException(TypeError::class);
        $this->isValid(false);
    }

    public function testValueNullIsNotValid(): void
    {
        $this->expectException(TypeError::class);
        $this->isValid(null);
    }

    public function testValueInfinityIsNotValid(): void
    {
        $this->expectException(TypeError::class);
        $this->isValid(INF);
    }

    public function testArrayIsNotValid(): void
    {
        $this->expectException(TypeError::class);
        $this->isValid([1, 2, 3]);
    }

    public function testObjectIsNotValid(): void
    {
        $this->expectException(TypeError::class);
        $this->isValid((object) ['a' => 1, 'b' => 2, 'c' => 3]);
    }

    public function testFunctionIsNotValid(): void
    {
        $this->expectException(TypeError::class);
        $this->isValid(function() {});
    }
}
