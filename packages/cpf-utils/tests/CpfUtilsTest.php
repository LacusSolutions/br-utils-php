<?php

declare(strict_types=1);

namespace Lacus\CpfUtils\Tests;

use Lacus\CpfUtils\CpfUtils;
use PHPUnit\Framework\TestCase;

class CpfUtilsTest extends TestCase
{
    private CpfUtils $utils;

    protected function setUp(): void
    {
        $this->utils = new CpfUtils();
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
        $result = $this->utils->format('12345678909');
        $this->assertIsString($result);
    }

    public function testGenerateMethodReturnsString(): void
    {
        $result = $this->utils->generate();
        $this->assertIsString($result);
    }

    public function testIsValidMethodReturnsBoolean(): void
    {
        $result = $this->utils->isValid('12345678909');
        $this->assertIsBool($result);
    }

    public function testFormatMethodFormatsCpfCorrectly(): void
    {
        $result = $this->utils->format('12345678909');
        $this->assertEquals('123.456.789-09', $result);
    }

    public function testGenerateMethodGeneratesValidCpf(): void
    {
        $result = $this->utils->generate();
        $this->assertTrue($this->utils->isValid($result));
    }

    public function testIsValidMethodValidatesCpfCorrectly(): void
    {
        $this->assertTrue($this->utils->isValid('123.456.789-09'));
        $this->assertFalse($this->utils->isValid('123.456.789-10'));
    }

    public function testGenerateWithFormatOption(): void
    {
        $result = $this->utils->generate(['format' => true]);
        $this->assertMatchesRegularExpression('/(\d{3}).(\d{3}).(\d{3})-(\d{2})/', $result);
    }

    public function testGenerateWithPrefixOption(): void
    {
        $result = $this->utils->generate(['prefix' => '123']);
        $this->assertStringStartsWith('123', $result);
        $this->assertTrue($this->utils->isValid($result));
    }
}
