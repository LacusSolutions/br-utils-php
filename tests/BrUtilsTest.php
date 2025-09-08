<?php

declare(strict_types=1);

namespace Lacus\BrUtils\Tests;

use Lacus\BrUtils\BrUtils;
use PHPUnit\Framework\TestCase;

class BrUtilsTest extends TestCase
{
    private BrUtils $utils;

    protected function setUp(): void
    {
        $this->utils = new BrUtils();
    }

    public function testCpfFormatMethodExists(): void
    {
        $this->assertTrue(method_exists($this->utils, 'cpfFormat'));
    }

    public function testCpfGenerateMethodExists(): void
    {
        $this->assertTrue(method_exists($this->utils, 'cpfGenerate'));
    }

    public function testCpfIsValidMethodExists(): void
    {
        $this->assertTrue(method_exists($this->utils, 'cpfIsValid'));
    }

    public function testCnpjFormatMethodExists(): void
    {
        $this->assertTrue(method_exists($this->utils, 'cnpjFormat'));
    }

    public function testCnpjGenerateMethodExists(): void
    {
        $this->assertTrue(method_exists($this->utils, 'cnpjGenerate'));
    }

    public function testCnpjIsValidMethodExists(): void
    {
        $this->assertTrue(method_exists($this->utils, 'cnpjIsValid'));
    }

    public function testCpfFormatMethodReturnsString(): void
    {
        $result = $this->utils->cpfFormat('12345678909');
        $this->assertIsString($result);
    }

    public function testCpfGenerateMethodReturnsString(): void
    {
        $result = $this->utils->cpfGenerate();
        $this->assertIsString($result);
    }

    public function testCpfIsValidMethodReturnsBoolean(): void
    {
        $result = $this->utils->cpfIsValid('12345678909');
        $this->assertIsBool($result);
    }

    public function testCnpjFormatMethodReturnsString(): void
    {
        $result = $this->utils->cnpjFormat('12345678000195');
        $this->assertIsString($result);
    }

    public function testCnpjGenerateMethodReturnsString(): void
    {
        $result = $this->utils->cnpjGenerate();
        $this->assertIsString($result);
    }

    public function testCnpjIsValidMethodReturnsBoolean(): void
    {
        $result = $this->utils->cnpjIsValid('12345678000195');
        $this->assertIsBool($result);
    }

    public function testCpfFormatMethodFormatsCpfCorrectly(): void
    {
        $result = $this->utils->cpfFormat('12345678909');
        $this->assertEquals('123.456.789-09', $result);
    }

    public function testCpfGenerateMethodGeneratesValidCpf(): void
    {
        $result = $this->utils->cpfGenerate();
        $this->assertTrue($this->utils->cpfIsValid($result));
    }

    public function testCpfIsValidMethodValidatesCpfCorrectly(): void
    {
        $this->assertTrue($this->utils->cpfIsValid('123.456.789-09'));
        $this->assertFalse($this->utils->cpfIsValid('123.456.789-10'));
    }

    public function testCnpjFormatMethodFormatsCnpjCorrectly(): void
    {
        $result = $this->utils->cnpjFormat('12345678000195');
        $this->assertEquals('12.345.678/0001-95', $result);
    }

    public function testCnpjGenerateMethodGeneratesValidCnpj(): void
    {
        $result = $this->utils->cnpjGenerate();
        $this->assertTrue($this->utils->cnpjIsValid($result));
    }

    public function testCnpjIsValidMethodValidatesCnpjCorrectly(): void
    {
        $this->assertTrue($this->utils->cnpjIsValid('12.345.678/0001-95'));
        $this->assertFalse($this->utils->cnpjIsValid('12.345.678/0001-96'));
    }

    public function testCpfGenerateWithFormatOption(): void
    {
        $result = $this->utils->cpfGenerate(['format' => true]);
        $this->assertMatchesRegularExpression('/(\d{3}).(\d{3}).(\d{3})-(\d{2})/', $result);
    }

    public function testCpfGenerateWithPrefixOption(): void
    {
        $result = $this->utils->cpfGenerate(['prefix' => '123']);
        $this->assertStringStartsWith('123', $result);
        $this->assertTrue($this->utils->cpfIsValid($result));
    }

    public function testCnpjGenerateWithFormatOption(): void
    {
        $result = $this->utils->cnpjGenerate(['format' => true]);
        $this->assertMatchesRegularExpression('/(\d{2}).(\d{3}).(\d{3})\/(\d{4})-(\d{2})/', $result);
    }

    public function testCnpjGenerateWithPrefixOption(): void
    {
        $result = $this->utils->cnpjGenerate(['prefix' => '123']);
        $this->assertStringStartsWith('123', $result);
        $this->assertTrue($this->utils->cnpjIsValid($result));
    }
}
