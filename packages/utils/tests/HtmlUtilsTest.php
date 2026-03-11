<?php

declare(strict_types=1);

namespace Lacus\Utils\Tests;

use Lacus\Utils\HtmlUtils;
use PHPUnit\Framework\TestCase;

final class HtmlUtilsTest extends TestCase
{
    public function testWhenGivenAmpersandReturnsAmpEntity(): void
    {
        $result = HtmlUtils::escape('&');

        $this->assertSame('&amp;', $result);
    }

    public function testWhenGivenLessThanReturnsLtEntity(): void
    {
        $result = HtmlUtils::escape('<');

        $this->assertSame('&lt;', $result);
    }

    public function testWhenGivenGreaterThanReturnsGtEntity(): void
    {
        $result = HtmlUtils::escape('>');

        $this->assertSame('&gt;', $result);
    }

    public function testWhenGivenDoubleQuoteReturnsQuotEntity(): void
    {
        $result = HtmlUtils::escape('"');

        $this->assertSame('&quot;', $result);
    }

    public function testWhenGivenSingleQuoteReturnsNumericEntity(): void
    {
        $result = HtmlUtils::escape("'");

        $this->assertSame('&#039;', $result);
    }

    public function testWhenGivenTomAndJerryEscapesAmpersand(): void
    {
        $result = HtmlUtils::escape('Tom & Jerry');

        $this->assertSame('Tom &amp; Jerry', $result);
    }

    public function testWhenGivenScriptTagEscapesAllSpecialChars(): void
    {
        $input = '<script>alert("XSS")</script>';
        $expected = '&lt;script&gt;alert(&quot;XSS&quot;)&lt;/script&gt;';

        $result = HtmlUtils::escape($input);

        $this->assertSame($expected, $result);
    }

    public function testUnescapeWhenGivenAmpEntityReturnsAmpersand(): void
    {
        $result = HtmlUtils::unescape('&amp;');

        $this->assertSame('&', $result);
    }

    public function testUnescapeWhenGivenLtEntityReturnsLessThan(): void
    {
        $result = HtmlUtils::unescape('&lt;');

        $this->assertSame('<', $result);
    }

    public function testUnescapeWhenGivenGtEntityReturnsGreaterThan(): void
    {
        $result = HtmlUtils::unescape('&gt;');

        $this->assertSame('>', $result);
    }

    public function testUnescapeWhenGivenQuotEntityReturnsDoubleQuote(): void
    {
        $result = HtmlUtils::unescape('&quot;');

        $this->assertSame('"', $result);
    }

    public function testUnescapeWhenGivenNumericEntityReturnsSingleQuote(): void
    {
        $result = HtmlUtils::unescape('&#039;');

        $this->assertSame("'", $result);
    }

    public function testUnescapeWhenGivenTomAndJerryDecodesAmpersand(): void
    {
        $result = HtmlUtils::unescape('Tom &amp; Jerry');

        $this->assertSame('Tom & Jerry', $result);
    }

    public function testUnescapeWhenGivenEscapedScriptTagDecodesAllEntities(): void
    {
        $input = '&lt;script&gt;alert(&quot;XSS&quot;)&lt;/script&gt;';
        $expected = '<script>alert("XSS")</script>';

        $result = HtmlUtils::unescape($input);

        $this->assertSame($expected, $result);
    }

    public function testUnescapeWhenGivenPlainTextReturnsUnchanged(): void
    {
        $result = HtmlUtils::unescape('hello world');

        $this->assertSame('hello world', $result);
    }

    public function testUnescapeWhenGivenEmptyStringReturnsEmpty(): void
    {
        $result = HtmlUtils::unescape('');

        $this->assertSame('', $result);
    }

    public function testRoundTripEscapeThenUnescape(): void
    {
        $value = '<div class="foo">Tom & Jerry</div>';

        $result = HtmlUtils::unescape(HtmlUtils::escape($value));

        $this->assertSame($value, $result);
    }
}
