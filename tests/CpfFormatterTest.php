<?php

declare(strict_types=1);

namespace Lacus\Formatters\Cpf\Tests;

use Lacus\Formatters\Cpf\CpfFormatter;
use PHPUnit\Framework\TestCase;

class CpfFormatterTest extends TestCase
{
    private CpfFormatter $formatter;

    protected function setUp(): void
    {
        $this->formatter = new CpfFormatter();
    }

    public function testCpfWithDotsFormatsToSameFormat(): void
    {
        $cpf = $this->formatter->format('809.765.110-61');

        $this->assertEquals('809.765.110-61', $cpf);
    }

    public function testCpfWithoutFormattingFormatsToDotsAndDash(): void
    {
        $cpf = $this->formatter->format('80976511061');

        $this->assertEquals('809.765.110-61', $cpf);
    }

    public function testCpfWithDashesFormatsToDotsAndDash(): void
    {
        $cpf = $this->formatter->format('809-765-110-61');

        $this->assertEquals('809.765.110-61', $cpf);
    }

    public function testCpfWithSpacesFormatsToDotsAndDash(): void
    {
        $cpf = $this->formatter->format('809 765 110 61');

        $this->assertEquals('809.765.110-61', $cpf);
    }

    public function testCpfWithTrailingSpaceFormatsToDotsAndDash(): void
    {
        $cpf = $this->formatter->format('80976511061 ');

        $this->assertEquals('809.765.110-61', $cpf);
    }

    public function testCpfWithLeadingSpaceFormatsToDotsAndDash(): void
    {
        $cpf = $this->formatter->format(' 80976511061');

        $this->assertEquals('809.765.110-61', $cpf);
    }

    public function testCpfWithIndividualDotsFormatsToDotsAndDash(): void
    {
        $cpf = $this->formatter->format('8.0.9.7.6.5.1.1.0.6.1');

        $this->assertEquals('809.765.110-61', $cpf);
    }

    public function testCpfWithIndividualDashesFormatsToDotsAndDash(): void
    {
        $cpf = $this->formatter->format('8-0-9-7-6-5-1-1-0-6-1');

        $this->assertEquals('809.765.110-61', $cpf);
    }

    public function testCpfWithIndividualSpacesFormatsToDotsAndDash(): void
    {
        $cpf = $this->formatter->format('8 0 9 7 6 5 1 1 0 6 1');

        $this->assertEquals('809.765.110-61', $cpf);
    }

    public function testCpfWithLettersFormatsToDotsAndDash(): void
    {
        $cpf = $this->formatter->format('80976511061abc');

        $this->assertEquals('809.765.110-61', $cpf);
    }

    public function testCpfWithDvTextFormatsToDotsAndDash(): void
    {
        $cpf = $this->formatter->format('809765110 dv 61');

        $this->assertEquals('809.765.110-61', $cpf);
    }

    public function testCpfFormatsToCustomDelimitersWithoutDots(): void
    {
        $cpf = $this->formatter->format('80976511061', [
            'delimiters' => ['dot' => '']
        ]);

        $this->assertEquals('809765110-61', $cpf);
    }

    public function testCpfFormatsToCustomDelimitersWithDotAsDash(): void
    {
        $cpf = $this->formatter->format('80976511061', [
            'delimiters' => ['dash' => '.']
        ]);

        $this->assertEquals('809.765.110.61', $cpf);
    }

    public function testCpfFormatsToNoDelimiters(): void
    {
        $cpf = $this->formatter->format('809.765.110-61', [
            'delimiters' => [
                'dot' => '',
                'dash' => '',
            ]
        ]);

        $this->assertEquals('80976511061', $cpf);
    }

    public function testCpfFormatsToCustomDelimitersWithEscape(): void
    {
        $cpf = $this->formatter->format('80976511061', [
            'delimiters' => [
                'dot' => '<',
                'dash' => '>',
            ],
            'escape' => true
        ]);

        $this->assertEquals('809&lt;765&lt;110&gt;61', $cpf);
    }

    public function testCpfFormatsToHiddenFormat(): void
    {
        $cpf = $this->formatter->format('80976511061', [
            'hidden' => true
        ]);

        $this->assertEquals('809.***.***-**', $cpf);
    }

    public function testCpfFormatsToHiddenFormatWithStartRange(): void
    {
        $cpf = $this->formatter->format('80976511061', [
            'hidden' => true,
            'hiddenRange' => ['start' => 6]
        ]);

        $this->assertEquals('809.765.***-**', $cpf);
    }

    public function testCpfFormatsToHiddenFormatWithEndRange(): void
    {
        $cpf = $this->formatter->format('80976511061', [
            'hidden' => true,
            'hiddenRange' => ['end' => 8]
        ]);

        $this->assertEquals('809.***.***-61', $cpf);
    }

    public function testCpfFormatsToHiddenFormatWithStartAndEndRange(): void
    {
        $cpf = $this->formatter->format('80976511061', [
            'hidden' => true,
            'hiddenRange' => [
                'start' => 0,
                'end' => 8,
            ]
        ]);

        $this->assertEquals('***.***.***-61', $cpf);
    }

    public function testCpfFormatsToHiddenFormatWithReversedRange(): void
    {
        $cpf = $this->formatter->format('80976511061', [
            'hidden' => true,
            'hiddenRange' => [
                'start' => 9,
                'end' => 3,
            ]
        ]);

        $this->assertEquals('809.***.***-*1', $cpf);
    }

    public function testCpfFormatsToHiddenFormatWithCustomKey(): void
    {
        $cpf = $this->formatter->format('80976511061', [
            'hidden' => true,
            'hiddenKey' => '#'
        ]);

        $this->assertEquals('809.###.###-##', $cpf);
    }

    public function testCpfFormatsToHiddenFormatWithCustomKeyAndRange(): void
    {
        $cpf = $this->formatter->format('80976511061', [
            'hidden' => true,
            'hiddenKey' => '#',
            'hiddenRange' => ['start' => 6]
        ]);

        $this->assertEquals('809.765.###-##', $cpf);
    }

    public function testInvalidInputFallsBackToOnFailCallback(): void
    {
        $cpf = $this->formatter->format('abc', [
            'onFail' => function($value) {
                return strtoupper($value);
            }
        ]);

        $this->assertEquals('ABC', $cpf);
    }

    public function testOptionWithRangeStartMinusOneThrowsTypeError(): void
    {
        $this->expectException(\TypeError::class);

        $this->formatter->format('80976511061', [
            'hidden' => true,
            'hiddenRange' => ['start' => -1]
        ]);
    }

    public function testOptionWithRangeStartGreaterThan10ThrowsTypeError(): void
    {
        $this->expectException(\TypeError::class);

        $this->formatter->format('80976511061', [
            'hidden' => true,
            'hiddenRange' => ['start' => 11]
        ]);
    }

    public function testOptionWithRangeEndMinusOneThrowsTypeError(): void
    {
        $this->expectException(\TypeError::class);

        $this->formatter->format('80976511061', [
            'hidden' => true,
            'hiddenRange' => ['end' => -1]
        ]);
    }

    public function testOptionWithRangeEndGreaterThan10ThrowsTypeError(): void
    {
        $this->expectException(\TypeError::class);

        $this->formatter->format('80976511061', [
            'hidden' => true,
            'hiddenRange' => ['end' => 11]
        ]);
    }

    public function testOptionWithOnFailAsNotFunctionThrowsTypeError(): void
    {
        $this->expectException(\TypeError::class);

        $this->formatter->format('80976511061', ['onFail' => 'testing']);
    }
}
