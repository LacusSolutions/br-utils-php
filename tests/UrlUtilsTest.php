<?php

declare(strict_types=1);

namespace Lacus\Utils\Tests;

use Lacus\Utils\UrlUtils;
use PHPUnit\Framework\TestCase;

final class UrlUtilsTest extends TestCase
{
    public function testEncodeFragmentEncodesSpace(): void
    {
        $result = UrlUtils::encodeUriComponent('a b');

        $this->assertSame('a%20b', $result);
    }

    public function testEncodeFragmentEncodesSlash(): void
    {
        $result = UrlUtils::encodeUriComponent('a/b');

        $this->assertSame('a%2Fb', $result);
    }

    public function testEncodeFragmentEncodesPercent(): void
    {
        $result = UrlUtils::encodeUriComponent('a%b');

        $this->assertSame('a%25b', $result);
    }

    public function testEncodeFragmentEncodesQueryReservedChars(): void
    {
        $equalsResult = UrlUtils::encodeUriComponent('a=b');
        $ampersandResult = UrlUtils::encodeUriComponent('a&b');
        $hashResult = UrlUtils::encodeUriComponent('a#b');

        $this->assertSame('a%3Db', $equalsResult);
        $this->assertSame('a%26b', $ampersandResult);
        $this->assertSame('a%23b', $hashResult);
    }

    public function testEncodeFragmentLeavesUnreservedUnchanged(): void
    {
        $result = UrlUtils::encodeUriComponent('abc-._~09AZaz');

        $this->assertSame('abc-._~09AZaz', $result);
    }

    public function testEncodeFragmentEmptyString(): void
    {
        $result = UrlUtils::encodeUriComponent('');

        $this->assertSame('', $result);
    }

    public function testDecodeUriComponentReversesEncodedSpace(): void
    {
        $result = UrlUtils::decodeUriComponent('a%20b');

        $this->assertSame('a b', $result);
    }

    public function testDecodeUriComponentReversesEncodedSlash(): void
    {
        $result = UrlUtils::decodeUriComponent('a%2Fb');

        $this->assertSame('a/b', $result);
    }

    public function testDecodeUriComponentReversesEncodedPercent(): void
    {
        $result = UrlUtils::decodeUriComponent('a%25b');

        $this->assertSame('a%b', $result);
    }

    public function testDecodeUriComponentLeavesPlainStringUnchanged(): void
    {
        $result = UrlUtils::decodeUriComponent('abc');

        $this->assertSame('abc', $result);
    }

    public function testDecodeUriComponentEmptyString(): void
    {
        $result = UrlUtils::decodeUriComponent('');

        $this->assertSame('', $result);
    }

    public function testRoundTripWithEncodeFragmentAndDecodeUriComponent(): void
    {
        $value = 'path/with spaces&special=chars#anchor';
        $encoded = UrlUtils::encodeUriComponent($value);

        $result = UrlUtils::decodeUriComponent($encoded);

        $this->assertSame($value, $result);
    }
}
