<?php

declare(strict_types=1);

namespace Lacus\CnpjVal\Tests;

use PHPUnit\Framework\TestCase;

abstract class CnpjValidatorTestCase extends TestCase
{
    abstract protected function isValid(string $cnpjString): bool;

    public function testCnpjStringWithDotsAndDashIsValid(): void
    {
        $result = $this->isValid('22.250.620/0001-11');

        $this->assertTrue($result);
    }

    public function testCnpjStringWithDotsAndDotIsValid(): void
    {
        $result = $this->isValid('53.975.985/0001.37');

        $this->assertTrue($result);
    }

    public function testCnpjStringWithUnderscoresAndPipeIsValid(): void
    {
        $result = $this->isValid('31_592_118|0001_80');

        $this->assertTrue($result);
    }

    public function testCnpjStringWithDashIsValid(): void
    {
        $result = $this->isValid('188549330001-01');

        $this->assertTrue($result);
    }

    public function testCnpjStringOnlyNumbersIsValid(): void
    {
        $result = $this->isValid('19593887000105');

        $this->assertTrue($result);
    }

    public function testCnpjString99042801000187IsValid(): void
    {
        $result = $this->isValid('99042801000187');

        $this->assertTrue($result);
    }

    public function testCnpjString27728000000169IsValid(): void
    {
        $result = $this->isValid('27728000000169');

        $this->assertTrue($result);
    }

    public function testCnpjString72199088000123IsValid(): void
    {
        $result = $this->isValid('72199088000123');

        $this->assertTrue($result);
    }

    public function testCnpjString00113719000139IsValid(): void
    {
        $result = $this->isValid('00113719000139');

        $this->assertTrue($result);
    }

    public function testCnpjString50096743000185IsValid(): void
    {
        $result = $this->isValid('50096743000185');

        $this->assertTrue($result);
    }

    // Invalid CNPJ test cases
    public function testCnpjStringWithDotsAndDashIsNotValid(): void
    {
        $result = $this->isValid('68.224.994/0001-62');

        $this->assertFalse($result);
    }

    public function testCnpjStringWithDotsAndPipeIsNotValid(): void
    {
        $result = $this->isValid('41.406.219|0001.73');

        $this->assertFalse($result);
    }

    public function testCnpjStringWithUnderscoresAndHashIsNotValid(): void
    {
        $result = $this->isValid('46_063_859#0001_41');

        $this->assertFalse($result);
    }

    public function testCnpjStringWithSlashIsNotValid(): void
    {
        $result = $this->isValid('54964126/000106');

        $this->assertFalse($result);
    }

    public function testCnpjString03783943000127IsNotValid(): void
    {
        $result = $this->isValid('03783943000127');

        $this->assertFalse($result);
    }

    // Other random values are invalid
    public function testValue123IsNotValid(): void
    {
        $result = $this->isValid('123');

        $this->assertFalse($result);
    }

    public function testValue123456IsNotValid(): void
    {
        $result = $this->isValid('123456');

        $this->assertFalse($result);
    }

    public function testValue123456789IsNotValid(): void
    {
        $result = $this->isValid('123456789');

        $this->assertFalse($result);
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
        $result = $this->isValid('true');

        $this->assertFalse($result);
    }

    public function testValueFalseIsNotValid(): void
    {
        $result = $this->isValid('false');

        $this->assertFalse($result);
    }

    public function testValueUndefinedIsNotValid(): void
    {
        $result = $this->isValid('undefined');

        $this->assertFalse($result);
    }

    public function testValueInfinityIsNotValid(): void
    {
        $result = $this->isValid('Infinity');

        $this->assertFalse($result);
    }

    public function testValueNullIsNotValid(): void
    {
        $result = $this->isValid('null');

        $this->assertFalse($result);
    }

    public function testEmptyStringIsNotValid(): void
    {
        $result = $this->isValid('');

        $this->assertFalse($result);
    }
}
