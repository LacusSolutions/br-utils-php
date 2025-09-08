<?php

declare(strict_types=1);

namespace Lacus\Cnpj\Utils\Tests;

use Lacus\Cnpj\Utils\CnpjUtils;
use PHPUnit\Framework\TestCase;

class CnpjUtilsTest extends TestCase
{
    private CnpjUtils $utils;

    protected function setUp(): void
    {
        $this->utils = new CnpjUtils();
    }

    public function testFormatMethodExists(): void
    {
        $this->assertTrue(method_exists($this->utils, 'format'));
    }

    public function testGenerateMethodExists(): void
    {
        $this->assertTrue(method_exists($this->utils, 'generate'));
    }

    public function testIsValidMethodExists(): void
    {
        $this->assertTrue(method_exists($this->utils, 'isValid'));
    }

    public function testFormatMethodReturnsString(): void
    {
        $result = $this->utils->format('12345678000195');
        $this->assertIsString($result);
    }

    public function testGenerateMethodReturnsString(): void
    {
        $result = $this->utils->generate();
        $this->assertIsString($result);
    }

    public function testIsValidMethodReturnsBoolean(): void
    {
        $result = $this->utils->isValid('12345678000195');
        $this->assertIsBool($result);
    }

    public function testFormatMethodFormatsCnpjCorrectly(): void
    {
        $result = $this->utils->format('12345678000195');
        $this->assertEquals('12.345.678/0001-95', $result);
    }

    public function testGenerateMethodGeneratesValidCnpj(): void
    {
        $result = $this->utils->generate();
        $this->assertTrue($this->utils->isValid($result));
    }

    public function testIsValidMethodValidatesCnpjCorrectly(): void
    {
        $this->assertTrue($this->utils->isValid('12.345.678/0001-95'));
        $this->assertFalse($this->utils->isValid('12.345.678/0001-96'));
    }

    public function testGenerateWithFormatOption(): void
    {
        $result = $this->utils->generate(['format' => true]);
        $this->assertMatchesRegularExpression('/(\d{2}).(\d{3}).(\d{3})\/(\d{4})-(\d{2})/', $result);
    }

    public function testGenerateWithPrefixOption(): void
    {
        $result = $this->utils->generate(['prefix' => '123']);
        $this->assertStringStartsWith('123', $result);
        $this->assertTrue($this->utils->isValid($result));
    }
}
