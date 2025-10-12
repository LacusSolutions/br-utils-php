<?php

declare(strict_types=1);

namespace Lacus\CpfFmt\Tests;

use Closure;
use InvalidArgumentException;
use TypeError;

trait CpfFormatterTestCases
{
    abstract protected function format(
        string $cpfString,
        ?bool $escape = null,
        ?bool $hidden = null,
        ?string $hiddenKey = null,
        ?int $hiddenStart = null,
        ?int $hiddenEnd = null,
        ?string $dotKey = null,
        ?string $dashKey = null,
        ?Closure $onFail = null,
    ): string;

    public function testCpfWithDotsAndDashFormatsToSameFormat(): void
    {
        $cpf = $this->format('809.765.110-61');

        $this->assertEquals('809.765.110-61', $cpf);
    }

    public function testCpfWithoutFormattingFormatsToDotsAndDash(): void
    {
        $cpf = $this->format('80976511061');

        $this->assertEquals('809.765.110-61', $cpf);
    }

    public function testCpfWithDashesFormatsToDotsAndDash(): void
    {
        $cpf = $this->format('809-765-110-61');

        $this->assertEquals('809.765.110-61', $cpf);
    }

    public function testCpfWithSpacesFormatsToDotsAndDash(): void
    {
        $cpf = $this->format('809 765 110 61');

        $this->assertEquals('809.765.110-61', $cpf);
    }

    public function testCpfWithTrailingSpaceFormatsToDotsAndDash(): void
    {
        $cpf = $this->format('80976511061 ');

        $this->assertEquals('809.765.110-61', $cpf);
    }

    public function testCpfWithLeadingSpaceFormatsToDotsAndDash(): void
    {
        $cpf = $this->format(' 80976511061');

        $this->assertEquals('809.765.110-61', $cpf);
    }

    public function testCpfWithIndividualDotsFormatsToDotsAndDash(): void
    {
        $cpf = $this->format('8.0.9.7.6.5.1.1.0.6.1');

        $this->assertEquals('809.765.110-61', $cpf);
    }

    public function testCpfWithIndividualDashesFormatsToDotsAndDash(): void
    {
        $cpf = $this->format('8-0-9-7-6-5-1-1-0-6-1');

        $this->assertEquals('809.765.110-61', $cpf);
    }

    public function testCpfWithIndividualSpacesFormatsToDotsAndDash(): void
    {
        $cpf = $this->format('8 0 9 7 6 5 1 1 0 6 1');

        $this->assertEquals('809.765.110-61', $cpf);
    }

    public function testCpfWithLettersFormatsToDotsAndDash(): void
    {
        $cpf = $this->format('80976511061abc');

        $this->assertEquals('809.765.110-61', $cpf);
    }

    public function testCpfWithMixedCharactersFormatsCorrectly(): void
    {
        $cpf = $this->format('809765110 dv 61');

        $this->assertEquals('809.765.110-61', $cpf);
    }

    public function testCpfFormatsToCustomDelimitersWithoutDots(): void
    {
        $cpf = $this->format(
            '80976511061',
            dotKey: ''
        );

        $this->assertEquals('809765110-61', $cpf);
    }

    public function testCpfFormatsToCustomDelimitersWithDashAsDot(): void
    {
        $cpf = $this->format(
            '80976511061',
            dashKey: '.'
        );

        $this->assertEquals('809.765.110.61', $cpf);
    }

    public function testCpfFormatsToNoDelimiters(): void
    {
        $cpf = $this->format(
            '809.765.110-61',
            dotKey: '',
            dashKey: ''
        );

        $this->assertEquals('80976511061', $cpf);
    }

    public function testCpfFormatsToCustomDelimitersWithEscape(): void
    {
        $cpf = $this->format(
            '80976511061',
            escape: true,
            dotKey: '<',
            dashKey: '>'
        );

        $this->assertEquals('809&lt;765&lt;110&gt;61', $cpf);
    }

    public function testCpfFormatsToHiddenFormat(): void
    {
        $cpf = $this->format(
            '80976511061',
            hidden: true
        );

        $this->assertEquals('809.***.***-**', $cpf);
    }

    public function testCpfFormatsToHiddenFormatWithStartRange(): void
    {
        $cpf = $this->format(
            '80976511061',
            hidden: true,
            hiddenStart: 6
        );

        $this->assertEquals('809.765.***-**', $cpf);
    }

    public function testCpfFormatsToHiddenFormatWithEndRange(): void
    {
        $cpf = $this->format(
            '80976511061',
            hidden: true,
            hiddenEnd: 8
        );

        $this->assertEquals('809.***.***-61', $cpf);
    }

    public function testCpfFormatsToHiddenFormatWithStartAndEndRange(): void
    {
        $cpf = $this->format(
            '80976511061',
            hidden: true,
            hiddenStart: 0,
            hiddenEnd: 8
        );

        $this->assertEquals('***.***.***-61', $cpf);
    }

    public function testCpfFormatsToHiddenFormatWithReversedRange(): void
    {
        $cpf = $this->format(
            '80976511061',
            hidden: true,
            hiddenStart: 9,
            hiddenEnd: 3
        );

        $this->assertEquals('809.***.***-*1', $cpf);
    }

    public function testCpfFormatsToHiddenFormatWithCustomKey(): void
    {
        $cpf = $this->format(
            '80976511061',
            hidden: true,
            hiddenKey: '#'
        );

        $this->assertEquals('809.###.###-##', $cpf);
    }

    public function testCpfFormatsToHiddenFormatWithCustomKeyAndRange(): void
    {
        $cpf = $this->format(
            '80976511061',
            hidden: true,
            hiddenKey: '#',
            hiddenStart: 6
        );

        $this->assertEquals('809.765.###-##', $cpf);
    }

    public function testInvalidInputFallsBackToOnFailCallback(): void
    {
        $cpf = $this->format(
            'abc',
            onFail: function ($value) {
                return strtoupper($value);
            }
        );

        $this->assertEquals('ABC', $cpf);
    }

    public function testOptionWithRangeStartMinusOneThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->format(
            '80976511061',
            hidden: true,
            hiddenStart: -1
        );
    }

    public function testOptionWithRangeStartGreaterThan10ThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->format(
            '80976511061',
            hidden: true,
            hiddenStart: 11
        );
    }

    public function testOptionWithRangeEndMinusOneThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->format(
            '80976511061',
            hidden: true,
            hiddenEnd: -1
        );
    }

    public function testOptionWithRangeEndGreaterThan10ThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->format(
            '80976511061',
            hidden: true,
            hiddenEnd: 11
        );
    }

    public function testOptionWithOnFailAsNotFunctionThrowsException(): void
    {
        $this->expectException(TypeError::class);

        $this->format(
            '80976511061',
            onFail: 'testing'
        );
    }
}
