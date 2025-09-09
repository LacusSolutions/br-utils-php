<?php

declare(strict_types=1);

namespace Lacus\Formatters\Cnpj\Tests;

use Lacus\Formatters\Cnpj\CnpjFormatter;
use PHPUnit\Framework\TestCase;

class CnpjFormatterTest extends TestCase
{
    private CnpjFormatter $formatter;

    protected function setUp(): void
    {
        $this->formatter = new CnpjFormatter();
    }

    public function testCnpjWithDotsAndSlashFormatsToSameFormat(): void
    {
        $cnpj = $this->formatter->format('03.603.568/0001-95');

        $this->assertEquals('03.603.568/0001-95', $cnpj);
    }

    public function testCnpjWithoutFormattingFormatsToDotsSlashAndDash(): void
    {
        $cnpj = $this->formatter->format('03603568000195');

        $this->assertEquals('03.603.568/0001-95', $cnpj);
    }

    public function testCnpjWithDashesFormatsToDotsSlashAndDash(): void
    {
        $cnpj = $this->formatter->format('03-603-568-0001-95');

        $this->assertEquals('03.603.568/0001-95', $cnpj);
    }

    public function testCnpjWithSpacesFormatsToDotsSlashAndDash(): void
    {
        $cnpj = $this->formatter->format('03 603 568 / 0001 95');

        $this->assertEquals('03.603.568/0001-95', $cnpj);
    }

    public function testCnpjWithTrailingSpaceFormatsToDotsSlashAndDash(): void
    {
        $cnpj = $this->formatter->format('03603568000195 ');

        $this->assertEquals('03.603.568/0001-95', $cnpj);
    }

    public function testCnpjWithLeadingSpaceFormatsToDotsSlashAndDash(): void
    {
        $cnpj = $this->formatter->format(' 03603568000195');

        $this->assertEquals('03.603.568/0001-95', $cnpj);
    }

    public function testCnpjWithIndividualDotsFormatsToDotsSlashAndDash(): void
    {
        $cnpj = $this->formatter->format('0.3.6.0.3.5.6.8.0.0.0.1.9.5');

        $this->assertEquals('03.603.568/0001-95', $cnpj);
    }

    public function testCnpjWithIndividualDashesFormatsToDotsSlashAndDash(): void
    {
        $cnpj = $this->formatter->format('0-3-6-0-3-5-6-8-/-0-0-0-1-95');

        $this->assertEquals('03.603.568/0001-95', $cnpj);
    }

    public function testCnpjWithIndividualSpacesFormatsToDotsSlashAndDash(): void
    {
        $cnpj = $this->formatter->format('0 3 6 0 3 5 6 8 0 0 0 1 9 5');

        $this->assertEquals('03.603.568/0001-95', $cnpj);
    }

    public function testCnpjWithLettersFormatsToDotsSlashAndDash(): void
    {
        $cnpj = $this->formatter->format('03603568slash0001dash95');

        $this->assertEquals('03.603.568/0001-95', $cnpj);
    }

    public function testCnpjWithDvTextFormatsToDotsSlashAndDash(): void
    {
        $cnpj = $this->formatter->format('036035680001 dv 95');

        $this->assertEquals('03.603.568/0001-95', $cnpj);
    }

    public function testCnpjFormatsToCustomDelimitersWithoutDots(): void
    {
        $cnpj = $this->formatter->format('03603568000195', [
            'delimiters' => ['dot' => '']
        ]);

        $this->assertEquals('03603568/0001-95', $cnpj);
    }

    public function testCnpjFormatsToCustomDelimitersWithSlashAsColon(): void
    {
        $cnpj = $this->formatter->format('03603568000195', [
            'delimiters' => ['slash' => ':']
        ]);

        $this->assertEquals('03.603.568:0001-95', $cnpj);
    }

    public function testCnpjFormatsToCustomDelimitersWithDashAsDot(): void
    {
        $cnpj = $this->formatter->format('03603568000195', [
            'delimiters' => ['dash' => '.']
        ]);

        $this->assertEquals('03.603.568/0001.95', $cnpj);
    }

    public function testCnpjFormatsToNoDelimiters(): void
    {
        $cnpj = $this->formatter->format('03.603.568/0001-95', [
            'delimiters' => [
                'dot' => '',
                'slash' => '',
                'dash' => '',
            ]
        ]);

        $this->assertEquals('03603568000195', $cnpj);
    }

    public function testCnpjFormatsToCustomDelimitersWithEscape(): void
    {
        $cnpj = $this->formatter->format('03603568000195', [
            'delimiters' => [
                'dot' => '<',
                'slash' => '&',
                'dash' => '>',
            ],
            'escape' => true
        ]);

        $this->assertEquals('03&lt;603&lt;568&amp;0001&gt;95', $cnpj);
    }

    public function testCnpjFormatsToHiddenFormat(): void
    {
        $cnpj = $this->formatter->format('03603568000195', [
            'hidden' => true
        ]);

        $this->assertEquals('03.603.***/****-**', $cnpj);
    }

    public function testCnpjFormatsToHiddenFormatWithStartRange(): void
    {
        $cnpj = $this->formatter->format('03603568000195', [
            'hidden' => true,
            'hiddenRange' => ['start' => 8]
        ]);

        $this->assertEquals('03.603.568/****-**', $cnpj);
    }

    public function testCnpjFormatsToHiddenFormatWithEndRange(): void
    {
        $cnpj = $this->formatter->format('03603568000195', [
            'hidden' => true,
            'hiddenRange' => ['end' => 11]
        ]);

        $this->assertEquals('03.603.***/****-95', $cnpj);
    }

    public function testCnpjFormatsToHiddenFormatWithStartAndEndRange(): void
    {
        $cnpj = $this->formatter->format('03603568000195', [
            'hidden' => true,
            'hiddenRange' => [
                'start' => 0,
                'end' => 7,
            ]
        ]);

        $this->assertEquals('**.***.***/0001-95', $cnpj);
    }

    public function testCnpjFormatsToHiddenFormatWithReversedRange(): void
    {
        $cnpj = $this->formatter->format('03603568000195', [
            'hidden' => true,
            'hiddenRange' => [
                'start' => 11,
                'end' => 2,
            ]
        ]);

        $this->assertEquals('03.***.***/****-95', $cnpj);
    }

    public function testCnpjFormatsToHiddenFormatWithCustomKey(): void
    {
        $cnpj = $this->formatter->format('03603568000195', [
            'hidden' => true,
            'hiddenKey' => '#'
        ]);

        $this->assertEquals('03.603.###/####-##', $cnpj);
    }

    public function testCnpjFormatsToHiddenFormatWithCustomKeyAndRange(): void
    {
        $cnpj = $this->formatter->format('03603568000195', [
            'hidden' => true,
            'hiddenKey' => '#',
            'hiddenRange' => ['start' => 8]
        ]);

        $this->assertEquals('03.603.568/####-##', $cnpj);
    }

    public function testInvalidInputFallsBackToOnFailCallback(): void
    {
        $cnpj = $this->formatter->format('abc', [
            'onFail' => function($value) {
                return strtoupper($value);
            }
        ]);

        $this->assertEquals('ABC', $cnpj);
    }

    public function testOptionWithRangeStartMinusOneThrowsTypeError(): void
    {
        $this->expectException(\TypeError::class);

        $this->formatter->format('03603568000195', [
            'hidden' => true,
            'hiddenRange' => ['start' => -1]
        ]);
    }

    public function testOptionWithRangeStartGreaterThan13ThrowsTypeError(): void
    {
        $this->expectException(\TypeError::class);

        $this->formatter->format('03603568000195', [
            'hidden' => true,
            'hiddenRange' => ['start' => 14]
        ]);
    }

    public function testOptionWithRangeEndMinusOneThrowsTypeError(): void
    {
        $this->expectException(\TypeError::class);

        $this->formatter->format('03603568000195', [
            'hidden' => true,
            'hiddenRange' => ['end' => -1]
        ]);
    }

    public function testOptionWithRangeEndGreaterThan13ThrowsTypeError(): void
    {
        $this->expectException(\TypeError::class);

        $this->formatter->format('03603568000195', [
            'hidden' => true,
            'hiddenRange' => ['end' => 14]
        ]);
    }

    public function testOptionWithOnFailAsNotFunctionThrowsTypeError(): void
    {
        $this->expectException(\TypeError::class);

        $this->formatter->format('03603568000195', ['onFail' => 'testing']);
    }
}
